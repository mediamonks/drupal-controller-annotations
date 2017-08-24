Step 2: Usage
=============

A full example will probably demonstrate quickly how this works:

.. code-block:: php

    namespace Drupal\acme\Controller;

    use Drupal\controller_annotations\Configuration\Route;
    use Drupal\controller_annotations\Configuration\Cache;
    use Drupal\controller_annotations\Configuration\Template;
    use Drupal\controller_annotations\Configuration\ParamConverter;
    use Drupal\controller_annotations\Configuration\Method;
    use Drupal\controller_annotations\Configuration\Security;
    use Drupal\controller_annotations\Configuration\Title;
    use Drupal\node\Entity\Node;

    /**
     * @Route("/articles")
     * @Cache(expires="tomorrow")
     */
    class ArticleController
    {
        /**
         * @Route
         * @Template
         * @Security(permission="access content")
         * @Title("My Title")
         */
        public function indexAction()
        {
            $articles = ...;

            return ['articles' => $articles];
        }

        /**
         * @Route("/{id}", name="article_edit")
         * @Method("GET")
         * @ParamConverter("article", options={"bundle": "article"})
         * @Template("acme:article:edit", vars={"article"})
         * @Cache(smaxage="15")
         * @Security(role="administrator")
         */
        public function editAction(Node $article) { }
    }

The documentation on the `Symfony Framework Extra Bundle`_ is a great read on what the possibilities are.
This document will mainly describe the differences between the Bundle and this module to prevent duplicating
the great documentation that is provided already.

@Route
------

The main difference is how the annotations are activated. In Drupal this should be added to your module routing
file which in the case of a module called "acme" be should be named ``acme.routing.yml`` and should be placed in the
root of your module folder.

.. code-block:: yml

    acme_annotations:
        path:
        options:
            type: annotation
            module: acme

This will assume the controllers of your module are placed in ``/modules/acme/src/Controller``

If you prefer to use a different path you can provide the path yourself manually instead:

.. code-block:: yml

    acme_annotations:
        path:
        options:
            type: annotation
            path: /modules/acme/src/SomewhereElse


You can give every route found within the supplied module or path a prefix by setting the path:

.. code-block:: yml

    acme_annotations:
        path: acme/
        options:
            type: annotation
            module: acme


An added feature to ``@Route`` is to flag your route as being an admin route:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Route;

    /**
     * @Route("path/to/route", admin=true)
     */

@Security
---------

Security is handled differently in Drupal so this section is different from the bundle.
It basically follows the same rules as usual and the options should look familiar.
Please note that no one will be able to access this route if this annotation isn't added.

Allow this route to be accessed under all circumstances:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(access=true)
     */

Require a specific permission:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(permission="access content")
     */

or role:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(role="administrator")
     */

or entity access:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(entity="node.view")
     */

or even point it to a custom access checker:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(custom="Drupal\acme\Security\Custom::access")
     */

Or if the callback function is defined in your class you can omit the class name:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;
    use Drupal\Core\Access\AccessResult;
    use Drupal\Core\Session\AccountInterface;

    /**
     * @Security(custom="access")
     */
    public function customAction() {
        return [];
    }

    /**
     * @param AccountInterface $account
     * @return AccessResult
     */
    public function access(AccountInterface $account)
    {
        return AccessResult::allowedIf($account->id() > 9000);
    }


You can also require a valid CSRF token for this endpoint:


.. code-block:: php

    use Drupal\controller_annotations\Configuration\Security;

    /**
     * @Security(access=true, csrf=true)
     */

@Cache
------

The cache annotation is very flexible and supports many different options:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Cache;

    /**
     * @Cache(expires="tomorrow", public=true)
     * @Cache(expires="+2 days")
     * @Cache(smaxage="15")
     * @Cache(vary={"Cookie"})
     */

@ParamConverter
---------------

Obviously the examples for Doctrine ORM are not applicable to Drupal but instead a ``NodeParamConverter`` is included:

.. code-block:: php

    use Drupal\node\Entity\Node;

    /**
     * @ParamConverter
     */
    public function editAction(Node $article) { }

You can also be a little more explicit and require a specific bundle:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\ParamConverter;
    use Drupal\node\Entity\Node;

    /**
     * @ParamConverter("article", options={"bundle": "article"})
     */
    public function editAction(Node $article) { }


This will also work for NodeInterface, Entity, EntityInterface, ContentEntity and ContentEntityInterface.

Just like with Symfony Framework you can add your own converters by creating a service which implements
``Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface``
and is tagged with ``controller_annotations.param_converter``.

@Template
---------

This basically does the same but the convention of resolving a string to a template is a little different.

If no template name is provided the template resolver will figure out the name of your module, controller and action
and convert this into the path of the template. This means that ``Drupal\<module>\Controller\<controller>Controller:<action>Action``
will be converted to the path ``modules/<module>/templates/<module>-<controller>(-<action>).html.twig``.

You can manually change the rendered template by using these formats instead:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Template;

    /**
     * @Template("acme:articles")
     * @Template("acme:articles:index")
     */

which will render to respectively ``modules/acme/templates/acme-articles.html.twig``
and ``modules/acme/templates/acme-articles-index.html.twig``


@Title
------

This one is specifically created for Drupal and allows to override the title

Set the title to a hardcoded value:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Title;

    /**
     * @Title("Hello World")
     */

Add arguments:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Title;

    /**
     * @Title("Hello @name", arguments={"@name":"You"})
     */

Add context:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Title;

    /**
     * @Title("Hello @name", context={"option":"value"})
     */

Use a callback:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Title;

    /**
     * @Title(callback="\Drupal\controller_annotations_test\Title\Custom::title")
     */

Or if the callback function is defined in your class you can omit the class name:

.. code-block:: php

    use Drupal\controller_annotations\Configuration\Title;

    /**
     * @Title(callback="title")
     */
    public function callbackAction() {
        return [];
    }

    /**
     * @return string
     */
    public function title() {
        return 'Hello Callback';
    }

Please note that is has to be public since otherwise it is not accessible from where it is called.

.. _`Symfony Framework Extra Bundle`: http://symfony.com/doc/master/bundles/SensioFrameworkExtraBundle/index.html

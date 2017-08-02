Step 1: Installation
====================

Since this module has other dependencies it is mandatory to install it with Composer.
Follow the instructions from the `Using Composer to manage Drupal site dependencies`_ page to install the module.

After this is setup correctly you can download this package with:

.. code-block:: bash

    composer require drupal/controller_annotations

You can then enable the module from the admin panel or use `Drush`_ to do it:

.. code-block:: bash

    drush en controller_annotations -y

.. _`Using Composer to manage Drupal site dependencies`: https://www.drupal.org/docs/develop/using-composer/using-composer-to-manage-drupal-site-dependencies
.. _`Drush`: http://www.drush.org/en/master/

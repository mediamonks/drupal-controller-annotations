Step 2: Configuration
=====================

To use the ``@Route`` annotation in your module you only need to add the code below to the ``acme.routing.yml`` of your module:

.. code-block:: yml

    acme_annotations:
        path:
        options:
            type: annotation
            module: acme

This will assume your controllers are placed in ``/modules/acme/src/Controller``

If you prefer to use a different path you can provide the path yourself manually instead:

.. code-block:: yml

    acme.annotations:
        path:
        options:
            type: annotation
            path: /modules/acme/src/Controller

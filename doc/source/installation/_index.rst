.. _ref_installation:

Installation
===============

The latest state of the packages can be installed directly from the GitHub repository.

.. note::
  This section describes how to install required components in a Linux environment.
  
  For other operating systems, please refer to the official documentation of your OS.

Install php
------------------------------

Install php8.1-cli package using the following command:

.. code-block:: shell

   sudo apt install php8.1-cli

Install package manager composer
---------------------------------

Composer is a dependency manager for PHP.

It allows you to manage your project dependencies and install packages from various sources, including GitHub.
See: `Composer <https://getcomposer.org/>`_ for more information.

.. code-block:: shell

   curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer && composer --version


Using composer to install the package
--------------------------------------

The otc-api-sign-sdk-php is available via `Packagist <https://packagist.org>`_ 
under the `opentelekomcloud-community/otc-api-sign-sdk-php <https://packagist.org/packages/opentelekomcloud-community/otc-api-sign-sdk-php>`_ package.

To add the dependency add following to your composer.json:
  
.. code-block:: json
   :caption: composer.json

    {     
      "require": {
        "opentelekomcloud-community/otc-api-sign-sdk-php": "dev-main as 1.0.0"
      }
    }

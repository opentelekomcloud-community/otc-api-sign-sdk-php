.. _ref_installation:

Installation
===============

The latest state of the packages can be installed directly from the GitHub repository.

Install php
------------------------------

.. code-block:: shell
   :caption: install php

   sudo apt install php8.1-cli

Install package manager composer
---------------------------------

Composer is a dependency manager for PHP.
It allows you to manage your project dependencies and install packages from various sources, including GitHub.
See: `Composer <https://getcomposer.org/>`_ for more information.

.. code-block:: shell
   :caption: install composer

   curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer && composer --version

GitHub Auth
---------------------------------

To access GitHub repository a GitHub OAuth Token is needed (see: 
`github-oauth <https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth>`_).


.. code-block:: shell
   :caption: env variable

   export COMPOSER_AUTH='{"github-oauth":{"github.com":"YOUR_GITHUB_TOKEN"}}'
  


Using composer to install the package
--------------------------------------

To install add following to your composer.json:
  
.. code-block:: json
   :caption: composer.json

    {     
      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/opentelekomcloud-community/otc-api-sign-sdk-php.git"
        }
      ],
      "require": {
        "opentelekomcloud-community/otc-api-sign-sdk-php": "dev-main as 1.0.0"
      }
    }


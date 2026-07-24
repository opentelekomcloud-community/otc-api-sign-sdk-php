Start ECS using ak/sk signing
=================================

.. toctree::
   :maxdepth: 3
   :includehidden:


Sample on how to start an ECS instance using an ak/sk request signing.


For complete source code, see :github_repo_master:`samples-doc/aksksigning/ecs-start <samples-doc/aksksigning/ecs-start>` on GitHub.   

Requirements
------------

.. list-table:: Environment variables
    :widths: 20 20 25
    :header-rows: 1

    * - Environment variable name
      - Value
      - Default

    * - ECS_INSTANCE_ID
      - <ID of ecs instance>
      - 

    * - OTC_SDK_PROJECTID
      - <Project ID>
      - Needed if ecs instance is in a sub project see :api_usage:`Obtaining a Project ID<guidelines/calling_apis/obtaining_required_information.html#obtaining-a-project-id>`

    * - OTC_SDK_AK
      - <Access Key>
      - see: :api_usage:`Generating AK and SK<guidelines/calling_apis/ak_sk_authentication/generating_an_ak_and_sk.html#apig-en-api-180328005>`

    * - OTC_SDK_SK
      - <Secret Key>
      - see: :api_usage:`Generating AK and SK<guidelines/calling_apis/ak_sk_authentication/generating_an_ak_and_sk.html#apig-en-api-180328005>`


Code
-------------------------

.. literalinclude:: ../../../../../samples-doc/aksksigning/ecs-start/startECS.php
   :language: php 


composer.json
-------------------------

.. literalinclude:: ../../../../../samples-doc/aksksigning/ecs-start/composer.json
   :language: json 


Running
-------------------------

.. code-block:: bash

   # Running the sample
   php startECS_AKSK.php


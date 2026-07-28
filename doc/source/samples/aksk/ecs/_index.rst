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

Following is the composer.json file for this sample. It is used to install dependencies.

.. literalinclude:: ../../../../../samples-doc/aksksigning/ecs-start/composer.json
   :language: json 

Install dependencies
-------------------------

Install dependencies using composer.

.. code-block:: bash

   composer update


Running
-------------------------

Run the sample using the following command. Make sure to set the required environment variables first.

.. code-block:: bash

   php startECS_AKSK.php


Following is a sample output of the script:

.. code-block:: text

    Starting ECS instance: cdb29bdd-1235-4e98-90d3-34bb77450393
    Using HTTP proxy: http://XXXXXXXX.XXX:8080
    Status Code: 200
    Headers: HTTP/1.0 200 Connection Established

    HTTP/1.1 200 
    Server: CloudWAF
    Date: Tue, 28 Jul 2026 11:30:53 GMT
    Content-Type: application/json
    Transfer-Encoding: chunked
    Connection: keep-alive
    Set-Cookie: CLOUDWAFSESID=e2dad73361814443ad; path=/
    Set-Cookie: CLOUDWAFSESTIME=1785238253109; path=/
    X-Request-Id: bd67bcc32b5e678f07fec825767676ae
    Accept-Ranges: bytes
    Vary: Accept-Charset, Accept-Encoding, Accept-Language, Accept
    Strict-Transport-Security: max-age=31536000; includeSubdomains;
    X-Frame-Options: SAMEORIGIN
    X-Content-Type-Options: nosniff
    X-Download-Options: noopen
    X-XSS-Protection: 1; mode=block;


    Response Body: {"job_id":"ff8080829df76048019fa87dee925ca3"}


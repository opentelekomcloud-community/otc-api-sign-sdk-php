Usage
======

.. toctree::
   :maxdepth: 2
   :includehidden:


For details on how to make API Requests,
see: :otc_docs:`Calling APIs <api-gateway/api-ref/calling_apis/index.html>`
in the API Gateway documentation.

Setup project
----------------------

Prerequisites
"""""""""""""""""""""""""
For prerequisites, please refer to the :ref:`installation section <ref_installation>`.

Create composer.json
"""""""""""""""""""""""""
Create composer.json file in your project root directory with following content,
as described in the :ref:`installation section <ref_installation>`.

.. code-block:: json
  :caption: composer.json

  {    
    "require": {
      "opentelekomcloud-community/otc-api-sign-sdk-php": "1.0.0"
    }
  }


Install dependencies
"""""""""""""""""""""""""
To install dependencies, run the following command:

.. code-block:: bash

  composer update

Create a php file
"""""""""""""""""""""""""

Create a php file in your project root directory with following content:

.. code-block:: php
  :caption: index.php

  <?php

  require __DIR__ . '/vendor/autoload.php';

  use OTC\Signer;
  use OTC\Request;

  // generate a signer instance and set your access key and secret key
  $signer = new Signer();
  $signer->Key = "YOUR_ACCESS_KEY";
  $signer->Secret = "YOUR_SECRET_KEY";

  // Generate a new request, and specify the domain name, method, request URI, and body
  $req = new Request('GET', 'https://service.region.example.com/v1/{project_id}/vpcs?limit=1');

  // Add a body if you have specified the PUT or POST method.
  // Special characters, such as the double quotation mark ("), 
  // contained in the body must be escaped.
  $req->body = '';

  // Add header parameters, for example, 
  // X-Domain-Id for invoking a global
  // service and X-Project-Id for invoking a project-level service.
  $req->headers = array(
    'Content-Type' => 'application/json;charset=utf8',
    'X-Project-Id' => 'xxx',
  );

  // Execute the following function to generate a $curl context variable.
  $curl = $signer->Sign($req);

  // Access the API and view the access result.
  $response = curl_exec($curl);
  echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
  echo $response;
  curl_close($curl);

  ?>

See the :ref:`Samples <ref_samples>` for more examples of using the SDK.

Start ECS using FunctionGraph and agency
========================================

.. toctree::
   :maxdepth: 10
   :includehidden:
   
Sample on how to start an ECS instance using an agency.

This sample show how to use functiongraph start an ECS instance using 
following credentials retrieved by the agency:


* context.getSecurityAccessKey()
* context.getSecuritySecretKey()
* context.getSecurityToken()


For complete source code, see :github_repo_master:`samples-doc/functiongraph/ecs-start<samples-doc/functiongraph/ecs-start>` on GitHub.

Code
-----

.. literalinclude:: ../../../../../samples-doc/functiongraph/ecs/src/index.php
   :language: php
   :caption: index.php
  
.. literalinclude:: ../../../../../samples-doc/functiongraph/ecs/composer.json
   :language: json
   :caption: composer.json


Deployment
----------

Build deployment zip
^^^^^^^^^^^^^^^^^^^^^^

Install dependencies using composer:

.. code-block:: bash

   composer update

To build the deployment zip, execute following command in
folder: **samples-doc/functiongraph/ecs-start**

.. code-block:: bash

   composer archive --format=zip --file=code 


This will create **code.zip** with code and dependencies included.
(See composer.json how zip is built).

Create FunctionGraph function
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Create function
*******************

Use `OpentelekomCloud FunctionGraph console <https://console.otc.t-systems.com/functiongraph/>`_ to create a function with following
settings:


**Create With**:  Create from scratch 

**Basic Information**

* **Function Type**  Event Function  
* **Region**  <YOUR REGION>  
* **Function Name** <YOUR FUNCTION NAME>  
* **Agency**  Specify an agency with policy to start ECS instance  
* **Runtime**  PHP 8.3

Upload code
*******************

Use **Upload** -> **Local ZIP** and upload *code.zip* from previous step.

Configure function
*******************

In **Configuration** -> **Basic Settings** -> **Handler**:   *src/index.handler*

In **Configuration** -> **Environment Variables** add following variables:

.. list-table:: Environment variables
    :widths: 20 20 25
    :header-rows: 1

    * - Environment variable name
      - Value
      - Default

    * - ECS_INSTANCE_ID
      - <ID of ecs instance>
      - 

    * - ECS_ENDPOINT
      - <ecs endpoint>
      - Default: ecs.eu-de.otc.t-systems.com , :otc_docs:`Regions and Endpoints<regions-and-endpoints/index.html>`


Create Test Event
*******************

In **Code** create a Test Event using "Blank Template" (Event is not used in function)

Test function
^^^^^^^^^^^^^^^

Click **Test** to test function.


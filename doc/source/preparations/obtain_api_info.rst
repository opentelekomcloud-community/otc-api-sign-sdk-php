Obtaining API Information
=========================

.. toctree::
   :maxdepth: 10
   :includehidden:

The following information is required for API calling:

- Endpoint and URI that will constitute the request URL
- AK/SK used for signing and authentication
- Project ID and subproject ID
- Account name and account ID
- API environment
- Domain name for Host

.. list-table::
   :header-rows: 1

   * - Information
     - Description

   * - Endpoint
     - Endpoint of a cloud service in a region.
       For details, see :otc_docs:`Regions and Endpoints <regions-and-endpoints/index.html>`.

   * -  URI
     - API request path and parameters.
       Obtain the request path and parameters from the API Reference of the cloud service.

   * - AK/SK
     - Access key ID (AK) and secret access key (SK), which are used to sign API requests.
       For details on how to obtain the AK/SK, see :ref:`Obtaining an AK/SK  <ref_obtain_aksk>`

   * - Project ID
     - Project ID, which needs to be configured for the URI of most APIs to identify different projects.

       To obtain the project ID, perform the following steps:

       1. Go to the console homepage.
       2. Hover over the username in the upper right, choose **My Credentials** from the drop-down list, and then view the project ID.
          Projects physically isolate cloud server resources by region.
          Multiple projects can be created in the same region to implement more fine-grained isolation.
          Find the region where your server locates on the right of the page, and obtain the corresponding project ID from the list on the left.

   * - X-Project-Id
     - Subproject ID, which is used in multi-project scenarios. To access resources in a subproject through AK/SK-based authentication,
       the X-Project-Id field must be added to the request header.

       To obtain the project ID, perform the following steps:

       1. Go to the console homepage.
       2. Hover the cursor on the username and choose **My Credentials** from the drop-down list.
       3. Find the project, click the plus sign (+) on the left of the project, and obtain the subproject ID.

   * - X-Domain-Id
     - Account ID, which is used to:

        - Obtain a token for token authentication.
        - Call APIs of global services through AK/SK authentication. Global services (such as IAM and OBS) are deployed without specifying physical regions.

       To obtain the account ID, perform the following steps:

       1. Go to the console homepage.
       2. Hover the cursor on the username and choose **My Credentials** from the drop-down list.
       3. View the **Account Name** and **Account ID**.

   * - x-stage
     - For details, see the API environment information of each cloud service.

   * - Host
     - Debugging domain name or independent domain name of the group to which the API belongs.
     
       For details, see the domain name information in the API group to which the API of each cloud service belongs.
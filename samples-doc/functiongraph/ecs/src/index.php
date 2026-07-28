<?php
require_once __DIR__ . '/../vendor/autoload.php';


function handler($event, $context)
{
  $project_id = getenv('RUNTIME_PROJECT_ID');
  $endpoint = $context->getUserData("ECS_ENDPOINT");
  $instance_id = $context->getUserData('ECS_INSTANCE_ID');

  $signer = new Signer();
  // use the temporary security credentials provided by the function context
  $signer->Key = $context->getSecurityAccessKey();
  $signer->Secret = $context->getSecuritySecretKey();
  $signer->SecurityToken = $context->getSecurityToken();

  echo "Starting ECS instance: " . $instance_id . "\n";

  // see https://docs.otc.t-systems.com/elastic-cloud-server/api-ref/apis_recommended/batch_operations/starting_ecss_in_a_batch.html#en-us-topic-0020212207
  $url = 'https://' . $endpoint . '/v1/' . $project_id . '/cloudservers/action';

  $body = json_encode([
    'os-start' => [
      'servers' => [['id' => $instance_id]],
    ],
  ]);

  $headers = [
    'Content-Type' => 'application/json;charset=utf8',
  ];

  if ($project_id !== false && $project_id !== '') {
    // To access resources in a sub-project (e.g. eu_de/myproject)
    // by calling APIs, X-Project-Id of "eu_de/myproject" is needed
    $headers['X-Project-Id'] = $project_id;
  }

  $req = new Request('POST', $url, $headers, $body);

  $curl = $signer->Sign($req);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, true);

  $response = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

  $response_body = "";

  if ($status == 0) {
    echo "Error: " . curl_error($curl) . "\n";
    $response_body = curl_error($curl);
    $status = 500;
  } else {
    $response_headers = substr($response, 0, $header_size);
    $response_body = substr($response, $header_size);
    echo "Status Code: " . $status . "\n";
    echo "Response Headers: " . $response_headers . "\n";
    echo "Response Body: " . $response_body . "\n";
  }
  curl_close($curl);

  $output = array(
    "statusCode" => $status,
    "headers" => array(
      "Content-Type" => "application/json",
    ),
    "isBase64Encoded" => false,
    "body" => $response_body,
  );
  return $output;
}
?>

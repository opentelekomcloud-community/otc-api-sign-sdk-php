<?php declare(strict_types=1);

/**
 * Start an ECS instance using the OTC API with streaming response.
 */

require ('../otc_api_sign_core/Signer.php');
require ('../otc_api_sign_core/Request.php');

use OTC\Request;
use OTC\Signer;

function checkEnvVars()
{
  $required = ['OTC_SDK_PROJECT_ID', 'OTC_SDK_AK', 'OTC_SDK_SK', 'ECS_INSTANCE_ID'];
  $missing = [];
  foreach ($required as $var) {
    $val = getenv($var);
    if ($val === false || $val === '') {
      $missing[] = $var;
    }
  }
  if (!empty($missing)) {
    echo "Error: missing required environment variables:\n";
    foreach ($missing as $var) {
      echo "  - $var\n";
    }
    exit(1);
  }
}

function startECSStream()
{
  checkEnvVars();

  $project_id = getenv('OTC_SDK_PROJECT_ID');
  $endpoint = 'ecs.eu-de.otc.t-systems.com';
  $instance_id = getenv('ECS_INSTANCE_ID');

  echo 'Starting ECS instance: ' . $instance_id . "\n";

  // see https://docs.otc.t-systems.com/elastic-cloud-server/api-ref/apis_recommended/batch_operations/starting_ecss_in_a_batch.html#en-us-topic-0020212207
  $url = 'https://' . $endpoint . '/v1/' . $project_id . '/cloudservers/action';

  $body = json_encode([
    'os-start' => [
      'servers' => [['id' => $instance_id]],
    ],
  ]);

  $headers = [
    'Content-Type' => 'application/json;charset=utf8',
    // 'X-Sdk-Content-Sha256' => 'UNSIGNED-PAYLOAD',
  ];

  if ($project_id !== false && $project_id !== '') {
    // To access resources in a sub-project (e.g. eu_de/myproject)
    // by calling APIs, X-Project-Id of "eu_de/myproject" is needed
    $headers['X-Project-Id'] = $project_id;
  }

  $req = new Request('POST', $url, $headers, $body);

  $signer = new Signer();
  $signer->Key = getenv('OTC_SDK_AK');
  $signer->Secret = getenv('OTC_SDK_SK');
  $signer->Sign($req);

  $options = [
    'http' => [
      'method' => 'POST',
      'header' => $signer->headersAsString($req),
      'content' => $body,
      'ignore_errors' => true  // Prevents PHP fatal errors if cloud responds with 4xx/5xx
    ]
  ];

  // use proxy if defined in environment
  $proxy = getenv('HTTP_PROXY');

  if ($proxy !== false && $proxy !== '') {
    $proxyParts = parse_url($proxy);
    if ($proxyParts === false || empty($proxyParts['host'])) {
      echo "Error: HTTP_PROXY must be a valid proxy URL.\n";
      exit(1);
    }

    $proxyPort = $proxyParts['port'] ?? 80;
    $proxyAddress = 'tcp://' . $proxyParts['host'] . ':' . $proxyPort;

    echo 'Using HTTP proxy: ' . $proxyAddress . "\n";
    $options['http']['proxy'] = $proxyAddress;
    $options['http']['request_fulluri'] = true;
  }

  $streamContext = stream_context_create($options);
  $response = file_get_contents($url, false, $streamContext);

  // 6. Check response codes
  $statusLine = $http_response_header[0] ?? '';
  preg_match('{HTTP\/\S*\s(\d+)}', $statusLine, $matches);
  $statusCode = isset($matches[1]) ? (int) $matches[1] : 500;

  if ($statusCode === 200 || $statusCode === 201) {
    echo 'ECS instance started successfully. Response: ' . $response . "\n";
  } else {
    echo "Failed to start ECS instance. Status code: $statusCode. Response: " . $response . "\n";
  }
}

startECSStream();
?>
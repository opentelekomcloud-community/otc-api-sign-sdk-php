<?php
require("../otc_api_sign_core/signer.php");

function checkEnvVars() {
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

function startECS() {
    checkEnvVars();

    $project_id = getenv('OTC_SDK_PROJECT_ID');
    $endpoint = 'ecs.eu-de.otc.t-systems.com';
    $instance_id = getenv('ECS_INSTANCE_ID');

    $signer = new Signer();
    $signer->Key = getenv('OTC_SDK_AK');
    $signer->Secret = getenv('OTC_SDK_SK');



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
        // 'X-Sdk-Content-Sha256' => 'UNSIGNED-PAYLOAD',
    ];

    if ($project_id !== false && $project_id !== '') {
        // To access resources in a sub-project (e.g. eu_de/myproject)
        // by calling APIs, X-Project-Id of "eu_de/myproject" is needed
        $headers['X-Project-Id'] = $project_id;
    }

    $req = new Request('POST', $url, $headers, $body);

    $curl = $signer->Sign($req);

    

    // use proxy if defined in environment
    $proxy = getenv('HTTP_PROXY');
    if ($proxy !== false && $proxy !== '') {
        echo "Using HTTP proxy: " . $proxy . "\n";
        curl_setopt($curl, CURLOPT_PROXY, $proxy);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);

    $response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

    if ($status == 0) {
        echo "Error: " . curl_error($curl) . "\n";
    } else {
        $response_headers = substr($response, 0, $header_size);
        $response_body = substr($response, $header_size);
        echo "Status Code: " . $status . "\n";
        echo "Headers: " . $response_headers . "\n";
        echo "Response Body: " . $response_body . "\n";
    }

    curl_close($curl);
}

startECS();
?>
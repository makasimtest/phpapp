<?php

require_once __DIR__.'/vendor/autoload.php';

$data = json_decode(file_get_contents(getenv("DUNDERGITCALL_FILE")), true);

$data['timestamp'] = time();

if (!empty($data['webhook'])) {
    $guzzle = new \GuzzleHttp\Client();
    $guzzle->post($data['webhook'], [
        'json' => $data,
    ]);
}

file_put_contents(getenv("DUNDERGITCALL_FILE"), json_encode($data));

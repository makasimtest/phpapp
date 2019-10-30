<?php

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

require_once __DIR__.'/vendor/autoload.php';

ini_set('memory_limit', -1);

$data = json_decode(file_get_contents(getenv("DUNDERGITCALL_FILE")), true);

$data['timestamp'] = time();
$data['app'] = 'phprepo';

if (!empty($data['webhook'])) {
    $guzzle = new \GuzzleHttp\Client();
    $guzzle->post($data['webhook'], [
        'json' => $data,
    ]);
}

if (!empty($data['s3'])) {
    echo "upload file\n";

    $s3 = new S3Client([
        'version' => 'latest',
        'region' => 'ams3',
        'endpoint' => 'https://fra1.digitaloceanspaces.com',
        'credentials' => [
           'key'    => $data['s3']['key'],
           'secret' => $data['s3']['secret'],
        ],
        'debug' => true
    ]);

    $fileName = time().'.txt';

    try {
        $result = $s3->putObject([
            'Content-Type'      => 'text/plain',
            //         'Content-Length'    => $fileSize,
            'Bucket'            => 'gitcalltest',
            'Key'               => $fileName,
            'Body'              => 'hello there!',
            'ACL'               => 'public-read',
        ]);

       var_dump($result->toArray());
    } catch (S3Exception $e) {
        echo "there has been an exception: \n\n".((string) $e);
    }
}

file_put_contents(getenv("DUNDERGITCALL_FILE"), json_encode($data));
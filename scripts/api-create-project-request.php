<?php

declare(strict_types=1);

$apiKey = 'qwerty';
$url = 'http://localhost:8000/api/projects';
$data = json_encode([
    'title' => 'Test Project',
    'description' => 'Test Project Description',
]);
$headers = [
    'Content-Type: application/json',
    'X-Api-Key: ' . $apiKey,
];

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => $headers,
));

$response = curl_exec($curl);

curl_close($curl);

echo "<pre>", print_r(json_decode($response, true)), "</pre>";
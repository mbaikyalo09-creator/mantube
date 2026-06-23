<?php

$api_key = "putypurapihere";
$email = "putyouremailhere";
$transaction_request_id = $_GET['id'] ?? null;

if (!$transaction_request_id) {
    echo json_encode(["error" => "Missing transaction_request_id"]);
    exit;
}

$payload = [
    "api_key" => $api_key,
    "email"   => $email,
    "transaction_request_id" => $transaction_request_id
];

$ch = curl_init("https://api.finaswift.com/v1/transactionstatus");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
]);

$response = curl_exec($ch);
curl_close($ch);

// DEBUG LOG
file_put_contents("verify_log.txt",
    "VERIFY REQUEST ID: $transaction_request_id\nRESPONSE:\n$response\n\n",
    FILE_APPEND
);

echo $response;

<?php

$data = json_decode(file_get_contents("php://input"), true);

$api_key = "putyourapihere";
$email = "putyouremailhere";

$amount = $data['amount'];
$msisdn = $data['msisdn'];

$payload = [
    "api_key"   => $api_key,
    "email"     => $email,
    "amount"    => $amount,
    "msisdn"    => $msisdn,
    "reference" => "Test Payment"
];

$ch = curl_init("https://api.finaswift.com//v1/stkpush");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// DEBUG LOG
file_put_contents("pay_log.txt", "PAY RESPONSE:\n" . $response . "\n\n", FILE_APPEND);

echo json_encode($result);

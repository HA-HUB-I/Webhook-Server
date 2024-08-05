<?php
// webhook-handler.php

// Retrieve the request's body
$body = file_get_contents('php://input');

// Retrieve the request's headers
$headers = getallheaders();

// Log the webhook for debugging purposes (optional)
file_put_contents('webhook.log', $body . PHP_EOL, FILE_APPEND);

// Verify the webhook signature if a secret is set in WooCommerce (optional but recommended)
$secret = 'WT;2z>pFw,Xt,1zCxmiBhGZ*r9d.AqO58$k4$#!HO}TP/Xn2=e'; // Set this to the secret you used in WooCommerce

if (isset($headers['x-wc-webhook-signature'])) {
    $signature = base64_encode(hash_hmac('sha256', $body, $secret, true));

    if ($signature !== $headers['x-wc-webhook-signature']) {
        // Invalid signature
        http_response_code(400);
        exit('Invalid signature');
    }
}

// Decode the JSON body
$data = json_decode($body, true);

if (json_last_error() === JSON_ERROR_NONE) {
    // Process the webhook payload
    if (isset($data['id']) && isset($data['status']) && $data['status'] == 'processing') {
        $order_id = $data['id'];
        $order_status = $data['status'];
        // Perform your custom actions with the new order data
        // For example, log the order ID
        $log_file_name = 'neworder_' . $order_id . '_' . $order_status . '.log';
        file_put_contents($log_file_name,  $body . PHP_EOL, FILE_APPEND);
    }

    if (isset($data['id']) && isset($data['status']) && $data['status'] == 'completed') {
        $order_id = $data['id'];
        $order_status = $data['status'];
        // Perform your custom actions with the new order data
        // For example, log the order ID
        $log_file_name = 'order_' . $order_id . '_' . $order_status . '.log';
        file_put_contents($log_file_name,  $body . PHP_EOL, FILE_APPEND);
    }

    if (isset($data['id']) && isset($data['status']) && $data['status'] == 'on-hold') {
        $order_id = $data['id'];
        $order_status = $data['status'];
        // Perform your custom actions with the new order data
        // For example, log the order ID
        $log_file_name = 'order_' . $order_id . '_' . $order_status . '.log';
        file_put_contents($log_file_name,  $body . PHP_EOL, FILE_APPEND);
    }


    // Respond with a 200 status code to acknowledge receipt of the webhook
    http_response_code(200);
} else {
    // Invalid JSON
    http_response_code(400);
    exit('Invalid JSON');
}
?>

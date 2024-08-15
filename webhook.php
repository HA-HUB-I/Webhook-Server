<?php
// webhook-handler.php

// Retrieve the request's body
$body = file_get_contents('php://input');

// Retrieve the request's headers in a case-insensitive manner
$headers = array_change_key_case(getallheaders(), CASE_LOWER);

// Log the webhook for debugging purposes (optional)
file_put_contents('webhook.log', $body . PHP_EOL, FILE_APPEND);

// Verify the webhook signature if a secret is set in WooCommerce (optional but recommended)
$secret = 'JEV#JE&s([HD8m%Q*ub!U[FNi/MZO`|Y]:J *:!>b-Wa|Ok-6['; // Set this to the secret you used in WooCommerce

if (isset($headers['x-wc-webhook-signature'])) {
    $signature = base64_encode(hash_hmac('sha256', $body, $secret, true));

    if ($signature !== $headers['x-wc-webhook-signature']) {
        // Invalid signature
        http_response_code(400);
        header('Content-Type: text/plain');
        exit('Invalid signature');
    }
}

// Decode the JSON body
$data = json_decode($body, true);

if (json_last_error() === JSON_ERROR_NONE) {
    // Ensure that the 'id' and 'status' fields are present
    if (isset($data['id']) && isset($data['status'])) {
        $order_id = $data['id'];
        $order_status = $data['status'];

        // Array of statuses you're interested in
        $valid_statuses = ['processing', 'completed', 'on-hold'];

        if (in_array($order_status, $valid_statuses)) {
            // Perform your custom actions with the new order data
            // For example, log the order ID
            $log_file_name = 'order_' . $order_id . '_' . $order_status . '.log';
            file_put_contents($log_file_name, $body . PHP_EOL, FILE_APPEND);
        }

        // Respond with a 200 status code to acknowledge receipt of the webhook
        http_response_code(200);
        header('Content-Type: text/plain');
        exit('Webhook processed');
    } else {
        // Missing fields
        http_response_code(400);
        header('Content-Type: text/plain');
        exit('Missing order ID or status');
    }
} else {
    // Invalid JSON
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Invalid JSON');
}
?>

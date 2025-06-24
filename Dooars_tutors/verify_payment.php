<?php
// verify_payment.php

require_once 'config.php';
require_once 'razorpay-php/razorpay-php/Razorpay.php';

use Razorpay\Api\Api;

header('Content-Type: application/json');

// Read and decode incoming JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['payment_id'], $input['order_id'], $input['signature'], $input['tutor_id'], $input['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Incomplete or invalid request data.']);
    exit;
}

$tutor_id   = intval($input['tutor_id']);
$payment_id = $input['payment_id'];
$order_id   = $input['order_id'];
$signature  = $input['signature'];
$amount     = floatval($input['amount']);

try {
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

    // Signature verification
    $attributes = [
        'razorpay_order_id'   => $order_id,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature'  => $signature
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // Payment verified - update DB
    $stmt = $connection->prepare("UPDATE tutors 
        SET payment_status = 'paid', 
            payment_id = ?, 
            payment_amount = ?, 
            order_id = ?, 
            payment_date = NOW() 
        WHERE id = ?");

    if (!$stmt) {
        throw new Exception("DB prepare error: " . $connection->error);
    }

    $stmt->bind_param("sdsi", $payment_id, $amount, $order_id, $tutor_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success'    => true,
            'message'    => 'Payment verified and updated.',
            'payment_id' => $payment_id
        ]);
    } else {
        throw new Exception("DB execution error: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Payment verification error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Payment verification failed. Please try again or contact support.'
    ]);
}
?>

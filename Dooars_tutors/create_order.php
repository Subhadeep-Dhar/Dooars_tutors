<?php
// create_order.php - NEW FILE
require_once 'config.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['tutor_id']) || !isset($input['amount'])) {
        throw new Exception('Missing required parameters');
    }
    
    $tutor_id = intval($input['tutor_id']);
    $amount = floatval($input['amount']);
    
    // Validate tutor exists
    $query = "SELECT id, name, email FROM tutors WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $tutor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Tutor not found');
    }
    
    // Create Razorpay order using cURL (since SDK might not be available)
    $order_data = array(
        'amount' => $amount * 100, // Amount in paise
        'currency' => 'INR',
        'receipt' => 'TUTOR_REG_' . $tutor_id . '_' . time(),
        'notes' => array(
            'tutor_id' => $tutor_id,
            'registration_fee' => $amount
        )
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        throw new Exception('Failed to create Razorpay order: ' . $response);
    }
    
    $order = json_decode($response, true);
    
    if (!$order || !isset($order['id'])) {
        throw new Exception('Invalid response from Razorpay');
    }
    
    echo json_encode([
        'success' => true,
        'order_id' => $order['id'],
        'amount' => $order['amount'],
        'currency' => $order['currency']
    ]);
    
} catch (Exception $e) {
    error_log('Create order error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
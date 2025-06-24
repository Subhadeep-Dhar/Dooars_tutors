<?php
// payment.php
require_once 'config.php';
require_once 'razorpay-php/razorpay-php/Razorpay.php';

use Razorpay\Api\Api;

$tutor_id = $_GET['tutor_id'] ?? null;

if (!$tutor_id || !is_numeric($tutor_id)) {
    die("Invalid tutor ID.");
}

// Fetch tutor info
$stmt = $connection->prepare("SELECT * FROM tutors WHERE id = ?");
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
$tutor = $result->fetch_assoc();

if (!$tutor) {
    die("Tutor not found.");
}

// Create Razorpay order
$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

$orderData = [
    'receipt'         => 'TUTOR_' . $tutor_id . '_' . time(),
    'amount'          => REGISTRATION_FEE * 100, // in paise
    'currency'        => CURRENCY,
    'payment_capture' => 1,
    'notes'           => ['tutor_id' => $tutor_id]
];

try {
    $razorpayOrder = $api->order->create($orderData);
    $order_id = $razorpayOrder['id'];
} catch (Exception $e) {
    die("Error creating Razorpay order: " . $e->getMessage());
}echo "<pre>";
print_r($razorpayOrder);
echo "</pre>";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Pay Registration Fee</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h2>Welcome <?php echo htmlspecialchars($tutor['name']); ?></h2>
    <p>Email: <?php echo htmlspecialchars($tutor['email']); ?></p>
    <p>Phone: <?php echo htmlspecialchars($tutor['phone']); ?></p>
    <h3>"amount": <?php echo (int) $razorpayOrder['amount']; ?>,</h3>
    <button id="payBtn">Pay Now</button>

    <script>
    const options = {
        "key": "<?php echo RAZORPAY_KEY_ID; ?>",
        "amount": <?php echo (int) $razorpayOrder['amount']; ?>,
        "currency": "<?php echo CURRENCY; ?>",
        "name": "<?php echo COMPANY_NAME; ?>",
        "description": "Tutor Registration Fee",
        "order_id": "<?php echo $order_id; ?>",
        "handler": function (response) {
            console.log("Payment success:", response);
            // Your fetch() here...
        },
        "prefill": {
            "name": "<?php echo htmlspecialchars($tutor['name']); ?>",
            "email": "<?php echo htmlspecialchars($tutor['email']); ?>",
            "contact": "<?php echo htmlspecialchars($tutor['phone']); ?>"
        },
        "theme": {
            "color": "#3399cc"
        }
    };

    console.log("Razorpay Options:", options);

    document.getElementById('payBtn').onclick = function () {
        const rzp = new Razorpay(options);
        rzp.open();
    };
</script>

</body>
</html>

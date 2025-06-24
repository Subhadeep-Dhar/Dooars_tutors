<?php
// DB config

// Database configuration
$host = 'localhost:3307';
$dbname = 'dooars_tutors';
$username = 'root';
$pass = '';


$pdo = new PDO("mysql:host=localhost:3307;dbname=dooars_tutors;charset=utf8", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Fetch all referrals where reward not given
$stmt = $pdo->query("SELECT * FROM referrals WHERE reward_given = 0");
$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($referrals as $ref) {
    $referrer_id = $ref['referrer_id'];
    $referee_id = $ref['referee_id'];

    // 2. Update referrer and referee wallet_balance
    $pdo->prepare("UPDATE tutors SET wallet_balance = wallet_balance + 50 WHERE id = :id")
        ->execute([':id' => $referrer_id]);

    $pdo->prepare("UPDATE tutors SET wallet_balance = wallet_balance + 50 WHERE id = :id")
        ->execute([':id' => $referee_id]);

    // 3. Mark reward as given
    $pdo->prepare("UPDATE referrals SET reward_given = 1 WHERE id = :id")
        ->execute([':id' => $ref['id']]);

    echo "Reward credited for referral ID {$ref['id']}\n";
}

<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$transactionId = (string) ($_GET['txn'] ?? '');

$orderQuery = $pdo->prepare(
    "SELECT orders.*, items.title, items.image
     FROM orders
     JOIN items ON orders.item_id = items.id
     WHERE orders.transaction_id = :txn AND orders.buyer_id = :buyer_id"
);
$orderQuery->execute([
    ':txn' => $transactionId,
    ':buyer_id' => $_SESSION['user_id'],
]);
$order = $orderQuery->fetch();

if ($order === false) {
    header('Location: my_wins.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Order Confirmed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <?php require __DIR__ . '/partials_header.php'; ?>
    <main class="auth-shell">
        <section class="auth-card auth-card-wide" style="text-align:center;">
            <div class="brand-mark" style="justify-content:center;">
                <div class="brand-icon" style="background:linear-gradient(135deg, #2ecc71, #27ae60);">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <h1 style="margin-top:10px;">Payment Successful!</h1>
            <p class="auth-copy">Your order has been confirmed. Here's your receipt.</p>

            <div class="detail-card" style="text-align:left; margin:20px 0;">
                <div style="margin-bottom:12px;">
                    <span class="detail-label">Transaction ID</span>
                    <strong><?php echo e($order['transaction_id']); ?></strong>
                </div>
                <div style="margin-bottom:12px;">
                    <span class="detail-label">Item</span>
                    <strong><?php echo e($order['title']); ?></strong>
                </div>
                <div style="margin-bottom:12px;">
                    <span class="detail-label">Amount Paid</span>
                    <strong>Rs. <?php echo number_format((float) $order['amount'], 2); ?></strong>
                </div>
                <div style="margin-bottom:12px;">
                    <span class="detail-label">Payment Method</span>
                    <strong><?php echo e($order['payment_method']); ?></strong>
                </div>
                <div>
                    <span class="detail-label">Date</span>
                    <strong><?php echo e($order['created_at']); ?></strong>
                </div>
            </div>

            <p class="auth-switch">
                <a href="my_orders.php">View Order History</a> &nbsp;|&nbsp;
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
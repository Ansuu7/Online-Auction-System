<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$ordersQuery = $pdo->prepare(
    "SELECT orders.*, items.title
     FROM orders
     JOIN items ON orders.item_id = items.id
     WHERE orders.buyer_id = :buyer_id
     ORDER BY orders.created_at DESC"
);
$ordersQuery->execute([':buyer_id' => $_SESSION['user_id']]);
$orders = $ordersQuery->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Order History</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2377/2377930.png">
</head>
<body class="auth-page">
    <?php require __DIR__ . '/partials_header.php'; ?>
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-receipt"></i></div>
                <div>
                    <p class="eyebrow">Payment History</p>
                    <h1>My Orders</h1>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <p class="auth-copy">No orders yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="detail-card" style="margin-bottom:14px; display:flex; flex-direction:column; gap:6px;">
                        <span class="detail-label"><?php echo e($order['title']); ?></span>
                        <strong>Rs. <?php echo number_format((float) $order['amount'], 2); ?> via <?php echo e($order['payment_method']); ?></strong>
                        <span class="detail-label"><?php echo e($order['transaction_id']); ?> — <?php echo e($order['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <p class="auth-switch">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
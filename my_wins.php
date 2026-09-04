<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}


$winsQuery = $pdo->prepare(
    "SELECT items.*, users.full_name AS seller_name
     FROM items
     JOIN users ON items.seller_id = users.id
     WHERE items.winner_id = :user_id
     ORDER BY items.end_time DESC"
);
$winsQuery->execute([':user_id' => $_SESSION['user_id']]);
$wonItems = $winsQuery->fetchAll();

$total = 0;
foreach ($wonItems as $wonItem) {
    $total += (float) $wonItem['current_price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | My Won Items</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <?php require __DIR__ . '/partials_header.php'; ?>
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-trophy"></i></div>
                <div>
                    <p class="eyebrow">Won Auctions</p>
                    <h1>My Won Items</h1>
                </div>
            </div>

            <?php if (empty($wonItems)): ?>
                <p class="auth-copy">You haven't won any auctions yet.</p>
            <?php else: ?>
                <?php foreach ($wonItems as $wonItem): ?>
                    <div class="detail-card" style="margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
                        <div>
                            <span class="detail-label"><?php echo e($wonItem['title']); ?> — Seller: <?php echo e($wonItem['seller_name']); ?></span>
                            <strong>Rs. <?php echo number_format((float) $wonItem['current_price'], 2); ?></strong>
                        </div>

                        <?php if ($wonItem['payment_status'] === 'paid'): ?>
                            <span class="session-badge">Paid</span>
                        <?php else: ?>
                            <a class="btn btn-flat btn-small" href="checkout.php?item_id=<?php echo (int) $wonItem['id']; ?>">Checkout</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="detail-card" style="margin-top:20px; background:var(--primary-soft);">
                    <span class="detail-label">Total (All Won Items)</span>
                    <strong style="font-size:1.3rem;">Rs. <?php echo number_format($total, 2); ?></strong>
                </div>
            <?php endif; ?>

            <p class="auth-switch">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
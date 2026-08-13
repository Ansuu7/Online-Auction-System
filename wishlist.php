<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$itemsQuery = $pdo->prepare(
    "SELECT items.*, users.full_name AS seller_name
     FROM wishlist
     JOIN items ON wishlist.item_id = items.id
     JOIN users ON items.seller_id = users.id
     WHERE wishlist.user_id = :user_id
     ORDER BY wishlist.created_at DESC"
);
$itemsQuery->execute([':user_id' => $_SESSION['user_id']]);
$items = $itemsQuery->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | My Wishlist</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-heart"></i></div>
                <div>
                    <p class="eyebrow">Saved Items</p>
                    <h1>My Wishlist</h1>
                </div>
            </div>

            <?php if (empty($items)): ?>
                <p class="auth-copy">You haven't wishlisted any items yet.</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="detail-card" style="margin-bottom:14px;">
                        <span class="detail-label"><?php echo e($item['title']); ?> — Rs. <?php echo number_format((float) $item['current_price'], 2); ?></span>
                        <strong><?php echo e($item['status']); ?></strong>
                        <a class="btn btn-primary btn-small" href="item_details.php?id=<?php echo (int) $item['id']; ?>" style="margin-top:8px;">View Item</a>
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
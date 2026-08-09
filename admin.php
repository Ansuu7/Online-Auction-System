<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['is_admin'])) {
    header('Location: dashboard.php');
    exit;
}

$usersQuery = $pdo->query('SELECT id, full_name, email, is_admin, created_at FROM users ORDER BY created_at DESC');
$users = $usersQuery->fetchAll();

$itemsQuery = $pdo->query(
    "SELECT items.*, users.full_name AS seller_name
     FROM items
     JOIN users ON items.seller_id = users.id
     ORDER BY items.created_at DESC"
);
$items = $itemsQuery->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div>
                    <p class="eyebrow">Admin Panel</p>
                    <h1>AuctionHub Admin</h1>
                </div>
            </div>

            <h2>All Users (<?php echo count($users); ?>)</h2>
            <?php foreach ($users as $u): ?>
                <div class="detail-card" style="margin-bottom:10px;">
                    <span class="detail-label"><?php echo e($u['email']); ?> — joined <?php echo e($u['created_at']); ?></span>
                    <strong><?php echo e($u['full_name']); ?><?php echo $u['is_admin'] ? ' (Admin)' : ''; ?></strong>
                </div>
            <?php endforeach; ?>

            <h2 style="margin-top:28px;">All Items (<?php echo count($items); ?>)</h2>
            <?php foreach ($items as $i): ?>
                <div class="detail-card" style="margin-bottom:10px;">
                    <span class="detail-label"><?php echo e($i['title']); ?> by <?php echo e($i['seller_name']); ?> — <?php echo e($i['status']); ?></span>
                    <strong>Rs. <?php echo number_format((float) $i['current_price'], 2); ?></strong>
                    <form method="post" action="admin_delete_item.php" style="margin-top:8px;">
                        <input type="hidden" name="item_id" value="<?php echo (int) $i['id']; ?>">
                        <button type="submit" class="btn-primary" style="background:#b42318;">Delete Item</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <p class="auth-switch">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
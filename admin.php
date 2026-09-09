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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['force_end_item'])) {
    $itemId = (int) ($_POST['item_id'] ?? 0);

    $winnerQuery = $pdo->prepare(
        'SELECT bidder_id FROM bids WHERE item_id = :item_id ORDER BY bid_amount DESC LIMIT 1'
    );
    $winnerQuery->execute([':item_id' => $itemId]);
    $winner = $winnerQuery->fetch();
    $winnerId = $winner !== false ? $winner['bidder_id'] : null;

    $closeItem = $pdo->prepare("UPDATE items SET status = 'closed', winner_id = :winner_id WHERE id = :id");
    $closeItem->execute([
        ':winner_id' => $winnerId,
        ':id' => $itemId,
    ]);

    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin'])) {
    $userId = (int) ($_POST['user_id'] ?? 0);

    $targetQuery = $pdo->prepare('SELECT email FROM users WHERE id = :id');
    $targetQuery->execute([':id' => $userId]);
    $target = $targetQuery->fetch();

    $isSuperadmin = $target !== false && $target['email'] === 'ansuu1@gmail.com';
    $isSelf = $userId === (int) $_SESSION['user_id'];

    if (!$isSuperadmin && !$isSelf) {
        $toggle = $pdo->prepare('UPDATE users SET is_admin = NOT is_admin WHERE id = :id');
        $toggle->execute([':id' => $userId]);
    }

    header('Location: admin.php');
    exit;
}

$usersQuery = $pdo->query('SELECT id, full_name, email, is_admin, created_at FROM users ORDER BY created_at DESC');
$users = $usersQuery->fetchAll();

$itemsQuery = $pdo->query(
    "SELECT items.*, users.full_name AS seller_name
     FROM items
     JOIN users ON items.seller_id = users.id
     WHERE items.status = 'active'
     ORDER BY items.created_at DESC"
);
$items = $itemsQuery->fetchAll();

$statsQuery = $pdo->query(
    "SELECT
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM items WHERE status = 'active') AS active_items,
        (SELECT COUNT(*) FROM items WHERE status = 'closed') AS closed_items,
        (SELECT COUNT(*) FROM orders) AS total_orders,
        (SELECT COALESCE(SUM(amount), 0) FROM orders) AS total_revenue"
);
$stats = $statsQuery->fetch();

$allBidsQuery = $pdo->query(
    "SELECT bids.*, items.title AS item_title, users.full_name AS bidder_name
     FROM bids
     JOIN items ON bids.item_id = items.id
     JOIN users ON bids.bidder_id = users.id
     ORDER BY bids.bid_time DESC
     LIMIT 50"
);
$allBids = $allBidsQuery->fetchAll();

$allOrdersQuery = $pdo->query(
    "SELECT orders.*, items.title AS item_title, users.full_name AS buyer_name
     FROM orders
     JOIN items ON orders.item_id = items.id
     JOIN users ON orders.buyer_id = users.id
     ORDER BY orders.created_at DESC"
);
$allOrders = $allOrdersQuery->fetchAll();
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
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/2377/2377930.png">
</head>
<body class="auth-page">
    <?php require __DIR__ . '/partials_header.php'; ?>
    <main class="auth-shell">
        <section class="auth-card auth-card-wide" style="max-width:960px;">

            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-user-shield"></i></div>
                <div>
                    <p class="eyebrow">Admin Panel</p>
                    <h1>AuctionHub Admin</h1>
                </div>
            </div>

            <h2 style="margin-top:20px;">Platform Stats</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Total Users</th>
                            <th>Active Items</th>
                            <th>Closed Items</th>
                            <th>Total Orders</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo (int) $stats['total_users']; ?></td>
                            <td><?php echo (int) $stats['active_items']; ?></td>
                            <td><?php echo (int) $stats['closed_items']; ?></td>
                            <td><?php echo (int) $stats['total_orders']; ?></td>
                            <td>Rs. <?php echo number_format((float) $stats['total_revenue'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top:32px;">All Users (<?php echo count($users); ?>)</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo e($u['full_name']); ?></td>
                                <td><?php echo e($u['email']); ?></td>
                                <td><?php echo $u['is_admin'] ? 'Admin' : 'Member'; ?></td>
                                <td><?php echo e($u['created_at']); ?></td>
                                <td>
                                    <?php if ($u['email'] === 'ansuu1@gmail.com'): ?>
                                        <span class="detail-label">Superadmin</span>
                                    <?php elseif ((int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                                        <form method="post" action="admin.php">
                                            <input type="hidden" name="user_id" value="<?php echo (int) $u['id']; ?>">
                                            <input type="hidden" name="toggle_admin" value="1">
                                            <button type="submit" class="btn-flat">
                                                <?php echo $u['is_admin'] ? 'Demote' : 'Promote'; ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="detail-label">(You)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top:32px;">Active Items (<?php echo count($items); ?>)</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Seller</th>
                            <th>Current Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td><?php echo e($i['title']); ?></td>
                                <td><?php echo e($i['seller_name']); ?></td>
                                <td>Rs. <?php echo number_format((float) $i['current_price'], 2); ?></td>
                                <td><?php echo e($i['status']); ?></td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <form method="post" action="admin.php" onsubmit="return confirm('Force end this auction now?');">
                                            <input type="hidden" name="item_id" value="<?php echo (int) $i['id']; ?>">
                                            <input type="hidden" name="force_end_item" value="1">
                                            <button type="submit" class="btn-flat">End Now</button>
                                        </form>
                                        <form method="post" action="admin_delete_item.php" onsubmit="return confirm('Are you sure you want to delete this item? This cannot be undone.');">
                                            <input type="hidden" name="item_id" value="<?php echo (int) $i['id']; ?>">
                                            <button type="submit" class="btn-flat btn-flat-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top:32px;">Recent Bids (<?php echo count($allBids); ?>)</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Bidder</th>
                            <th>Amount</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allBids as $b): ?>
                            <tr>
                                <td><?php echo e($b['item_title']); ?></td>
                                <td><?php echo e($b['bidder_name']); ?></td>
                                <td>Rs. <?php echo number_format((float) $b['bid_amount'], 2); ?></td>
                                <td><?php echo e($b['bid_time']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top:32px;">All Orders (<?php echo count($allOrders); ?>)</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Buyer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allOrders as $o): ?>
                            <tr>
                                <td><?php echo e($o['item_title']); ?></td>
                                <td><?php echo e($o['buyer_name']); ?></td>
                                <td>Rs. <?php echo number_format((float) $o['amount'], 2); ?></td>
                                <td><?php echo e($o['payment_method']); ?></td>
                                <td><?php echo e($o['transaction_id']); ?></td>
                                <td><?php echo e($o['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="auth-switch" style="margin-top:24px;">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
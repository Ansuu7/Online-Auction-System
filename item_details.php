<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$itemId = (int) ($_GET['id'] ?? 0);

if ($itemId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$itemQuery = $pdo->prepare(
    "SELECT items.*, users.full_name AS seller_name
     FROM items
     JOIN users ON items.seller_id = users.id
     WHERE items.id = :id"
);
$itemQuery->execute([':id' => $itemId]);
$item = $itemQuery->fetch();

if ($item === false) {
    header('Location: dashboard.php');
    exit;
}

if ($item['status'] === 'active' && strtotime($item['end_time']) <= time()) {
    $winnerQuery = $pdo->prepare(
        'SELECT bidder_id FROM bids WHERE item_id = :item_id ORDER BY bid_amount DESC LIMIT 1'
    );
    $winnerQuery->execute([':item_id' => $itemId]);
    $winner = $winnerQuery->fetch();

    $winnerId = $winner !== false ? $winner['bidder_id'] : null;

    $closeItem = $pdo->prepare('UPDATE items SET status = :status, winner_id = :winner_id WHERE id = :id');
    $closeItem->execute([
        ':status' => 'closed',
        ':winner_id' => $winnerId,
        ':id' => $itemId,
    ]);

    $item['status'] = 'closed';
    $item['winner_id'] = $winnerId;
}

$bidsQuery = $pdo->prepare(
    "SELECT bids.*, users.full_name AS bidder_name
     FROM bids
     JOIN users ON bids.bidder_id = users.id
     WHERE bids.item_id = :item_id
     ORDER BY bids.bid_amount DESC"
);
$bidsQuery->execute([':item_id' => $itemId]);
$bids = $bidsQuery->fetchAll();
$wishlistCheck = $pdo->prepare('SELECT id FROM wishlist WHERE user_id = :user_id AND item_id = :item_id');
$wishlistCheck->execute([
    ':user_id' => $_SESSION['user_id'],
    ':item_id' => $itemId,
]);
$isWishlisted = $wishlistCheck->fetch() !== false;

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['end_auction'])) {
    if ((int) $item['seller_id'] === (int) $_SESSION['user_id']) {
        $winnerQuery = $pdo->prepare(
            'SELECT bidder_id FROM bids WHERE item_id = :item_id ORDER BY bid_amount DESC LIMIT 1'
        );
        $winnerQuery->execute([':item_id' => $itemId]);
        $winner = $winnerQuery->fetch();
        $winnerId = $winner !== false ? $winner['bidder_id'] : null;

        $closeItem = $pdo->prepare('UPDATE items SET status = :status, winner_id = :winner_id WHERE id = :id');
        $closeItem->execute([
            ':status' => 'closed',
            ':winner_id' => $winnerId,
            ':id' => $itemId,
        ]);

        header('Location: item_details.php?id=' . $itemId);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bidAmount = (string) ($_POST['bid_amount'] ?? '');
    $minValidBid = (float) $item['current_price'] + (float) $item['min_increment'];

    if ($bidAmount === '' || !is_numeric($bidAmount)) {
        $errorMessage = 'Please enter a valid bid amount.';
    } elseif ((float) $bidAmount < $minValidBid) {
        $errorMessage = 'Your bid must be at least Rs. ' . number_format($minValidBid, 2) . '.';
    } elseif ($item['status'] !== 'active') {
        $errorMessage = 'This auction is closed.';
    } elseif ((int) $item['seller_id'] === (int) $_SESSION['user_id']) {
        $errorMessage = 'You cannot bid on your own item.';
    } else {
        $insertBid = $pdo->prepare(
            'INSERT INTO bids (item_id, bidder_id, bid_amount) VALUES (:item_id, :bidder_id, :bid_amount)'
        );
        $insertBid->execute([
            ':item_id' => $itemId,
            ':bidder_id' => $_SESSION['user_id'],
            ':bid_amount' => $bidAmount,
        ]);

        $updateItem = $pdo->prepare('UPDATE items SET current_price = :price WHERE id = :id');
        $updateItem->execute([
            ':price' => $bidAmount,
            ':id' => $itemId,
        ]);

        header('Location: item_details.php?id=' . $itemId);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | <?php echo e($item['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>" style="width:100%; border-radius:16px; margin-bottom:20px; aspect-ratio:16/9; object-fit:cover;">

            <div class="brand-mark">
                <div>
                    <p class="eyebrow">Seller: <?php echo e($item['seller_name']); ?></p>
                    <h1><?php echo e($item['title']); ?></h1>
                </div>
            </div>

            <p class="auth-copy"><?php echo e($item['description']); ?></p>

            <div class="detail-card" style="margin-bottom:20px;">
                <span class="detail-label">Current Price</span>
                <strong>Rs. <?php echo number_format((float) $item['current_price'], 2); ?></strong>
            </div>
            <div class="detail-card" style="margin-bottom:20px;">
                <span class="detail-label">Time Remaining</span>
                <strong id="countdown">Calculating...</strong>
            </div>
               <button type="button" class="btn-ghost wishlist-btn" data-item-id="<?php echo (int) $item['id']; ?>" data-wishlisted="<?php echo $isWishlisted ? '1' : '0'; ?>">
                    <?php echo $isWishlisted ? '♥ Remove from Wishlist' : '♡ Add to Wishlist'; ?>
                </button>

            <?php if ($errorMessage !== ''): ?>
                <div class="message error"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <?php if ($item['status'] === 'active'): ?>
                <?php if ((int) $item['seller_id'] === (int) $_SESSION['user_id']): ?>
                    <form method="post" action="item_details.php?id=<?php echo (int) $item['id']; ?>" style="margin-bottom:16px;">
                        <input type="hidden" name="end_auction" value="1">
                        <button type="submit" class="btn-primary" style="background:#b42318;" onclick="return confirm('End this auction now and accept the current highest bid?');">End Auction Now</button>
                    </form>
                <?php else: ?>
                    <form method="post" action="item_details.php?id=<?php echo (int) $item['id']; ?>">
                        <div class="form-group">
                            <label for="bid_amount">Your Bid (minimum: Rs. <?php echo number_format((float) $item['current_price'] + (float) $item['min_increment'], 2); ?>)</label>
                            <div class="input-wrap">
                                <input type="number" id="bid_amount" name="bid_amount" step="0.01" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Place Bid</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="message error">
                    This auction has ended.
                    <?php if (!empty($item['winner_id'])): ?>
                        <?php if ((int) $item['winner_id'] === (int) $_SESSION['user_id']): ?>
                            You won this auction!
                        <?php else: ?>
                            Winning bidder: Rs. <?php echo number_format((float) $item['current_price'], 2); ?>
                        <?php endif; ?>
                    <?php else: ?>
                        No bids were placed.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h2 style="margin-top:28px;">Bid History</h2>
            <?php if (empty($bids)): ?>
                <p class="auth-copy">No bids yet. Be the first!</p>
            <?php else: ?>
                <?php foreach ($bids as $bid): ?>
                    <div class="detail-card" style="margin-bottom:10px;">
                        <span class="detail-label"><?php echo e($bid['bidder_name']); ?> — <?php echo e($bid['bid_time']); ?></span>
                        <strong>Rs. <?php echo number_format((float) $bid['bid_amount'], 2); ?></strong>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <p class="auth-switch">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>

    <script>
        const endTime = new Date("<?php echo e($item['end_time']); ?>").getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;
            const countdownEl = document.getElementById('countdown');

            if (distance <= 0) {
                countdownEl.textContent = 'Auction Ended';
                clearInterval(timerInterval);
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownEl.textContent = `${hours}h ${minutes}m ${seconds}s`;
        }

        updateCountdown();
        const timerInterval = setInterval(updateCountdown, 1000);
    </script>
</body>
</html>
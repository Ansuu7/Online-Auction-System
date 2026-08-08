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

$bidsQuery = $pdo->prepare(
    "SELECT bids.*, users.full_name AS bidder_name
     FROM bids
     JOIN users ON bids.bidder_id = users.id
     WHERE bids.item_id = :item_id
     ORDER BY bids.bid_amount DESC"
);
$bidsQuery->execute([':item_id' => $itemId]);
$bids = $bidsQuery->fetchAll();

$errorMessage = '';
?>
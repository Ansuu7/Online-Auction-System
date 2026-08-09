<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = (int) ($_POST['item_id'] ?? 0);

    if ($itemId > 0) {
        $deleteBids = $pdo->prepare('DELETE FROM bids WHERE item_id = :id');
        $deleteBids->execute([':id' => $itemId]);

        $deleteItem = $pdo->prepare('DELETE FROM items WHERE id = :id');
        $deleteItem->execute([':id' => $itemId]);

        flash('success', 'Item deleted.');
    }
}

header('Location: admin.php');
exit;
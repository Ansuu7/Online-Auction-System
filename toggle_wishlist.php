<?php
require __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$itemId = (int) ($_POST['item_id'] ?? 0);

if ($itemId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid item']);
    exit;
}

$check = $pdo->prepare('SELECT id FROM wishlist WHERE user_id = :user_id AND item_id = :item_id');
$check->execute([
    ':user_id' => $_SESSION['user_id'],
    ':item_id' => $itemId,
]);
$existing = $check->fetch();

if ($existing !== false) {
    $delete = $pdo->prepare('DELETE FROM wishlist WHERE id = :id');
    $delete->execute([':id' => $existing['id']]);
    echo json_encode(['wishlisted' => false]);
} else {
    $insert = $pdo->prepare('INSERT INTO wishlist (user_id, item_id) VALUES (:user_id, :item_id)');
    $insert->execute([
        ':user_id' => $_SESSION['user_id'],
        ':item_id' => $itemId,
    ]);
    echo json_encode(['wishlisted' => true]);
}
<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_wins.php');
    exit;
}

$itemId = (int) ($_POST['item_id'] ?? 0);
$paymentMethod = (string) ($_POST['payment_method'] ?? '');

$allowedMethods = ['Card', 'eSewa', 'Khalti'];
if (!in_array($paymentMethod, $allowedMethods, true)) {
    header('Location: my_wins.php');
    exit;
}

$itemQuery = $pdo->prepare(
    "SELECT * FROM items WHERE id = :id AND winner_id = :winner_id AND payment_status = 'pending'"
);
$itemQuery->execute([
    ':id' => $itemId,
    ':winner_id' => $_SESSION['user_id'],
]);
$item = $itemQuery->fetch();

if ($item === false) {
    header('Location: my_wins.php');
    exit;
}

$transactionId = 'TXN-' . strtoupper(bin2hex(random_bytes(5)));

$insertOrder = $pdo->prepare(
    'INSERT INTO orders (item_id, buyer_id, amount, payment_method, transaction_id, status)
     VALUES (:item_id, :buyer_id, :amount, :payment_method, :transaction_id, :status)'
);
$insertOrder->execute([
    ':item_id' => $itemId,
    ':buyer_id' => $_SESSION['user_id'],
    ':amount' => $item['current_price'],
    ':payment_method' => $paymentMethod,
    ':transaction_id' => $transactionId,
    ':status' => 'completed',
]);

$updateItem = $pdo->prepare("UPDATE items SET payment_status = 'paid' WHERE id = :id");
$updateItem->execute([':id' => $itemId]);

header('Location: order_confirmation.php?txn=' . urlencode($transactionId));
exit;
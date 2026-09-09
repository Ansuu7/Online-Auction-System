<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$itemId = (int) ($_GET['item_id'] ?? 0);

$itemQuery = $pdo->prepare(
    "SELECT items.*, users.full_name AS seller_name
     FROM items
     JOIN users ON items.seller_id = users.id
     WHERE items.id = :id AND items.winner_id = :winner_id AND items.payment_status = 'pending'"
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Checkout</title>
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
                <div class="brand-icon"><i class="fa-solid fa-lock"></i></div>
                <div>
                    <p class="eyebrow">Secure Checkout</p>
                    <h1>Complete Your Payment</h1>
                </div>
            </div>

            <div class="detail-card" style="margin-bottom:20px;">
                <span class="detail-label">Item</span>
                <strong><?php echo e($item['title']); ?></strong>
                <div style="margin-top:10px;">
                    <span class="detail-label">Seller</span>
                    <strong><?php echo e($item['seller_name']); ?></strong>
                </div>
                <div style="margin-top:10px;">
                    <span class="detail-label">Amount Due</span>
                    <strong style="font-size:1.3rem; color:var(--primary-dark);">Rs. <?php echo number_format((float) $item['current_price'], 2); ?></strong>
                </div>
            </div>

            <form method="post" action="process_payment.php" id="paymentForm">
                <input type="hidden" name="item_id" value="<?php echo (int) $item['id']; ?>">

                <div class="form-group">
                    <label>Select Payment Method</label>
                    <div class="role-options" style="grid-template-columns: repeat(3, minmax(0,1fr));">
                        <label class="role-pill">
                            <input type="radio" name="payment_method" value="Card" required>
                            <span><i class="fa-solid fa-credit-card"></i> Card</span>
                        </label>
                        <label class="role-pill">
                            <input type="radio" name="payment_method" value="eSewa">
                            <span><i class="fa-solid fa-wallet"></i> eSewa</span>
                        </label>
                        <label class="role-pill">
                            <input type="radio" name="payment_method" value="Khalti">
                            <span><i class="fa-solid fa-mobile-screen"></i> Khalti</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="payButton">
                    Pay Rs. <?php echo number_format((float) $item['current_price'], 2); ?>
                </button>
            </form>

            <p class="auth-switch">
                <a href="my_wins.php">Cancel and go back</a>
            </p>
        </section>
    </main>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function (event) {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            if (!selected) {
                return;
            }

            event.preventDefault();
            const form = this;
            const button = document.getElementById('payButton');

            button.disabled = true;
            button.textContent = 'Processing Payment...';

            setTimeout(function () {
                form.submit();
            }, 1800);
        });
    </script>
</body>
</html>
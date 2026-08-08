<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $startingPrice = (string) ($_POST['starting_price'] ?? '');
    $minIncrement = (string) ($_POST['min_increment'] ?? '');
    $startTime = (string) ($_POST['start_time'] ?? '');
    $endTime = (string) ($_POST['end_time'] ?? '');

    if ($title === '' || $description === '' || $startingPrice === '' || $minIncrement === '' || $startTime === '' || $endTime === '') {
        $errorMessage = 'All fields are required.';
    } elseif (!is_numeric($startingPrice) || (float) $startingPrice < 0) {
        $errorMessage = 'Starting price must be a valid positive number.';
    } elseif (!is_numeric($minIncrement) || (float) $minIncrement <= 0) {
        $errorMessage = 'Minimum increment must be a valid positive number.';
    } elseif (strtotime($endTime) <= strtotime($startTime)) {
        $errorMessage = 'End time must be after start time.';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Please upload a valid image.';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes, true)) {
            $errorMessage = 'Only JPG, PNG, WEBP, or GIF images are allowed.';
        } else {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('item_', true) . '.' . $extension;
            $uploadPath = __DIR__ . '/uploads/' . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errorMessage = 'Failed to upload image. Please try again.';
            } else {
                $insert = $pdo->prepare(
                    'INSERT INTO items (seller_id, title, description, image, starting_price, current_price, min_increment, start_time, end_time, status)
                     VALUES (:seller_id, :title, :description, :image, :starting_price, :current_price, :min_increment, :start_time, :end_time, :status)'
                );
                $insert->execute([
                    ':seller_id' => $_SESSION['user_id'],
                    ':title' => $title,
                    ':description' => $description,
                    ':image' => 'uploads/' . $fileName,
                    ':starting_price' => $startingPrice,
                    ':current_price' => $startingPrice,
                    ':min_increment' => $minIncrement,
                    ':start_time' => $startTime,
                    ':end_time' => $endTime,
                    ':status' => 'active',
                ]);

                flash('success', 'Item posted successfully.');
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Post Item</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-gavel"></i></div>
                <div>
                    <p class="eyebrow">New Auction</p>
                    <h1>Post an Item</h1>
                </div>
            </div>

            <?php if (isset($errorMessage) && $errorMessage !== ''): ?>
                <div class="message error"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <form method="post" action="post_item.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Item Title</label>
                    <div class="input-wrap">
                        <input type="text" id="title" name="title" placeholder="e.g. Vintage Camera" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <div class="input-wrap">
                        <textarea id="description" name="description" rows="4" placeholder="Describe the item..." required></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Item Image</label>
                    <div class="input-wrap">
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="starting_price">Starting Price</label>
                        <div class="input-wrap">
                            <input type="number" id="starting_price" name="starting_price" step="0.01" min="0" placeholder="e.g. 100.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="min_increment">Minimum Bid Increment</label>
                        <div class="input-wrap">
                            <input type="number" id="min_increment" name="min_increment" step="0.01" min="0.01" placeholder="e.g. 5.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <div class="input-wrap">
                            <input type="datetime-local" id="start_time" name="start_time" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <div class="input-wrap">
                            <input type="datetime-local" id="end_time" name="end_time" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Post Item</button>
            </form>

            <p class="auth-switch">
                <a href="dashboard.php">Back to Dashboard</a>
            </p>
        </section>
    </main>
</body>
</html>
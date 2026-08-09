<?php
require __DIR__ . '/db.php';

$flash = pull_flash();
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errorMessage = 'Please enter both your email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        $query = $pdo->prepare('SELECT id, full_name, email, password_hash, is_admin FROM users WHERE email = :email LIMIT 1');
        $query->execute([':email' => $email]);
        $user = $query->fetch();

        if ($user === false || !password_verify($password, $user['password_hash'])) {
            $errorMessage = 'Invalid credentials.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = (bool) $user['is_admin'];
            $_SESSION['user_role'] = $user['role'] ?: 'Member';

            flash('success', 'Login successful.');
            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page" data-page="login">
    <main class="auth-shell">
        <section class="auth-card">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-gavel"></i></div>
                <div>
                    <p class="eyebrow">Online Auction System</p>
                    <h1>AuctionHub</h1>
                </div>
            </div>

            <p class="auth-copy">Log in to access your account.</p>

            <?php if ($flash !== null): ?>
                <div class="message <?php echo e($flash['type']); ?>" aria-live="polite"><?php echo e($flash['message']); ?></div>
            <?php endif; ?>
            <?php if ($errorMessage !== ''): ?>
                <div class="message error" aria-live="polite"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <form id="loginForm" method="post" action="index.php" novalidate>
                <div id="loginMessage" class="message"></div>

                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="loginEmail" name="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <p class="auth-switch">
                Don't have an account? <a href="signup.php">Sign Up</a>
            </p>
        </section>
    </main>

    <script src="script.js?v=2"></script>
</body>
</html>
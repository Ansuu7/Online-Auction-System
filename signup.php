<?php
require __DIR__ . '/db.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $errorMessage = 'Please complete your name, email, password, and confirmation.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $errorMessage = 'Password must contain at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $errorMessage = 'Passwords do not match.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $check->execute([':email' => $email]);

        if ($check->fetch() !== false) {
            $errorMessage = 'This email is already registered.';
        } else {
            $insert = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role)');
            $insert->execute([
                ':full_name' => $fullName,
                ':email' => $email,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => 'Member',
            ]);

            flash('success', 'Registration successful. You can now log in.');
            header('Location: index.php');
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
    <title>AuctionHub | Sign Up</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page" data-page="signup">
    <main class="auth-shell">
        <section class="auth-card auth-card-wide">
            <div class="brand-mark">
                <div class="brand-icon"><i class="fa-solid fa-gavel"></i></div>
                <div>
                    <p class="eyebrow">Create Account</p>
                    <h1>AuctionHub</h1>
                </div>
            </div>

            <p class="auth-copy">Create your AuctionHub account to sign in.</p>

            <?php if ($errorMessage !== ''): ?>
                <div class="message error" aria-live="polite"><?php echo e($errorMessage); ?></div>
            <?php endif; ?>

            <form id="signupForm" method="post" action="signup.php" novalidate>
                <div id="signupMessage" class="message"></div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="signupFullName">Full Name</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-user"></i>
                            <input type="text" id="signupFullName" name="full_name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupEmail">Email</label>
                        <div class="input-wrap">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" id="signupEmail" name="email" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signupPassword">Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="signupPassword" name="password" placeholder="At least 8 characters" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input type="password" id="confirmPassword" name="confirm_password" placeholder="Re-enter your password" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Sign Up</button>
            </form>

            <p class="auth-switch">
                Already have an account? <a href="index.php">Login</a>
            </p>
        </section>
    </main>

    <script src="script.js?v=2"></script>
</body>
</html>
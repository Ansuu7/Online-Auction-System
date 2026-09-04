<header class="site-header" style="position:static; margin-bottom:24px;">
    <nav class="navbar" style="max-width:900px; margin:0 auto;">
        <a class="brand" href="dashboard.php">
            <span class="brand-mark"><i class="fa-solid fa-gavel"></i></span>
            <span class="brand-text">AuctionHub</span>
        </a>
        <div class="nav-actions">
            <a class="btn btn-ghost btn-small" href="dashboard.php">Dashboard</a>
            <span class="session-badge subtle"><?php echo e($_SESSION['user_name'] ?? 'Member'); ?></span>
            <a class="btn btn-ghost" href="logout.php">Logout</a>
        </div>
    </nav>
</header>
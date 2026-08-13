<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$outbidQuery = $pdo->prepare(
    "SELECT items.id, items.title
     FROM bids
     JOIN items ON bids.item_id = items.id
     WHERE bids.bidder_id = :user_id
     AND items.status = 'active'
     AND bids.bid_amount < items.current_price
     GROUP BY items.id, items.title"
);
$outbidQuery->execute([':user_id' => $_SESSION['user_id']]);
$outbidItems = $outbidQuery->fetchAll();

$search = trim((string) ($_GET['search'] ?? ''));

if ($search !== '') {
    $itemsQuery = $pdo->prepare(
        "SELECT items.*, users.full_name AS seller_name
         FROM items
         JOIN users ON items.seller_id = users.id
         WHERE items.status = 'active' AND items.title LIKE :search
         ORDER BY items.end_time ASC"
    );
    $itemsQuery->execute([':search' => '%' . $search . '%']);
} else {
    $itemsQuery = $pdo->query(
        "SELECT items.*, users.full_name AS seller_name
         FROM items
         JOIN users ON items.seller_id = users.id
         WHERE items.status = 'active'
         ORDER BY items.end_time ASC"
    );
}
$items = $itemsQuery->fetchAll();
$wishlistQuery = $pdo->prepare('SELECT item_id FROM wishlist WHERE user_id = :user_id');
$wishlistQuery->execute([':user_id' => $_SESSION['user_id']]);
$wishlistedIds = array_column($wishlistQuery->fetchAll(), 'item_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuctionHub | Dashboard</title>
    <meta name="description" content="AuctionHub dashboard for browsing auctions and featured listings after login.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body class="landing-page dashboard-page">
    <div class="dashboard-shell">
        <div class="dashboard-content-wrap">
        <header class="site-header">
            <nav class="navbar" aria-label="Primary navigation">
                <a class="brand" href="#home" aria-label="AuctionHub home">
                    <span class="brand-mark"><i class="fa-solid fa-gavel"></i></span>
                    <span class="brand-text">AuctionHub</span>
                </a>

                <button class="nav-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="primaryNav">
                    <span>hello</span>
                    <span>this is</span>
                    <span>check</span>
                </button>

                <div class="nav-panel" id="primaryNav">
                    <div class="nav-links">
                        <a href="#home">Home</a>
                        <a href="#featured-auctions">Auctions</a>
                        <a href="#how-it-works">How It Works</a>
                        <a href="#about">About</a>
                        <a href="#contact">Contact</a>
                    </div>

                    <div class="nav-actions">
                        <?php if (!empty($_SESSION['is_admin'])): ?>
                            <a class="btn btn-ghost btn-small" href="admin.php">Admin Panel</a>
                        <?php endif; ?>
                        <a class="btn btn-ghost btn-small" href="wishlist.php">My Wishlist</a>
                        <a class="btn btn-primary btn-small" href="post_item.php">Post an Item</a>
                        <span class="session-badge subtle"><?php echo e($_SESSION['user_name'] ?? 'Member'); ?></span>
                        <a class="btn btn-ghost" href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </header>

        <main id="home">
            <?php if (!empty($outbidItems)): ?>
                <div class="message error" style="margin-bottom:16px;">
                    You've been outbid on:
                    <?php foreach ($outbidItems as $index => $outbidItem): ?>
                        <a href="item_details.php?id=<?php echo (int) $outbidItem['id']; ?>"><?php echo e($outbidItem['title']); ?></a><?php echo $index < count($outbidItems) - 1 ? ', ' : ''; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <section class="dashboard-hero section-reveal">
                <div class="hero-content">
                    <span class="section-kicker">Online Auctioning System</span>
                    <h1>Bid. Win. Own.</h1>
                    <p>Discover exciting auctions, place competitive bids, and win amazing products securely online.</p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#featured-auctions">Start Bidding</a>
                        <a class="btn btn-ghost" href="post_item.php">Post an Item</a>
                    </div>

                    <div class="hero-stats" aria-label="AuctionHub highlights">
                        <div>
                            <strong>5K+</strong>
                            <span>Active bidders</span>
                        </div>
                        <div>
                            <strong>1,200+</strong>
                            <span>Live auctions</span>
                        </div>
                        <div>
                            <strong>99%</strong>
                            <span>User satisfaction</span>
                        </div>
                    </div>
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <div class="hero-card floating-card">
                        <div class="live-badge"><i class="fa-solid fa-circle"></i> Live Auction</div>
                        <img src="img/auction_img.jpeg" alt="Auction illustration">
                    </div>
                    <div class="hero-float hero-float-one">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Real-time bids</span>
                    </div>
                    <div class="hero-float hero-float-two">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Secure checkout</span>
                    </div>
                </div>
            </section>

            <section class="section section-reveal" id="featured-auctions">
                <div class="section-heading">
                    <span class="section-kicker">Featured Auctions</span>
                    <h2>Live auction listings from across AuctionHub.</h2>
                </div>

                <form method="get" action="dashboard.php" style="margin-bottom:20px; max-width:420px;">
                    <div class="input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" placeholder="Search items..." id="search" value="<?php echo e($search); ?>">
                    </div>
                </form>

                <div class="auction-grid">
                    <?php if (empty($items)): ?>
                        <p><?php echo $search !== '' ? 'No items match your search.' : 'No active auctions right now. Check back soon!'; ?></p>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <article class="auction-card">
                                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['title']); ?>">
                                <div class="auction-body">
                                    <h3><?php echo e($item['title']); ?></h3>
                                    <div class="auction-meta"><span>Current Bid</span><strong>Rs. <?php echo number_format((float) $item['current_price'], 2); ?></strong></div>
                                    <div class="auction-meta"><span>Seller</span><strong><?php echo e($item['seller_name']); ?></strong></div>
                                    <a class="btn btn-primary btn-small" href="item_details.php?id=<?php echo (int) $item['id']; ?>">View & Bid</a>
                                    <button type="button" class="btn-ghost btn-small wishlist-btn" data-item-id="<?php echo (int) $item['id']; ?>" data-wishlisted="<?php echo in_array($item['id'], $wishlistedIds, false) ? '1' : '0'; ?>">
                                        <?php echo in_array($item['id'], $wishlistedIds, false) ? '♥ Wishlisted' : '♡ Wishlist'; ?>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="section section-reveal section-alt" id="how-it-works">
                <div class="section-heading">
                    <span class="section-kicker">How It Works</span>
                    <h2>Join, bid, and win in four simple steps.</h2>
                </div>

                <div class="steps-grid">
                    <a href ="signup.php"> <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-user-plus"></i></div>
                        <h3>Create an Account</h3>
                        <p>Register in seconds to unlock bidding, tracking, and account management features.</p>
                        </article>
                        </a>

                    <a href ="#search">
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <h3>Browse Auctions</h3>
                        <p>Search by category, price, and time remaining to find items you want most.</p>
                    </article>
                        </a>
                    <a href ="#featured-auctions">    
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-gavel"></i></div>
                        <h3>Place Your Bid</h3>
                        <p>Submit competitive bids and watch the live auction updates in real time.</p>
                        </article>
                        </a>
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-box-open"></i></div>
                        <h3>Win &amp; Receive Your Item</h3>
                        <p>Complete payment securely and have your item delivered quickly and safely.</p>
                    </article>
                </div>
            </section>

            <section class="section section-reveal" id="why-choose">
                <div class="section-heading">
                    <span class="section-kicker">Why Choose AuctionHub</span>
                    <h2>A secure and reliable platform built for confident bidding.</h2>
                </div>

                <div class="card-grid feature-grid-home">
                    <article class="info-card feature-highlight">
                        <i class="fa-solid fa-lock"></i>
                        <h3>Secure Payments</h3>
                        <p>Transactions are protected with trusted payment handling and platform safeguards.</p>
                    </article>
                    <article class="info-card feature-highlight">
                        <i class="fa-solid fa-circle-check"></i>
                        <h3>Verified Sellers</h3>
                        <p>Buy with confidence from sellers who are reviewed and validated.</p>
                    </article>
                    <article class="info-card feature-highlight">
                        <i class="fa-solid fa-tower-broadcast"></i>
                        <h3>Real-Time Bidding</h3>
                        <p>Follow live bid updates and react quickly when competition heats up.</p>
                    </article>
                    <article class="info-card feature-highlight">
                        <i class="fa-solid fa-truck-fast"></i>
                        <h3>Fast Delivery</h3>
                        <p>Enjoy quick order processing and shipping after a winning bid is confirmed.</p>
                    </article>
                </div>
            </section>

            <section class="section section-reveal section-alt" id="about">
                <div class="about-layout">
                    <div class="about-copy">
                        <span class="section-kicker">About AuctionHub</span>
                        <h2>Designed for students, collectors, and everyday shoppers.</h2>
                        <p>AuctionHub is a university-friendly online auctioning platform concept focused on usability, trust, and a smooth bidding experience. It combines a clean visual style with practical features so users can explore auctions without friction.</p>
                        <p>Built with semantic HTML5, CSS3, and vanilla JavaScript, the interface stays fast, responsive, and easy to extend for future project features.</p>
                    </div>

                    <div class="about-panel">
                        <div class="about-item"><i class="fa-solid fa-circle-check"></i><span>Modern UX</span></div>
                        <div class="about-item"><i class="fa-solid fa-mobile-screen"></i><span>Mobile-first design</span></div>
                        <div class="about-item"><i class="fa-solid fa-shield-heart"></i><span>Trust-focused flow</span></div>
                        <div class="about-item"><i class="fa-solid fa-chart-line"></i><span>Built for growth</span></div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="site-footer" id="contact">
            <div class="footer-grid">
                <div>
                    <a class="brand footer-brand" href="#home">
                        <span class="brand-mark"><i class="fa-solid fa-gavel"></i></span>
                        <span class="brand-text">AuctionHub</span>
                    </a>
                    <p>A modern online auctioning system concept for discovering, bidding, and winning products securely online.</p>
                </div>

                <div>
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#featured-auctions">Auctions</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Contact Information</h3>
                    <ul class="footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i><span>Sagarmatha College of Science and Technology, Sanepa</span></li>
                        <li><i class="fa-solid fa-envelope"></i><span>support@auctionhub.com</span></li>
                        <li><i class="fa-solid fa-phone"></i><span>98765445634</span></li>
                    </ul>
                </div>

                <div>
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 AuctionHub. All rights reserved.</p>
            </div>
        </footer>

        </div> <!-- /.main-content -->

    </div> <!--/.dashboard-shell -->

    <script src="script.js?v=3"></script>
</body>
</html>
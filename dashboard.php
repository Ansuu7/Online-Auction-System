<?php
require __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>AuctionHub | Dashboard</title>
    <meta name="description" content="AuctionHub dashboard for browsing auctions, categories, and featured listings after login.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet"> 
        <title>AuctionHub | Dashboard</title>
        <meta name="description" content="AuctionHub dashboard for browsing auctions, categories, and featured listings after login.">         
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-page dashboard-page" >
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
                        <a href="#categories">Categories</a>
                        <a href="#how-it-works">How It Works</a>
                        <a href="#about">About</a>
                        <a href="#contact">Contact</a>
                    </div>

                    <div class="nav-actions">
                        <span class="session-badge subtle"><?php echo e($_SESSION['user_role'] ?? 'Member'); ?></span>
                        <a class="btn btn-ghost" href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </header>

        <main id="home">
            <section class="dashboard-hero section-reveal">
                <div class="hero-content">
                    <span class="section-kicker">Online Auctioning System</span>
                    <h1>Bid. Win. Own.</h1>
                    <p>Discover exciting auctions, place competitive bids, and win amazing products securely online.</p>

                    <div class="hero-actions">
                        <a class="btn btn-primary" href="#featured-auctions">Start Bidding</a>
                        <a class="btn btn-ghost" href="#how-it-works">Learn More</a>
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
                        <img src="https://placehold.co/700x520/eff5ff/165dff?text=AuctionHub+Hero+Image" alt="Auction illustration">
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

            <section class="section section-reveal" id="categories">
                <div class="section-heading">
                    <span class="section-kicker">Featured Categories</span>
                    <h2>Explore the most popular auction categories.</h2>
                </div>

                <div class="card-grid category-grid">
                    <article class="info-card category-card">
                        <i class="fa-solid fa-microchip"></i>
                        <h3>Electronics</h3>
                        <p>Find phones, laptops, gaming gear, and smart devices at competitive auction prices.</p>
                    </article>
                    <article class="info-card category-card">
                        <i class="fa-solid fa-car-side"></i>
                        <h3>Vehicles</h3>
                        <p>Bid on cars, motorcycles, and accessories from verified sellers and dealers.</p>
                    </article>
                    <article class="info-card category-card">
                        <i class="fa-solid fa-shirt"></i>
                        <h3>Fashion</h3>
                        <p>Discover branded apparel, shoes, and accessories for every style and budget.</p>
                    </article>
                    <article class="info-card category-card">
                        <i class="fa-solid fa-couch"></i>
                        <h3>Furniture</h3>
                        <p>Upgrade your home or workspace with elegant furniture and decor items.</p>
                    </article>
                    <article class="info-card category-card">
                        <i class="fa-solid fa-palette"></i>
                        <h3>Art</h3>
                        <p>Browse paintings, sculptures, and creative pieces from talented artists.</p>
                    </article>
                    <article class="info-card category-card">
                        <i class="fa-solid fa-gem"></i>
                        <h3>Collectibles</h3>
                        <p>Shop rare collectibles, vintage treasures, and limited-edition items.</p>
                    </article>
                </div>
            </section>

            <section class="section section-reveal section-alt" id="how-it-works">
                <div class="section-heading">
                    <span class="section-kicker">How It Works</span>
                    <h2>Join, bid, and win in four simple steps.</h2>
                </div>

                <div class="steps-grid">
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-user-plus"></i></div>
                        <h3>Create an Account</h3>
                        <p>Register in seconds to unlock bidding, tracking, and account management features.</p>
                    </article>
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <h3>Browse Auctions</h3>
                        <p>Search by category, price, and time remaining to find items you want most.</p>
                    </article>
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-gavel"></i></div>
                        <h3>Place Your Bid</h3>
                        <p>Submit competitive bids and watch the live auction updates in real time.</p>
                    </article>
                    <article class="step-card">
                        <div class="step-icon"><i class="fa-solid fa-box-open"></i></div>
                        <h3>Win &amp; Receive Your Item</h3>
                        <p>Complete payment securely and have your item delivered quickly and safely.</p>
                    </article>
                </div>
            </section>

            <section class="section section-reveal" id="featured-auctions">
                <div class="section-heading">
                    <span class="section-kicker">Featured Auctions</span>
                    <h2>Sample auction listings from across AuctionHub.</h2>
                </div>

                <div class="auction-grid">
                    <article class="auction-card">
                        <img src="img/iphone_17_pro_max.jpeg" alt="Apple MacBook Pro auction item">
                        <div class="auction-body">
                            <h3>Apple MacBook Pro 14"</h3>
                            <div class="auction-meta"><span>Current Bid</span><strong>$1,680</strong></div>
                            <div class="auction-meta"><span>Time Remaining</span><strong>02h 14m</strong></div>
                            <a class="btn btn-primary btn-small" href="#contact">Bid Now</a>
                        </div>
                    </article>

                    <article class="auction-card">
                        <img src="https://placehold.co/640x420/e9f4ff/165dff?text=Toyota+Corolla" alt="Toyota Corolla auction item">
                        <div class="auction-body">
                            <h3>Toyota Corolla 2021</h3>
                            <div class="auction-meta"><span>Current Bid</span><strong>$12,400</strong></div>
                            <div class="auction-meta"><span>Time Remaining</span><strong>05h 36m</strong></div>
                            <a class="btn btn-primary btn-small" href="#contact">Bid Now</a>
                        </div>
                    </article>

                    <article class="auction-card">
                        <img src="https://placehold.co/640x420/f2f7ff/165dff?text=Modern+Wall+Art" alt="Modern wall art auction item">
                        <div class="auction-body">
                            <h3>Modern Abstract Wall Art</h3>
                            <div class="auction-meta"><span>Current Bid</span><strong>$320</strong></div>
                            <div class="auction-meta"><span>Time Remaining</span><strong>01h 48m</strong></div>
                            <a class="btn btn-primary btn-small" href="#contact">Bid Now</a>
                        </div>
                    </article>
                </div>
            </section>

            <section class="section section-reveal section-alt" id="why-choose">
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
                        <i class="fa-solid fa-badge-check"></i>
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

            <section class="section section-reveal" id="about">
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

            <section class="section section-reveal section-alt testimonials" id="testimonials">
                <div class="section-heading">
                    <span class="section-kicker">Testimonials</span>
                    <h2>What our users say about AuctionHub.</h2>
                </div>

                <div class="testimonial-grid">
                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <img src="https://placehold.co/120x120/dfeeff/165dff?text=AR" alt="Avatar of Ayesha Rahman">
                            <div>
                                <h3>Ayesha Rahman</h3>
                                <div class="stars" aria-label="5 out of 5 stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p>“The bidding process feels smooth and trustworthy. I liked how easy it was to browse different categories and place bids quickly.”</p>
                    </article>

                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <img src="https://placehold.co/120x120/e9f4ff/165dff?text=MK" alt="Avatar of Marcus Kim">
                            <div>
                                <h3>Marcus Kim</h3>
                                <div class="stars" aria-label="5 out of 5 stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p>“AuctionHub looks professional and feels responsive on my phone. It’s the kind of project that stands out in a class presentation.”</p>
                    </article>

                    <article class="testimonial-card">
                        <div class="testimonial-top">
                            <img src="https://placehold.co/120x120/f2f7ff/165dff?text=SN" alt="Avatar of Sofia Nguyen">
                            <div>
                                <h3>Sofia Nguyen</h3>
                                <div class="stars" aria-label="5 out of 5 stars">
                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p>“The interface is clean, modern, and easy to understand. I especially like the category cards and featured auction layout.”</p>
                    </article>
                </div>
            </section>

            <section class="cta-banner section-reveal" id="cta">
                <div>
                    <span class="section-kicker">Join Today</span>
                    <h2>Ready to Join the Auction?</h2>
                    <p>Create your account to start bidding, save auctions, and track winning items with ease.</p>
                </div>
                <div class="cta-actions">
                    <a class="btn btn-light" href="signup.php">Register Now</a>
                    <a class="btn btn-ghost btn-ghost-light" href="logout.php">Logout</a>
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
                        <li><a href="#categories">Categories</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#testimonials">Testimonials</a></li>
                    </ul>
                </div>

                <div>
                    <h3>Contact Information</h3>
                    <ul class="footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i><span>University Project, Web Development Lab</span></li>
                        <li><i class="fa-solid fa-envelope"></i><span>support@auctionhub.local</span></li>
                        <li><i class="fa-solid fa-phone"></i><span>+1 (555) 012-3456</span></li>
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
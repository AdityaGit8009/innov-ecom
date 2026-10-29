<?php
if (!isset($conn)) {
  include 'config/db.php';
}

$footerCats = mysqli_query($conn, "
    SELECT c.id, c.name 
    FROM categories c 
    WHERE c.status = 1 
    ORDER BY c.name 
    LIMIT 10
");

$footerSubs = mysqli_query($conn, "
    SELECT sc.id, sc.name, c.name AS cat_name 
    FROM sub_categories sc 
    JOIN categories c ON c.id = sc.category_id 
    WHERE sc.status = 1 
    ORDER BY c.name, sc.name 
    LIMIT 35
");
?>

</main>

<footer class="site-footer">

  <!-- CATEGORY DIRECTORY -->
  <div class="footer-category">
    <div class="container">

      <h2 class="footer-category-title">Category Directory</h2>

      <div class="footer-category-row">
        <span class="footer-category-label">Categories:</span>

        <div class="footer-category-links">
          <?php if ($footerCats && mysqli_num_rows($footerCats) > 0): ?>
            <?php while ($c = mysqli_fetch_assoc($footerCats)): ?>
              <a href="products.php?cat_id=<?= (int)$c['id']; ?>">
                <?= htmlspecialchars($c['name']); ?>
              </a>
            <?php endwhile; ?>
          <?php else: ?>
            <a href="products.php">Electronics Components</a>
            <a href="products.php">Development Boards</a>
            <a href="products.php">Sensors</a>
            <a href="products.php">Modules</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="footer-category-row">
        <span class="footer-category-label">Sub Categories:</span>

        <div class="footer-category-links">
          <?php if ($footerSubs && mysqli_num_rows($footerSubs) > 0): ?>
            <?php while ($s = mysqli_fetch_assoc($footerSubs)): ?>
              <a href="products.php?sub_id=<?= (int)$s['id']; ?>">
                <?= htmlspecialchars($s['name']); ?>
              </a>
            <?php endwhile; ?>
          <?php else: ?>
            <a href="products.php">Arduino Boards</a>
            <a href="products.php">Raspberry Pi</a>
            <a href="products.php">ESP8266 & ESP32</a>
            <a href="products.php">Motor Drivers</a>
            <a href="products.php">Communication Modules</a>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- SERVICE HIGHLIGHTS -->
  <div class="footer-service-area">
    <div class="container">
      <div class="footer-service-grid">

        <div class="footer-service-card">
          <div class="footer-service-icon">
            <ion-icon name="cube-outline"></ion-icon>
          </div>
          <div>
            <h4>Quality Products</h4>
            <p>Checked electronics components for projects and practical work.</p>
          </div>
        </div>

        <div class="footer-service-card">
          <div class="footer-service-icon">
            <ion-icon name="school-outline"></ion-icon>
          </div>
          <div>
            <h4>Project Support</h4>
            <p>Mini projects, major projects, training and internship support.</p>
          </div>
        </div>

        <div class="footer-service-card">
          <div class="footer-service-icon">
            <ion-icon name="shield-checkmark-outline"></ion-icon>
          </div>
          <div>
            <h4>Secure Checkout</h4>
            <p>Safe cart, order tracking and payment management flow.</p>
          </div>
        </div>

        <div class="footer-service-card">
          <div class="footer-service-icon">
            <ion-icon name="call-outline"></ion-icon>
          </div>
          <div>
            <h4>Customer Help</h4>
            <p>Support for orders, components, projects and institute requirements.</p>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- MAIN FOOTER NAV + MAP -->
  <div class="footer-nav">
    <div class="container">
      <div class="footer-nav-grid">

        <ul class="footer-nav-list">
          <li>
            <h2 class="nav-title">Company</h2>
          </li>
          <li><a href="about.php" class="footer-nav-link">About Us</a></li>
          <li><a href="directors.php" class="footer-nav-link">Director Profile</a></li>

          <li><a href="contactus.php" class="footer-nav-link">Contact Us</a></li>
        </ul>

        <ul class="footer-nav-list">
          <li>
            <h2 class="nav-title">Customer</h2>
          </li>
          <li><a href="products.php" class="footer-nav-link">Products</a></li>
          <li><a href="cart.php" class="footer-nav-link">Cart</a></li>
          <li><a href="wishlist.php" class="footer-nav-link">Wishlist</a></li>
          <li><a href="user_profile.php?tab=latest" class="footer-nav-link">My Orders</a></li>
          <li><a href="user_profile.php?tab=track" class="footer-nav-link">Track Order</a></li>
        </ul>

        <ul class="footer-nav-list">
          <li>
            <h2 class="nav-title">Projects</h2>
          </li>
          <li><a href="mini-projects.php" class="footer-nav-link">Mini Projects</a></li>
          <li><a href="major-projects.php" class="footer-nav-link">Major Projects</a></li>
          <li><a href="services.php" class="footer-nav-link">Services</a></li>
          <li><a href="training.php" class="footer-nav-link">Training/Internship</a></li>
        </ul>

        <ul class="footer-nav-list">
          <li>
            <h2 class="nav-title">Support</h2>
          </li>
          <li><a href="track_order.php" class="footer-nav-link">Track Your Order</a></li>
          <li><a href="user_profile.php?tab=previous" class="footer-nav-link">Order History</a></li>
          <li><a href="cart.php" class="footer-nav-link">Shopping Cart</a></li>
          <li><a href="wishlist.php" class="footer-nav-link">Saved Wishlist</a></li>
          <li><a href="contactus.php" class="footer-nav-link">Help & Support</a></li>
          <!-- <li><a href="privacy-policy.php" class="footer-nav-link">Privacy Policy</a></li>
          <li><a href="terms-and-conditions.php" class="footer-nav-link">Terms & Conditions</a></li>
          <li><a href="cancellation-policy.php" class="footer-nav-link">Cancellation Policy</a></li>
          <li><a href="refund-policy.php" class="footer-nav-link">Refund Policy</a></li>
          <li><a href="site-map.php" class="footer-nav-link">Site Map</a></li> -->
        </ul>

        <!-- MAP AFTER SUPPORT -->
        <div class="footer-map-box">
          <h2 class="nav-title">Location</h2>

          <div class="footer-map">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1323.521136048854!2d80.94930639816161!3d26.88301149725619!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399957d61ec6375f%3A0x9ade5ef82fe504ee!2sSOFTPRO%20INNOVATIONS%20PVT%20LTD!5e1!3m2!1sen!2sus!4v1778071570713!5m2!1sen!2sus"
              width="100%"
              height="100%"
              style="border:0;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- FOOTER EXTRA -->
  <div class="footer-extra">
    <div class="container">
      <div class="footer-extra-grid">

        <div class="footer-about-box">
          <h3>Softpro e-Commerce</h3>
          <p>
            Trusted platform for electronics components, student projects, embedded modules,
            IoT products, development boards and technical project support.
          </p>
        </div>

        <div class="footer-contact-box">
          <h3>Contact Details</h3>

          <p>
            <ion-icon name="location-outline"></ion-icon>
            Lucknow, Uttar Pradesh, India
          </p>

          <p>
            <ion-icon name="mail-outline"></ion-icon>
            <a href="mailto:info@softproindia.in">info@softproindia.in</a>
          </p>

          <p>
            <ion-icon name="globe-outline"></ion-icon>
            <a href="https://www.softproindia.in/" target="_blank" rel="noopener">www.softproindia.in</a>
          </p>
        </div>

        <div class="footer-trust-box">
          <h3>Why Choose Us?</h3>

          <div class="footer-trust-tags">
            <span>Fast Support</span>
            <span>Project Guidance</span>
            <span>Quality Checked</span>
            <span>Secure Payment</span>
            <span>Order Tracking</span>
          </div>

          <div class="footer-social-icons mt-3">
            <a href="https://www.facebook.com/softproindia" target="_blank" rel="noopener">
              <ion-icon name="logo-facebook"></ion-icon>
            </a>
            <a href="https://www.linkedin.com/company/softpro-india" target="_blank" rel="noopener">
              <ion-icon name="logo-linkedin"></ion-icon>
            </a>
            <a href="https://www.instagram.com/softproindia" target="_blank" rel="noopener">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
            <a href="https://www.youtube.com/" target="_blank" rel="noopener">
              <ion-icon name="logo-youtube"></ion-icon>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- FOOTER LEGAL LINKS -->
  <div class="footer-legal-links">
    <div class="container">
      <div class="footer-legal-row">
        <a href="privacy-policy.php"><i class="fa-solid fa-shield-halved"></i> Privacy Policy</a>
        <a href="terms-and-conditions.php"><i class="fa-solid fa-shield-halved"></i> Terms & Conditions</a>
        <a href="cancellation-policy.php"><i class="fa-solid fa-shield-halved"></i> Cancellation Policy</a>
        <a href="refund-policy.php"><i class="fa-solid fa-shield-halved"></i> Refund Policy</a>
        <a href="site-map.php"><i class="fa-solid fa-sitemap"></i> Site Map</a>
      </div>
    </div>
  </div>

  <!-- FOOTER BOTTOM -->
  <div class="footer-bottom">
    <div class="container">
      <p class="copyright">
        &copy; <?= date('Y') ?> <b>Softpro Group of Companies.</b> All Rights Reserved.
        <br>
        Designed by
        <a href="https://www.softproindia.in/" target="_blank" rel="noopener">
          Softpro India Computer Technologies (P) Ltd.
        </a>
      </p>
    </div>
  </div>

</footer>

<style>
  .site-footer {
    /* background: var(--eerie-black); */
    color: var(--sonic-silver);
    margin-top: 40px;
    font-family: inherit;
  }

  .site-footer ul {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
  }

  .site-footer a {
    text-decoration: none;
    transition: 0.25s ease;
  }

  /* CATEGORY DIRECTORY - SAME STYLE ON DESKTOP AND MOBILE */
  .footer-category {
    padding: 40px 0 38px;
    background: var(--eerie-black);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .footer-category-title {
    color: var(--salmon-pink);
    font-size: 16px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 17px;
    letter-spacing: 0.3px;
  }

  .footer-category-row {
    display: block;
    margin-bottom: 16px;
  }

  .footer-category-label {
    display: block;
    color: var(--white);
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    line-height: 22px;
    margin-bottom: 5px;
  }

  .footer-category-links {
    display: block;
    line-height: 24px;
  }

  .footer-category-links a {
    color: var(--sonic-silver);
    font-size: 14px;
    line-height: 24px;
    display: inline;
    white-space: normal;
  }

  .footer-category-links a:not(:last-child)::after {
    content: "|";
    color: var(--sonic-silver);
    margin: 0 7px;
  }

  .footer-category-links a:hover {
    color: var(--salmon-pink);
  }

  /* SERVICE HIGHLIGHTS */
  .footer-service-area {
    padding: 32px 0;
    background: var(--eerie-black);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .footer-service-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
  }

  .footer-service-card {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: var(--eerie-black);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 18px;
    border-radius: 12px;
    min-height: 112px;
    transition: 0.25s ease;
  }

  .footer-service-card:hover {
    transform: translateY(-3px);
    border-color: rgba(239, 102, 47, 0.35);
  }

  .footer-service-icon {
    min-width: 42px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(239, 102, 47, 0.12);
    color: var(--salmon-pink);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }

  .footer-service-card h4 {
    color: var(--white);
    font-size: 15px;
    font-weight: 800;
    margin: 0 0 6px;
  }

  .footer-service-card p {
    color: var(--sonic-silver);
    font-size: 13px;
    line-height: 1.6;
    margin: 0;
  }

  /* FOOTER NAV */
  .footer-nav {
    padding: 42px 0 38px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: var(--eerie-black);
  }

  .footer-nav-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1.65fr;
    gap: 36px;
    align-items: flex-start;
  }

  .nav-title {
    color: var(--white);
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 18px;
    position: relative;
    display: inline-block;
  }

  .nav-title::after {
    content: "";
    width: 42px;
    height: 2px;
    background: var(--salmon-pink);
    position: absolute;
    left: 0;
    bottom: -6px;
  }

  .footer-nav-link {
    color: var(--sonic-silver);
    font-size: 15px;
    display: inline-block;
    margin-bottom: 10px;
  }

  .footer-nav-link:hover {
    color: var(--salmon-pink);
    padding-left: 4px;
  }

  /* MAP */
  .footer-map-box {
    width: 100%;
  }

  .footer-map {
    width: 100%;
    height: 190px;
    border-radius: 10px;
    overflow: hidden;
    background: var(--eerie-black);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18);
  }

  .footer-map iframe {
    filter: grayscale(20%);
    display: block;
  }

  /* EXTRA FOOTER */
  .footer-extra {
    padding: 34px 0;
    background: var(--eerie-black);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .footer-extra-grid {
    display: grid;
    grid-template-columns: 1.1fr 1fr 1fr;
    gap: 30px;
    align-items: flex-start;
  }

  .footer-about-box h3,
  .footer-contact-box h3,
  .footer-trust-box h3 {
    color: var(--white);
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 12px;
  }

  .footer-about-box p {
    color: var(--sonic-silver);
    font-size: 14px;
    line-height: 1.8;
    margin-bottom: 0;
  }

  .footer-contact-box p {
    color: #9d9d9d;
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 9px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
  }

  .footer-contact-box ion-icon {
    color: var(--salmon-pink);
    font-size: 18px;
    margin-top: 2px;
    min-width: 18px;
  }

  .footer-contact-box a {
    color: #9d9d9d;
  }

  .footer-contact-box a:hover {
    color: var(--salmon-pink);
  }

  .footer-trust-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .footer-trust-tags span {
    background: var(--eerie-black);
    color: var(--sonic-silver);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 30px;
    padding: 8px 12px;
    font-size: 13px;
  }

  .footer-social-icons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .footer-social-icons a {
    width: 36px;
    height: 36px;
    background: var(--eerie-black);
    color: var(--white);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .footer-social-icons a:hover {
    background: var(--salmon-pink);
    color: var(--white);
    transform: translateY(-2px);
  }

  .mt-3 {
    margin-top: 16px;
  }


  .footer-legal-links {
    padding: 15px 0;
    background: var(--eerie-black);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    text-align: center;
  }

  .footer-legal-row {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px 24px;
  }

  .footer-legal-row a {
    color: var(--sonic-silver);
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
  }

  .footer-legal-row a:hover {
    color: var(--salmon-pink);
  }

  /* FOOTER BOTTOM */
  .footer-bottom {
    padding: 22px 0;
    background: var(--eerie-black);
    text-align: center;
  }

  .copyright {
    color: var(--sonic-silver);
    font-size: 14px;
    line-height: 1.8;
    margin: 0;
  }

  .copyright b {
    color: var(--white);
  }

  .copyright a {
    color: var(--salmon-pink);
    font-weight: 600;
  }

  .copyright a:hover {
    color: var(--white);
  }

  /* RESPONSIVE */
  @media (max-width: 1199px) {
    .footer-service-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .footer-nav-grid {
      grid-template-columns: repeat(4, 1fr);
    }

    .footer-map-box {
      grid-column: span 4;
    }

    .footer-map {
      height: 220px;
    }

    .footer-extra-grid {
      grid-template-columns: 1fr 1fr;
    }
  }

  @media (max-width: 768px) {
    .footer-category {
      padding: 30px 0 28px;
    }

    .footer-category-title {
      font-size: 15px;
      margin-bottom: 14px;
    }

    .footer-category-label {
      font-size: 12.5px;
      margin-bottom: 4px;
    }

    .footer-category-links a {
      font-size: 13.5px;
      line-height: 24px;
    }

    .footer-service-grid,
    .footer-nav-grid,
    .footer-extra-grid {
      grid-template-columns: 1fr;
    }

    .footer-service-area {
      padding: 28px 0;
    }

    .footer-service-card {
      min-height: auto;
    }

    .footer-nav {
      padding: 34px 0 30px;
    }

    .footer-map-box {
      grid-column: auto;
    }

    .footer-map {
      height: 210px;
    }

    .nav-title {
      font-size: 14px;
    }
  }

  @media (max-width: 480px) {
    .footer-category {
      padding: 28px 0 24px;
    }

    .footer-category-links a {
      font-size: 13px;
    }

    .footer-category-links a:not(:last-child)::after {
      margin: 0 6px;
    }

    .footer-map {
      height: 190px;
    }

    .footer-service-card {
      padding: 15px;
    }
  }


  /* ===== Final theme override: keeps all pages consistent after page-level CSS ===== */
  :root {
    --eerie-black: var(--theme-dark);
  }

  .desktop-navigation-menu {
    background: var(--white) !important;
    border-bottom: 1px solid var(--cultured) !important;
    position: relative;
    z-index: 50
  }

  .desktop-menu-category-list {
    gap: 26px !important
  }

  .desktop-menu-category-list .menu-category {
    position: relative !important
  }

  .desktop-menu-category-list .menu-category>.menu-title {
    position: relative !important;
    display: inline-flex !important;
    color: var(--eerie-black) !important;
    font-weight: 700 !important;
    padding: 15px 0 13px !important
  }

  .desktop-menu-category-list .menu-category>.menu-title:hover,
  .desktop-menu-category-list .menu-category:hover>.menu-title {
    color: var(--salmon-pink) !important
  }

  .desktop-menu-category-list .menu-category>.menu-title::after {
    content: "" !important;
    position: absolute !important;
    left: 0 !important;
    bottom: 5px !important;
    width: 100% !important;
    height: 3px !important;
    border-radius: 20px !important;
    background: var(--salmon-pink) !important;
    transform: scaleX(0) !important;
    transform-origin: left center !important;
    transition: transform .22s ease !important;
    display: block !important;
    opacity: 1 !important
  }

  .desktop-menu-category-list .menu-category:hover>.menu-title::after,
  .desktop-menu-category-list .menu-category>.menu-title:hover::after {
    transform: scaleX(1) !important
  }

  .header-main .logo-text h3,
  .logo-text h3,
  .company-name {
    font-family: 'Revue BT', 'RevueBT', 'Revue', Georgia, serif !important;
    color: var(--eerie-black) !important;
    letter-spacing: .3px;
    line-height: 1.05
  }

  .banner {
    margin-bottom: 12px !important
  }

  .category {
    margin: 8px 0 18px !important
  }

  .product-container {
    margin-top: 0 !important;
    padding-top: 0 !important
  }

  .product-container .container {
    gap: 22px !important;
    padding-top: 0 !important
  }

  .category-item-container {
    gap: 8px !important;
    padding-bottom: 4px !important
  }

  .category .swiper {
    width: 100%;
    overflow: hidden
  }

  .category .category-item {
    min-width: 0 !important;
    width: 100% !important;
    padding: 11px 12px !important;
    gap: 9px !important
  }

  .category .category-img-box {
    width: 48px;
    height: 48px;
    min-width: 48px;
    display: grid;
    place-items: center;
    padding: 7px !important
  }

  .category .category-img-box img {
    max-width: 34px;
    max-height: 34px;
    object-fit: contain
  }

  .category .category-item-title {
    max-width: 120px !important
  }

  .product-grid {
    gap: 10px !important;
    align-items: stretch
  }

  .product-grid .showcase {
    min-height: 355px !important;
    border-color: #ececec !important;
    background: var(--white) !important;
    display: flex !important;
    flex-direction: column !important
  }

  .product-grid .showcase-banner {
    height: 168px !important;
    background: #fafafa !important;
    overflow: hidden !important
  }

  .product-grid .product-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    padding: 10px !important
  }

  .product-grid .showcase-actions {
    top: 10px !important;
    right: 10px !important;
    transform: translateX(0) !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    z-index: 8 !important
  }

  .product-grid .btn-action {
    width: 34px !important;
    height: 34px !important;
    padding: 0 !important;
    margin: 0 !important;
    display: grid !important;
    place-items: center !important;
    background: rgba(255, 255, 255, .96) !important;
    color: var(--eerie-black) !important;
    border: 1px solid #ededed !important;
    border-radius: 9px !important;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .08)
  }

  .product-grid .btn-action ion-icon {
    color: currentColor !important;
    font-size: 18px
  }

  .product-grid .btn-action:hover,
  .product-grid .wishlist-btn:hover,
  .product-grid .wishlist-btn.wishlisted:hover {
    background: var(--salmon-pink) !important;
    color: var(--white) !important;
    border-color: var(--salmon-pink) !important
  }

  .product-grid .btn-action:hover ion-icon,
  .product-grid .wishlist-btn:hover ion-icon,
  .wishlist-btn:hover ion-icon {
    color: var(--white) !important
  }

  .product-grid .showcase-content {
    flex: 1 !important;
    padding: 13px 16px 16px !important
  }

  .product-grid .showcase-title {
    color: var(--eerie-black) !important;
    font-size: 15px !important;
    line-height: 1.35 !important;
    min-height: 40px !important;
    font-weight: 500 !important
  }

  .product-grid .showcase-category {
    margin-bottom: 6px !important
  }

  .product-grid .showcase-rating {
    margin-bottom: 6px !important
  }

  .product-grid .buy-now-wrap {
    margin-top: auto !important;
    padding-top: 10px !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important
  }

  .product-grid .buy-now-btn {
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 39px;
    background: var(--salmon-pink) !important;
    color: var(--white) !important;
    border-radius: 11px !important;
    font-weight: 800 !important;
    text-decoration: none !important
  }

  .product-grid .buy-now-btn:hover {
    background: var(--eerie-black) !important;
    color: var(--white) !important
  }

  .product-minimal {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 22px !important;
    margin-top: 28px !important;
    margin-bottom: 28px !important
  }

  .product-minimal .product-showcase {
    min-width: 0 !important;
    width: 100% !important;
    margin-bottom: 0 !important
  }

  .product-minimal .title {
    margin-bottom: 14px !important
  }

  .product-minimal .showcase-wrapper {
    display: block !important;
    overflow: visible !important
  }

  .product-minimal .showcase-container {
    min-width: 0 !important;
    width: 100% !important;
    padding: 0 !important
  }

  .product-minimal .showcase {
    width: 100% !important;
    min-height: 86px;
    margin-bottom: 12px !important;
    padding: 10px !important;
    gap: 12px !important;
    background: var(--white) !important;
    border-color: #eee !important
  }

  .product-minimal .showcase-img {
    width: 64px !important;
    height: 64px !important;
    object-fit: contain !important
  }

  .product-minimal .showcase-content {
    width: calc(100% - 76px) !important;
    min-width: 0 !important
  }

  .product-minimal .showcase-title {
    white-space: normal !important;
    line-height: 1.25 !important
  }

  .site-footer,
  .site-footer .footer-category,
  .site-footer .footer-service-area,
  .site-footer .footer-service-card,
  .site-footer .footer-nav,
  .site-footer .footer-map,
  .site-footer .footer-extra,
  .site-footer .footer-trust-tags span,
  .site-footer .footer-social-icons a,
  .site-footer .footer-bottom,
  footer,
  .footer-category,
  .footer-nav,
  .footer-bottom {
    background: var(--eerie-black) !important
  }

  .site-footer {
    color: var(--sonic-silver) !important
  }

  .site-footer .footer-category-title,
  .site-footer .copyright a {
    color: var(--salmon-pink) !important
  }

  .site-footer .footer-category-label,
  .site-footer .nav-title,
  .site-footer h3,
  .site-footer h4,
  .site-footer .copyright b {
    color: var(--white) !important
  }

  .site-footer a,
  .site-footer p,
  .site-footer .footer-nav-link,
  .site-footer .footer-category-links a,
  .site-footer .footer-service-card p,
  .site-footer .footer-about-box p,
  .site-footer .footer-contact-box p,
  .site-footer .footer-contact-box a,
  .site-footer .copyright {
    color: var(--sonic-silver) !important
  }

  .site-footer a:hover,
  .site-footer .footer-nav-link:hover,
  .site-footer .footer-category-links a:hover {
    color: var(--salmon-pink) !important
  }

  @media(min-width:1024px) {
    .product-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr)) !important
    }
  }

  @media(min-width:768px) and (max-width:1023px) {
    .product-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 18px !important
    }

    .product-grid .showcase-banner {
      height: 150px !important
    }

    .product-minimal {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 18px !important
    }

    .product-minimal .showcase {
      min-height: 78px;
      padding: 8px !important
    }

    .product-minimal .showcase-img {
      width: 56px !important;
      height: 56px !important
    }

    .product-minimal .showcase-content {
      width: calc(100% - 68px) !important
    }
  }

  @media(max-width:767px) {
    .product-minimal {
      grid-template-columns: 1fr !important;
      gap: 16px !important
    }

    .product-grid .showcase-actions {
      right: 8px !important;
      top: 8px !important
    }
  }

  /* ===== User request final fix V2: black menu hover and product action animation ===== */
  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title:hover,
  .desktop-navigation-menu .desktop-menu-category-list .menu-category:hover>.menu-title,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title:hover,
  .desktop-menu-category-list.nav-menu .menu-category:hover>.menu-title {
    color: #111 !important
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title::after,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title::after {
    background: #111 !important;
    border-color: #111 !important
  }

  .product-grid .showcase {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important
  }

  .product-grid .showcase-actions {
    top: 10px !important;
    right: 10px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    z-index: 9 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateX(18px) scale(.96) !important;
    transition: opacity .25s ease, transform .25s ease, visibility .25s ease !important
  }

  .product-grid .showcase:hover .showcase-actions,
  .product-grid .showcase:focus-within .showcase-actions {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateX(0) scale(1) !important
  }

  .product-grid .btn-action {
    width: 34px !important;
    height: 34px !important;
    display: grid !important;
    place-items: center !important;
    background: rgba(255, 255, 255, .96) !important;
    color: #111 !important;
    border: 1px solid #eee !important;
    border-radius: 9px !important;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .08) !important
  }

  .product-grid .btn-action ion-icon,
  .product-grid .btn-action i {
    color: currentColor !important
  }

  .product-grid .btn-action:hover,
  .product-grid .wishlist-btn:hover,
  .product-grid .wishlist-btn.wishlisted:hover {
    background: var(--salmon-pink) !important;
    color: #fff !important;
    border-color: var(--salmon-pink) !important
  }

  .product-grid .btn-action:hover ion-icon,
  .product-grid .wishlist-btn:hover ion-icon,
  .product-grid .btn-action:hover i,
  .product-grid .wishlist-btn:hover i {
    color: #fff !important
  }

  .product-grid .buy-now-wrap {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    margin-top: auto !important;
    padding-top: 12px !important
  }

  .product-grid .buy-now-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    min-height: 39px !important;
    visibility: visible !important;
    opacity: 1 !important;
    text-decoration: none !important
  }

  .shop-layout .product-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 25px !important
  }

  @media(max-width:991px) {
    .shop-layout .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important
    }
  }

  @media(max-width:575px) {
    .shop-layout .product-grid {
      grid-template-columns: 1fr !important
    }
  }



  /* =========================================================
   FINAL SPECIFIC HEADER + PRODUCT FIX V3
   - normal menu orange, hover black + black underline
   - logo image path/size stable
   - product actions hidden until card hover with animation
   - Buy Now always visible
   ========================================================= */
  .header-main .logo,
  .logo {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
  }

  .header-main .header-logo,
  .header-main .logo a.header-logo {
    width: 80px !important;
    height: 80px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 80px !important;
    overflow: hidden !important;
  }

  .header-main .header-logo img,
  .header-main .logo img {
    width: 80px !important;
    height: 80px !important;
    object-fit: contain !important;
    display: block !important;
    max-width: 80px !important;
  }

  .header-main .logo-text h3.company-name,
  .header-main .company-name,
  .logo-text .company-name {
    font-family: 'Revue BT', 'RevueBT', 'Revue', Georgia, serif !important;
    color: #111111 !important;
    /* font-size: 24px !important; */
    line-height: 1.05 !important;
    letter-spacing: .25px !important;
    margin: 0 0 2px !important;
    font-weight: 800 !important;
  }

  .header-main .logo-text p {
    color: var(--salmon-pink) !important;
    margin: 0 !important;
    line-height: 1.35 !important;
    font-size: 13px !important;
    font-weight: 700 !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list,
  .desktop-menu-category-list.nav-menu {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 60px !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title {
    color: var(--salmon-pink) !important;
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    padding: 17px 0 16px !important;
    font-weight: 800 !important;
    font-size: 14px !important;
    letter-spacing: .45px !important;
    text-transform: uppercase !important;
    text-decoration: none !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title:hover,
  .desktop-navigation-menu .desktop-menu-category-list .menu-category:hover>.menu-title,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title:hover,
  .desktop-menu-category-list.nav-menu .menu-category:hover>.menu-title {
    color: #111111 !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title::after,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title::after {
    content: '' !important;
    position: absolute !important;
    left: 0 !important;
    bottom: 8px !important;
    width: 100% !important;
    height: 2px !important;
    border-radius: 20px !important;
    background: #111111 !important;
    transform: scaleX(0) !important;
    transform-origin: left center !important;
    transition: transform .22s ease !important;
    opacity: 1 !important;
    display: block !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category:hover>.menu-title::after,
  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title:hover::after,
  .desktop-menu-category-list.nav-menu .menu-category:hover>.menu-title::after,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title:hover::after {
    transform: scaleX(1) !important;
  }

  .product-grid .showcase {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
  }

  .product-grid .showcase-actions {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
    z-index: 10 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateX(22px) scale(.96) !important;
    transition: opacity .25s ease, transform .25s ease, visibility .25s ease !important;
  }

  .product-grid .showcase:hover .showcase-actions,
  .product-grid .showcase:focus-within .showcase-actions {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateX(0) scale(1) !important;
  }

  .product-grid .btn-action {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    padding: 0 !important;
    margin: 0 !important;
    display: grid !important;
    place-items: center !important;
    background: rgba(255, 255, 255, .96) !important;
    color: #111111 !important;
    border: 1px solid #eeeeee !important;
    border-radius: 9px !important;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .08) !important;
  }

  .product-grid .btn-action ion-icon,
  .product-grid .btn-action i {
    color: currentColor !important;
    font-size: 18px !important;
  }

  .product-grid .btn-action:hover,
  .product-grid .wishlist-btn:hover,
  .product-grid .wishlist-btn.wishlisted:hover {
    background: var(--salmon-pink) !important;
    color: #ffffff !important;
    border-color: var(--salmon-pink) !important;
  }

  .product-grid .btn-action:hover ion-icon,
  .product-grid .wishlist-btn:hover ion-icon,
  .product-grid .btn-action:hover i,
  .product-grid .wishlist-btn:hover i {
    color: #ffffff !important;
  }

  .product-grid .buy-now-wrap {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    margin-top: auto !important;
    padding-top: 12px !important;
  }

  .product-grid .buy-now-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    min-height: 39px !important;
    visibility: visible !important;
    opacity: 1 !important;
    text-decoration: none !important;
  }

  .shop-layout .product-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 25px !important;
  }

  @media (max-width: 991px) {
    .shop-layout .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .desktop-navigation-menu .desktop-menu-category-list,
    .desktop-menu-category-list.nav-menu {
      gap: 24px !important;
    }
  }

  @media (max-width: 575px) {
    .shop-layout .product-grid {
      grid-template-columns: 1fr !important;
    }
  }
</style>


<style>
  /* =========================================================
   FINAL RESPONSIVE + PRODUCT + SIDEBAR FIX
   Added by ChatGPT for SIP Innovation theme
   ========================================================= */

  /* Product card border: home column-right + products page */
  .product-grid .showcase,
  .product-main .product-grid .showcase,
  .shop-layout .product-grid .showcase {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    background: #ffffff !important;
    border: 1px solid #111111 !important;
    border-radius: 18px !important;
    transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease !important;
  }

  .product-grid .showcase:hover,
  .product-grid .showcase:focus-within,
  .product-main .product-grid .showcase:hover,
  .shop-layout .product-grid .showcase:hover,
  .shop-layout .product-grid .showcase:focus-within {
    border-color: var(--salmon-pink) !important;
    box-shadow: 0 16px 35px rgba(239, 102, 47, .16) !important;
    transform: translateY(-3px) !important;
  }

  /* Product image area stable on every view */
  .product-grid .showcase-banner,
  .shop-layout .product-grid .showcase-banner {
    position: relative !important;
    height: 168px !important;
    background: #fafafa !important;
    overflow: hidden !important;
  }

  .product-grid .product-img,
  .shop-layout .product-grid .product-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    padding: 10px !important;
  }

  .product-grid .showcase-content,
  .shop-layout .product-grid .showcase-content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
    padding: 14px 16px 16px !important;
  }

  /* Showcase actions: hidden on hover devices, animated on card hover */
  .product-grid .showcase-actions,
  .shop-layout .product-grid .showcase-actions {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 6px !important;
    z-index: 20 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transform: translateX(24px) scale(.96) !important;
    transition: opacity .25s ease, transform .25s ease, visibility .25s ease !important;
  }

  .product-grid .showcase:hover .showcase-actions,
  .product-grid .showcase:focus-within .showcase-actions,
  .shop-layout .product-grid .showcase:hover .showcase-actions,
  .shop-layout .product-grid .showcase:focus-within .showcase-actions {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    transform: translateX(0) scale(1) !important;
  }

  .product-grid .btn-action,
  .shop-layout .product-grid .btn-action {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    min-height: 34px !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(255, 255, 255, .96) !important;
    color: #111111 !important;
    border: 1px solid #e8e8e8 !important;
    border-radius: 9px !important;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .08) !important;
    transition: background .22s ease, color .22s ease, border-color .22s ease, transform .22s ease !important;
  }

  .product-grid .btn-action ion-icon,
  .product-grid .btn-action i,
  .shop-layout .product-grid .btn-action ion-icon,
  .shop-layout .product-grid .btn-action i {
    color: currentColor !important;
    font-size: 18px !important;
    line-height: 1 !important;
  }

  .product-grid .btn-action:hover,
  .product-grid .wishlist-btn:hover,
  .product-grid .wishlist-btn.wishlisted:hover,
  .shop-layout .product-grid .btn-action:hover,
  .shop-layout .product-grid .wishlist-btn:hover,
  .shop-layout .product-grid .wishlist-btn.wishlisted:hover {
    background: var(--salmon-pink) !important;
    color: #ffffff !important;
    border-color: var(--salmon-pink) !important;
    transform: translateX(-2px) !important;
  }

  .product-grid .btn-action:hover ion-icon,
  .product-grid .wishlist-btn:hover ion-icon,
  .product-grid .btn-action:hover i,
  .product-grid .wishlist-btn:hover i,
  .shop-layout .product-grid .btn-action:hover ion-icon,
  .shop-layout .product-grid .wishlist-btn:hover ion-icon,
  .shop-layout .product-grid .btn-action:hover i,
  .shop-layout .product-grid .wishlist-btn:hover i {
    color: #ffffff !important;
  }

  /* Touch devices do not have real hover, so actions stay usable */
  @media (hover: none),
  (pointer: coarse) {

    .product-grid .showcase-actions,
    .shop-layout .product-grid .showcase-actions {
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
      transform: translateX(0) scale(1) !important;
    }
  }

  /* Buy Now always visible */
  .product-grid .buy-now-wrap,
  .shop-layout .product-grid .buy-now-wrap {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    margin-top: auto !important;
    padding-top: 12px !important;
  }

  .product-grid .buy-now-btn,
  .shop-layout .product-grid .buy-now-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    min-height: 39px !important;
    padding: 9px 12px !important;
    border-radius: 11px !important;
    background: var(--salmon-pink) !important;
    color: #ffffff !important;
    font-weight: 800 !important;
    text-align: center !important;
    text-decoration: none !important;
    visibility: visible !important;
    opacity: 1 !important;
  }

  .product-grid .buy-now-btn:hover,
  .shop-layout .product-grid .buy-now-btn:hover {
    background: #111111 !important;
    color: #ffffff !important;
  }

  /* Products page layout */
  .shop-layout {
    display: grid !important;
    grid-template-columns: 320px minmax(0, 1fr) !important;
    gap: 25px !important;
    align-items: start !important;
  }

  .shop-layout .product-grid {
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 25px !important;
  }

  @media (max-width: 1199px) and (min-width: 992px) {
    .shop-layout {
      grid-template-columns: 300px minmax(0, 1fr) !important;
      gap: 20px !important;
    }

    .shop-layout .product-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 18px !important;
    }
  }

  @media (max-width: 991px) {
    .shop-layout {
      display: block !important;
    }

    .filter-sidebar {
      display: none !important;
    }

    .shop-layout .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      gap: 16px !important;
    }
  }

  @media (max-width: 575px) {

    .shop-layout .product-grid,
    .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      gap: 12px !important;
    }

    .product-grid .showcase-banner,
    .shop-layout .product-grid .showcase-banner {
      height: 150px !important;
    }

    .product-grid .showcase-content,
    .shop-layout .product-grid .showcase-content {
      padding: 11px 10px 12px !important;
    }

    .product-grid .showcase-title,
    .shop-layout .product-grid .showcase-title {
      font-size: 13px !important;
      line-height: 1.3 !important;
      min-height: 34px !important;
    }

    .product-grid .btn-action,
    .shop-layout .product-grid .btn-action {
      width: 30px !important;
      height: 30px !important;
      min-width: 30px !important;
      min-height: 30px !important;
    }

    .product-grid .buy-now-btn,
    .shop-layout .product-grid .buy-now-btn {
      min-height: 34px !important;
      font-size: 12px !important;
    }
  }

  /* 869px to 992px home page blank space fix: sidebar/category space removed */
  @media (min-width: 869px) and (max-width: 992px) {
    body:not(.products-page) .product-container>.container {
      display: block !important;
      max-width: 930px !important;
      width: 100% !important;
      padding-top: 0 !important;
      margin-top: 0 !important;
    }

    body:not(.products-page) .product-container .sp-category-wrapper,
    body:not(.products-page) .product-container .sp-sidebar,
    body:not(.products-page) .product-container .sp-category-card,
    body:not(.products-page) .product-container .sidebar.sp-sidebar {
      display: none !important;
      visibility: hidden !important;
      opacity: 0 !important;
      height: 0 !important;
      min-height: 0 !important;
      max-height: 0 !important;
      width: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: hidden !important;
      position: absolute !important;
      pointer-events: none !important;
    }

    body:not(.products-page) .product-box,
    body:not(.products-page) .product-main {
      width: 100% !important;
      min-width: 100% !important;
      max-width: 100% !important;
      margin-top: 0 !important;
      padding-top: 0 !important;
    }

    body:not(.products-page) .product-container {
      margin-top: 0 !important;
      padding-top: 0 !important;
    }
  }

  /* broader tablet cleanup for same issue */
  @media (min-width: 769px) and (max-width: 992px) {
    body:not(.products-page) .product-container>.container {
      display: block !important;
    }

    body:not(.products-page) .product-container .sp-category-wrapper {
      display: none !important;
    }

    body:not(.products-page) .product-main .title,
    body:not(.products-page) .product-container .title {
      margin-top: 0 !important;
    }

    body:not(.products-page) .product-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 16px !important;
    }

    body:not(.products-page) .product-grid .showcase-banner {
      height: 165px !important;
    }
  }

  /* Toast only on desktop */
  @media (max-width: 1023px) {

    .toast-link-wrap,
    .notification-toast,
    #toastLink,
    #productToast {
      display: none !important;
      opacity: 0 !important;
      visibility: hidden !important;
      pointer-events: none !important;
    }
  }

  @media (min-width: 1024px) {
    .notification-toast {
      display: flex !important;
    }
  }

  /* Keep desktop menu spaced and hover black */
  .desktop-navigation-menu .desktop-menu-category-list,
  .desktop-menu-category-list.nav-menu {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-wrap: nowrap !important;
    gap: 54px !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category,
  .desktop-menu-category-list.nav-menu .menu-category {
    margin: 0 !important;
    padding: 0 !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title {
    color: var(--salmon-pink) !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title:hover,
  .desktop-navigation-menu .desktop-menu-category-list .menu-category:hover>.menu-title,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title:hover,
  .desktop-menu-category-list.nav-menu .menu-category:hover>.menu-title {
    color: #111111 !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list .menu-category>.menu-title::after,
  .desktop-menu-category-list.nav-menu .menu-category>.menu-title::after {
    background: #111111 !important;
  }

  @media (max-width: 1199px) {

    .desktop-navigation-menu .desktop-menu-category-list,
    .desktop-menu-category-list.nav-menu {
      gap: 30px !important;
    }
  }

  @media (max-width: 767px) {
    .desktop-navigation-menu {
      display: none !important;
    }
  }
</style>


<style id="sip-final-responsive-fixes">
  /* =========================================================
   FINAL RESPONSIVE PRODUCT / MENU / PROFILE FIXES
   Applies after page inline styles.
   ========================================================= */
  :root {
    --sip-orange: #ef662f;
    --sip-dark: #101522;
    --sip-black: #111827;
    --sip-soft: #f6f7fb;
    --sip-white: #ffffff;
  }

  /* Simple clean profile dropdown */
  .header-user-actions .dropdown-menu {
    min-width: 210px !important;
    padding: 8px !important;
    border: 1px solid rgba(17, 24, 39, .10) !important;
    border-radius: 14px !important;
    box-shadow: 0 16px 45px rgba(17, 24, 39, .14) !important;
    background: #fff !important;
  }

  .header-user-actions .dropdown-item {
    display: flex !important;
    align-items: center !important;
    gap: 9px !important;
    padding: 10px 12px !important;
    border-radius: 10px !important;
    color: #1f2937 !important;
    font-size: 14px !important;
    font-weight: 700 !important;
  }

  .header-user-actions .dropdown-item:hover {
    background: rgba(239, 102, 47, .10) !important;
    color: var(--sip-orange) !important;
  }

  /* Desktop menu: more width + simple dropdown */
  .desktop-navigation-menu .container-fluid {
    max-width: 1210px !important;
    margin: 0 auto !important;
    padding: 0 14px !important;
  }

  .desktop-navigation-menu .desktop-menu-category-list,
  .desktop-menu-category-list.nav-menu {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 0 !important;
    width: 100% !important;
    margin: 0 auto !important;
    padding: 0 !important;
  }

  .desktop-navigation-menu .menu-category {
    position: relative !important;
    margin: 0 !important;
    padding: 0 !important;
    flex: 0 0 auto !important;
  }

  .desktop-navigation-menu .menu-title {
    color: var(--sip-orange) !important;
    padding: 17px 0 !important;
    font-size: 14px !important;
    font-weight: 800 !important;
    letter-spacing: .45px !important;
    text-transform: uppercase !important;
    white-space: nowrap !important;
  }

  .desktop-navigation-menu .menu-title:hover,
  .desktop-navigation-menu .menu-category:hover>.menu-title {
    color: #111 !important;
  }

  .desktop-navigation-menu .menu-title::after {
    content: "" !important;
    position: absolute !important;
    left: 0 !important;
    bottom: 8px !important;
    width: 100% !important;
    height: 2px !important;
    background: #111 !important;
    border-radius: 20px !important;
    transform: scaleX(0) !important;
    transform-origin: left center !important;
    transition: .22s ease !important;
  }

  .desktop-navigation-menu .menu-category:hover>.menu-title::after,
  .desktop-navigation-menu .menu-title:hover::after {
    transform: scaleX(1) !important;
  }

  .desktop-navigation-menu .dropdown-list {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    min-width: 230px !important;
    width: max-content !important;
    max-width: 280px !important;
    padding: 8px !important;
    background: #fff !important;
    border: 1px solid rgba(17, 24, 39, .10) !important;
    border-radius: 14px !important;
    box-shadow: 0 18px 45px rgba(17, 24, 39, .16) !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(10px) !important;
    transition: .22s ease !important;
    z-index: 99999 !important;
  }

  .desktop-navigation-menu .menu-category:hover>.dropdown-list {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
  }

  .desktop-navigation-menu .dropdown-item {
    padding: 0 !important;
    margin: 0 !important;
    background: transparent !important;
  }

  .desktop-navigation-menu .dropdown-item a {
    display: block !important;
    padding: 10px 12px !important;
    border-radius: 10px !important;
    color: #4b5563 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    line-height: 1.25 !important;
    white-space: nowrap !important;
  }

  .desktop-navigation-menu .dropdown-item a:hover {
    background: rgba(239, 102, 47, .10) !important;
    color: var(--sip-orange) !important;
  }

  /* Product/card baseline for index, products, column-right */
  .product-grid {
    display: grid !important;
    align-items: stretch !important;
  }

  .product-grid .showcase {
    position: relative !important;
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    min-height: 0 !important;
    background: #fff !important;
    border: 1px solid #111827 !important;
    border-radius: 18px !important;
    overflow: hidden !important;
    box-shadow: 0 8px 22px rgba(17, 24, 39, .05) !important;
    transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease !important;
  }

  .product-grid .showcase:hover {
    border-color: var(--sip-orange) !important;
    box-shadow: 0 14px 34px rgba(239, 102, 47, .16) !important;
    transform: translateY(-3px) !important;
  }

  .product-grid .showcase-banner {
    position: relative !important;
    width: 100% !important;
    height: 190px !important;
    min-height: 190px !important;
    overflow: hidden !important;
    background: #f8fafc !important;
  }

  .product-grid .product-img,
  .product-grid .product-img.default,
  .product-grid .product-img.hover {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain !important;
    object-position: center !important;
    padding: 10px !important;
  }

  .product-grid .product-img.hover {
    display: none !important;
    opacity: 0 !important;
  }

  .product-grid .showcase:hover .product-img.default {
    opacity: 1 !important;
    transform: scale(1.035) !important;
  }

  .product-grid .showcase-content {
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
    padding: 14px 16px 16px !important;
  }

  .product-grid .showcase-category {
    color: var(--sip-orange) !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: .55px !important;
    margin-bottom: 5px !important;
    text-transform: uppercase !important;
  }

  .product-grid .showcase-title {
    color: #1f2937 !important;
    font-size: 15px !important;
    font-weight: 800 !important;
    line-height: 1.35 !important;
    min-height: 40px !important;
    margin-bottom: 8px !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
  }

  .product-grid .showcase-rating {
    color: var(--sip-orange) !important;
    margin-bottom: 6px !important;
  }

  .product-grid .price-box {
    margin: 0 0 8px !important;
  }

  .product-grid .price {
    color: var(--sip-orange) !important;
    font-size: 18px !important;
    font-weight: 900 !important;
  }

  .product-grid .tax-line,
  .product-grid .stock-line {
    font-size: 12px !important;
    font-weight: 800 !important;
    line-height: 1.35 !important;
  }

  .product-grid .buy-now-wrap {
    margin-top: auto !important;
    padding-top: 12px !important;
  }

  .product-grid .buy-now-btn,
  .product-grid .showcase-content a.buy-now-btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    min-height: 40px !important;
    padding: 10px 12px !important;
    border-radius: 12px !important;
    background: var(--sip-orange) !important;
    color: #fff !important;
    border: 1px solid var(--sip-orange) !important;
    font-size: 14px !important;
    font-weight: 900 !important;
    text-decoration: none !important;
    opacity: 1 !important;
    visibility: visible !important;
  }

  .product-grid .buy-now-btn:hover {
    background: #111827 !important;
    border-color: #111827 !important;
    color: #fff !important;
  }

  /* Showcase actions: desktop hover, touch visible */
  .product-grid .showcase-actions {
    position: absolute !important;
    top: 10px !important;
    right: 10px !important;
    z-index: 30 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 7px !important;
    width: auto !important;
    max-width: 36px !important;
    max-height: calc(100% - 20px) !important;
    overflow: visible !important;
    transition: opacity .25s ease, visibility .25s ease, transform .25s ease !important;
  }

  .product-grid .showcase-actions .btn-action,
  .product-grid .btn-action {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    min-height: 34px !important;
    padding: 0 !important;
    margin: 0 !important;
    border-radius: 10px !important;
    background: #fff !important;
    color: #111827 !important;
    border: 1px solid rgba(17, 24, 39, .10) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    box-shadow: 0 6px 18px rgba(17, 24, 39, .13) !important;
    line-height: 1 !important;
    text-decoration: none !important;
  }

  .product-grid .btn-action ion-icon {
    font-size: 18px !important;
    color: inherit !important;
    --ionicon-stroke-width: 44px;
  }

  .product-grid .btn-action:hover {
    background: var(--sip-orange) !important;
    color: #fff !important;
    border-color: var(--sip-orange) !important;
  }

  @media (hover:hover) and (pointer:fine) {
    .product-grid .showcase-actions {
      opacity: 0 !important;
      visibility: hidden !important;
      transform: translateX(16px) !important;
    }

    .product-grid .showcase:hover .showcase-actions {
      opacity: 1 !important;
      visibility: visible !important;
      transform: translateX(0) !important;
    }
  }

  @media (hover:none),
  (pointer:coarse) {
    .product-grid .showcase-actions {
      opacity: 1 !important;
      visibility: visible !important;
      transform: none !important;
    }
  }

  /* Products page/sidebar layout */
  .products-page .shop-layout {
    display: grid !important;
    grid-template-columns: 300px minmax(0, 1fr) !important;
    gap: 24px !important;
    align-items: start !important;
  }

  .products-page .filter-sidebar {
    width: 100% !important;
    min-width: 0 !important;
  }

  .products-page .product-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 22px !important;
  }

  /* Index / column-right product grids */
  body:not(.products-page) .product-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 22px !important;
  }

  @media (max-width:1199px) {
    .desktop-navigation-menu .container-fluid {
      max-width: 1060px !important;
    }

    .desktop-navigation-menu .menu-title {
      font-size: 13px !important;
    }

    .products-page .shop-layout {
      grid-template-columns: 270px minmax(0, 1fr) !important;
      gap: 18px !important;
    }

    .products-page .product-grid,
    body:not(.products-page) .product-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 18px !important;
    }
  }

  @media (min-width:769px) and (max-width:1023px) {
    /* .mobile-bottom-navigation {
      display: none !important;
      opacity: 0 !important;
      visibility: hidden !important;
      pointer-events: none !important;
    } */

    .products-page .shop-layout {
      display: block !important;
    }

    .products-page .filter-sidebar {
      display: none !important;
    }

    .products-page .product-grid,
    body:not(.products-page) .product-grid {
      grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
      gap: 16px !important;
    }

    .product-grid .showcase-banner {
      height: 160px !important;
      min-height: 160px !important;
    }

    .product-grid .showcase-content {
      padding: 12px !important;
    }

    .product-grid .showcase-title {
      font-size: 14px !important;
      min-height: 38px !important;
    }

    .product-grid .price {
      font-size: 16px !important;
    }
  }

  @media (max-width:768px) {
    .products-page .shop-layout {
      display: block !important;
    }

    .products-page .filter-sidebar {
      display: none !important;
    }

    .products-page .product-grid,
    body:not(.products-page) .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      gap: 12px !important;
    }

    .product-grid .showcase {
      border-radius: 14px !important;
    }

    .product-grid .showcase-banner {
      height: 150px !important;
      min-height: 100px !important;
    }

    .product-grid .product-img {
      padding: 8px !important;
    }

    .product-grid .showcase-content {
      padding: 10px !important;
    }

    .product-grid .showcase-title {
      font-size: 13px !important;
      min-height: 36px !important;
      letter-spacing: .2px !important;
    }

    .product-grid .showcase-category {
      font-size: 10px !important;
    }

    .product-grid .showcase-rating {
      font-size: 13px !important;
    }

    .product-grid .price {
      font-size: 15px !important;
    }

    .product-grid .tax-line,
    .product-grid .stock-line {
      font-size: 11px !important;
    }

    .product-grid .buy-now-btn {
      min-height: 36px !important;
      padding: 8px !important;
      font-size: 12px !important;
      border-radius: 9px !important;
    }

    .product-grid .showcase-actions {
      top: 8px !important;
      right: 8px !important;
      gap: 5px !important;
      max-width: 30px !important;
    }

    .product-grid .showcase-actions .btn-action,
    .product-grid .btn-action {
      width: 30px !important;
      height: 30px !important;
      min-width: 30px !important;
      min-height: 30px !important;
      border-radius: 8px !important;
    }

    .product-grid .btn-action ion-icon {
      font-size: 16px !important;
    }
  }

  @media (max-width:400px) {
    .container {
      width: 100% !important;
      max-width: 100% !important;
      padding-left: 10px !important;
      padding-right: 10px !important;
    }

    .products-page .product-grid,
    body:not(.products-page) .product-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      gap: 10px !important;
    }

    .product-grid .showcase-banner {
      height: 140px !important;
      min-height: 122px !important;
    }

    .product-grid .showcase-content {
      padding: 8px !important;
    }

    .product-grid .showcase-title {
      font-size: 12px !important;
      min-height: 34px !important;
    }

    .product-grid .price {
      font-size: 14px !important;
    }

    .product-grid .showcase-actions .btn-action,
    .product-grid .btn-action {
      width: 28px !important;
      height: 28px !important;
      min-width: 28px !important;
      min-height: 28px !important;
    }

    .product-grid .showcase-actions {
      max-width: 28px !important;
    }
  }

  @media (max-width:340px) {
    .product-grid .showcase-banner {
      height: 130px !important;
      min-height: 108px !important;
    }

    .product-grid .showcase-actions .btn-action,
    .product-grid .btn-action {
      width: 26px !important;
      height: 26px !important;
      min-width: 26px !important;
      min-height: 26px !important;
    }

    .product-grid .btn-action ion-icon {
      font-size: 14px !important;
    }
  }

  /* Profile fields must never look blank */
  .profile-info-card input[readonly],
  .profile-info-card input:disabled {
    color: #111827 !important;
    -webkit-text-fill-color: #111827 !important;
    opacity: 1 !important;
    background: #f8fafc !important;
    border-color: #d7dce6 !important;
    font-weight: 800 !important;
  }

  .profile-info-card input[name="email"],
  .profile-info-card input[name="mobile"] {
    cursor: not-allowed !important;
  }
</style>

<script src="./assets/js/script.js"></script>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons/ionicons.js"></script>

<script src="./assets/js/script2.js"></script>



<!-- ================= SIP QUICK VIEW + RECENTLY VIEWED FEATURE ================= -->
<style>
  .sip-qv-modal {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, .72);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    z-index: 100000
  }

  .sip-qv-modal.open {
    display: flex
  }

  .sip-qv-box {
    width: min(860px, 96vw);
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 24px 80px rgba(0, 0, 0, .25);
    position: relative;
    overflow: hidden;
    animation: sipQvPop .18s ease-out
  }

  @keyframes sipQvPop {
    from {
      transform: translateY(12px) scale(.98);
      opacity: .7
    }

    to {
      transform: none;
      opacity: 1
    }
  }

  .sip-qv-close {
    position: absolute;
    right: 14px;
    top: 14px;
    width: 38px;
    height: 38px;
    border: 0;
    border-radius: 50%;
    background: #f3f4f6;
    color: #111827;
    font-size: 26px;
    line-height: 1;
    z-index: 2;
    cursor: pointer
  }

  .sip-qv-content {
    display: grid;
    grid-template-columns: 330px 1fr;
    gap: 0
  }

  .sip-qv-img {
    background: linear-gradient(135deg, #fff7ed, #fff);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 26px;
    min-height: 360px
  }

  .sip-qv-img img {
    max-width: 100%;
    max-height: 310px;
    object-fit: contain;
    filter: drop-shadow(0 12px 24px rgba(15, 23, 42, .12))
  }

  .sip-qv-info {
    padding: 34px 30px 28px
  }

  .sip-qv-category {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #ef662f;
    margin-bottom: 8px
  }

  .sip-qv-title {
    font-size: 26px;
    line-height: 1.25;
    margin: 0 0 12px;
    color: #111827;
    font-weight: 800
  }

  .sip-qv-rating {
    color: #f59e0b;
    font-weight: 800;
    margin-bottom: 12px
  }

  .sip-qv-price {
    display: flex;
    gap: 12px;
    align-items: center;
    margin: 14px 0
  }

  .sip-qv-price strong {
    font-size: 28px;
    color: #ef662f
  }

  .sip-qv-price del {
    font-size: 15px;
    color: #9ca3af
  }

  .sip-qv-stock {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 800;
    border-radius: 999px;
    padding: 8px 13px;
    background: #ecfdf5;
    color: #047857;
    margin: 8px 0 18px
  }

  .sip-qv-stock.out {
    background: #fef2f2;
    color: #b91c1c
  }

  .sip-qv-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px
  }

  .sip-qv-actions button,
  .sip-qv-actions a {
    height: 44px;
    border-radius: 12px;
    border: 0;
    padding: 0 18px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer
  }

  .sip-qv-cart {
    background: #ef662f;
    color: #fff
  }

  .sip-qv-cart:disabled {
    opacity: .55;
    cursor: not-allowed
  }

  .sip-qv-view {
    background: #111827;
    color: #fff
  }

  .sip-qv-wish,
  .sip-qv-compare {
    background: #f3f4f6;
    color: #111827
  }

  .recently-viewed-wrap {
    max-width: 1280px;
    margin: 34px auto;
    padding: 0 20px
  }

  .recently-viewed-head {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px
  }

  .recently-viewed-head h2 {
    font-size: 26px;
    font-weight: 900;
    margin: 0;
    color: #111827
  }

  .rv-kicker {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .14em;
    font-weight: 900;
    color: #ef662f
  }

  .rv-view-all {
    font-weight: 800;
    color: #ef662f;
    text-decoration: none
  }

  .recently-viewed-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px
  }

  .rv-card {
    position: relative;
    background: #fff;
    border: 1px solid #eef0f4;
    border-radius: 20px;
    padding: 14px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
    transition: .2s ease
  }

  .rv-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 42px rgba(15, 23, 42, .10)
  }

  .rv-quick {
    position: absolute;
    right: 14px;
    top: 14px;
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 50%;
    background: #fff;
    color: #ef662f;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .13);
    z-index: 2;
    cursor: pointer
  }

  .rv-img-box {
    height: 145px;
    background: #fff7ed;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    overflow: hidden
  }

  .rv-img-box img {
    max-width: 92%;
    max-height: 130px;
    object-fit: contain
  }

  .rv-title {
    font-weight: 800;
    color: #111827;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 42px
  }

  .rv-price-row {
    display: flex;
    gap: 8px;
    align-items: center;
    margin: 10px 0;
    color: #ef662f
  }

  .rv-price-row del {
    color: #9ca3af;
    font-size: 13px
  }

  .rv-actions .cart-btn {
    width: 100%;
    height: 40px;
    border: 0;
    border-radius: 12px;
    background: #111827;
    color: #fff;
    font-weight: 800;
    cursor: pointer
  }

  .rv-actions .cart-btn:disabled {
    opacity: .55;
    cursor: not-allowed
  }

  .product-grid .showcase-actions,
  .showcase-actions {
    z-index: 30 !important;
    pointer-events: auto !important
  }

  .product-grid .showcase-actions .btn-action,
  .showcase-actions .btn-action {
    pointer-events: auto !important;
    cursor: pointer !important
  }

  @media(max-width:768px) {
    .sip-qv-content {
      grid-template-columns: 1fr
    }

    .sip-qv-img {
      min-height: 230px
    }

    .sip-qv-info {
      padding: 22px
    }

    .sip-qv-title {
      font-size: 21px
    }

    .sip-qv-price strong {
      font-size: 24px
    }

    .recently-viewed-head {
      align-items: flex-start;
      flex-direction: column
    }

    .recently-viewed-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px
    }

    .recently-viewed-wrap {
      margin: 24px auto;
      padding: 0 12px
    }

    .rv-img-box {
      height: 125px
    }

    .rv-title {
      font-size: 13px
    }

    .sip-qv-actions button,
    .sip-qv-actions a {
      flex: 1 1 46%;
      padding: 0 10px
    }
  }

  @media(max-width:420px) {
    .recently-viewed-grid {
      grid-template-columns: 1fr 1fr
    }

    .sip-qv-actions button,
    .sip-qv-actions a {
      font-size: 13px
    }
  }
</style>

<div class="sip-qv-modal" id="sipQuickViewModal" aria-hidden="true">
  <div class="sip-qv-box" role="dialog" aria-modal="true" aria-label="Product quick view">
    <button type="button" class="sip-qv-close" data-sip-qv-close>&times;</button>
    <div class="sip-qv-content" id="sipQuickViewContent">
      <div class="sip-qv-img"><img src="assets/images/no-image.png" alt="Product"></div>
      <div class="sip-qv-info">
        <p>Loading product...</p>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    function baseUrl(path) {
      path = String(path || '').replace(/^\/+/, '');
      if (typeof sipUrl === 'function') return sipUrl(path);
      var base = window.SIP_BASE_URL || '';
      return base + '/' + path;
    }

    function escapeHtml(v) {
      return String(v == null ? '' : v).replace(/[&<>"']/g, function(c) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#039;'
        } [c];
      });
    }

    function openModal() {
      var m = document.getElementById('sipQuickViewModal');
      if (!m) return;
      m.classList.add('open');
      m.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      var m = document.getElementById('sipQuickViewModal');
      if (!m) return;
      m.classList.remove('open');
      m.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
    document.addEventListener('click', function(e) {
      var close = e.target.closest('[data-sip-qv-close]');
      if (close) {
        e.preventDefault();
        closeModal();
        return;
      }
      var modal = document.getElementById('sipQuickViewModal');
      if (modal && e.target === modal) {
        closeModal();
        return;
      }
      var btn = e.target.closest('.quick-view-btn');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      var id = btn.getAttribute('data-id');
      var box = document.getElementById('sipQuickViewContent');
      if (!id || !box) return;
      box.innerHTML = '<div class="sip-qv-img"><img src="assets/images/no-image.png" alt="Product"></div><div class="sip-qv-info"><p>Loading product...</p></div>';
      openModal();
      fetch(baseUrl('ajax/quick_view_product.php?id=' + encodeURIComponent(id)), {
          headers: {
            'Accept': 'application/json'
          }
        })
        .then(function(r) {
          return r.text();
        })
        .then(function(text) {
          var data;
          try {
            data = JSON.parse(text);
          } catch (err) {
            throw new Error('Invalid quick view response.');
          }
          if (!data || data.status !== 'success') throw new Error((data && data.message) || 'Product not found.');
          var p = data.product;
          var stockClass = Number(p.stock) > 0 ? '' : ' out';
          var disabled = Number(p.stock) > 0 ? '' : ' disabled';
          box.innerHTML = '<div class="sip-qv-img"><img src="' + escapeHtml(baseUrl(p.image)) + '" alt="' + escapeHtml(p.title) + '"></div>' +
            '<div class="sip-qv-info">' +
            '<div class="sip-qv-category">' + escapeHtml(p.category) + '</div>' +
            '<h3 class="sip-qv-title">' + escapeHtml(p.title) + '</h3>' +
            '<div class="sip-qv-rating">★ ' + escapeHtml(p.rating) + ' ' + (p.bought ? '<span style="color:#6b7280;font-weight:700"> | ' + escapeHtml(p.bought) + '</span>' : '') + '</div>' +
            '<div class="sip-qv-price"><strong>₹' + escapeHtml(p.price) + '</strong>' + (p.old_price ? '<del>₹' + escapeHtml(p.old_price) + '</del>' : '') + '</div>' +
            '<div class="sip-qv-stock' + stockClass + '">' + escapeHtml(p.stock_text) + '</div>' +
            '<div class="sip-qv-actions">' +
            '<button type="button" class="sip-qv-cart cart-btn" data-id="' + escapeHtml(p.id) + '"' + disabled + '><ion-icon name="bag-add-outline"></ion-icon> Add to Cart</button>' +
            '<button type="button" class="sip-qv-wish wishlist-btn" data-id="' + escapeHtml(p.id) + '"><ion-icon name="heart-outline"></ion-icon> Wishlist</button>' +
            '<button type="button" class="sip-qv-compare compare-btn" data-id="' + escapeHtml(p.id) + '"><ion-icon name="repeat-outline"></ion-icon> Compare</button>' +
            '<a class="sip-qv-view" href="' + escapeHtml(baseUrl(p.url)) + '"><ion-icon name="open-outline"></ion-icon> Full Details</a>' +
            '</div>' +
            '</div>';
        })
        .catch(function(err) {
          box.innerHTML = '<div class="sip-qv-img"><img src="assets/images/no-image.png" alt="Product"></div><div class="sip-qv-info"><h3 class="sip-qv-title">Unable to load product</h3><p>' + escapeHtml(err.message || 'Please try again.') + '</p></div>';
        });
    }, true);
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closeModal();
    });
  })();
</script>

<script>
  (function() {
    if (window.__sipProductActionsBound) return;
    window.__sipProductActionsBound = true;

    function postAction(url, data, done) {
      if (!window.jQuery) return;
      var payload = (typeof data === 'object') ? data : {
        product_id: data
      };
      $.post((typeof sipUrl === 'function' ? sipUrl(url) : url), payload, function(res) {
        done($.trim(res || ''));
      }).fail(function() {
        if (typeof ecomToast === 'function') ecomToast('Action failed. Please try again.');
      });
    }
    $(document).on('click', '.wishlist-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var pid = $(this).data('id');
      if (!pid) return;
      postAction('ajax/add_to_wishlist.php', pid, function(res) {
        if (res === 'login_required') {
          if (typeof openAuthModal === 'function') openAuthModal();
          return;
        }
        if (typeof updateHeaderCounts === 'function') updateHeaderCounts();
        if (typeof ecomToast === 'function') ecomToast('❤️ Added to Wishlist');
      });
    });
    $(document).on('click', '.cart-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var btn = $(this),
        pid = btn.data('id'),
        itype = btn.data('type') || 'product';
      if (!pid || btn.prop('disabled')) return;
      postAction('ajax/add_to_cart.php', {
        product_id: pid,
        item_type: itype
      }, function(res) {
        res = res.trim();
        if (res === 'login_required') {
          if (typeof openAuthModal === 'function') openAuthModal();
          return;
        }
        if (res === 'out_of_stock') {
          if (typeof ecomToast === 'function') ecomToast('❌ Product out of stock');
          return;
        }
        if (res.startsWith('success')) {
          var parts = res.split(':');
          var qtyStr = parts.length > 1 ? ' (' + parts[1] + ')' : '';
          if (typeof updateHeaderCounts === 'function') updateHeaderCounts();
          if (typeof ecomToast === 'function') ecomToast('👜 Added to Cart' + qtyStr);
        }
      });
    });
    $(document).on('click', '.compare-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var pid = $(this).data('id');
      if (!pid) return;
      postAction('ajax/add_to_compare.php', pid, function(res) {
        if (res === 'login_required') {
          if (typeof openAuthModal === 'function') openAuthModal();
          return;
        }
        if (res === 'limit') {
          if (typeof ecomToast === 'function') ecomToast('Compare limit is 4 products');
          return;
        }
        if (typeof updateHeaderCounts === 'function') updateHeaderCounts();
        if (typeof ecomToast === 'function') ecomToast('✅ Added to Compare');
      });
    });
  })();
</script>

</body>

</html>
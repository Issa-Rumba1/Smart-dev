<?php
/**
 * SMART DEV - Public Site Footer
 */
$profile = getProfile();
?>
</main>

<!-- ======================== FOOTER ======================== -->
<footer class="site-footer">
    <div class="footer-container">
        <!-- Brand Column -->
        <div class="footer-brand">
            <div class="footer-logo">
                <div class="logo-icon small">
                    <span class="logo-s">S</span><span class="logo-d">D</span>
                </div>
                <span class="brand-name">SMART DEV</span>
            </div>
            <p class="footer-tagline"><?= e($profile['tagline'] ?? 'Innovation. Technology. Digital Solutions.') ?></p>
            <p class="footer-desc">A professional technology and digital solutions brand by <strong><?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?></strong>.</p>
        </div>

        <!-- Quick Links -->
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>/about.php">About Me</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">Services</a></li>
                <li><a href="<?= BASE_URL ?>/portfolio.php">Portfolio</a></li>
                <li><a href="<?= BASE_URL ?>/contact.php">Contact</a></li>
                <?php if (!empty($profile['cv_file'])): ?>
                <li><a href="<?= BASE_URL ?>/download_cv.php"><i class="fas fa-download"></i> Download CV</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Services -->
        <div class="footer-links">
            <h4>Services</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/services.php">Computer Maintenance</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">Networking</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">Web Development</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">Database Management</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">Graphic Design</a></li>
                <li><a href="<?= BASE_URL ?>/services.php">IT Support</a></li>
            </ul>
        </div>

        <!-- Contact & Social -->
        <div class="footer-contact">
            <h4>Get In Touch</h4>
            <?php if (!empty($profile['email'])): ?>
            <p><i class="fas fa-envelope"></i> <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a></p>
            <?php endif; ?>
            <?php if (!empty($profile['show_residence']) && !empty($profile['current_residence'])): ?>
            <p><i class="fas fa-map-marker-alt"></i> <?= e($profile['current_residence']) ?></p>
            <?php endif; ?>

            <!-- Social Media -->
            <div class="footer-social">
                <?php if (!empty($profile['linkedin_url'])): ?>
                <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                <?php endif; ?>
                <?php if (!empty($profile['github_url'])): ?>
                <a href="<?= e($profile['github_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><i class="fab fa-github"></i></a>
                <?php endif; ?>
                <?php if (!empty($profile['facebook_url'])): ?>
                <a href="<?= e($profile['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <?php endif; ?>
                <?php if (!empty($profile['twitter_url'])): ?>
                <a href="<?= e($profile['twitter_url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                <?php endif; ?>
                <?php if (!empty($profile['whatsapp_number'])): ?>
                <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $profile['whatsapp_number'])) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <strong>SMART DEV</strong> &mdash; <?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?>. All rights reserved.</p>
        <p class="footer-stack">Built with <i class="fas fa-heart" style="color:#F47B20"></i> using PHP, MySQL &amp; HTML5</p>
    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top" title="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Main JS -->
<script src="<?= BASE_URL ?>/assets/js/script.js"></script>
</body>
</html>

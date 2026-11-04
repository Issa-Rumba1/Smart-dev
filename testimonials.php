<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Testimonials | SMART DEV – Client Reviews';
$metaDescription = 'Client testimonials and reviews for SMART DEV IT services by Issa Nyamawi Rumba.';

$db = getDB();
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY sort_order ASC, created_at DESC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Testimonials</span>
        </nav>
        <h1>Client <span style="color: var(--orange)">Testimonials</span></h1>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <span class="badge">What Clients Say</span>
            <h2>Real Feedback, Real <span>Results</span></h2>
            <p>Hear from clients who have experienced SMART DEV's professional technology services first-hand.</p>
            <div class="divider"></div>
        </div>

        <?php if ($testimonials): ?>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <span class="testimonial-quote">"</span>
                <div class="testimonial-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star" <?= $i > ($t['rating'] ?? 5) ? 'style="color:#ddd"' : '' ?>></i>
                    <?php endfor; ?>
                </div>
                <p class="testimonial-message"><?= e($t['message']) ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">
                        <?php if (!empty($t['client_photo']) && file_exists(__DIR__ . '/uploads/profile/' . $t['client_photo'])): ?>
                        <img src="uploads/profile/<?= e($t['client_photo']) ?>" alt="<?= e($t['client_name']) ?>">
                        <?php else: ?>
                        <?= strtoupper(substr($t['client_name'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="testimonial-author-info">
                        <h4><?= e($t['client_name']) ?></h4>
                        <?php if (!empty($t['client_title'])): ?>
                        <span><?= e($t['client_title']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>Testimonials coming soon</h3>
            <p>Client reviews will appear here after project completion. Be the first to leave feedback!</p>
            <a href="contact.php" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-envelope"></i> Get In Touch
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section style="padding: 4rem 0; text-align: center;">
    <div class="container">
        <h3>Ready to Experience SMART DEV?</h3>
        <p style="max-width: 500px; margin: 0.75rem auto 1.5rem;">Contact me today to discuss your IT needs and join a growing list of satisfied clients.</p>
        <a href="contact.php" class="btn btn-primary btn-lg">
            <i class="fas fa-paper-plane"></i> Contact Me
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

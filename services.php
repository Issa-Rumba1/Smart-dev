<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Services | SMART DEV – IT Support, Web Development & More';
$metaDescription = 'SMART DEV professional services: computer maintenance, networking, database management, website development, graphic design, IT support and computer training.';

$db = getDB();
$services = $db->query('SELECT * FROM services WHERE is_visible = 1 ORDER BY sort_order ASC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Services</span>
        </nav>
        <h1>Professional <span style="color: var(--orange)">Services</span></h1>
    </div>
</section>

<!-- Services Grid -->
<section>
    <div class="container">
        <div class="section-header">
            <span class="badge">What I Offer</span>
            <h2>Technology Services by <span>SMART DEV</span></h2>
            <p>Comprehensive technology and digital solutions tailored to the needs of individuals, small businesses and organisations.</p>
            <div class="divider"></div>
        </div>

        <?php if ($services): ?>
        <div class="services-grid">
            <?php foreach ($services as $svc): ?>
            <div class="service-card" id="service-<?= (int)$svc['id'] ?>">
                <div class="service-icon">
                    <i class="<?= e($svc['icon_class'] ?? 'fas fa-cog') ?>"></i>
                </div>
                <h3><?= e($svc['title']) ?></h3>
                <p><?= e($svc['short_description']) ?></p>
                <?php if (!empty($svc['full_description'])): ?>
                <p style="font-size: 0.875rem; color: var(--grey-mid);"><?= e($svc['full_description']) ?></p>
                <?php endif; ?>
                <a href="contact.php?subject=<?= urlencode('Enquiry: ' . $svc['title']) ?>" class="btn btn-outline btn-sm">
                    <i class="fas fa-envelope"></i> Enquire Now
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-concierge-bell"></i>
            <h3>Services coming soon</h3>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Why Choose Me -->
<section style="background: #fff; padding: 5rem 0;">
    <div class="container">
        <div class="section-header">
            <span class="badge">Why SMART DEV?</span>
            <h2>The <span>SMART DEV</span> Advantage</h2>
            <div class="divider"></div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <?php
            $advantages = [
                ['fas fa-check-circle', 'Reliable & Professional', 'Every service is delivered with attention to detail and a commitment to quality.'],
                ['fas fa-lock', 'Security-First Approach', 'All digital solutions include cybersecurity best practices by default.'],
                ['fas fa-clock', 'Timely Delivery', 'Projects are completed within agreed timeframes without compromising quality.'],
                ['fas fa-headset', 'Ongoing Support', 'I provide continued support and maintenance after project completion.'],
                ['fas fa-graduation-cap', 'Qualified Professional', 'Diploma in Computer Science with hands-on technical expertise.'],
                ['fas fa-handshake', 'Client-Centred', 'Solutions are customised to your specific needs and budget.'],
            ];
            foreach ($advantages as $a): ?>
            <div style="display: flex; gap: 1rem; align-items: flex-start; padding: 1.5rem; background: var(--bg-soft); border-radius: var(--radius); box-shadow: var(--shadow);">
                <div style="width: 44px; height: 44px; background: rgba(244,123,32,0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--orange); font-size: 1.2rem; flex-shrink: 0;">
                    <i class="<?= $a[0] ?>"></i>
                </div>
                <div>
                    <h4 style="margin-bottom: 0.25rem; font-size: 1rem;"><?= $a[1] ?></h4>
                    <p style="font-size: 0.875rem; margin: 0;"><?= $a[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background: linear-gradient(135deg, var(--charcoal), #111); padding: 4rem 0; text-align: center; color: #fff;">
    <div class="container">
        <h2 style="color: #fff; margin-bottom: 1rem;">Need a Custom Solution?</h2>
        <p style="color: rgba(255,255,255,0.6); max-width: 500px; margin: 0 auto 2rem;">
            Contact me to discuss your specific requirements and I will provide a tailored solution that fits your budget and timeline.
        </p>
        <a href="contact.php" class="btn btn-primary btn-lg">
            <i class="fas fa-paper-plane"></i> Start a Conversation
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

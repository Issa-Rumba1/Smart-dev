<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'SMART DEV | Issa Nyamawi Rumba – IT Support, Web Development & Digital Solutions';
$metaDescription = 'SMART DEV – Innovation. Technology. Digital Solutions. Professional IT services by Issa Nyamawi Rumba: computer maintenance, networking, web development, database management and graphic design.';

$profile  = getProfile();
$db       = getDB();

// Featured services (max 6)
$services = $db->query('SELECT * FROM services WHERE is_visible = 1 ORDER BY sort_order ASC LIMIT 6')->fetchAll();

// Featured projects (max 3)
$projects = $db->query('SELECT * FROM projects WHERE is_visible = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT 3')->fetchAll();

// Testimonials (max 3)
$testimonials = $db->query('SELECT * FROM testimonials WHERE is_visible = 1 ORDER BY sort_order ASC LIMIT 3')->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- ======================================================
     HERO SECTION
====================================================== -->
<section class="hero" id="home">
    <div class="hero-bg-code" aria-hidden="true">
&lt;?php class SmartDev {
  private $name = 'Issa Nyamawi Rumba';
  public function innovate() { return 'Technology Solutions'; }
  public function build() { return 'websites, networks, databases'; }
}
SELECT * FROM services WHERE quality = 'premium';
function connectPeople() { return new DigitalBridge(); }
&lt;html&gt;&lt;head&gt;&lt;title&gt;SMART DEV&lt;/title&gt;&lt;/head&gt;
.brand { color: #F47B20; innovation: true; }
CREATE TABLE solutions (id INT, service VARCHAR(100));
npm install smart-dev --save-future
git commit -m "Innovation. Technology. Digital Solutions."
    </div>

    <div class="hero-container">
        <!-- Text Content -->
        <div class="hero-text">
            <div class="hero-badge">
                <span class="dot"></span>
                Available for Projects &amp; Collaborations
            </div>

            <h1 class="hero-title">
                <span class="orange">SMART DEV</span><br>
                Digital Solutions
            </h1>
            <p class="hero-name">Founded by <strong><?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?></strong></p>

            <p class="hero-desc">
                <?= e($profile['short_bio'] ?? 'Welcome to SMART DEV – a technology and digital solutions brand specializing in computer maintenance, networking, database management, website development, graphic design and general IT support.') ?>
            </p>

            <div class="hero-roles">
                <span class="hero-role-tag"><i class="fas fa-laptop-code"></i> Web Developer</span>
                <span class="hero-role-tag"><i class="fas fa-database"></i> Database Admin</span>
                <span class="hero-role-tag"><i class="fas fa-network-wired"></i> Network Tech</span>
                <span class="hero-role-tag"><i class="fas fa-paint-brush"></i> Graphic Designer</span>
                <span class="hero-role-tag"><i class="fas fa-tools"></i> IT Support</span>
            </div>

            <div class="hero-cta">
                <a href="services.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-concierge-bell"></i> Explore Services
                </a>
                <a href="portfolio.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-briefcase"></i> View Portfolio
                </a>
                <a href="contact.php" class="btn btn-dark btn-lg">
                    <i class="fas fa-envelope"></i> Contact Me
                </a>
                <?php if (!empty($profile['cv_file'])): ?>
                <a href="download_cv.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-download"></i> Download CV
                </a>
                <?php endif; ?>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="6" data-suffix="+">0+</span>
                    <span class="hero-stat-label">Service Areas</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="32" data-suffix="+">0+</span>
                    <span class="hero-stat-label">Technical Skills</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number" data-count="3">0</span>
                    <span class="hero-stat-label">Qualifications</span>
                </div>
            </div>
        </div>

        <!-- Hero Visual -->
        <div class="hero-visual">
            <div class="hero-photo-frame">
                <div class="hero-photo-circle">
                    <div class="hero-photo-inner">
                        <?php if (!empty($profile['profile_photo']) && file_exists(__DIR__ . '/uploads/profile/' . $profile['profile_photo'])): ?>
                            <img src="uploads/profile/<?= e($profile['profile_photo']) ?>" alt="<?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?>">
                        <?php else: ?>
                            <div class="hero-photo-placeholder">
                                <i class="fas fa-user-circle"></i>
                                <span>Photo Coming Soon</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Floating badges -->
                <div class="hero-float-badge"><i class="fas fa-code"></i> PHP Developer</div>
                <div class="hero-float-badge"><i class="fas fa-shield-alt"></i> IT Security</div>
                <div class="hero-float-badge"><i class="fas fa-database"></i> MySQL Expert</div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================
     ABOUT PREVIEW
====================================================== -->
<section style="background: #fff; padding: 4rem 0;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <div class="section-header" style="text-align: left; margin-bottom: 1.5rem;">
                    <span class="badge">About Me</span>
                    <h2>Who is <span>Issa Nyamawi Rumba?</span></h2>
                    <div class="divider" style="margin-left: 0;"></div>
                </div>
                <p style="font-size: 1rem; line-height: 1.9;">
                    I am a dedicated Computer Science professional and the founder of SMART DEV. With a Diploma in Computer Science from the National Youth Service Camp Engineering Institute, I bring expertise across hardware maintenance, networking, web development, database administration and graphic design.
                </p>
                <p style="font-size: 1rem; line-height: 1.9;">
                    My mission is simple: deliver practical, reliable and innovative technology solutions that genuinely make a difference for individuals and businesses.
                </p>
                <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem;">
                    <a href="about.php" class="btn btn-primary">
                        <i class="fas fa-user"></i> About Me
                    </a>
                    <a href="education.php" class="btn btn-outline">
                        <i class="fas fa-graduation-cap"></i> My Education
                    </a>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <?php
                $highlights = [
                    ['fas fa-graduation-cap', 'Diploma in CS', 'NYS Camp Engineering Institute'],
                    ['fas fa-laptop-code', 'Web Developer', 'Full-stack PHP & MySQL Development'],
                    ['fas fa-network-wired', 'Networking', 'LAN/WAN Installation & Config'],
                    ['fas fa-paint-brush', 'Graphic Design', 'Logo & Brand Identity Design'],
                ];
                foreach ($highlights as $h): ?>
                <div class="about-highlight-card">
                    <i class="<?= $h[0] ?>"></i>
                    <h4><?= $h[1] ?></h4>
                    <p><?= $h[2] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================
     SERVICES PREVIEW
====================================================== -->
<?php if ($services): ?>
<section style="padding: 5rem 0;">
    <div class="container">
        <div class="section-header">
            <span class="badge">What I Do</span>
            <h2>Professional <span>Services</span></h2>
            <p>From computer maintenance to website development, I provide end-to-end technology solutions tailored to your needs.</p>
            <div class="divider"></div>
        </div>
        <div class="services-grid">
            <?php foreach ($services as $svc): ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="<?= e($svc['icon_class'] ?? 'fas fa-cog') ?>"></i>
                </div>
                <h3><?= e($svc['title']) ?></h3>
                <p><?= e($svc['short_description']) ?></p>
                <a href="services.php" class="btn btn-outline btn-sm">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="services.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th-list"></i> View All Services
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======================================================
     FEATURED PROJECTS
====================================================== -->
<?php if ($projects): ?>
<section style="background: #fff; padding: 5rem 0;">
    <div class="container">
        <div class="section-header">
            <span class="badge">Portfolio</span>
            <h2>Featured <span>Projects</span></h2>
            <p>A selection of recent work showcasing my technical capabilities and creative problem-solving.</p>
            <div class="divider"></div>
        </div>
        <div class="projects-grid">
            <?php foreach ($projects as $proj): ?>
            <div class="project-card" data-tech="<?= e($proj['technologies'] ?? '') ?>">
                <div class="project-thumbnail">
                    <?php if (!empty($proj['thumbnail']) && file_exists(__DIR__ . '/uploads/projects/' . $proj['thumbnail'])): ?>
                        <img src="uploads/projects/<?= e($proj['thumbnail']) ?>" alt="<?= e($proj['title']) ?>">
                    <?php else: ?>
                        <div class="project-thumbnail-placeholder">
                            <i class="fas fa-code"></i>
                            <span><?= e($proj['title']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($proj['is_featured']): ?>
                    <span class="project-featured-badge">Featured</span>
                    <?php endif; ?>
                </div>
                <div class="project-body">
                    <h3><?= e($proj['title']) ?></h3>
                    <p><?= e(truncate($proj['description'] ?? '', 120)) ?></p>
                    <?php if (!empty($proj['technologies'])): ?>
                    <div class="project-tech">
                        <?php foreach (explode(',', $proj['technologies']) as $tech): ?>
                        <span class="tech-tag"><?= e(trim($tech)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="project-links">
                        <?php if (!empty($proj['github_url'])): ?>
                        <a href="<?= e($proj['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-sm">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($proj['live_url'])): ?>
                        <a href="<?= e($proj['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                            <i class="fas fa-external-link-alt"></i> Live Demo
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="portfolio.php" class="btn btn-primary btn-lg">
                <i class="fas fa-briefcase"></i> View Full Portfolio
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======================================================
     TESTIMONIALS PREVIEW
====================================================== -->
<?php if ($testimonials): ?>
<section style="padding: 5rem 0;">
    <div class="container">
        <div class="section-header">
            <span class="badge">Testimonials</span>
            <h2>What Clients <span>Say</span></h2>
            <div class="divider"></div>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <span class="testimonial-quote">"</span>
                <div class="testimonial-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?= $i > ($t['rating'] ?? 5) ? ' text-muted' : '' ?>" <?= $i > ($t['rating'] ?? 5) ? 'style="color:#ddd"' : '' ?>></i>
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
        <div style="text-align: center; margin-top: 2rem;">
            <a href="testimonials.php" class="btn btn-outline">View All Testimonials</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======================================================
     CTA BANNER
====================================================== -->
<section style="background: linear-gradient(135deg, var(--charcoal), #111); padding: 5rem 0; text-align: center; color: #fff;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2.2rem; margin-bottom: 1rem;">
            Ready to Work Together?
        </h2>
        <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto 2rem; font-size: 1.05rem;">
            Whether you need IT support, a new website, or database management, I'm here to help you achieve your technology goals.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="contact.php" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane"></i> Get In Touch
            </a>
            <a href="services.php" class="btn btn-outline btn-lg" style="border-color: rgba(255,255,255,0.4); color: rgba(255,255,255,0.8);">
                <i class="fas fa-list"></i> View Services
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

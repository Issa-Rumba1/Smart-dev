<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'About Me | SMART DEV – Issa Nyamawi Rumba';
$metaDescription = 'Learn about Issa Nyamawi Rumba, founder of SMART DEV – a Computer Science professional specializing in IT support, web development, networking and database management.';
$profile = getProfile();
include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>About Me</span>
        </nav>
        <h1>About <span style="color: var(--orange)">Me</span></h1>
    </div>
</section>

<!-- About Content -->
<section>
    <div class="container">
        <div class="about-grid">
            <!-- Profile Card -->
            <div>
                <div class="about-photo-card">
                    <div class="about-photo-wrapper">
                        <?php if (!empty($profile['profile_photo']) && file_exists(__DIR__ . '/uploads/profile/' . $profile['profile_photo'])): ?>
                        <img src="uploads/profile/<?= e($profile['profile_photo']) ?>" alt="<?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?>">
                        <?php else: ?>
                        <div class="about-photo-placeholder">
                            <i class="fas fa-user-circle"></i>
                            <span>Issa Nyamawi Rumba</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="about-photo-info">
                        <h3><?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?></h3>
                        <p class="role">Founder, SMART DEV</p>
                        <div class="about-detail-list">
                            <div class="about-detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Born in Kwale, Kinango, Kenya</span>
                            </div>
                            <div class="about-detail-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span>Age 26</span>
                            </div>
                            <div class="about-detail-item">
                                <i class="fas fa-graduation-cap"></i>
                                <span>Diploma in Computer Science</span>
                            </div>
                            <div class="about-detail-item">
                                <i class="fas fa-briefcase"></i>
                                <span>IT Support Specialist</span>
                            </div>
                            <?php if (!empty($profile['email'])): ?>
                            <div class="about-detail-item">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Location (privacy-controlled) -->
                        <div class="location-banner" style="margin-top: 1rem; padding: 1rem;">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="location-info">
                                <h4>Current Location</h4>
                                <p id="locationText">
                                    <?php if (!empty($profile['show_residence']) && !empty($profile['current_residence'])): ?>
                                    <?= e($profile['current_residence']) ?>
                                    <?php else: ?>
                                    <span style="color: #aaa; font-style: italic;">Not publicly displayed</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <!-- CV Download -->
                        <?php if (!empty($profile['cv_file'])): ?>
                        <a href="download_cv.php" class="btn btn-primary w-100" style="margin-top: 1rem; justify-content: center;">
                            <i class="fas fa-download"></i> Download My CV
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Social Links -->
                <?php if (!empty($profile['linkedin_url']) || !empty($profile['github_url'])): ?>
                <div class="about-photo-card" style="margin-top: 1rem; padding: 1.25rem;">
                    <h4 style="margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--grey-mid);">Connect With Me</h4>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <?php if (!empty($profile['linkedin_url'])): ?>
                        <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($profile['github_url'])): ?>
                        <a href="<?= e($profile['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-dark btn-sm">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Bio Content -->
            <div class="about-content">
                <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
                    <span class="badge">Professional Profile</span>
                    <h2>The Story Behind <span>SMART DEV</span></h2>
                    <div class="divider" style="margin-left: 0;"></div>
                </div>

                <div class="about-bio">
                    <?php
                    $bio = $profile['full_bio'] ?? "I am Issa Nyamawi Rumba, the founder of SMART DEV — a technology and digital solutions brand dedicated to providing practical, reliable and innovative IT solutions. With a Diploma in Computer Science from the National Youth Service Camp Engineering Institute, I have built expertise across computer hardware maintenance, networking, database management, web development, graphic design and general IT support.\n\nMy goal is to leverage technology to solve real-world problems, empower businesses and bridge the digital divide. I am passionate about continuous learning and staying updated with the latest developments in the technology industry. SMART DEV is my commitment to delivering quality technology services that make a genuine difference.";
                    foreach (explode("\n", $bio) as $para):
                        if (trim($para)):
                    ?>
                    <p><?= e(trim($para)) ?></p>
                    <?php endif; endforeach; ?>
                </div>

                <!-- Highlights Grid -->
                <div class="about-highlights">
                    <div class="about-highlight-card">
                        <i class="fas fa-laptop-code"></i>
                        <h4>Web Development</h4>
                        <p>Full-stack PHP & MySQL website development with responsive, accessible design.</p>
                    </div>
                    <div class="about-highlight-card">
                        <i class="fas fa-network-wired"></i>
                        <h4>Networking</h4>
                        <p>LAN/WAN installation, router configuration and network troubleshooting.</p>
                    </div>
                    <div class="about-highlight-card">
                        <i class="fas fa-database"></i>
                        <h4>Database Management</h4>
                        <p>MySQL & MariaDB database design, administration and optimization.</p>
                    </div>
                    <div class="about-highlight-card">
                        <i class="fas fa-tools"></i>
                        <h4>IT Support</h4>
                        <p>Hardware maintenance, software troubleshooting and comprehensive IT consultancy.</p>
                    </div>
                </div>

                <!-- Geo location detector -->
                <div class="location-banner" style="margin-top: 2rem;">
                    <i class="fas fa-crosshairs"></i>
                    <div class="location-info" style="flex:1;">
                        <h4>Detect My Approximate Location</h4>
                        <p>Requires your permission. No precise GPS coordinates are stored or published.</p>
                    </div>
                    <button class="btn btn-outline btn-sm" id="detectLocationBtn">
                        <i class="fas fa-crosshairs"></i> Detect
                    </button>
                </div>

                <!-- CTA -->
                <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="skills.php" class="btn btn-primary">
                        <i class="fas fa-star"></i> View My Skills
                    </a>
                    <a href="contact.php" class="btn btn-outline">
                        <i class="fas fa-envelope"></i> Get In Touch
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

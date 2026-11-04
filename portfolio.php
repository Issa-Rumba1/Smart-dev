<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Portfolio | SMART DEV – Projects by Issa Nyamawi Rumba';
$metaDescription = 'Portfolio of projects by Issa Nyamawi Rumba at SMART DEV: websites, database solutions, networking projects and more.';

$db = getDB();
$projects = $db->query('SELECT * FROM projects WHERE is_visible = 1 ORDER BY is_featured DESC, sort_order ASC, created_at DESC')->fetchAll();

// Build list of unique tech tags for filter
$allTechs = [];
foreach ($projects as $p) {
    if (!empty($p['technologies'])) {
        foreach (array_map('trim', explode(',', $p['technologies'])) as $t) {
            if ($t) $allTechs[$t] = true;
        }
    }
}
ksort($allTechs);

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Portfolio</span>
        </nav>
        <h1>My <span style="color: var(--orange)">Portfolio</span></h1>
    </div>
</section>

<!-- Portfolio -->
<section>
    <div class="container">
        <div class="section-header">
            <span class="badge">Work Showcase</span>
            <h2>Projects &amp; <span>Work</span></h2>
            <p>A collection of projects demonstrating my technical skills across web development, database management, and digital solutions.</p>
            <div class="divider"></div>
        </div>

        <!-- Filter buttons -->
        <?php if ($allTechs): ?>
        <div class="portfolio-filter">
            <button class="filter-btn active" data-filter="all">All Projects</button>
            <?php foreach (array_keys($allTechs) as $tech): ?>
            <button class="filter-btn" data-filter="<?= e($tech) ?>"><?= e($tech) ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Projects Grid -->
        <?php if ($projects): ?>
        <div class="projects-grid">
            <?php foreach ($projects as $proj): ?>
            <div class="project-card" data-tech="<?= e($proj['technologies'] ?? '') ?>">
                <div class="project-thumbnail">
                    <?php if (!empty($proj['thumbnail']) && file_exists(__DIR__ . '/uploads/projects/' . $proj['thumbnail'])): ?>
                        <img src="uploads/projects/<?= e($proj['thumbnail']) ?>" alt="<?= e($proj['title']) ?>">
                    <?php else: ?>
                        <div class="project-thumbnail-placeholder">
                            <i class="fas fa-code"></i>
                            <span><?= e(truncate($proj['title'], 30)) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($proj['is_featured']): ?>
                    <span class="project-featured-badge">Featured</span>
                    <?php endif; ?>
                </div>
                <div class="project-body">
                    <h3><?= e($proj['title']) ?></h3>
                    <p><?= e(truncate($proj['description'] ?? '', 130)) ?></p>
                    <?php if (!empty($proj['technologies'])): ?>
                    <div class="project-tech">
                        <?php foreach (array_map('trim', explode(',', $proj['technologies'])) as $tech): ?>
                        <span class="tech-tag"><?= e($tech) ?></span>
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
                            <i class="fas fa-external-link-alt"></i> Live
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>Portfolio projects coming soon</h3>
            <p>Projects will be added here as they are completed. Check back soon!</p>
            <a href="contact.php" class="btn btn-primary" style="margin-top: 1rem;">
                <i class="fas fa-envelope"></i> Discuss a Project
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section style="background: #fff; padding: 4rem 0; text-align: center;">
    <div class="container">
        <h3>Have a Project in Mind?</h3>
        <p style="max-width: 500px; margin: 0.75rem auto 1.5rem;">
            I am available for new projects and collaborations. Let's discuss how SMART DEV can deliver your next digital solution.
        </p>
        <a href="contact.php" class="btn btn-primary btn-lg">
            <i class="fas fa-paper-plane"></i> Let's Talk
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Skills | SMART DEV – Issa Nyamawi Rumba';
$metaDescription = 'Technical skills of Issa Nyamawi Rumba: computer maintenance, networking, database management, web development (HTML5, CSS3, PHP, MySQL), graphic design and IT support.';

$db = getDB();
$allSkills = $db->query('SELECT * FROM skills WHERE is_visible = 1 ORDER BY sort_order ASC')->fetchAll();

// Group by category
$grouped = [];
foreach ($allSkills as $skill) {
    $grouped[$skill['category']][] = $skill;
}

$categoryIcons = [
    'Computer Hardware & Maintenance' => 'fas fa-microchip',
    'Computer Networking'             => 'fas fa-network-wired',
    'Database Management'             => 'fas fa-database',
    'Website Development'             => 'fas fa-code',
    'Graphic Design'                  => 'fas fa-paint-brush',
    'General IT Support'              => 'fas fa-headset',
];

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Skills</span>
        </nav>
        <h1>Technical <span style="color: var(--orange)">Skills</span></h1>
    </div>
</section>

<!-- Skills Section -->
<section class="skills-section">
    <div class="container">
        <div class="section-header">
            <span class="badge">Competencies</span>
            <h2>Professional <span>Skills</span></h2>
            <p>A comprehensive overview of my technical skills and professional competencies across six key areas of information technology.</p>
            <div class="divider"></div>
        </div>

        <?php if ($grouped): ?>
        <div class="skills-grid">
            <?php foreach ($grouped as $category => $skills): ?>
            <div class="skill-category-card">
                <div class="skill-cat-header">
                    <div class="skill-cat-icon">
                        <i class="<?= e($categoryIcons[$category] ?? 'fas fa-star') ?>"></i>
                    </div>
                    <h3><?= e($category) ?></h3>
                </div>
                <div class="skill-items">
                    <?php foreach ($skills as $skill): ?>
                    <div class="skill-item">
                        <i class="<?= e($skill['icon_class'] ?? 'fas fa-check') ?>"></i>
                        <span><?= e($skill['skill_name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-star"></i>
            <h3>Skills coming soon</h3>
        </div>
        <?php endif; ?>

        <!-- Skills summary counts -->
        <div style="margin-top: 4rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; text-align: center;">
            <?php
            $stats = [
                ['fas fa-layer-group', count($grouped), 'Skill Categories'],
                ['fas fa-star',         count($allSkills), 'Total Skills'],
                ['fas fa-code',         1, 'Stacks Covered'],
                ['fas fa-graduation-cap', 1, 'Diploma Qualification'],
            ];
            foreach ($stats as $s):
            ?>
            <div style="background: #fff; border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow);">
                <i class="<?= $s[0] ?>" style="font-size: 2rem; color: var(--orange); display: block; margin-bottom: 0.75rem;"></i>
                <span style="font-size: 2.5rem; font-weight: 800; color: var(--charcoal); display: block;" data-count="<?= $s[1] ?>">0</span>
                <span style="font-size: 0.8rem; color: var(--grey-mid);"><?= $s[2] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Technology Logos Bar -->
<section style="background: #fff; padding: 3rem 0;">
    <div class="container">
        <div class="section-header">
            <span class="badge">Tools & Technologies</span>
            <h2>Technologies I <span>Work With</span></h2>
            <div class="divider"></div>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center;">
            <?php
            $techs = [
                ['fab fa-html5', 'HTML5', '#e34f26'],
                ['fab fa-css3-alt', 'CSS3', '#264de4'],
                ['fab fa-js', 'JavaScript', '#f7df1e'],
                ['fab fa-php', 'PHP 8', '#777bb4'],
                ['fas fa-database', 'MySQL', '#00758f'],
                ['fab fa-linux', 'Linux', '#fcc624'],
                ['fab fa-github', 'Git / GitHub', '#333'],
                ['fas fa-terminal', 'CLI Tools', '#333'],
            ];
            foreach ($techs as $t): ?>
            <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-soft); border-radius: 50px; padding: 0.6rem 1.25rem; font-size: 0.9rem; font-weight: 600; box-shadow: var(--shadow);">
                <i class="<?= $t[0] ?>" style="color: <?= $t[2] ?>; font-size: 1.2rem;"></i>
                <span><?= $t[1] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

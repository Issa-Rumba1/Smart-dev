<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Education | SMART DEV – Issa Nyamawi Rumba';
$metaDescription = 'Educational background of Issa Nyamawi Rumba: Lutsangani Primary School, The Lenana School, and Diploma in Computer Science from NYS Camp Engineering Institute.';

$db = getDB();
$educations = $db->query('SELECT * FROM education WHERE is_visible = 1 ORDER BY sort_order ASC')->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Education</span>
        </nav>
        <h1>My <span style="color: var(--orange)">Education</span></h1>
    </div>
</section>

<!-- Education Timeline -->
<section>
    <div class="container">
        <div class="section-header">
            <span class="badge">Academic Journey</span>
            <h2>Educational <span>Background</span></h2>
            <p>A chronological overview of my academic journey from primary school through to tertiary education.</p>
            <div class="divider"></div>
        </div>

        <?php if ($educations): ?>
        <div class="timeline">
            <?php foreach ($educations as $i => $edu): ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <span class="timeline-level"><?= e($edu['level']) ?> Education</span>
                    <h3><?= e($edu['institution']) ?></h3>
                    <?php if (!empty($edu['qualification'])): ?>
                    <p class="qualification"><i class="fas fa-certificate"></i> <?= e($edu['qualification']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($edu['field_of_study'])): ?>
                    <p style="color: var(--grey-mid); font-size: 0.875rem; margin-bottom: 0.25rem;">
                        <i class="fas fa-book"></i> <?= e($edu['field_of_study']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($edu['start_year']) || !empty($edu['end_year'])): ?>
                    <p class="years">
                        <i class="fas fa-calendar-alt"></i>
                        <?= !empty($edu['start_year']) ? e($edu['start_year']) : '' ?>
                        <?= (!empty($edu['start_year']) && !empty($edu['end_year'])) ? ' – ' : '' ?>
                        <?= !empty($edu['end_year']) ? e($edu['end_year']) : '' ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($edu['description'])): ?>
                    <p><?= e($edu['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-graduation-cap"></i>
            <h3>Education records coming soon</h3>
            <p>Educational background will be displayed here shortly.</p>
        </div>
        <?php endif; ?>

        <!-- Diploma highlight card -->
        <div style="max-width: 700px; margin: 4rem auto 0; background: linear-gradient(135deg, var(--charcoal), #111); border-radius: var(--radius); padding: 2.5rem; text-align: center; color: #fff; position: relative; overflow: hidden;">
            <div style="position: absolute; inset: 0; background: radial-gradient(ellipse at center, rgba(244,123,32,0.15) 0%, transparent 70%);"></div>
            <div style="position: relative; z-index: 1;">
                <i class="fas fa-award" style="font-size: 3rem; color: var(--orange); display: block; margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.4rem; margin-bottom: 0.5rem;">Diploma in Computer Science</h3>
                <p style="color: rgba(255,255,255,0.6); margin-bottom: 0.25rem;">National Youth Service Camp Engineering Institute</p>
                <p style="color: var(--orange); font-weight: 600; font-size: 0.9rem;">Highest Qualification Attained</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="background: #fff; padding: 3rem 0;">
    <div class="container" style="text-align: center;">
        <h3>Continuing to Learn &amp; Grow</h3>
        <p>Technology evolves rapidly. I am committed to continuous professional development and staying current with emerging technologies.</p>
        <a href="skills.php" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-star"></i> View My Skills
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

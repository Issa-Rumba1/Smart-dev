<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

$db = getDB();

// Stats
$totalMessages  = (int)$db->query('SELECT COUNT(*) FROM contact_requests WHERE is_deleted = 0')->fetchColumn();
$unreadMessages = (int)$db->query('SELECT COUNT(*) FROM contact_requests WHERE is_read = 0 AND is_deleted = 0')->fetchColumn();
$totalProjects  = (int)$db->query('SELECT COUNT(*) FROM projects WHERE is_visible = 1')->fetchColumn();
$totalSkills    = (int)$db->query('SELECT COUNT(*) FROM skills WHERE is_visible = 1')->fetchColumn();
$totalTestimonials = (int)$db->query('SELECT COUNT(*) FROM testimonials WHERE is_visible = 1')->fetchColumn();

// Recent messages (5)
$recentMessages = $db->query('SELECT * FROM contact_requests WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT 5')->fetchAll();

// Recent activity (10)
$recentActivity = $db->query(
    'SELECT al.*, a.username FROM activity_logs al
     LEFT JOIN administrators a ON a.id = al.admin_id
     ORDER BY al.created_at DESC LIMIT 10'
)->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<!-- Stats Grid -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-icon"><i class="fas fa-envelope"></i></div>
        <div class="admin-stat-info">
            <span class="stat-number" data-count="<?= $totalMessages ?>"><?= $totalMessages ?></span>
            <span class="stat-label">Total Messages</span>
        </div>
    </div>
    <div class="admin-stat-card" style="border-left-color: #e53e3e;">
        <div class="admin-stat-icon" style="background: rgba(229,62,62,0.12); color: #e53e3e;"><i class="fas fa-bell"></i></div>
        <div class="admin-stat-info">
            <span class="stat-number" data-count="<?= $unreadMessages ?>"><?= $unreadMessages ?></span>
            <span class="stat-label">Unread Messages</span>
        </div>
    </div>
    <div class="admin-stat-card" style="border-left-color: #38a169;">
        <div class="admin-stat-icon" style="background: rgba(56,161,105,0.12); color: #38a169;"><i class="fas fa-briefcase"></i></div>
        <div class="admin-stat-info">
            <span class="stat-number" data-count="<?= $totalProjects ?>"><?= $totalProjects ?></span>
            <span class="stat-label">Projects</span>
        </div>
    </div>
    <div class="admin-stat-card" style="border-left-color: #805ad5;">
        <div class="admin-stat-icon" style="background: rgba(128,90,213,0.12); color: #805ad5;"><i class="fas fa-star"></i></div>
        <div class="admin-stat-info">
            <span class="stat-number" data-count="<?= $totalSkills ?>"><?= $totalSkills ?></span>
            <span class="stat-label">Skills</span>
        </div>
    </div>
    <div class="admin-stat-card" style="border-left-color: #dd6b20;">
        <div class="admin-stat-icon" style="background: rgba(221,107,32,0.12); color: #dd6b20;"><i class="fas fa-comments"></i></div>
        <div class="admin-stat-info">
            <span class="stat-number" data-count="<?= $totalTestimonials ?>"><?= $totalTestimonials ?></span>
            <span class="stat-label">Testimonials</span>
        </div>
    </div>
</div>

<!-- Two columns: Recent Messages + Activity Log -->
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">

    <!-- Recent Messages -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-envelope" style="color: var(--orange); margin-right: 0.5rem;"></i>Recent Messages</h2>
            <a href="messages.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <?php if ($recentMessages): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $msg): ?>
                    <tr>
                        <td><strong><?= e($msg['full_name']) ?></strong><br><small style="color:#718096;"><?= e($msg['email']) ?></small></td>
                        <td><?= e(truncate($msg['subject'], 40)) ?></td>
                        <td style="white-space: nowrap; font-size: 0.8rem;"><?= formatDate($msg['created_at']) ?></td>
                        <td>
                            <?php if ($msg['is_read']): ?>
                            <span class="badge-read">Read</span>
                            <?php else: ?>
                            <span class="badge-unread">New</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="messages.php?view=<?= (int)$msg['id'] ?>" class="btn-icon btn-icon-view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="admin-card-body">
            <p style="color: #718096; text-align: center; padding: 1rem 0;">No messages yet.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Activity Log -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-history" style="color: var(--orange); margin-right: 0.5rem;"></i>Recent Activity</h2>
        </div>
        <?php if ($recentActivity): ?>
        <div style="padding: 0.5rem 1rem;">
            <?php foreach ($recentActivity as $log): ?>
            <div style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #e2e8f0;">
                <div style="width: 32px; height: 32px; background: rgba(244,123,32,0.12); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--orange); flex-shrink: 0;">
                    <i class="fas fa-check" style="font-size: 0.75rem;"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 0.82rem; color: #2d3748; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= e($log['action']) ?></p>
                    <span style="font-size: 0.72rem; color: #a0aec0;"><?= formatDate($log['created_at']) ?> by <?= e($log['username'] ?? 'System') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="admin-card-body"><p style="color: #718096;">No activity recorded yet.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Links -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-top: 1.5rem;">
    <?php
    $quickLinks = [
        ['admin/messages.php',    'fas fa-envelope',           'Messages',    '#F47B20'],
        ['admin/projects.php',    'fas fa-briefcase',          'Projects',    '#38a169'],
        ['admin/skills.php',      'fas fa-star',               'Skills',      '#805ad5'],
        ['admin/testimonials.php','fas fa-comments',           'Testimonials','#dd6b20'],
        ['admin/profile.php',     'fas fa-user-circle',        'Profile',     '#3182ce'],
        ['admin/settings.php',    'fas fa-cog',                'Settings',    '#718096'],
    ];
    foreach ($quickLinks as $ql): ?>
    <a href="<?= BASE_URL ?>/<?= $ql[0] ?>" style="background: #fff; border-radius: var(--radius); padding: 1.25rem; text-align: center; box-shadow: 0 1px 8px rgba(0,0,0,0.06); text-decoration: none; transition: all 0.2s; display: block; border-top: 3px solid <?= $ql[3] ?>;"
       onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
        <i class="<?= $ql[1] ?>" style="font-size: 1.75rem; color: <?= $ql[3] ?>; display: block; margin-bottom: 0.5rem;"></i>
        <span style="font-size: 0.875rem; font-weight: 600; color: #2d3748;"><?= $ql[2] ?></span>
    </a>
    <?php endforeach; ?>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

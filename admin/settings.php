<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Site Settings';
$activeNav = 'settings';

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $keys = ['site_name', 'site_tagline', 'contact_email', 'max_upload_size_mb',
             'dark_mode_default', 'show_testimonials', 'show_portfolio', 'maintenance_mode'];

    foreach ($keys as $key) {
        $value = sanitize($_POST[$key] ?? '');
        $stmt  = $db->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?');
        $stmt->execute([$key, $value, $value]);
    }

    logActivity('Updated site settings', '', adminId());
    setFlash('success', 'Settings saved successfully.');
    redirect(BASE_URL . '/admin/settings.php');
}

// Load all settings
$settingsStmt = $db->query('SELECT setting_key, setting_value FROM settings');
$settings = [];
foreach ($settingsStmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Settings</span></nav>

<div class="admin-form-card">
    <h3 style="margin-bottom:1.5rem;">Website Settings</h3>

    <form method="POST">
        <?= csrfField() ?>

        <h4 style="margin-bottom:1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">General</h4>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Site Name</label>
                <input type="text" class="form-control" name="site_name" value="<?= e($settings['site_name'] ?? 'SMART DEV') ?>">
            </div>
            <div class="admin-form-group">
                <label>Contact Email</label>
                <input type="email" class="form-control" name="contact_email" value="<?= e($settings['contact_email'] ?? '') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Site Tagline</label>
                <input type="text" class="form-control" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>">
            </div>
        </div>

        <h4 style="margin:1.5rem 0 1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">Display Options</h4>
        <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:1.5rem;">
            <?php
            $toggles = [
                ['show_testimonials', 'Show Testimonials Section'],
                ['show_portfolio',    'Show Portfolio Section'],
                ['dark_mode_default', 'Enable Dark Mode by Default'],
                ['maintenance_mode',  'Maintenance Mode (hides site from public)'],
            ];
            foreach ($toggles as [$key, $label]):
            ?>
            <label style="display:flex;align-items:center;gap:.75rem;cursor:pointer;font-size:.9rem;">
                <input type="checkbox" name="<?= $key ?>" value="1" <?= ($settings[$key] ?? '0') === '1' ? 'checked' : '' ?>>
                <?= $label ?>
            </label>
            <?php endforeach; ?>
        </div>

        <h4 style="margin:1.5rem 0 1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">Upload Limits</h4>
        <div class="admin-form-group" style="max-width:250px;">
            <label>Max Upload Size (MB)</label>
            <input type="number" class="form-control" name="max_upload_size_mb" value="<?= (int)($settings['max_upload_size_mb'] ?? 10) ?>" min="1" max="50">
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </form>
</div>

<!-- System Info -->
<div class="admin-form-card" style="margin-top:1.5rem;">
    <h3 style="margin-bottom:1rem;">System Information</h3>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
        <?php
        $sysInfo = [
            ['PHP Version',   PHP_VERSION],
            ['Server',        $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'],
            ['Database',      'MySQL / MariaDB'],
            ['Date / Time',   date('d M Y H:i:s')],
        ];
        foreach ($sysInfo as [$label, $value]):
        ?>
        <div style="background:#f7fafc;border-radius:var(--radius-sm);padding:1rem;">
            <p style="font-size:.75rem;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:.3rem;"><?= $label ?></p>
            <p style="font-weight:600;font-size:.9rem;margin:0;color:#2d3748;"><?= e($value) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

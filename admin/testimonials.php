<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Testimonials';
$activeNav = 'testimonials';

$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $row = $db->prepare('SELECT client_photo FROM testimonials WHERE id = ?');
        $row->execute([$id]);
        $t = $row->fetch();
        if ($t && !empty($t['client_photo'])) @unlink(dirname(__DIR__) . '/uploads/profile/' . $t['client_photo']);
        $db->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
        logActivity('Deleted testimonial', "ID: $id", adminId());
        setFlash('success', 'Testimonial deleted.');
        redirect(BASE_URL . '/admin/testimonials.php');
    }

    if (in_array($action, ['add', 'edit'], true)) {
        $id          = (int)($_POST['id'] ?? 0);
        $clientName  = sanitize($_POST['client_name']  ?? '');
        $clientTitle = sanitize($_POST['client_title'] ?? '');
        $message     = sanitize($_POST['message']      ?? '');
        $rating      = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $sortOrder   = (int)($_POST['sort_order'] ?? 0);
        $isVisible   = isset($_POST['is_visible']) ? 1 : 0;

        if (!$clientName) $errors['client_name'] = 'Client name is required.';
        if (mb_strlen($message) < 10) $errors['message'] = 'Message is required.';

        $photo = $_POST['existing_photo'] ?? '';
        if (!empty($_FILES['client_photo']['name'])) {
            $file = $_FILES['client_photo'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $errors['client_photo'] = 'Only image files allowed.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors['client_photo'] = 'Image must be under 2 MB.';
            }
            if (empty($errors['client_photo'])) {
                $newName = 'testi_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/profile/' . $newName)) {
                    if ($photo) @unlink(dirname(__DIR__) . '/uploads/profile/' . $photo);
                    $photo = $newName;
                }
            }
        }

        if (empty($errors)) {
            if ($action === 'add') {
                $db->prepare('INSERT INTO testimonials (client_name, client_title, client_photo, message, rating, sort_order, is_visible) VALUES (?,?,?,?,?,?,?)')
                   ->execute([$clientName, $clientTitle, $photo ?: null, $message, $rating, $sortOrder, $isVisible]);
                logActivity('Added testimonial', $clientName, adminId());
                setFlash('success', 'Testimonial added.');
            } else {
                $db->prepare('UPDATE testimonials SET client_name=?, client_title=?, client_photo=?, message=?, rating=?, sort_order=?, is_visible=? WHERE id=?')
                   ->execute([$clientName, $clientTitle, $photo ?: null, $message, $rating, $sortOrder, $isVisible, $id]);
                logActivity('Updated testimonial', $clientName, adminId());
                setFlash('success', 'Testimonial updated.');
            }
            redirect(BASE_URL . '/admin/testimonials.php');
        }
    }
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
$showForm = isset($_GET['add']) || $editItem !== null || !empty($errors);
$items    = $db->query('SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC')->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Testimonials</span></nav>
<div style="display:flex;justify-content:flex-end;margin-bottom:1rem;">
    <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Testimonial</a>
</div>

<?php if ($showForm): ?>
<div class="admin-form-card">
    <h3 style="margin-bottom:1.5rem;"><?= $editItem ? 'Edit' : 'Add' ?> Testimonial</h3>
    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'edit' : 'add' ?>">
        <?php if ($editItem): ?>
        <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
        <input type="hidden" name="existing_photo" value="<?= e($editItem['client_photo'] ?? '') ?>">
        <?php endif; ?>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Client Name *</label>
                <input type="text" class="form-control" name="client_name" value="<?= e($_POST['client_name'] ?? $editItem['client_name'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label>Client Title / Role</label>
                <input type="text" class="form-control" name="client_title" placeholder="e.g. Business Owner" value="<?= e($_POST['client_title'] ?? $editItem['client_title'] ?? '') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Testimonial Message *</label>
                <textarea class="form-control" name="message" rows="4" required><?= e($_POST['message'] ?? $editItem['message'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group">
                <label>Rating (1–5)</label>
                <input type="number" class="form-control" name="rating" min="1" max="5" value="<?= (int)($_POST['rating'] ?? $editItem['rating'] ?? 5) ?>">
            </div>
            <div class="admin-form-group">
                <label>Client Photo <small>(max 2MB)</small></label>
                <input type="file" class="form-control" name="client_photo" accept=".jpg,.jpeg,.png,.gif,.webp">
            </div>
            <div class="admin-form-group">
                <label>Sort Order</label>
                <input type="number" class="form-control" name="sort_order" value="<?= (int)($_POST['sort_order'] ?? $editItem['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="admin-form-group" style="padding-top:1.5rem;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input type="checkbox" name="is_visible" <?= (!empty($_POST) ? (isset($_POST['is_visible']) ? 'checked' : '') : (($editItem['is_visible'] ?? 1) ? 'checked' : '')) ?>>
                    Visible on Site
                </label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:.5rem;">
            <button type="submit" class="btn btn-primary"><?= $editItem ? 'Update' : 'Add' ?></button>
            <a href="testimonials.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-comments" style="color:var(--orange);margin-right:.5rem;"></i>Testimonials (<?= count($items) ?>)</h2>
    </div>
    <?php if ($items): ?>
    <table class="admin-table">
        <thead><tr><th>Photo</th><th>Client</th><th>Rating</th><th>Message</th><th>Visible</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
                <td>
                    <?php if (!empty($it['client_photo']) && file_exists(dirname(__DIR__) . '/uploads/profile/' . $it['client_photo'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/profile/<?= e($it['client_photo']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                    <div style="width:40px;height:40px;background:var(--orange);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">
                        <?= strtoupper(substr($it['client_name'],0,1)) ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td><strong><?= e($it['client_name']) ?></strong><br><small style="color:#718096;"><?= e($it['client_title'] ?? '') ?></small></td>
                <td><?= str_repeat('⭐', (int)$it['rating']) ?></td>
                <td style="font-size:.82rem;color:#4a5568;"><?= e(truncate($it['message'], 80)) ?></td>
                <td><?= $it['is_visible'] ? '✅' : '❌' ?></td>
                <td>
                    <div class="action-btns">
                        <a href="?edit=<?= (int)$it['id'] ?>" class="btn-icon btn-icon-edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                            <button type="submit" class="btn-icon btn-icon-delete" data-confirm="Delete testimonial?"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="admin-card-body" style="text-align:center;color:#718096;">No testimonials yet. <a href="?add=1">Add one.</a></div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

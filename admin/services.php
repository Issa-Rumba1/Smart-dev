<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Services';
$activeNav = 'services';

$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM services WHERE id = ?')->execute([(int)$_POST['id']]);
        logActivity('Deleted service', '', adminId());
        setFlash('success', 'Service deleted.');
        redirect(BASE_URL . '/admin/services.php');
    }

    if (in_array($action, ['add', 'edit'], true)) {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = sanitize($_POST['title']             ?? '');
        $shortDesc   = sanitize($_POST['short_description'] ?? '');
        $fullDesc    = sanitize($_POST['full_description']  ?? '');
        $iconClass   = sanitize($_POST['icon_class']        ?? '');
        $sortOrder   = (int)($_POST['sort_order']           ?? 0);
        $isVisible   = isset($_POST['is_visible']) ? 1 : 0;

        if (!$title) $errors['title'] = 'Title is required.';

        if (empty($errors)) {
            if ($action === 'add') {
                $db->prepare('INSERT INTO services (title, short_description, full_description, icon_class, sort_order, is_visible) VALUES (?,?,?,?,?,?)')
                   ->execute([$title, $shortDesc, $fullDesc, $iconClass, $sortOrder, $isVisible]);
                logActivity('Added service', $title, adminId());
                setFlash('success', 'Service added.');
            } else {
                $db->prepare('UPDATE services SET title=?, short_description=?, full_description=?, icon_class=?, sort_order=?, is_visible=? WHERE id=?')
                   ->execute([$title, $shortDesc, $fullDesc, $iconClass, $sortOrder, $isVisible, $id]);
                logActivity('Updated service', $title, adminId());
                setFlash('success', 'Service updated.');
            }
            redirect(BASE_URL . '/admin/services.php');
        }
    }
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
$showForm = isset($_GET['add']) || $editItem !== null || !empty($errors);
$items    = $db->query('SELECT * FROM services ORDER BY sort_order ASC')->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Services</span></nav>
<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
    <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Service</a>
</div>

<?php if ($showForm): ?>
<div class="admin-form-card">
    <h3 style="margin-bottom:1.5rem;"><?= $editItem ? 'Edit Service' : 'Add Service' ?></h3>
    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'edit' : 'add' ?>">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="admin-form-grid">
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Service Title *</label>
                <input type="text" class="form-control" name="title" value="<?= e($_POST['title'] ?? $editItem['title'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label>Icon Class (Font Awesome)</label>
                <input type="text" class="form-control" name="icon_class" placeholder="fas fa-globe" value="<?= e($_POST['icon_class'] ?? $editItem['icon_class'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Sort Order</label>
                <input type="number" class="form-control" name="sort_order" value="<?= (int)($_POST['sort_order'] ?? $editItem['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Short Description</label>
                <textarea class="form-control" name="short_description" rows="2"><?= e($_POST['short_description'] ?? $editItem['short_description'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Full Description <small>(optional)</small></label>
                <textarea class="form-control" name="full_description" rows="4"><?= e($_POST['full_description'] ?? $editItem['full_description'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input type="checkbox" name="is_visible" <?= (!empty($_POST) ? (isset($_POST['is_visible']) ? 'checked' : '') : (($editItem['is_visible'] ?? 1) ? 'checked' : '')) ?>>
                    Visible on Site
                </label>
            </div>
        </div>
        <div style="display:flex;gap:.75rem;margin-top:.5rem;">
            <button type="submit" class="btn btn-primary"><?= $editItem ? 'Update' : 'Add' ?></button>
            <a href="services.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-concierge-bell" style="color:var(--orange);margin-right:.5rem;"></i>All Services</h2>
    </div>
    <?php if ($items): ?>
    <table class="admin-table">
        <thead><tr><th>Icon</th><th>Title</th><th>Short Description</th><th>Order</th><th>Visible</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
                <td><i class="<?= e($it['icon_class'] ?? 'fas fa-cog') ?>" style="color:var(--orange);font-size:1.2rem;"></i></td>
                <td><strong><?= e($it['title']) ?></strong></td>
                <td style="font-size:.82rem;color:#718096;"><?= e(truncate($it['short_description'] ?? '', 70)) ?></td>
                <td><?= (int)$it['sort_order'] ?></td>
                <td><?= $it['is_visible'] ? '✅' : '❌' ?></td>
                <td>
                    <div class="action-btns">
                        <a href="?edit=<?= (int)$it['id'] ?>" class="btn-icon btn-icon-edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" style="display:inline">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$it['id'] ?>">
                            <button type="submit" class="btn-icon btn-icon-delete" data-confirm="Delete service?"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="admin-card-body" style="text-align:center;color:#718096;">No services. <a href="?add=1">Add one.</a></div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

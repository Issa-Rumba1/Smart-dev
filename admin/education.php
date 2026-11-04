<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Education';
$activeNav = 'education';

$db     = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $db->prepare('DELETE FROM education WHERE id = ?')->execute([(int)$_POST['id']]);
        logActivity('Deleted education record', '', adminId());
        setFlash('success', 'Record deleted.');
        redirect(BASE_URL . '/admin/education.php');
    }

    if (in_array($action, ['add', 'edit'], true)) {
        $id          = (int)($_POST['id'] ?? 0);
        $level       = sanitize($_POST['level']        ?? '');
        $institution = sanitize($_POST['institution']  ?? '');
        $qualification = sanitize($_POST['qualification'] ?? '');
        $field       = sanitize($_POST['field_of_study']  ?? '');
        $startYear   = $_POST['start_year'] ? (int)$_POST['start_year'] : null;
        $endYear     = $_POST['end_year']   ? (int)$_POST['end_year']   : null;
        $description = sanitize($_POST['description']   ?? '');
        $sortOrder   = (int)($_POST['sort_order']       ?? 0);
        $isVisible   = isset($_POST['is_visible']) ? 1 : 0;

        if (!$level)       $errors['level']       = 'Level is required.';
        if (!$institution) $errors['institution'] = 'Institution name is required.';

        if (empty($errors)) {
            if ($action === 'add') {
                $db->prepare('INSERT INTO education (level, institution, qualification, field_of_study, start_year, end_year, description, sort_order, is_visible) VALUES (?,?,?,?,?,?,?,?,?)')
                   ->execute([$level, $institution, $qualification, $field, $startYear, $endYear, $description, $sortOrder, $isVisible]);
                logActivity('Added education', $institution, adminId());
                setFlash('success', 'Education record added.');
            } else {
                $db->prepare('UPDATE education SET level=?, institution=?, qualification=?, field_of_study=?, start_year=?, end_year=?, description=?, sort_order=?, is_visible=? WHERE id=?')
                   ->execute([$level, $institution, $qualification, $field, $startYear, $endYear, $description, $sortOrder, $isVisible, $id]);
                logActivity('Updated education', $institution, adminId());
                setFlash('success', 'Education record updated.');
            }
            redirect(BASE_URL . '/admin/education.php');
        }
    }
}

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM education WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
$showForm = isset($_GET['add']) || $editItem !== null || !empty($errors);
$items    = $db->query('SELECT * FROM education ORDER BY sort_order ASC')->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Education</span></nav>
<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
    <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Record</a>
</div>

<?php if ($showForm): ?>
<div class="admin-form-card">
    <h3 style="margin-bottom:1.5rem;"><?= $editItem ? 'Edit' : 'Add' ?> Education Record</h3>
    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editItem ? 'edit' : 'add' ?>">
        <?php if ($editItem): ?><input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>"><?php endif; ?>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Level *</label>
                <select class="form-control" name="level" required>
                    <?php foreach (['Primary', 'Secondary', 'Tertiary', 'University', 'Certificate', 'Short Course'] as $lvl): ?>
                    <option value="<?= $lvl ?>" <?= ($_POST['level'] ?? $editItem['level'] ?? '') === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-form-group">
                <label>Institution *</label>
                <input type="text" class="form-control" name="institution" value="<?= e($_POST['institution'] ?? $editItem['institution'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label>Qualification / Certification</label>
                <input type="text" class="form-control" name="qualification" value="<?= e($_POST['qualification'] ?? $editItem['qualification'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Field of Study</label>
                <input type="text" class="form-control" name="field_of_study" value="<?= e($_POST['field_of_study'] ?? $editItem['field_of_study'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Start Year</label>
                <input type="number" class="form-control" name="start_year" min="1990" max="2030" value="<?= e($_POST['start_year'] ?? $editItem['start_year'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>End Year</label>
                <input type="number" class="form-control" name="end_year" min="1990" max="2030" value="<?= e($_POST['end_year'] ?? $editItem['end_year'] ?? '') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3"><?= e($_POST['description'] ?? $editItem['description'] ?? '') ?></textarea>
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
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary"><?= $editItem ? 'Update' : 'Add Record' ?></button>
            <a href="education.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-graduation-cap" style="color:var(--orange);margin-right:.5rem;"></i>Education Records</h2>
    </div>
    <?php if ($items): ?>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead><tr><th>Level</th><th>Institution</th><th>Qualification</th><th>Years</th><th>Visible</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><span class="badge-unread"><?= e($item['level']) ?></span></td>
                    <td><strong><?= e($item['institution']) ?></strong></td>
                    <td style="font-size:0.85rem;"><?= e($item['qualification'] ?? '—') ?></td>
                    <td style="font-size:0.82rem;color:#718096;"><?= $item['start_year'] ?? '' ?><?= ($item['start_year'] && $item['end_year']) ? ' – ' : '' ?><?= $item['end_year'] ?? '' ?></td>
                    <td><?= $item['is_visible'] ? '✅' : '❌' ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= (int)$item['id'] ?>" class="btn-icon btn-icon-edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button type="submit" class="btn-icon btn-icon-delete" data-confirm="Delete this record?"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="admin-card-body" style="text-align:center;color:#718096;">No records yet. <a href="?add=1">Add one.</a></div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

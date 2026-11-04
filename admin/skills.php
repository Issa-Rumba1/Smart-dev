<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Skills';
$activeNav = 'skills';

$db     = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $db->prepare('DELETE FROM skills WHERE id = ?')->execute([$id]);
        logActivity('Deleted skill', "ID: $id", adminId());
        setFlash('success', 'Skill deleted.');
        redirect(BASE_URL . '/admin/skills.php');
    }

    if (in_array($action, ['add', 'edit'], true)) {
        $id         = (int)($_POST['id'] ?? 0);
        $category   = sanitize($_POST['category']   ?? '');
        $skillName  = sanitize($_POST['skill_name'] ?? '');
        $iconClass  = sanitize($_POST['icon_class'] ?? '');
        $description= sanitize($_POST['description']?? '');
        $sortOrder  = (int)($_POST['sort_order']   ?? 0);
        $isVisible  = isset($_POST['is_visible']) ? 1 : 0;

        if (!$category) $errors['category'] = 'Category is required.';
        if (!$skillName) $errors['skill_name'] = 'Skill name is required.';

        if (empty($errors)) {
            if ($action === 'add') {
                $db->prepare('INSERT INTO skills (category, skill_name, icon_class, description, sort_order, is_visible) VALUES (?,?,?,?,?,?)')
                   ->execute([$category, $skillName, $iconClass, $description, $sortOrder, $isVisible]);
                logActivity('Added skill', $skillName, adminId());
                setFlash('success', 'Skill added.');
            } else {
                $db->prepare('UPDATE skills SET category=?, skill_name=?, icon_class=?, description=?, sort_order=?, is_visible=? WHERE id=?')
                   ->execute([$category, $skillName, $iconClass, $description, $sortOrder, $isVisible, $id]);
                logActivity('Updated skill', $skillName, adminId());
                setFlash('success', 'Skill updated.');
            }
            redirect(BASE_URL . '/admin/skills.php');
        }
    }
}

$editSkill = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM skills WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editSkill = $stmt->fetch();
}
$showForm = isset($_GET['add']) || $editSkill !== null || !empty($errors);

$allSkills = $db->query('SELECT * FROM skills ORDER BY sort_order ASC, category ASC, skill_name ASC')->fetchAll();
$grouped   = [];
foreach ($allSkills as $s) { $grouped[$s['category']][] = $s; }

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Skills</span></nav>
<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
    <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Skill</a>
</div>

<?php if ($showForm): ?>
<div class="admin-form-card">
    <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><?= $editSkill ? 'Edit Skill' : 'Add Skill' ?></h3>
    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>
    <form method="POST">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editSkill ? 'edit' : 'add' ?>">
        <?php if ($editSkill): ?><input type="hidden" name="id" value="<?= (int)$editSkill['id'] ?>"><?php endif; ?>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Category *</label>
                <input type="text" class="form-control" name="category" list="categories"
                       value="<?= e($_POST['category'] ?? $editSkill['category'] ?? '') ?>" required>
                <datalist id="categories">
                    <?php foreach (array_keys($grouped) as $cat): ?>
                    <option value="<?= e($cat) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="admin-form-group">
                <label>Skill Name *</label>
                <input type="text" class="form-control" name="skill_name" value="<?= e($_POST['skill_name'] ?? $editSkill['skill_name'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label>Font Awesome Icon Class</label>
                <input type="text" class="form-control" name="icon_class" placeholder="fas fa-code" value="<?= e($_POST['icon_class'] ?? $editSkill['icon_class'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Sort Order</label>
                <input type="number" class="form-control" name="sort_order" value="<?= (int)($_POST['sort_order'] ?? $editSkill['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="admin-form-group" style="grid-column: 1/-1;">
                <label>Description <small>(optional)</small></label>
                <input type="text" class="form-control" name="description" value="<?= e($_POST['description'] ?? $editSkill['description'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input type="checkbox" name="is_visible" <?= (!empty($_POST) ? (isset($_POST['is_visible']) ? 'checked' : '') : (($editSkill['is_visible'] ?? 1) ? 'checked' : '')) ?>>
                    Visible on Site
                </label>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="submit" class="btn btn-primary"><?= $editSkill ? 'Update' : 'Add Skill' ?></button>
            <a href="skills.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Skills grouped table -->
<?php foreach ($grouped as $cat => $skills): ?>
<div class="admin-card" style="margin-bottom: 1rem;">
    <div class="admin-card-header">
        <h2 style="font-size: 0.95rem;"><?= e($cat) ?> <span style="color:#718096;font-weight:400;">(<?= count($skills) ?>)</span></h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead><tr><th>Icon</th><th>Skill</th><th>Description</th><th>Order</th><th>Visible</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($skills as $sk): ?>
                <tr>
                    <td><i class="<?= e($sk['icon_class'] ?? 'fas fa-star') ?>" style="color:var(--orange);font-size:1.2rem;"></i></td>
                    <td><strong><?= e($sk['skill_name']) ?></strong></td>
                    <td style="font-size:0.8rem;color:#718096;"><?= e(truncate($sk['description'] ?? '', 60)) ?></td>
                    <td><?= (int)$sk['sort_order'] ?></td>
                    <td><?= $sk['is_visible'] ? '✅' : '❌' ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= (int)$sk['id'] ?>" class="btn-icon btn-icon-edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$sk['id'] ?>">
                                <button type="submit" class="btn-icon btn-icon-delete" data-confirm="Delete skill '<?= e(addslashes($sk['skill_name'])) ?>'?"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($grouped)): ?>
<div class="admin-card"><div class="admin-card-body" style="text-align:center;color:#718096;">No skills yet. <a href="?add=1">Add the first one.</a></div></div>
<?php endif; ?>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

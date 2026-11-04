<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Projects';
$activeNav = 'projects';

$db     = getDB();
$errors = [];

// ─── Handle POST ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    // Delete
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        // Remove thumbnail file
        $row = $db->prepare('SELECT thumbnail FROM projects WHERE id = ?');
        $row->execute([$id]);
        $p = $row->fetch();
        if ($p && !empty($p['thumbnail'])) {
            @unlink(dirname(__DIR__) . '/uploads/projects/' . $p['thumbnail']);
        }
        $db->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
        logActivity('Deleted project', "ID: $id", adminId());
        setFlash('success', 'Project deleted.');
        redirect(BASE_URL . '/admin/projects.php');
    }

    // Add / Edit
    if (in_array($action, ['add', 'edit'], true)) {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = sanitize($_POST['title']       ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $technologies= sanitize($_POST['technologies']?? '');
        $githubUrl   = sanitize($_POST['github_url']  ?? '');
        $liveUrl     = sanitize($_POST['live_url']    ?? '');
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
        $isVisible   = isset($_POST['is_visible'])  ? 1 : 0;
        $sortOrder   = (int)($_POST['sort_order']  ?? 0);

        if (mb_strlen($title) < 2) $errors['title'] = 'Title is required.';

        // Handle thumbnail upload
        $thumbnail = $_POST['existing_thumbnail'] ?? '';
        if (!empty($_FILES['thumbnail']['name'])) {
            $file = $_FILES['thumbnail'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors['thumbnail'] = 'Only JPG, PNG, GIF or WebP images allowed.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors['thumbnail'] = 'Image must be under 5 MB.';
            } elseif (!in_array(mime_content_type($file['tmp_name']), allowedImageTypes(), true)) {
                $errors['thumbnail'] = 'Invalid image file.';
            }
            if (empty($errors['thumbnail'])) {
                $newName = 'proj_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/projects/' . $newName)) {
                    // Remove old
                    if ($thumbnail) @unlink(dirname(__DIR__) . '/uploads/projects/' . $thumbnail);
                    $thumbnail = $newName;
                } else {
                    $errors['thumbnail'] = 'Upload failed. Check folder permissions.';
                }
            }
        }

        if (empty($errors)) {
            if ($action === 'add') {
                $db->prepare(
                    'INSERT INTO projects (title, description, technologies, thumbnail, github_url, live_url, is_featured, is_visible, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?)'
                )->execute([$title, $description, $technologies, $thumbnail ?: null, $githubUrl ?: null, $liveUrl ?: null, $isFeatured, $isVisible, $sortOrder]);
                logActivity('Added project', $title, adminId());
                setFlash('success', 'Project added successfully.');
            } else {
                $db->prepare(
                    'UPDATE projects SET title=?, description=?, technologies=?, thumbnail=?, github_url=?, live_url=?, is_featured=?, is_visible=?, sort_order=? WHERE id=?'
                )->execute([$title, $description, $technologies, $thumbnail ?: null, $githubUrl ?: null, $liveUrl ?: null, $isFeatured, $isVisible, $sortOrder, $id]);
                logActivity('Updated project', $title, adminId());
                setFlash('success', 'Project updated successfully.');
            }
            redirect(BASE_URL . '/admin/projects.php');
        }
    }
}

// Edit mode
$editProject = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editProject = $stmt->fetch();
}

$showForm = isset($_GET['add']) || $editProject !== null || !empty($errors);

$projects = $db->query('SELECT * FROM projects ORDER BY sort_order ASC, created_at DESC')->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Projects</span></nav>

<div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
    <a href="?add=1" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Project</a>
</div>

<?php if ($showForm): ?>
<!-- Add / Edit Form -->
<div class="admin-form-card">
    <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem;"><?= $editProject ? 'Edit Project' : 'Add New Project' ?></h3>
    <?php if ($errors): ?>
    <div class="alert alert-danger"><ul style="margin: 0; padding-left: 1.25rem;"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="<?= $editProject ? 'edit' : 'add' ?>">
        <?php if ($editProject): ?>
        <input type="hidden" name="id" value="<?= (int)$editProject['id'] ?>">
        <input type="hidden" name="existing_thumbnail" value="<?= e($editProject['thumbnail'] ?? '') ?>">
        <?php endif; ?>

        <div class="admin-form-grid">
            <div class="admin-form-group" style="grid-column: 1/-1;">
                <label>Project Title *</label>
                <input type="text" class="form-control" name="title" value="<?= e($_POST['title'] ?? $editProject['title'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group" style="grid-column: 1/-1;">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="4"><?= e($_POST['description'] ?? $editProject['description'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group">
                <label>Technologies Used <small style="color:#718096;">(comma-separated)</small></label>
                <input type="text" class="form-control" name="technologies" placeholder="PHP, MySQL, JavaScript" value="<?= e($_POST['technologies'] ?? $editProject['technologies'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Thumbnail Image <small style="color:#718096;">(max 5MB: jpg/png/webp/gif)</small></label>
                <?php if (!empty($editProject['thumbnail'])): ?>
                <p style="font-size: 0.8rem; color: var(--orange); margin-bottom: 0.4rem;">Current: <?= e($editProject['thumbnail']) ?></p>
                <?php endif; ?>
                <input type="file" class="form-control" name="thumbnail" accept=".jpg,.jpeg,.png,.gif,.webp">
            </div>
            <div class="admin-form-group">
                <label>GitHub Repository URL</label>
                <input type="url" class="form-control" name="github_url" value="<?= e($_POST['github_url'] ?? $editProject['github_url'] ?? '') ?>" placeholder="https://github.com/…">
            </div>
            <div class="admin-form-group">
                <label>Live Demo URL</label>
                <input type="url" class="form-control" name="live_url" value="<?= e($_POST['live_url'] ?? $editProject['live_url'] ?? '') ?>" placeholder="https://…">
            </div>
            <div class="admin-form-group">
                <label>Sort Order</label>
                <input type="number" class="form-control" name="sort_order" value="<?= (int)($_POST['sort_order'] ?? $editProject['sort_order'] ?? 0) ?>" min="0">
            </div>
            <div class="admin-form-group" style="display: flex; gap: 1.5rem; align-items: center; padding-top: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_featured" <?= (!empty($_POST) ? (isset($_POST['is_featured']) ? 'checked' : '') : ($editProject['is_featured'] ?? 0 ? 'checked' : '')) ?>>
                    Featured Project
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_visible" <?= (!empty($_POST) ? (isset($_POST['is_visible']) ? 'checked' : '') : (($editProject['is_visible'] ?? 1) ? 'checked' : '')) ?>>
                    Visible on Site
                </label>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary"><?= $editProject ? 'Update Project' : 'Add Project' ?></button>
            <a href="projects.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Projects Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-briefcase" style="color:var(--orange);margin-right:.5rem;"></i>All Projects (<?= count($projects) ?>)</h2>
    </div>
    <?php if ($projects): ?>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead><tr><th>Thumbnail</th><th>Title</th><th>Technologies</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                <tr>
                    <td style="width: 80px;">
                        <?php if (!empty($p['thumbnail']) && file_exists(dirname(__DIR__) . '/uploads/projects/' . $p['thumbnail'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/projects/<?= e($p['thumbnail']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">
                        <?php else: ?>
                        <div style="width:60px;height:40px;background:#e2e8f0;border-radius:4px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-image" style="color:#a0aec0;"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= e($p['title']) ?></strong><?php if ($p['is_featured']): ?> <span class="badge-unread">Featured</span><?php endif; ?></td>
                    <td style="font-size: 0.8rem; color: #718096;"><?= e(truncate($p['technologies'] ?? '', 60)) ?></td>
                    <td><?= $p['is_visible'] ? '<span style="color:#38a169;font-size:0.8rem;font-weight:700;">Visible</span>' : '<span style="color:#718096;font-size:0.8rem;">Hidden</span>' ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="?edit=<?= (int)$p['id'] ?>" class="btn-icon btn-icon-edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn-icon btn-icon-delete" title="Delete" data-confirm="Delete project '<?= e(addslashes($p['title'])) ?>'?"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="admin-card-body" style="text-align:center;padding:2rem;color:#718096;">
        No projects yet. <a href="?add=1">Add the first one.</a>
    </div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

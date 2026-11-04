<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Messages';
$activeNav = 'messages';

$db = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        switch ($action) {
            case 'mark_read':
                $db->prepare('UPDATE contact_requests SET is_read = 1 WHERE id = ?')->execute([$id]);
                logActivity('Message marked as read', "ID: $id", adminId());
                setFlash('success', 'Message marked as read.');
                break;
            case 'mark_unread':
                $db->prepare('UPDATE contact_requests SET is_read = 0 WHERE id = ?')->execute([$id]);
                setFlash('success', 'Message marked as unread.');
                break;
            case 'delete':
                $db->prepare('UPDATE contact_requests SET is_deleted = 1 WHERE id = ?')->execute([$id]);
                logActivity('Message deleted', "ID: $id", adminId());
                setFlash('success', 'Message deleted.');
                break;
        }
    }
    redirect(BASE_URL . '/admin/messages.php');
}

// View single message
$viewId = (int)($_GET['view'] ?? 0);
$viewMsg = null;
if ($viewId > 0) {
    $stmt = $db->prepare('SELECT * FROM contact_requests WHERE id = ? AND is_deleted = 0');
    $stmt->execute([$viewId]);
    $viewMsg = $stmt->fetch();
    // Auto-mark as read
    if ($viewMsg && !$viewMsg['is_read']) {
        $db->prepare('UPDATE contact_requests SET is_read = 1 WHERE id = ?')->execute([$viewId]);
        $viewMsg['is_read'] = 1;
    }
}

// Search + filter
$search  = sanitize($_GET['search'] ?? '');
$filter  = $_GET['filter'] ?? 'all';
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

$where  = ['m.is_deleted = 0'];
$params = [];
if ($search !== '') {
    $where[]  = '(m.full_name LIKE ? OR m.email LIKE ? OR m.subject LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter === 'unread') { $where[] = 'm.is_read = 0'; }
if ($filter === 'read')   { $where[] = 'm.is_read = 1'; }

$whereSQL = implode(' AND ', $where);
$total = (int)$db->prepare("SELECT COUNT(*) FROM contact_requests m WHERE $whereSQL")
                 ->execute($params) ? $db->prepare("SELECT COUNT(*) FROM contact_requests m WHERE $whereSQL")->execute($params) : 0;
$countStmt = $db->prepare("SELECT COUNT(*) FROM contact_requests m WHERE $whereSQL");
$countStmt->execute($params);
$total    = (int)$countStmt->fetchColumn();
$pages    = ceil($total / $perPage);

$msgStmt = $db->prepare("SELECT * FROM contact_requests m WHERE $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$msgStmt->execute($params);
$messages = $msgStmt->fetchAll();

include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Messages</span></nav>

<?php if ($viewMsg): ?>
<!-- Single Message View -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-envelope-open" style="color:var(--orange);margin-right:.5rem;"></i>Message from <?= e($viewMsg['full_name']) ?></h2>
        <a href="messages.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>
    <div class="admin-card-body">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <p style="font-size: 0.8rem; color: #718096; margin-bottom: 0.2rem;">FROM</p>
                <p style="font-weight: 600; margin: 0;"><?= e($viewMsg['full_name']) ?></p>
                <p style="font-size: 0.875rem; color: var(--orange); margin: 0;"><a href="mailto:<?= e($viewMsg['email']) ?>"><?= e($viewMsg['email']) ?></a></p>
            </div>
            <div>
                <p style="font-size: 0.8rem; color: #718096; margin-bottom: 0.2rem;">RECEIVED</p>
                <p style="font-weight: 600; margin: 0;"><?= formatDate($viewMsg['created_at']) ?></p>
                <p style="font-size: 0.8rem; color: #718096; margin: 0;"><?= e($viewMsg['ip_address'] ?? '') ?></p>
            </div>
        </div>
        <div style="background: #f7fafc; border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.5rem;">
            <p style="font-size: 0.8rem; color: #718096; margin-bottom: 0.25rem;">SUBJECT</p>
            <h3 style="margin: 0; font-size: 1.1rem;"><?= e($viewMsg['subject']) ?></h3>
        </div>
        <div style="background: #f7fafc; border-radius: var(--radius-sm); padding: 1.25rem; white-space: pre-wrap; line-height: 1.8; font-size: 0.95rem;">
            <?= e($viewMsg['message']) ?>
        </div>
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <a href="mailto:<?= e($viewMsg['email']) ?>?subject=Re: <?= urlencode($viewMsg['subject']) ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-reply"></i> Reply via Email
            </a>
            <form method="POST" style="display: inline;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$viewMsg['id'] ?>">
                <input type="hidden" name="action" value="<?= $viewMsg['is_read'] ? 'mark_unread' : 'mark_read' ?>">
                <button type="submit" class="btn btn-outline btn-sm">
                    <i class="fas fa-<?= $viewMsg['is_read'] ? 'envelope' : 'envelope-open' ?>"></i>
                    Mark as <?= $viewMsg['is_read'] ? 'Unread' : 'Read' ?>
                </button>
            </form>
            <form method="POST" style="display: inline;">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$viewMsg['id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-sm" style="background:#fdecea;color:#d32f2f;border:none;" data-confirm="Delete this message permanently?">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Messages List -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fas fa-inbox" style="color:var(--orange);margin-right:.5rem;"></i>Contact Messages</h2>
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <!-- Filter tabs -->
            <div style="display: flex; gap: 0.4rem;">
                <a href="?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-primary' : 'btn-outline' ?>">All</a>
                <a href="?filter=unread" class="btn btn-sm <?= $filter === 'unread' ? 'btn-primary' : 'btn-outline' ?>">Unread</a>
                <a href="?filter=read"   class="btn btn-sm <?= $filter === 'read' ? 'btn-primary' : 'btn-outline' ?>">Read</a>
            </div>
            <!-- Search -->
            <form method="GET" style="display: flex; gap: 0.4rem;">
                <input type="hidden" name="filter" value="<?= e($filter) ?>">
                <input type="text" class="form-control" name="search" value="<?= e($search) ?>" placeholder="Search messages…" style="width: 220px; padding: 0.45rem 1rem; font-size: 0.85rem;">
                <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

    <?php if ($messages): ?>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr style="<?= $msg['is_read'] ? '' : 'background: rgba(244,123,32,0.04); font-weight: 500;' ?>">
                    <td>
                        <?= e($msg['full_name']) ?><br>
                        <small style="color: #718096;"><?= e($msg['email']) ?></small>
                    </td>
                    <td><?= e(truncate($msg['subject'], 55)) ?></td>
                    <td style="white-space: nowrap; font-size: 0.8rem;"><?= date('d M Y', strtotime($msg['created_at'])) ?></td>
                    <td>
                        <?= $msg['is_read'] ? '<span class="badge-read">Read</span>' : '<span class="badge-unread">New</span>' ?>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="?view=<?= (int)$msg['id'] ?>" class="btn-icon btn-icon-view" title="View"><i class="fas fa-eye"></i></a>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                                <input type="hidden" name="action" value="<?= $msg['is_read'] ? 'mark_unread' : 'mark_read' ?>">
                                <button type="submit" class="btn-icon btn-icon-edit" title="Toggle read"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" style="display:inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn-icon btn-icon-delete" title="Delete" data-confirm="Delete this message?"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <div class="pagination" style="padding: 1.25rem;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?>&filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>"
           class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="admin-card-body" style="text-align: center; padding: 2.5rem;">
        <i class="fas fa-inbox" style="font-size: 2.5rem; color: #e2e8f0; display: block; margin-bottom: 1rem;"></i>
        <p style="color: #718096;">No messages found.</p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

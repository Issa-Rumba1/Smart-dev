<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
$pageTitle = 'Profile Settings';
$activeNav = 'profile';

$db      = getDB();
$errors  = [];
$profile = getProfile();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? 'update_profile';

    // ── Update Profile ──────────────────────────────────────────
    if ($action === 'update_profile') {
        $fullName    = sanitize($_POST['full_name']        ?? '');
        $brandName   = sanitize($_POST['brand_name']       ?? '');
        $tagline     = sanitize($_POST['tagline']          ?? '');
        $headline    = sanitize($_POST['headline']         ?? '');
        $shortBio    = sanitize($_POST['short_bio']        ?? '');
        $fullBio     = sanitize($_POST['full_bio']         ?? '');
        $placeOfBirth= sanitize($_POST['place_of_birth']   ?? '');
        $residence   = sanitize($_POST['current_residence']?? '');
        $showResidence = isset($_POST['show_residence']) ? 1 : 0;
        $email       = sanitize($_POST['email']            ?? '');
        $phone       = sanitize($_POST['phone']            ?? '');
        $linkedin    = sanitize($_POST['linkedin_url']     ?? '');
        $github      = sanitize($_POST['github_url']       ?? '');
        $facebook    = sanitize($_POST['facebook_url']     ?? '');
        $twitter     = sanitize($_POST['twitter_url']      ?? '');
        $whatsapp    = sanitize($_POST['whatsapp_number']  ?? '');
        $metaDesc    = sanitize($_POST['meta_description'] ?? '');

        if (!$fullName) $errors['full_name'] = 'Full name is required.';
        if ($email && !isValidEmail($email)) $errors['email'] = 'Invalid email address.';

        // Handle profile photo upload
        $photo = $profile['profile_photo'] ?? '';
        if (!empty($_FILES['profile_photo']['name'])) {
            $file = $_FILES['profile_photo'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
                $errors['profile_photo'] = 'Only JPG, PNG or WebP images allowed.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors['profile_photo'] = 'Image must be under 5 MB.';
            } elseif (!in_array(mime_content_type($file['tmp_name']), allowedImageTypes(), true)) {
                $errors['profile_photo'] = 'Invalid image file.';
            }
            if (empty($errors['profile_photo'])) {
                $newName = 'profile_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/profile/' . $newName)) {
                    if ($photo) @unlink(dirname(__DIR__) . '/uploads/profile/' . $photo);
                    $photo = $newName;
                } else {
                    $errors['profile_photo'] = 'Photo upload failed. Check folder permissions.';
                }
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare(
                'UPDATE profile SET full_name=?, brand_name=?, tagline=?, headline=?, short_bio=?, full_bio=?,
                 place_of_birth=?, current_residence=?, show_residence=?, profile_photo=?,
                 email=?, phone=?, linkedin_url=?, github_url=?, facebook_url=?, twitter_url=?,
                 whatsapp_number=?, meta_description=? WHERE id=1'
            );
            $stmt->execute([
                $fullName, $brandName, $tagline, $headline, $shortBio, $fullBio,
                $placeOfBirth, $residence, $showResidence, $photo ?: null,
                $email, $phone, $linkedin, $github, $facebook, $twitter, $whatsapp, $metaDesc
            ]);
            logActivity('Updated profile', '', adminId());
            setFlash('success', 'Profile updated successfully.');
            redirect(BASE_URL . '/admin/profile.php');
        }
    }

    // ── Upload CV ───────────────────────────────────────────────
    if ($action === 'upload_cv') {
        if (!empty($_FILES['cv_file']['name'])) {
            $file = $_FILES['cv_file'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $errors['cv_file'] = 'Only PDF files are allowed.';
            } elseif ($file['size'] > 10 * 1024 * 1024) {
                $errors['cv_file'] = 'CV must be under 10 MB.';
            } elseif (mime_content_type($file['tmp_name']) !== 'application/pdf') {
                $errors['cv_file'] = 'Invalid PDF file.';
            }
            if (empty($errors)) {
                $cvName = 'cv_' . time() . '.pdf';
                if (move_uploaded_file($file['tmp_name'], dirname(__DIR__) . '/uploads/cv/' . $cvName)) {
                    // Delete old
                    if (!empty($profile['cv_file'])) @unlink(dirname(__DIR__) . '/uploads/cv/' . $profile['cv_file']);
                    $db->prepare('UPDATE profile SET cv_file=? WHERE id=1')->execute([$cvName]);
                    logActivity('Uploaded CV', $cvName, adminId());
                    setFlash('success', 'CV uploaded successfully.');
                    redirect(BASE_URL . '/admin/profile.php');
                } else {
                    $errors['cv_file'] = 'CV upload failed. Check folder permissions.';
                }
            }
        } else {
            $errors['cv_file'] = 'No file selected.';
        }
    }

    // ── Change Password ─────────────────────────────────────────
    if ($action === 'change_password') {
        $currentPw  = $_POST['current_password']  ?? '';
        $newPw      = $_POST['new_password']       ?? '';
        $confirmPw  = $_POST['confirm_password']   ?? '';

        $stmt = $db->prepare('SELECT password_hash FROM administrators WHERE id = ?');
        $stmt->execute([adminId()]);
        $admin = $stmt->fetch();

        if (!password_verify($currentPw, $admin['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect.';
        }
        if (strlen($newPw) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        }
        if ($newPw !== $confirmPw) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
            $db->prepare('UPDATE administrators SET password_hash=? WHERE id=?')->execute([$hash, adminId()]);
            logActivity('Changed admin password', '', adminId());
            setFlash('success', 'Password changed successfully.');
            redirect(BASE_URL . '/admin/profile.php');
        }
    }
}

// Reload profile
$profile = getProfile();
include dirname(__DIR__) . '/includes/admin_header.php';
?>

<nav class="breadcrumb"><a href="dashboard.php">Dashboard</a><span class="breadcrumb-sep">/</span><span>Profile</span></nav>

<!-- Profile Form -->
<div class="admin-form-card">
    <h3 style="margin-bottom:1.5rem;">Profile &amp; Brand Information</h3>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><?= implode('<br>', array_map('e', $errors)) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="update_profile">

        <h4 style="margin-bottom:1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">Personal Information</h4>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Full Name *</label>
                <input type="text" class="form-control" name="full_name" value="<?= e($profile['full_name'] ?? '') ?>" required>
            </div>
            <div class="admin-form-group">
                <label>Brand Name</label>
                <input type="text" class="form-control" name="brand_name" value="<?= e($profile['brand_name'] ?? 'SMART DEV') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Brand Tagline</label>
                <input type="text" class="form-control" name="tagline" value="<?= e($profile['tagline'] ?? '') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Hero Headline</label>
                <input type="text" class="form-control" name="headline" value="<?= e($profile['headline'] ?? '') ?>">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Short Bio (Homepage)</label>
                <textarea class="form-control" name="short_bio" rows="2"><?= e($profile['short_bio'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label>Full Biography (About Page)</label>
                <textarea class="form-control" name="full_bio" rows="6"><?= e($profile['full_bio'] ?? '') ?></textarea>
            </div>
            <div class="admin-form-group">
                <label>Place of Birth</label>
                <input type="text" class="form-control" name="place_of_birth" value="<?= e($profile['place_of_birth'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Current Residence <small style="color:#718096;">(general area only)</small></label>
                <input type="text" class="form-control" name="current_residence" value="<?= e($profile['current_residence'] ?? '') ?>" placeholder="e.g. Nairobi, Kenya">
            </div>
            <div class="admin-form-group" style="grid-column:1/-1;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                    <input type="checkbox" name="show_residence" <?= ($profile['show_residence'] ?? 0) ? 'checked' : '' ?>>
                    Show residence publicly on website
                </label>
            </div>
        </div>

        <h4 style="margin:1.5rem 0 1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">Contact &amp; Social Media</h4>
        <div class="admin-form-grid">
            <div class="admin-form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="email" value="<?= e($profile['email'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" name="phone" value="<?= e($profile['phone'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>LinkedIn URL</label>
                <input type="url" class="form-control" name="linkedin_url" value="<?= e($profile['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/…">
            </div>
            <div class="admin-form-group">
                <label>GitHub URL</label>
                <input type="url" class="form-control" name="github_url" value="<?= e($profile['github_url'] ?? '') ?>" placeholder="https://github.com/…">
            </div>
            <div class="admin-form-group">
                <label>Facebook URL</label>
                <input type="url" class="form-control" name="facebook_url" value="<?= e($profile['facebook_url'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>Twitter / X URL</label>
                <input type="url" class="form-control" name="twitter_url" value="<?= e($profile['twitter_url'] ?? '') ?>">
            </div>
            <div class="admin-form-group">
                <label>WhatsApp Number <small>(with country code)</small></label>
                <input type="text" class="form-control" name="whatsapp_number" value="<?= e($profile['whatsapp_number'] ?? '') ?>" placeholder="+254700000000">
            </div>
        </div>

        <h4 style="margin:1.5rem 0 1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">SEO</h4>
        <div class="admin-form-group">
            <label>Meta Description</label>
            <textarea class="form-control" name="meta_description" rows="2" maxlength="300"><?= e($profile['meta_description'] ?? '') ?></textarea>
        </div>

        <h4 style="margin:1.5rem 0 1rem;color:var(--orange);font-size:0.9rem;text-transform:uppercase;letter-spacing:1px;">Profile Photo</h4>
        <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1rem;">
            <?php if (!empty($profile['profile_photo']) && file_exists(dirname(__DIR__) . '/uploads/profile/' . $profile['profile_photo'])): ?>
            <img src="<?= BASE_URL ?>/uploads/profile/<?= e($profile['profile_photo']) ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--orange);">
            <?php else: ?>
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--orange),var(--orange-deep));display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;"><i class="fas fa-user"></i></div>
            <?php endif; ?>
            <div>
                <input type="file" class="form-control" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" style="max-width:350px;">
                <p style="font-size:0.78rem;color:#718096;margin-top:.3rem;">JPG, PNG or WebP, max 5MB</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
    </form>
</div>

<!-- CV Upload -->
<div class="admin-form-card" style="margin-top:1.5rem;">
    <h3 style="margin-bottom:1rem;">CV / Resume Upload</h3>
    <?php if (!empty($profile['cv_file'])): ?>
    <div class="alert alert-info" style="margin-bottom:1rem;">
        <i class="fas fa-file-pdf"></i>
        Current CV: <strong><?= e($profile['cv_file']) ?></strong>
        &nbsp;&mdash;&nbsp;<a href="<?= BASE_URL ?>/download_cv.php" target="_blank">Preview Download</a>
    </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="upload_cv">
        <div style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
            <div class="admin-form-group" style="flex:1;min-width:250px;margin:0;">
                <label>CV File (PDF only, max 10MB)</label>
                <input type="file" class="form-control" name="cv_file" accept=".pdf" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="margin-bottom:0;"><i class="fas fa-upload"></i> Upload CV</button>
        </div>
        <?php if (!empty($errors['cv_file'])): ?><p style="color:#e53e3e;font-size:.85rem;margin-top:.3rem;"><?= e($errors['cv_file']) ?></p><?php endif; ?>
    </form>
</div>

<!-- Change Password -->
<div class="admin-form-card" style="margin-top:1.5rem;">
    <h3 style="margin-bottom:1.5rem;">Change Admin Password</h3>
    <form method="POST" style="max-width:480px;">
        <?= csrfField() ?>
        <input type="hidden" name="action" value="change_password">
        <div class="admin-form-group">
            <label>Current Password</label>
            <input type="password" class="form-control" name="current_password" required autocomplete="current-password">
            <?php if (!empty($errors['current_password'])): ?><p style="color:#e53e3e;font-size:.82rem;"><?= e($errors['current_password']) ?></p><?php endif; ?>
        </div>
        <div class="admin-form-group">
            <label>New Password <small>(min 8 characters)</small></label>
            <input type="password" class="form-control" name="new_password" required minlength="8" autocomplete="new-password">
            <?php if (!empty($errors['new_password'])): ?><p style="color:#e53e3e;font-size:.82rem;"><?= e($errors['new_password']) ?></p><?php endif; ?>
        </div>
        <div class="admin-form-group">
            <label>Confirm New Password</label>
            <input type="password" class="form-control" name="confirm_password" required minlength="8" autocomplete="new-password">
            <?php if (!empty($errors['confirm_password'])): ?><p style="color:#e53e3e;font-size:.82rem;"><?= e($errors['confirm_password']) ?></p><?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Change Password</button>
    </form>
</div>

<?php include dirname(__DIR__) . '/includes/admin_footer.php'; ?>

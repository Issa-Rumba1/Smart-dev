<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Contact | SMART DEV – Get In Touch';
$metaDescription = 'Contact SMART DEV and Issa Nyamawi Rumba for IT support, web development, networking, database management and graphic design services.';

$profile = getProfile();
$errors  = [];
$success = false;

// Pre-fill subject from URL parameter
$preSubject = isset($_GET['subject']) ? sanitize($_GET['subject']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $fullName = sanitize($_POST['full_name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $subject  = sanitize($_POST['subject'] ?? '');
    $message  = sanitize($_POST['message'] ?? '');

    // Rate limit: max 3 submissions per IP in 10 minutes
    $db  = getDB();
    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $chk = $db->prepare(
        'SELECT COUNT(*) FROM contact_requests
         WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)'
    );
    $chk->execute([$ip]);
    if ((int)$chk->fetchColumn() >= 3) {
        $errors['general'] = 'Too many messages sent. Please wait 10 minutes before trying again.';
    }

    // Validate
    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
        $errors['full_name'] = 'Please enter your full name (2–100 characters).';
    }
    if (!isValidEmail($email)) {
        $errors['email'] = 'Please enter a valid email address.';
    }
    if (mb_strlen($subject) < 3 || mb_strlen($subject) > 255) {
        $errors['subject'] = 'Subject must be 3–255 characters.';
    }
    if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) {
        $errors['message'] = 'Message must be 10–5000 characters.';
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare(
                'INSERT INTO contact_requests (full_name, email, subject, message, ip_address)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$fullName, $email, $subject, $message, $ip]);
            $success = true;
            setFlash('success', 'Thank you! Your message has been received. I will respond as soon as possible.');
            redirect('contact.php');
        } catch (Exception $e) {
            error_log('Contact form error: ' . $e->getMessage());
            $errors['general'] = 'A server error occurred. Please try again later.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="page-hero-content container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>Contact</span>
        </nav>
        <h1>Get In <span style="color: var(--orange)">Touch</span></h1>
    </div>
</section>

<!-- Contact Section -->
<section>
    <div class="container">
        <div class="section-header">
            <span class="badge">Let's Connect</span>
            <h2>Start a <span>Conversation</span></h2>
            <p>Have a project in mind or need IT support? Fill in the form below and I will respond within 24 hours.</p>
            <div class="divider"></div>
        </div>

        <div class="contact-grid">
            <!-- Contact Info Card -->
            <div class="contact-info">
                <div class="contact-info-card">
                    <h3>Contact Information</h3>
                    <p>Reach out through any of the channels below or use the contact form.</p>

                    <div class="contact-detail">
                        <div class="contact-detail-icon"><i class="fas fa-user"></i></div>
                        <div class="contact-detail-text">
                            <h4>Name</h4>
                            <p><?= e($profile['full_name'] ?? 'Issa Nyamawi Rumba') ?></p>
                        </div>
                    </div>

                    <?php if (!empty($profile['email'])): ?>
                    <div class="contact-detail">
                        <div class="contact-detail-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-detail-text">
                            <h4>Email</h4>
                            <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="contact-detail">
                        <div class="contact-detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-detail-text">
                            <h4>Origin</h4>
                            <p>Kwale, Kinango, Kenya</p>
                        </div>
                    </div>

                    <div class="contact-detail">
                        <div class="contact-detail-icon"><i class="fas fa-briefcase"></i></div>
                        <div class="contact-detail-text">
                            <h4>Brand</h4>
                            <p>SMART DEV &mdash; Innovation. Technology. Digital Solutions.</p>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <?php if (!empty($profile['linkedin_url']) || !empty($profile['github_url']) || !empty($profile['facebook_url']) || !empty($profile['whatsapp_number'])): ?>
                    <div class="contact-social-links">
                        <?php if (!empty($profile['linkedin_url'])): ?>
                        <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" class="contact-social-link" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($profile['github_url'])): ?>
                        <a href="<?= e($profile['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="contact-social-link" aria-label="GitHub"><i class="fab fa-github"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($profile['facebook_url'])): ?>
                        <a href="<?= e($profile['facebook_url']) ?>" target="_blank" rel="noopener noreferrer" class="contact-social-link" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($profile['whatsapp_number'])): ?>
                        <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $profile['whatsapp_number'])) ?>" target="_blank" rel="noopener noreferrer" class="contact-social-link" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Response time note -->
                <div style="background: #fff; border-radius: var(--radius); padding: 1.5rem; margin-top: 1.5rem; box-shadow: var(--shadow); display: flex; align-items: flex-start; gap: 1rem;">
                    <i class="fas fa-clock" style="color: var(--orange); font-size: 1.5rem; margin-top: 0.1rem;"></i>
                    <div>
                        <h4 style="margin-bottom: 0.25rem;">Response Time</h4>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--grey-mid);">I typically respond to all enquiries within 24 hours during business days.</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card">
                <h3><i class="fas fa-paper-plane" style="color: var(--orange);"></i> Send Me a Message</h3>

                <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i><?= e($errors['general']) ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php" id="contactForm" novalidate>
                    <?= csrfField() ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name <span class="required">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['full_name']) ? 'error' : '' ?>"
                                   id="full_name" name="full_name"
                                   value="<?= e($_POST['full_name'] ?? '') ?>"
                                   placeholder="Enter your full name"
                                   maxlength="100" required>
                            <span class="form-error <?= isset($errors['full_name']) ? 'show' : '' ?>" id="full_name_error">
                                <?= e($errors['full_name'] ?? '') ?>
                            </span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                                   id="email" name="email"
                                   value="<?= e($_POST['email'] ?? '') ?>"
                                   placeholder="your@email.com"
                                   maxlength="150" required>
                            <span class="form-error <?= isset($errors['email']) ? 'show' : '' ?>" id="email_error">
                                <?= e($errors['email'] ?? '') ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject <span class="required">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['subject']) ? 'error' : '' ?>"
                               id="subject" name="subject"
                               value="<?= e($_POST['subject'] ?? $preSubject) ?>"
                               placeholder="How can I help you?"
                               maxlength="255" required>
                        <span class="form-error <?= isset($errors['subject']) ? 'show' : '' ?>" id="subject_error">
                            <?= e($errors['subject'] ?? '') ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="message">Message <span class="required">*</span></label>
                        <textarea class="form-control <?= isset($errors['message']) ? 'error' : '' ?>"
                                  id="message" name="message"
                                  rows="6" placeholder="Tell me about your project or enquiry…"
                                  maxlength="5000" required><?= e($_POST['message'] ?? '') ?></textarea>
                        <span class="form-error <?= isset($errors['message']) ? 'show' : '' ?>" id="message_error">
                            <?= e($errors['message'] ?? '') ?>
                        </span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="justify-content: center; font-size: 1.05rem; padding: 0.9rem;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

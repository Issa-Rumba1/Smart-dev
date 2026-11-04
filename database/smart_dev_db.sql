-- ============================================================
-- SMART DEV - Database Schema
-- Database: smart_dev_db
-- Compatible with: MySQL / MariaDB (XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `smart_dev_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smart_dev_db`;

-- ============================================================
-- TABLE: administrators
-- ============================================================
CREATE TABLE IF NOT EXISTS `administrators` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `password_reset_token` VARCHAR(64) DEFAULT NULL,
  `password_reset_expires` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: login_attempts
-- ============================================================
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `username_tried` VARCHAR(100) NOT NULL DEFAULT '',
  `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activity_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin` (`admin_id`),
  CONSTRAINT `fk_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `administrators` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: profile
-- ============================================================
CREATE TABLE IF NOT EXISTS `profile` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL DEFAULT 'Issa Nyamawi Rumba',
  `brand_name` VARCHAR(100) NOT NULL DEFAULT 'SMART DEV',
  `tagline` VARCHAR(255) NOT NULL DEFAULT 'Innovation. Technology. Digital Solutions.',
  `headline` VARCHAR(255) NOT NULL DEFAULT 'Empowering Businesses Through Technology, Innovation and Creative Digital Solutions.',
  `short_bio` TEXT DEFAULT NULL,
  `full_bio` TEXT DEFAULT NULL,
  `place_of_birth` VARCHAR(100) DEFAULT 'Kwale, Kinango, Kenya',
  `current_residence` VARCHAR(150) DEFAULT NULL,
  `show_residence` TINYINT(1) NOT NULL DEFAULT 0,
  `profile_photo` VARCHAR(255) DEFAULT NULL,
  `cv_file` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `linkedin_url` VARCHAR(255) DEFAULT NULL,
  `github_url` VARCHAR(255) DEFAULT NULL,
  `facebook_url` VARCHAR(255) DEFAULT NULL,
  `twitter_url` VARCHAR(255) DEFAULT NULL,
  `whatsapp_number` VARCHAR(30) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: education
-- ============================================================
CREATE TABLE IF NOT EXISTS `education` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `level` VARCHAR(50) NOT NULL,
  `institution` VARCHAR(150) NOT NULL,
  `qualification` VARCHAR(255) DEFAULT NULL,
  `field_of_study` VARCHAR(150) DEFAULT NULL,
  `start_year` YEAR DEFAULT NULL,
  `end_year` YEAR DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: skills
-- ============================================================
CREATE TABLE IF NOT EXISTS `skills` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(100) NOT NULL,
  `skill_name` VARCHAR(100) NOT NULL,
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: services
-- ============================================================
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `short_description` TEXT DEFAULT NULL,
  `full_description` TEXT DEFAULT NULL,
  `icon_class` VARCHAR(100) DEFAULT NULL,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: projects
-- ============================================================
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `technologies` VARCHAR(500) DEFAULT NULL,
  `thumbnail` VARCHAR(255) DEFAULT NULL,
  `github_url` VARCHAR(255) DEFAULT NULL,
  `live_url` VARCHAR(255) DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: testimonials
-- ============================================================
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_name` VARCHAR(100) NOT NULL,
  `client_title` VARCHAR(150) DEFAULT NULL,
  `client_photo` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: contact_requests
-- ============================================================
CREATE TABLE IF NOT EXISTS `contact_requests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_read` (`is_read`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default profile
INSERT INTO `profile` (`full_name`, `brand_name`, `tagline`, `headline`, `short_bio`, `full_bio`, `place_of_birth`, `email`, `meta_description`) VALUES
(
  'Issa Nyamawi Rumba',
  'SMART DEV',
  'Innovation. Technology. Digital Solutions.',
  'Empowering Businesses Through Technology, Innovation and Creative Digital Solutions.',
  'Welcome to SMART DEV, a technology and digital solutions brand founded by Issa Nyamawi Rumba. I specialize in computer maintenance, networking, database management, website development, graphic design and general IT support.',
  'I am Issa Nyamawi Rumba, the founder of SMART DEV — a technology and digital solutions brand dedicated to providing practical, reliable and innovative IT solutions. With a Diploma in Computer Science from the National Youth Service Camp Engineering Institute, I have built expertise across computer hardware maintenance, networking, database management, web development, graphic design and general IT support. My goal is to leverage technology to solve real-world problems, empower businesses and bridge the digital divide. I am passionate about continuous learning and staying updated with the latest developments in the technology industry. SMART DEV is my commitment to delivering quality technology services that make a genuine difference.',
  'Kwale, Kinango, Kenya',
  'contact@smartdev.co.ke',
  'SMART DEV is a professional technology and digital solutions brand by Issa Nyamawi Rumba. IT support, web development, networking, database management and graphic design services.'
);

-- Set profile photo (file must exist at uploads/profile/profile_photo.png)
UPDATE `profile` SET `profile_photo` = 'profile_photo.png' WHERE `id` = 1;

-- Education records
INSERT INTO `education` (`level`, `institution`, `qualification`, `field_of_study`, `description`, `sort_order`) VALUES
('Primary', 'Lutsangani Primary School', 'Kenya Certificate of Primary Education (KCPE)', 'General Primary Education', 'Completed primary education at Lutsangani Primary School, building a strong academic foundation.', 1),
('Secondary', 'The Lenana School', 'Kenya Certificate of Secondary Education (KCSE)', 'General Secondary Education', 'Attended The Lenana School, one of Kenya\'s premier national schools, gaining a well-rounded secondary education with a focus on sciences and mathematics.', 2),
('Tertiary', 'National Youth Service Camp Engineering Institute', 'Diploma in Computer Science', 'Computer Science', 'Completed a comprehensive Diploma in Computer Science covering hardware maintenance, networking, database management, web development and software engineering fundamentals.', 3);

-- Skills
INSERT INTO `skills` (`category`, `skill_name`, `icon_class`, `description`, `sort_order`) VALUES
('Computer Hardware & Maintenance', 'Hardware Installation', 'fas fa-microchip', 'Installation and configuration of computer hardware components', 1),
('Computer Hardware & Maintenance', 'Hardware Troubleshooting', 'fas fa-tools', 'Diagnosing and resolving hardware faults and component failures', 2),
('Computer Hardware & Maintenance', 'Computer Servicing', 'fas fa-wrench', 'Preventive maintenance and servicing of desktop and laptop computers', 3),
('Computer Hardware & Maintenance', 'OS Installation', 'fas fa-compact-disc', 'Installation and configuration of Windows, Linux operating systems', 4),
('Computer Hardware & Maintenance', 'Software Installation', 'fas fa-download', 'Installing, configuring and updating software applications', 5),
('Computer Hardware & Maintenance', 'Computer Repair', 'fas fa-laptop-medical', 'Diagnosis and repair of hardware and software faults', 6),
('Computer Networking', 'LAN & WAN', 'fas fa-network-wired', 'Understanding and implementation of local and wide area networks', 7),
('Computer Networking', 'Network Installation', 'fas fa-server', 'Physical and logical network setup and cabling', 8),
('Computer Networking', 'Router & Switch Config', 'fas fa-route', 'Basic configuration of routers and switches', 9),
('Computer Networking', 'Network Troubleshooting', 'fas fa-bug', 'Identifying and resolving network connectivity issues', 10),
('Computer Networking', 'Internet Connectivity', 'fas fa-wifi', 'Setup and support of internet connections', 11),
('Computer Networking', 'Basic Network Security', 'fas fa-shield-alt', 'Implementing basic security measures on network infrastructure', 12),
('Database Management', 'MySQL', 'fas fa-database', 'Database creation, administration and optimization using MySQL', 13),
('Database Management', 'MariaDB', 'fas fa-database', 'Database management using MariaDB', 14),
('Database Management', 'Database Design', 'fas fa-project-diagram', 'Entity relationship modelling and schema design', 15),
('Database Management', 'SQL Queries', 'fas fa-code', 'Writing complex SQL queries, joins, and stored procedures', 16),
('Database Management', 'PHP & DB Integration', 'fab fa-php', 'Connecting PHP applications to MySQL databases', 17),
('Database Management', 'Database Troubleshooting', 'fas fa-stethoscope', 'Diagnosing and resolving database performance and integrity issues', 18),
('Website Development', 'HTML5', 'fab fa-html5', 'Semantic and accessible HTML5 markup', 19),
('Website Development', 'CSS3', 'fab fa-css3-alt', 'Modern CSS3 styling, animations and responsive layouts', 20),
('Website Development', 'JavaScript', 'fab fa-js', 'Interactive frontend JavaScript development', 21),
('Website Development', 'PHP', 'fab fa-php', 'Server-side web development with PHP 8', 22),
('Website Development', 'Responsive Design', 'fas fa-mobile-alt', 'Mobile-first responsive web design', 23),
('Website Development', 'Website Maintenance', 'fas fa-tools', 'Ongoing website updates, security patches and performance optimisation', 24),
('Graphic Design', 'Logo Design', 'fas fa-pen-nib', 'Creating professional brand logos and identity marks', 25),
('Graphic Design', 'Brand Identity', 'fas fa-palette', 'Complete brand identity development and style guides', 26),
('Graphic Design', 'Poster & Banner Design', 'fas fa-image', 'Designing marketing materials, posters and digital banners', 27),
('Graphic Design', 'Digital Graphics', 'fas fa-paint-brush', 'Digital illustration and creative visual assets', 28),
('General IT Support', 'IT Consultancy', 'fas fa-user-tie', 'Technology consultancy and strategic IT advice', 29),
('General IT Support', 'Software Support', 'fas fa-headset', 'Technical support for software applications and systems', 30),
('General IT Support', 'Cybersecurity Awareness', 'fas fa-lock', 'Basic cybersecurity training and awareness', 31),
('General IT Support', 'Computer Training', 'fas fa-chalkboard-teacher', 'Training users on computer applications and IT fundamentals', 32);

-- Services
INSERT INTO `services` (`title`, `short_description`, `icon_class`, `sort_order`) VALUES
('Computer Maintenance & Troubleshooting', 'Professional computer hardware and software maintenance, repair and technical support for desktops and laptops.', 'fas fa-laptop-medical', 1),
('Computer Networking & Configuration', 'LAN/WAN installation, router and switch configuration, network troubleshooting and internet connectivity support.', 'fas fa-network-wired', 2),
('Database Creation & Management', 'MySQL and MariaDB database design, creation, administration, optimisation and troubleshooting.', 'fas fa-database', 3),
('Website Design & Development', 'Professional, responsive and database-driven website development using HTML5, CSS3, JavaScript and PHP.', 'fas fa-globe', 4),
('Graphic Design & Branding', 'Logo design, brand identity development, poster design and professional digital graphics.', 'fas fa-paint-brush', 5),
('General IT Support & Consultancy', 'Comprehensive IT support, software troubleshooting, hardware assistance and technology consultancy.', 'fas fa-headset', 6),
('Basic Computer Training', 'Hands-on computer training covering fundamental computing, office productivity, internet usage and digital literacy.', 'fas fa-chalkboard-teacher', 7),
('Website Maintenance & Support', 'Ongoing website updates, content management, security monitoring and technical assistance.', 'fas fa-tools', 8);

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'SMART DEV'),
('site_tagline', 'Innovation. Technology. Digital Solutions.'),
('contact_email', 'contact@smartdev.co.ke'),
('max_upload_size_mb', '10'),
('allowed_image_types', 'jpg,jpeg,png,webp,gif'),
('allowed_cv_types', 'pdf'),
('dark_mode_default', '0'),
('show_testimonials', '1'),
('show_portfolio', '1'),
('maintenance_mode', '0');

-- Default admin (password: Admin@SmartDev2024 — CHANGE IMMEDIATELY)
INSERT INTO `administrators` (`username`, `email`, `password_hash`, `full_name`) VALUES
('admin', 'admin@smartdev.co.ke', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Issa Nyamawi Rumba');
-- NOTE: The above hash corresponds to password "password" — you MUST change it after first login.
-- Use the admin settings panel to update the password immediately.

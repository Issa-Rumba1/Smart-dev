<?php
require_once dirname(__DIR__) . '/includes/init.php';
requireAdmin();
logActivity('Admin logout', '', adminId());
adminLogout();
redirect(BASE_URL . '/admin/login.php');

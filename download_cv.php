<?php
/**
 * SMART DEV - CV Download Handler
 * Serves the CV PDF securely via PHP, preventing direct URL access to the file.
 */
require_once __DIR__ . '/includes/init.php';

$profile = getProfile();

if (empty($profile['cv_file'])) {
    setFlash('warning', 'No CV is currently available for download. Please check back later.');
    redirect('index.php');
}

$filename = basename($profile['cv_file']); // Prevent directory traversal
$filepath = __DIR__ . '/uploads/cv/' . $filename;

// Validate file exists and is a PDF
if (!file_exists($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'pdf') {
    setFlash('error', 'CV file not found. Please contact me directly.');
    redirect('index.php');
}

// Serve the file
$downloadName = 'Issa_Nyamawi_Rumba_CV.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
readfile($filepath);
exit;

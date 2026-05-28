<?php
$basePath = '../';
require_once $basePath . 'admin/includes/AdminAuth.php';

// Logout the admin
AdminAuth::logout();

// Redirect to home page
header('Location: ' . $basePath . 'index.php');
exit;

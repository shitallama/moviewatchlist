<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';
require_once __DIR__ . '/../admin/includes/AdminAuth.php';

session_start();

// Check login
AdminAuth::startSession();
$isAdmin = AdminAuth::isLoggedIn();

if (!isset($_SESSION['user_id']) && !$isAdmin) {
    header("Location: ../Login/login.php");
    exit();
}

$movieRepository = new MovieRepository($pdo);

// Delete movie
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    $movieRepository->delete($id, $isAdmin ? null : $user_id);
}

header("Location: view_movies.php");
exit();
?>
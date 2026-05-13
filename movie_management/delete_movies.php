<?php
include('../includes/db.php');
require_once __DIR__ . '/MovieManager.php';

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

$movieRepository = new MovieRepository($pdo);

// Delete movie
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = (int)$_SESSION['user_id'];

    $movieRepository->delete($id, $user_id);
}

header("Location: view_movies.php");
exit();
?>
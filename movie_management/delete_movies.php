<?php
include('../includes/db.php');

session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit();
}

// Delete movie
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("DELETE FROM Movies WHERE movie_id = ? AND user_id = ?");
exit();
?>
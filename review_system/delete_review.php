<?php
include("review_db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (deleteReview($id)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
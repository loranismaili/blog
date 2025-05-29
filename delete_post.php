<?php
session_start();
include 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $post_id = $conn->real_escape_string($_GET['id']);

    $sql = "DELETE FROM posts WHERE id = '$post_id'";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Post deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting post: " . $conn->error;
    }
} else {
    $_SESSION['message'] = "Invalid post ID.";
}

header("Location: dashboard.php");
exit();
?>
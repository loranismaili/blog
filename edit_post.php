<?php
session_start();
include 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit();
}

$post_id = $_GET['id'] ?? null;
$post = null;
$message = '';

if ($post_id) {
    // Fetch post details
    $stmt = $conn->prepare("SELECT id, title, content FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        $message = "Post not found.";
    }
    $stmt->close();
} else {
    header("Location: dashboard.php"); // Redirect if no ID is provided
    exit();
}

// Handle Update Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_post'])) {
    $new_title = $conn->real_escape_string($_POST['title']);
    $new_content = $conn->real_escape_string($_POST['content']);
    $post_id_to_update = $conn->real_escape_string($_POST['post_id']);

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_title, $new_content, $post_id_to_update);

    if ($stmt->execute()) {
        $message = "Post updated successfully.";
        // Refresh post data after update
        $stmt_refresh = $conn->prepare("SELECT id, title, content FROM posts WHERE id = ?");
        $stmt_refresh->bind_param("i", $post_id_to_update);
        $stmt_refresh->execute();
        $result_refresh = $stmt_refresh->get_result();
        $post = $result_refresh->fetch_assoc();
        $stmt_refresh->close();
    } else {
        $message = "Error updating post: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit Post</h1>
        </header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="dashboard.php?logout=true">Logout</a></li>
            </ul>
        </nav>

        <main>
            <?php if ($message): ?>
                <div class="messages"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($post): ?>
            <form action="edit_post.php?id=<?php echo htmlspecialchars($post['id']); ?>" method="POST">
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">

                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

                <label for="content">Content:</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>

                <input type="submit" name="update_post" value="Update Post">
            </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
<?php
$conn->close();
?>
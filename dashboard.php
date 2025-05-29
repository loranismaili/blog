<?php
session_start();
include 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php");
    exit();
}

$message = '';

// Handle Add Post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_post'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
    if ($conn->query($sql) === TRUE) {
        $message = "New post created successfully.";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
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

            <h2>Add New Post</h2>
            <form action="dashboard.php" method="POST">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="content">Content:</label>
                <textarea id="content" name="content" required></textarea>

                <input type="submit" name="add_post" value="Add Post">
            </form>

            <h2>Manage Posts</h2>
            <?php
            $sql = "SELECT id, title, created_at FROM posts ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='post'>";
                    echo "<h3>" . htmlspecialchars($row["title"]) . "</h3>";
                    echo "<span class='post-date'>" . date("F j, Y, g:i a", strtotime($row["created_at"])) . "</span>";
                    echo "<div class='dashboard-actions'>";
                    echo "<a class='edit' href='edit_post.php?id=" . $row["id"] . "'>Edit</a>";
                    echo "<a class='delete' href='delete_post.php?id=" . $row["id"] . "' onclick=\"return confirm('Are you sure you want to delete this post?');\">Delete</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>No posts to manage.</p>";
            }
            $conn->close();
            ?>
        </main>
    </div>
</body>
</html>
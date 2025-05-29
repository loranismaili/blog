<?php
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>My Simple Blog</h1>
        </header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="admin.php">Admin Login</a></li>
            </ul>
        </nav>

        <main>
            <?php
            $sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<article class='post'>";
                    echo "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
                    echo "<span class='post-date'>" . date("F j, Y, g:i a", strtotime($row["created_at"])) . "</span>";
                    echo "<p>" . nl2br(htmlspecialchars($row["content"])) . "</p>";
                    echo "</article>";
                }
            } else {
                echo "<p>No posts found.</p>";
            }
            $conn->close();
            ?>
        </main>

        <div class="admin-link">
            <p><a href="admin.php">Go to Admin Login</a></p>
        </div>
    </div>
</body>
</html>
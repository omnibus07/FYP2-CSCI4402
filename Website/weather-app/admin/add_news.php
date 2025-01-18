<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

?>
<?php require('./header.php') ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'weather_app');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $published_date = $conn->real_escape_string($_POST['published_date']);
    $created_by = $_SESSION['user_id']; // Assuming user is logged in

    $sql = "INSERT INTO news (title, content, published_date, created_by) 
            VALUES ('$title', '$content', '$published_date', $created_by)";

    if ($conn->query($sql)) {
        $new_article_id = $conn->insert_id;
        echo "<script>window.location.href='edit_news.php?id=$new_article_id';</script>";
        exit;
    } else {
        echo "<script>alert('Error creating article: " . $conn->error . "'); window.location.href='add_news.php';</script>";
    }

    $conn->close();
}
?>
<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">

    <div class="d-flex justify-content-between align-items-center">
        <h2 class="content-header">Add New Article</h2>
        <a href="manage_news.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Articles List
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Content</label>
                    <textarea class="form-control" name="content" rows="10" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Published Date</label>
                    <input type="date" class="form-control" name="published_date" required>
                </div>
            </div>

            <div class="mt-4">
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="manage_news.php" class="btn btn-secondary btn-action">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-action">Add Article</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-top: 1rem;
    }

    .form-label.required::after {
        content: " *";
        color: red;
    }

    .btn-action {
        min-width: 120px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(15, 5, 88, 0.25);
    }
</style>

<?php require('./footer.php')?>
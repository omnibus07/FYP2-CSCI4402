<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require('./header.php');

$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = null;

if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();
}

if (!$article) {
    echo "<script>alert('Article not found'); window.location.href='manage_news.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $published_date = $conn->real_escape_string($_POST['published_date']);

    $sql = "UPDATE news SET 
            title = ?, 
            content = ?, 
            published_date = ?
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $published_date, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Article updated successfully!'); window.location.href='manage_news.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating article: " . $conn->error . "');</script>";
    }
}
?>

<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="content-header">Edit Article</h2>
        <a href="manage_news.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Articles List
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control" name="title" required 
                           value="<?php echo htmlspecialchars($article['title']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Content</label>
                    <textarea class="form-control" name="content" rows="10" required><?php 
                        echo htmlspecialchars($article['content']); 
                    ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Published Date</label>
                    <input type="date" class="form-control" name="published_date" required
                           value="<?php echo $article['published_date']; ?>">
                </div>
            </div>

            <div class="mt-4">
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="manage_news.php" class="btn btn-secondary btn-action">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-action">Update Article</button>
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

<?php require('./footer.php'); ?>
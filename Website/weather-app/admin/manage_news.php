<?php require('header.php') ?>
<?php

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Clear the error after displaying
}

?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#newsTable').DataTable();
});
</script>

<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="content-header">Manage News & Press Releases</h2>
        <a href="add_news.php" class="btn btn-primary" style="background-color: #0f0558; border-radius: 30px; color:#fff">
            <i class="fas fa-plus me-2"></i>Add New Article
        </a>
    </div>

    <div class="table-container">
        <table id="newsTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content Preview</th>
                    <th>Published Date</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);
                
                $conn = new mysqli('localhost', 'root', '', 'weather_app');

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT n.*, u.username as author FROM news n 
                        LEFT JOIN users u ON n.created_by = u.id 
                        ORDER BY n.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $content_preview = substr(strip_tags($row['content']), 0, 100) . '...';
                        echo '<tr>
                                <td class="align-middle">' . $row['id'] . '</td>
                                <td class="align-middle">' . htmlspecialchars($row['title']) . '</td>
                                <td class="align-middle">' . htmlspecialchars($content_preview) . '</td>
                                <td class="align-middle">' . date('Y-m-d', strtotime($row['published_date'])) . '</td>
                                <td class="align-middle">' . htmlspecialchars($row['author']) . '</td>
                                <td class="align-middle">' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>
                                <td class="align-middle">' . date('Y-m-d H:i', strtotime($row['updated_at'])) . '</td>
                                <td class="action-btns align-middle">
                                    <a href="edit_news.php?id=' . $row['id'] . '" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <a href="delete_news.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this article?\')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">No articles found</td></tr>';
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require('./footer.php') ?>
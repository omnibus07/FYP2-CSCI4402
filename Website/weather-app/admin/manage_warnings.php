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
    $('#warningTable').DataTable({
        order: [[4, 'desc']] // Sort by start_date by default
    });
});
</script>

<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="content-header">Weather Warnings</h2>
        <a href="add_warning.php" class="btn btn-primary" style="background-color: #0f0558; border-radius: 30px; color:#fff">
            <i class="fas fa-plus me-2"></i>Add New Warning
        </a>
    </div>

    <div class="table-container">
        <table id="warningTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Severity</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'weather_app');

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch weather warnings with location information
                $sql = "SELECT w.*, l.name as location_name 
                        FROM weather_warnings w 
                        LEFT JOIN locations l ON w.location_id = l.id 
                        ORDER BY w.start_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Determine severity badge color
                        $severityColor = '';
                        switch(strtolower($row['severity'])) {
                            case 'high':
                                $severityColor = 'danger';
                                break;
                            case 'medium':
                                $severityColor = 'warning';
                                break;
                            case 'low':
                                $severityColor = 'info';
                                break;
                            default:
                                $severityColor = 'secondary';
                        }

                        echo '<tr>
                                <td class="align-middle">' . $row['id'] . '</td>
                                <td class="align-middle">' . htmlspecialchars($row['title']) . '</td>
                                <td class="align-middle">' . substr(htmlspecialchars($row['description']), 0, 100) . '...</td>
                                <td class="align-middle">
                                    <span class="badge bg-' . $severityColor . '">' . htmlspecialchars($row['severity']) . '</span>
                                </td>
                                <td class="align-middle">' . date('Y-m-d H:i', strtotime($row['start_date'])) . '</td>
                                <td class="align-middle">' . date('Y-m-d H:i', strtotime($row['end_date'])) . '</td>
                                <td class="align-middle">' . htmlspecialchars($row['location_name']) . '</td>
                                <td class="action-btns align-middle">
                                    <a href="edit_warning.php?id=' . $row['id'] . '" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete_warning.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" 
                                       onclick="return confirm(\'Are you sure you want to delete this warning?\')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">No weather warnings found</td></tr>';
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require('./footer.php') ?>
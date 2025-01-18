<?php 
// Check for session errors
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']); // Clear the error after displaying
}

require('header.php') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#userTable').DataTable(); // Initialize DataTable
});
</script>

<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="content-header">Manage Users</h2>
        <a href="add_user.php" class="btn btn-primary" style="background-color: #0f0558; border-radius: 30px; color:#fff">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
    </div>

    <div class="table-container">
        <table id="userTable" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
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

                // Fetch users
                $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td class="align-middle">' . $row['id'] . '</td>
                                <td class="align-middle">' . $row['username'] . '</td>
                                <td class="align-middle">' . $row['email'] . '</td>
                                <td class="align-middle"><span class="badge bg-' . ($row['role'] == 'admin' ? 'danger' : 'primary') . '">' . $row['role'] . '</span></td>
                                <td class="align-middle">' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</td>
                                <td class="action-btns align-middle">
                                    <a href="edit_user.php?id=' . $row['id'] . '" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                    <a href="delete_user.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this user?\')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">No users found</td></tr>';
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php require('./footer.php') ?>
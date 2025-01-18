<?php require('./header.php') ?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateUniqueFileName($originalName)
{
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $ext;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'weather_app');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $conn->real_escape_string($_POST['role']);

    // Handle file upload
    $profile_photo = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['avatar']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (!in_array(strtolower($filetype), $allowed)) {
            echo "<script>alert('Only JPG, JPEG, PNG & GIF files are allowed!'); window.location.href='add_user.php';</script>";
            exit;
        }

        $uniqueName = generateUniqueFileName($filename);
        $upload_path = 'uploads/avatars/';

        // Create directory if it doesn't exist
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path . $uniqueName)) {
            $profile_photo = $uniqueName;
        } else {
            echo "<script>alert('Failed to upload file!'); ";
            exit;
        }
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='add_user.php';</script>";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format!'); window.location.href='add_user.php';</script>";
        exit;
    }

    // Check if username or email already exists
    $check_sql = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Username or email already exists!'); window.location.href='add_user.php';</script>";
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (username, email, password_hash, role, profile_photo) 
            VALUES ('$username', '$email', '$password_hash', '$role', " .
        ($profile_photo ? "'$profile_photo'" : "NULL") . ")";

    if ($conn->query($sql)) {
        $new_user_id = $conn->insert_id;
        header("Location: edit_user.php?id=$new_user_id");
        exit;
    } else {
        echo "<script>alert('Error creating user: " . $conn->error . "'); window.location.href='add_user.php';</script>";
    }

    $conn->close();
}
?>

<style>
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(15, 5, 88, 0.25);
    }

    .avatar-upload {
        position: relative;
        max-width: 200px;
        margin: 0 auto;
    }

    .avatar-edit {
        position: absolute;
        right: 12px;
        z-index: 1;
        top: 10px;
    }

    .avatar-edit input {
        display: none;
    }

    .avatar-edit label {
        display: inline-block;
        width: 34px;
        height: 34px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #FFFFFF;
        border: 1px solid transparent;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all .2s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-edit label:hover {
        background: #f1f1f1;
        border-color: #d6d6d6;
    }

    .avatar-preview {
        width: 192px;
        height: 192px;
        position: relative;
        border-radius: 100%;
        border: 6px solid #F8F8F8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }

    .avatar-preview>div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        background-image: url('uploads/avatars/default-avatar.png');
    }

    .content-header {
        color: var(--primary);
        border-bottom: 2px solid var(--primary);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        color: var(--primary);
        margin-bottom: 1rem;
        font-size: 1.1rem;
        font-weight: 600;
    }
</style>

<!-- Main Content -->
<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="content-header mb-0">Add New User</h2>
        <a href="manage_users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users List
        </a>
    </div>

    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Profile Photo Section -->
            <div class="form-section text-center">
                <h3 class="form-section-title">Profile Photo</h3>
                <div class="avatar-upload">
                    <div class="avatar-edit">
                        <input type='file' id="imageUpload" name="avatar" accept=".png, .jpg, .jpeg" />
                        <label for="imageUpload">
                            <i class="fas fa-pencil-alt"></i>
                        </label>
                    </div>
                    <div class="avatar-preview">
                        <div id="imagePreview"></div>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">Click the pencil icon to upload a profile photo</small>
            </div>

            <!-- Basic Information Section -->
            <div class="form-section">
                <h3 class="form-section-title">Basic Information</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="form-section">
                <h3 class="form-section-title">Security</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role Section -->
            <div class="form-section">
                <h3 class="form-section-title">Role & Permissions</h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required">Role</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user-shield"></i>
                            </span>
                            <select class="form-select" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="manage_users.php" class="btn btn-secondary btn-action">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-action">
                        <i class="fas fa-save me-2"></i>Add User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function readURL(input) {
if (input.files && input.files[0]) {
const reader = new FileReader();

reader.onload = function(e) {
$('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
$('#imagePreview').hide();
$('#imagePreview').fadeIn(650);
}
reader.readAsDataURL(input.files[0]);
}
}

$("#imageUpload").change(function() {
readURL(this);
});
</script>

<?php require('./footer.php') ?>
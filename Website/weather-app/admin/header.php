<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$current_page = basename($_SERVER['PHP_SELF']);

if (!$isLoggedIn && $current_page !== 'sign-in.php') {
    header('Location: sign-in.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MYRamalanCuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./template.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            width: 220px;
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: absolute;
            left: 20px;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 0.8rem 1rem;
            margin: 0.2rem 0;
            border-radius: 5px;
            display: block;
            text-decoration: none;
        }

        .sidebar .nav-link.active {
            background-color: #f0f0f0;
            color: var(--primary-color);
            font-weight: bold;
            border-left: 3px solid #007BFF;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="../images/logo.svg" alt="Logo" class="me-2">
            <div>
                <h4 class="mb-0">MYRamalanCuaca</h4><small>Malaysia Weather Dashboard</small>
            </div>
        </a>
        <div class="d-flex align-items-center gap-3">
        <?php if ($isLoggedIn): ?>
            <div class="search-box">
                <input type="search" placeholder="Search" class="text-white">
                <i class="fas fa-search text-white"></i>
            </div>
            <?php endif ?>
            <div class="dropdown">
                <a class="d-flex align-items-center" href="#" role="button" <?php echo $isLoggedIn ? 'data-bs-toggle="dropdown"' : 'data-bs-toggle="modal" data-bs-target="#loginModal"' ?>>
                    <?php if ($isLoggedIn): ?>
                        <img src="./uploads/avatars/<?php echo $_SESSION['user_avatar'] ?? '../images/user.png' ?>" alt="User"
                            class="rounded-circle border border-2" width="40" height="40">
                    <?php endif ?>
                </a>
                <?php if ($isLoggedIn): ?>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="./manage_profile.php">Manage Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="./logout.php">Logout</a></li>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </nav>

    <?php if ($isLoggedIn): ?>
    <div class="sidebar">
        <a href="./manage_users.php"
            class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
           Manage Users
        </a>
        <a href="./manage_news.php"
            class="nav-link <?php echo ($current_page == 'manage_news.php') ? 'active' : ''; ?>">
            Manage News
        </a>
        <a href="./manage_warnings.php"
            class="nav-link <?php echo ($current_page == 'manage_warnings.php') ? 'active' : ''; ?>">
            Manage Warnings
        </a>
        <a href="./logout.php" class="nav-link text-danger">
            Logout
        </a>
    </div>
    <?php endif; ?>

    <div class="side-nav">
        <a href="./dashboard.php" title="Home"><i class="fas fa-home fa-lg"></i></a>
        <a href="./map.php" title="Map"><i class="fas fa-map-marker-alt fa-lg"></i></a>
        <a href="./weather-info.php" title="Education"><i class="fas fa-graduation-cap fa-lg"></i></a>
        <a href="#" title="Analysis"><i class="fas fa-chart-line fa-lg"></i></a>
        <a href="#" title="Settings"><i class="fas fa-cog fa-lg"></i></a>
    </div>
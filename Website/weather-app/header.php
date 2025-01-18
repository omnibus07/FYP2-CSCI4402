<?php

session_start();
$isLoggedIn = isset($_SESSION['user_id']);

$location = isset($_GET['location']) ? htmlspecialchars($_GET['location']) : '';

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
        .search-box {
            position: relative;
        }

        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
            margin-top: 5px;
        }

        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            color: #333;
        }

        .suggestion-item:hover {
            background-color: #f5f5f5;
        }

        /* Scrollbar styling */
        .search-suggestions::-webkit-scrollbar {
            width: 8px;
        }

        .search-suggestions::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .search-suggestions::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .search-suggestions::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body>
    <?php require('./login-modal.php'); ?>
    <nav class="navbar">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="./images/logo.svg" alt="Logo" class="me-2">
            <div>
                <h4 class="mb-0">MYRamalanCuaca</h4><small>Malaysia Weather Dashboard</small>
            </div>
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="search-box">
                <input type="search" placeholder="Search" class="text-white">
                <i class="fas fa-search text-white"></i>
            </div>
            <div class="dropdown">
                <a class="d-flex align-items-center" href="#" role="button" <?php echo $isLoggedIn ? 'data-bs-toggle="dropdown"' : 'data-bs-toggle="modal" data-bs-target="#loginModal"' ?>>
                    <?php if ($isLoggedIn): ?>
                        <?php if (!empty($_SESSION['user_avatar'])): ?>
                            <img src="./admin/uploads/avatars/<?php echo $_SESSION['user_avatar']; ?>" alt="User"
                                class="rounded-circle border border-2" width="40" height="40">
                        <?php else: ?>
                            <img src="./images/user.png" alt="Guest" class="rounded-circle border border-2" width="40">
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="./images/user.png" alt="Guest" class="rounded-circle border border-2" width="40"
                                height="40">
                            <div class="text-muted small">Guest</div>
                        </div>
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

    <div class="side-nav">
        <a href="./dashboard.php" title="Home"><i class="fas fa-home fa-lg"></i></a>
        <a href="./map.php" title="Map"><i class="fas fa-map-marker-alt fa-lg"></i></a>
        <a href="./weather-info.php" title="Education"><i class="fas fa-graduation-cap fa-lg"></i></a>
    </div>

    <script src="./js/locations.js"></script>
    <script src="./js/search.js"></script>
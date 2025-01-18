<?php

session_start();
$isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MYRamalanCuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../template.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            <div class="search-box">
                <input type="search" placeholder="Search" class="text-white">
                <i class="fas fa-search text-white"></i>
            </div>
            <div class="dropdown">
                <a class="d-flex align-items-center" href="#" role="button" <?php echo $isLoggedIn ? 'data-bs-toggle="dropdown"' : 'data-bs-toggle="modal" data-bs-target="#loginModal"' ?>>
                    <?php if ($isLoggedIn): ?>
                        <img src="<?php echo $_SESSION['user_avatar'] ?? '../images/user.png' ?>" alt="User"
                            class="rounded-circle border border-2" width="40" height="40">
                    <?php else: ?>
                        <div class="text-center">
                            <img src="../images/user.png" alt="Guest" class="rounded-circle border border-2" width="40"
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
        <a href="#" title="Analysis"><i class="fas fa-chart-line fa-lg"></i></a>
        <a href="#" title="Settings"><i class="fas fa-cog fa-lg"></i></a>
    </div>
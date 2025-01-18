<?php require('./header.php'); ?>
<style>
    /* Main content area */
    .main-content {
    margin-left: 260px;
    padding: 20px;
    }

    /* Side navigation */
    .side-nav {
    position: fixed;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    padding: 1rem;
    border-radius: 15px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 100;
    }


    .tips-container {
    display: flex;
    gap: 2rem;
    padding: 1rem;
    }

    .tip-card {
    flex: 1;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .tip-card img {
    width: 100%;
    height: auto;
    object-fit: cover;
    }

    </style>

    <div class="container">
        <div class="row justify-content-center" style="height: 70vh;">
            <div class="col-md-6 col-lg-4 m-auto">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4" style="color: #0f0558;">Sign In</h2>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="./functions/process_login.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100"
                                style="background-color: #0f0558; border-radius: 30px;">
                                Sign In
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require('./footer.php') ?>
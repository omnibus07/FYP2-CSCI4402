<?php require('header.php'); ?>
<?php $current_page = basename($_SERVER['PHP_SELF']); ?>

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
        top: 100px;
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
        border-left: 3px solid var(--primary-color);
    }

    /* Main content area */
    .main-content {
        margin-left: 260px;
        padding: 20px;
    }

    /* Content area */
    .content-header {
        color: var(--primary-color);
        font-size: 1.5rem;
        padding-left: 1rem;
        border-left: 4px solid var(--primary-color);
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


<div class="sidebar">
    <a href="./weather-info.php" class="nav-link <?php echo ($current_page == 'weather-info.php') ? 'active' : ''; ?>">
        Weather Info
    </a>
    <a href="./weather-tips.php" class="nav-link <?php echo ($current_page == 'weather-tips.php') ? 'active' : ''; ?>">
        Weather Tips
    </a>
</div>

<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="content-header">Tips in Hot Season</h2>
    </div>

    <div class="row g-4">
        <!-- Weather Terminology Card -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body" style="background-color: #0f0558; color: white; border-radius: 8px;">
                    <h5 class="card-title mb-3">Weather Terminology</h5>
                    <div class="position-relative">
                        <img src="./images/edu_tips_1.png" alt="Weather Terminology" class="img-fluid rounded mb-2"
                            style="width: 100%; height: auto; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3">
                            <span class="badge bg-primary">For Everyone To Know</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Types of Extreme Weather Card -->
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body" style="border-radius: 8px;">
                    <h5 class="card-title mb-3">Types of Extreme Weather</h5>
                    <img src="./images/edu_tips_2.png" alt="Extreme Weather Types" class="img-fluid rounded mb-2"
                        style="width: 100%; height: auto; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Styles -->
<style>
    .card {
        transition: transform 0.3s ease-in-out;
        cursor: pointer;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        font-weight: 600;
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.5em 1em;
    }

    .main-content {
        padding: 2rem;
    }

    .list-unstyled li {
        margin-bottom: 0.5rem;
        color: #666;
    }
</style>

<!-- Optional JavaScript for animations and interactivity -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add click listeners to cards for navigation
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('click', function () {
                // Get the title of the card and create a URL-friendly string
                const title = this.querySelector('.card-title').textContent.toLowerCase().replace(/\s+/g, '-');
                window.location.href = `education/${title}.php`;
            });
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require('footer.php'); ?>
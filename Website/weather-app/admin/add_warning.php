<?php require('./header.php') ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'weather_app');

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Validate input data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $severity = $_POST['severity'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $location_id = $_POST['location_id'] ?? '';
        $affected_area = $_POST['affected_area'] ?? '';
        $created_by = $_SESSION['user_id'] ?? 1;

        // Basic validation
        if (empty($title) || empty($description) || empty($severity) || 
            empty($start_date) || empty($end_date) || empty($location_id)) {
            throw new Exception("All required fields must be filled out.");
        }

        // Validate dates
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        
        if (!$start_timestamp || !$end_timestamp) {
            throw new Exception("Invalid date format.");
        }

        if ($end_timestamp <= $start_timestamp) {
            throw new Exception("End date must be after start date.");
        }

        // Validate affected area JSON if provided
        if (!empty($affected_area)) {
            $decoded = json_decode($affected_area);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid affected area format.");
            }
        }

        // Validate severity
        $valid_severities = ['Low', 'Medium', 'High'];
        if (!in_array($severity, $valid_severities)) {
            throw new Exception("Invalid severity level.");
        }

        // Prepare the insert statement
        $sql = "INSERT INTO weather_warnings (
                    title, description, severity, start_date, end_date, 
                    location_id, affected_area, created_by, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sssssssi", 
            $title,
            $description,
            $severity,
            $start_date,
            $end_date,
            $location_id,
            $affected_area,
            $created_by
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $new_warning_id = $conn->insert_id;

        $_SESSION['success_message'] = "Warning created successfully!";
        header("Location: edit_warning.php?id=" . $new_warning_id);
        exit;

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: add_warning.php");
        exit;
    }
}
?>

<!-- Add Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

<style>
    .form-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .map-container {
        position: relative;
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .alert {
        margin: 1rem 0;
        padding: 1rem;
        border-radius: 4px;
    }
    .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
</style>

<!-- Main Content -->
<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="content-header">Add New Weather Warning</h2>
        <a href="manage_warnings.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Warnings List
        </a>
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control" name="title" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Location</label>
                    <select class="form-select" name="location_id" required>
                        <?php
                        // Fetch locations from database
                        $conn = new mysqli('localhost', 'root', '', 'weather_app');
                        $locations = $conn->query("SELECT id, name, latitude, longitude FROM locations ORDER BY name");
                        while ($location = $locations->fetch_assoc()) {
                            echo "<option value='" . $location['id'] . "' " .
                                 "data-lat='" . $location['latitude'] . "' " .
                                 "data-lng='" . $location['longitude'] . "'>" . 
                                 htmlspecialchars($location['name']) . "</option>";
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label class="form-label required">Affected Area</label>
                    <div class="map-container">
                        <div id="map"></div>
                        <div class="map-controls">
                            <button type="button" class="btn btn-sm btn-primary" id="clearDrawing">Clear Drawing</button>
                        </div>
                    </div>
                    <input type="hidden" name="affected_area" id="affected_area" value="">
                    <small class="text-muted">Draw the affected area on the map using the drawing tools</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Description</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label required">Severity</label>
                    <select class="form-select" name="severity" id="severity" required>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" name="start_date" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required">End Date & Time</label>
                    <input type="datetime-local" class="form-control" name="end_date" required>
                </div>
            </div>

            <div class="mt-4">
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="manage_warnings.php" class="btn btn-secondary btn-action">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-action">Create Warning</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

<script>
// Initialize the map
let map = L.map('map').setView([0, 0], 2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Initialize the FeatureGroup to store editable layers
let drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Initialize draw control
let drawControl = new L.Control.Draw({
    draw: {
        polygon: {
            shapeOptions: {
                color: '#ff0000'
            }
        },
        circle: false,
        circlemarker: false,
        marker: false,
        polyline: false,
        rectangle: true
    },
    edit: {
        featureGroup: drawnItems
    }
});
map.addControl(drawControl);

// Center map on selected location
function centerMapOnLocation() {
    let selectedOption = document.querySelector('select[name="location_id"] option:checked');
    let lat = selectedOption.getAttribute('data-lat');
    let lng = selectedOption.getAttribute('data-lng');
    
    if (lat && lng) {
        map.setView([lat, lng], 10);
    }
}

// Update area color based on severity
function getSeverityColor(severity) {
    switch(severity.toLowerCase()) {
        case 'high': return '#ff0000';
        case 'medium': return '#ff8c00';
        case 'low': return '#ffd700';
        default: return '#ff0000';
    }
}

// Handle location change
document.querySelector('select[name="location_id"]').addEventListener('change', centerMapOnLocation);

// Handle severity change
document.getElementById('severity').addEventListener('change', function(e) {
    drawnItems.eachLayer(layer => {
        layer.setStyle({
            color: getSeverityColor(e.target.value)
        });
    });
});

// Handle drawing events
map.on('draw:created', function(e) {
    drawnItems.clearLayers();
    let layer = e.layer;
    layer.setStyle({
        color: getSeverityColor(document.getElementById('severity').value)
    });
    drawnItems.addLayer(layer);
    updateAffectedArea();
});

map.on('draw:edited', function(e) {
    updateAffectedArea();
});

map.on('draw:deleted', function(e) {
    updateAffectedArea();
});

// Clear drawing button
document.getElementById('clearDrawing').addEventListener('click', function() {
    drawnItems.clearLayers();
    updateAffectedArea();
});

// Update the hidden input with GeoJSON data
function updateAffectedArea() {
    let geoJSON = drawnItems.toGeoJSON();
    document.getElementById('affected_area').value = JSON.stringify(geoJSON);
}

// Initial setup
centerMapOnLocation();
</script>

<?php require('./footer.php') ?>
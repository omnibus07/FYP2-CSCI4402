<?php require('./header.php') ?>
<?php
// Get warning ID from URL
$warning_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Database connection
$conn = new mysqli('localhost', 'root', '', 'weather_app');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT w.*, l.name as location_name, l.latitude, l.longitude 
        FROM weather_warnings w 
        LEFT JOIN locations l ON w.location_id = l.id 
        WHERE w.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $warning_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Warning not found!";
    header("Location: manage_warnings.php");
    exit;
}

$warning = $result->fetch_assoc();
$stmt->close();

// Debug output (remove in production)
if (isset($warning['affected_area'])) {
    error_log("Affected Area Data: " . $warning['affected_area']);
}

// <?php
// Place this code at the beginning of your file, where the form submission is handled
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $severity = $_POST['severity'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $location_id = $_POST['location_id'] ?? '';
        $affected_area = $_POST['affected_area'] ?? '';

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

        // Prepare the update statement
        $sql = "UPDATE weather_warnings SET 
                title = ?, 
                description = ?, 
                severity = ?, 
                start_date = ?, 
                end_date = ?, 
                location_id = ?, 
                affected_area = ?,
                updated_at = NOW()
                WHERE id = ?";

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
            $warning_id
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        if ($stmt->affected_rows === 0 && $stmt->errno === 0) {
            // No rows were updated, but no error occurred
            // This might happen if the data wasn't changed
            $_SESSION['warning_message'] = "No changes were made to the warning.";
        } else {
            $_SESSION['success_message'] = "Warning updated successfully!";
        }

        // Redirect with success message
        header("Location: edit_warning.php?id=$warning_id");
        exit;

    } catch (Exception $e) {
        // Store error message in session
        $_SESSION['error_message'] = $e->getMessage();
        
        // Log the error
        error_log("Error updating warning ID $warning_id: " . $e->getMessage());
        
        // Redirect back to the form
        header("Location: edit_warning.php?id=$warning_id");
        exit;
    }
}

// Add this at the top of your HTML to display messages
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['warning_message'])) {
    echo "<div class='alert alert-warning'>" . htmlspecialchars($_SESSION['warning_message']) . "</div>";
    unset($_SESSION['warning_message']);
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

    .severity-low { background-color: #ffd700; }
    .severity-medium { background-color: #ff8c00; }
    .severity-high { background-color: #ff0000; }
</style>

<!-- Main Content -->
<div class="main-content rounded" style="background:#fff; margin-right:1rem; min-height: 70vh; height:auto">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="content-header">Edit Weather Warning</h2>
        <a href="manage_warnings.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Warnings List
        </a>
    </div>
    
    <div class="form-container">
        <form method="POST" action="" id="warningForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Title</label>
                    <input type="text" class="form-control" name="title" 
                           value="<?php echo htmlspecialchars($warning['title']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label required">Location</label>
                    <select class="form-select" name="location_id" required>
                        <?php
                        // Fetch locations from database
                        $locations = $conn->query("SELECT id, name, latitude, longitude FROM locations ORDER BY name");
                        while ($location = $locations->fetch_assoc()) {
                            $selected = ($location['id'] == $warning['location_id']) ? 'selected' : '';
                            echo "<option value='" . $location['id'] . "' data-lat='" . $location['latitude'] . 
                                 "' data-lng='" . $location['longitude'] . "' $selected>" . 
                                 htmlspecialchars($location['name']) . "</option>";
                        }
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
                    <input type="hidden" name="affected_area" id="affected_area" 
                           value="<?php echo htmlspecialchars($warning['affected_area'] ?? ''); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label required">Description</label>
                    <textarea class="form-control" name="description" rows="4" required><?php 
                        echo htmlspecialchars($warning['description']); 
                    ?></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label required">Severity</label>
                    <select class="form-select" name="severity" id="severity" required>
                        <?php
                        $severities = ['Low', 'Medium', 'High'];
                        foreach ($severities as $severity) {
                            $selected = ($severity == $warning['severity']) ? 'selected' : '';
                            echo "<option value='$severity' $selected>$severity</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" name="start_date" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($warning['start_date'])); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label required">End Date & Time</label>
                    <input type="datetime-local" class="form-control" name="end_date" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($warning['end_date'])); ?>" required>
                </div>
            </div>

            <div class="mt-4">
                <hr>
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted">
                            Created: <?php echo date('Y-m-d H:i', strtotime($warning['created_at'])); ?><br>
                            Last Updated: <?php echo date('Y-m-d H:i', strtotime($warning['updated_at'])); ?>
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="manage_warnings.php" class="btn btn-secondary btn-action">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-action">Save Changes</button>
                    </div>
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

// Load existing affected area if available
let existingArea = document.getElementById('affected_area').value;
if (existingArea) {
    try {
        let geoJSON = JSON.parse(existingArea);
        L.geoJSON(geoJSON, {
            style: function(feature) {
                return {
                    color: getSeverityColor(document.getElementById('severity').value)
                };
            }
        }).eachLayer(layer => {
            drawnItems.addLayer(layer);
            map.fitBounds(layer.getBounds());
        });
    } catch (e) {
        console.error('Error loading existing area:', e);
    }
}

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

<?php 
$conn->close();
require('./footer.php');
?>
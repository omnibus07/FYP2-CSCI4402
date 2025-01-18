<?php require('header.php') ?>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$location = isset($_GET['location']) ? $_GET['location'] : 'Gombak';

$clean_location = $conn->real_escape_string($location);

$news_result = $conn->query("SELECT title,content,published_date FROM news ORDER BY published_date DESC LIMIT 3");

$news_result_2 = $conn->query("SELECT title,content,published_date FROM news ORDER BY published_date DESC LIMIT 3");

$warnings_result = $conn->query("
    SELECT w.*, l.name as location_name 
    FROM weather_warnings w 
    LEFT JOIN locations l ON w.location_id = l.id 
    WHERE l.name LIKE '%{$clean_location}%' 
    ORDER BY w.severity DESC, w.start_date DESC 
    LIMIT 3
");

$isLoggedIn = isset($_SESSION['user_id']);

?>

<style>
    .weather-time-selector {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .weather-time-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: inherit;
        transition: all 0.3s ease;
        padding: 8px;
    }

    .weather-time-btn span {
        font-size: 0.6rem;
        margin-top: 2px;
    }

    .weather-time-btn.active {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(1.1);
    }

    .weather-time-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<link href="./template.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<div class="main-content my-4">
    <ul class="nav nav-pills mb-3" id="weatherTabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#today">Today</button>
        </li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#forecast">7-day
                forecast</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="today">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="weather-card">
                        <div class="weather-info">
                            <h2 id="location">Loading...</h2>
                            <p id="timestamp" class="mb-4">As of 8:00 am</p>

                            <div class="weather-time-selector mb-3">
                                <button class="weather-time-btn active" data-time="now">
                                    <i class="fas fa-clock"></i>
                                    <span>Now</span>
                                </button>
                                <button class="weather-time-btn" data-time="morning">
                                    <i class="fas fa-sun"></i>
                                    <span>Morning</span>
                                </button>
                                <button class="weather-time-btn" data-time="afternoon">
                                    <i class="fas fa-cloud-sun"></i>
                                    <span>Afternoon</span>
                                </button>
                                <button class="weather-time-btn" data-time="night">
                                    <i class="fas fa-moon"></i>
                                    <span>Night</span>
                                </button>
                            </div>

                            <div class="temperature" id="temperature">--°C</div>
                            <div id="condition" class="h4">Loading...</div>
                        </div>
                    </div>

                </div>
                <div class="col-md mb-3">
                    <div class="news-card pb-0">
                        <h4><i class="fas fa-newspaper me-2"></i>News & Press Release</h4>
                        <div class="">
                            <?php while ($news = $news_result->fetch_assoc()): ?>
                                <div class="mt-4">
                                    <div class="custom-published-date" style="font-size:small;">
                                        <?= date('d M Y', strtotime($news['published_date'])) ?>
                                    </div>
                                    <p class="mt-2" style="font-size:smaller; font-weight: 700;">
                                        <?= htmlspecialchars($news['title']) ?>
                                    </p>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="forecast">
            <div class="row">
                <div class="col-8 mb-3">
                    <div class="d-flex g-3 overflow-auto" id="forecastContainer"
                        style="border-radius:15px;background:url('./images/7-days-forecast-background.png')">
                    </div>
                </div>
                <div class="col-md mb-3">
                    <div class="news-card pb-0">
                        <h4><i class="fas fa-newspaper me-2"></i>News & Press Release</h4>
                        <div class="">
                            <?php while ($news = $news_result_2->fetch_assoc()): ?>
                                <div class="mt-4">
                                    <div class="custom-published-date" style="font-size:small;">
                                        <?= date('d M Y', strtotime($news['published_date'])) ?>
                                    </div>
                                    <p class="mt-2" style="font-size:smaller; font-weight: 700;">
                                        <?= htmlspecialchars($news['title']) ?>
                                    </p>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="about-section" style="min-height:370px">
                <h4><i class="fas fa-info-circle me-2"></i>About</h4>
                <p style="font-size:small">Welcome to <strong style="font-weight:600;">MYRamalanCuaca</strong>, your
                    comprehensive source for
                    accurate and timely weather forecasts tailored specifically for Malaysia. Our mission is to
                    enhance public safety and preparedness by providing reliable weather information using
                    cutting-edge technology.</p>
                <p style="font-size:small" class="mt-3">At <strong style="font-weight:600;">MYRamalanCuaca</strong>, we
                    aim to protect public
                    safety through precise weather predictions. By leveraging advanced deep learning and machine
                    learning algorithms, we offer a modern, cost-effective approach to weather forecasting, ensuring
                    that you stay informed about extreme weather conditions.</p>
            </div>
        </div>
        <div class="col-md">
            <div class="warning-section" style="min-height:370px">
                <h4 class="mb-4">FORECASTED WARNINGS</h4>
                <div class="row g-3">
                    <?php if ($warnings_result->num_rows > 0):
                        while ($warning = $warnings_result->fetch_assoc()): ?>
                            <div class="col-md-4">
                                <div class="warning-card">
                                    <div class="severity-indicator severity-<?= strtolower($warning['severity']) ?>"></div>
                                    <h5 class="text-dark mb-3"><?= htmlspecialchars($warning['title']) ?></h5>
                                    <div class="p-2">
                                        <p class="small text-dark mb-2"><?= htmlspecialchars($warning['description']) ?></p>
                                        <small class="d-block text-dark mb-2">
                                            Location: <?= htmlspecialchars($warning['location_name']) ?>
                                        </small>
                                        <!-- Add individual map container -->
                                        <div class="warning-map" id="map-<?= $warning['id'] ?>"
                                            data-area='<?= htmlspecialchars($warning['affected_area'] ?? '') ?>'
                                            data-severity="<?= htmlspecialchars($warning['severity']) ?>"
                                            style="height: 150px; margin: 10px 0; border-radius: 8px;">
                                        </div>

                                        <small class="d-block text-dark">
                                            Until: <?= date('d M Y H:i', strtotime($warning['end_date'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div>No forecast warning found.</div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const defaultLocation = {
        name: <?php echo json_encode($location); ?>,
    };
    const API = {
        key: '3cae79ace9f209e67c57f2376ce5dfeb',
        city: defaultLocation.name + ',MY'
    };

    console.log(API.city)

    function getWeatherBackgroundClass(code) {
        if (code >= 200 && code < 300) return 'weather-thunderstorm';
        if (code >= 300 && code < 600) return 'weather-rain';
        if (code >= 700 && code < 800) return 'weather-mist';
        if (code === 800) return 'weather-clear';
        if (code > 800) return 'weather-clouds';
        return 'weather-clear';
    }

    function getWeatherIcon(e) {
        return e >= 200 && e < 300 ? '<i class="fas fa-bolt fa-2x text-warning"></i>' : e >= 300 && e < 600 ?
            '<i class="fas fa-cloud-rain fa-2x text-info"></i>' : e >= 600 && e < 700 ?
                '<i class="fas fa-snowflake fa-2x text-primary"></i>' : e >= 700 && e < 800 ?
                    '<i class="fas fa-smog fa-2x text-secondary"></i>' : 800 === e ?
                        '<i class="fas fa-sun fa-2x text-warning"></i>' : '<i class="fas fa-cloud fa-2x text-secondary"></i>'
    }
    async function getForecastData() {
        try {
            const e = await (await fetch(
                `https://api.openweathermap.org/data/2.5/forecast?q=${API.city}&units=metric&appid=${API.key}`
            )).json(),
                t = {};
            e.list.forEach(e => {
                const a = new Date(1e3 * e.dt).toLocaleDateString("en-US", {
                    weekday: "long"
                });
                t[a] ? t[a].temp_max = Math.max(t[a].temp_max, e.main.temp_max) : t[a] = {
                    temp_max: e.main.temp_max,
                    temp_min: e.main.temp_min,
                    weather: e.weather[0]
                }
            }), document.getElementById("forecastContainer").innerHTML = Object.entries(t).slice(0, 7).map(([e,
                t
            ]) =>
                `<div class="col-md-3 m-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body text-center px-0"><h5 class="mb-3">${e}</h5><div class="mb-3">${getWeatherIcon(t.weather.id)}</div><div class="temperature-range"><span class="h4 text-danger">${Math.round(t.temp_max)}°C</span><span class="mx-2">/</span><span class="h5 text-primary">${Math.round(t.temp_min)}°C</span></div><p class="mt-2 text-muted">${t.weather.main}</p></div></div></div>`
            ).join("")
        } catch (e) {
            console.error("Error:", e)
        }
    }

    async function getWeatherData() {
        try {
            const response = await fetch(
                `https://api.openweathermap.org/data/2.5/weather?q=${API.city}&units=metric&appid=${API.key}`);
            const data = await response.json();

        // Log the entire API response
        console.log("Current Weather Data:", data);

            // Update weather info
            document.getElementById("location").textContent = `${data.name}, Malaysia`;
            document.getElementById("temperature").textContent = `${Math.round(data.main.temp)}°C`;
            document.getElementById("condition").textContent = data.weather[0].main;
            document.getElementById("timestamp").textContent = `As of ${new Date().toLocaleTimeString()}`;

            // Update weather background
            const weatherCard = document.querySelector('.weather-card');
            weatherCard.classList.remove('weather-clear', 'weather-clouds', 'weather-rain', 'weather-thunderstorm',
                'weather-mist');
            const newWeatherClass = getWeatherBackgroundClass(data.weather[0].id);
            weatherCard.classList.add(newWeatherClass);

            // Update text color based on weather
            const weatherInfo = document.querySelector('.weather-info');
            if (newWeatherClass === 'weather-thunderstorm' || newWeatherClass === 'weather-rain') {
                weatherInfo.style.color = 'white';
                weatherInfo.style.textShadow = '1px 1px 3px rgba(0,0,0,0.8)';
            } else {
                weatherInfo.style.color = 'black';
                weatherInfo.style.textShadow = '1px 1px 3px rgba(255,255,255,0.8)';
            }
        } catch (e) {
            console.error("Error:", e);
            document.getElementById("location").textContent = "Error loading weather data";
        }
    }

    document.querySelectorAll('.weather-time-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.weather-time-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateWeatherTime(this.dataset.time);
        });
    });

    async function updateWeatherTime(timeOfDay) {
    const forecasts = await getForecastForTime(timeOfDay);

    // Update temperature and condition text
    document.getElementById('temperature').textContent = `${Math.round(forecasts.temp)}°C`;
    document.getElementById('condition').textContent = forecasts.condition;

    // Update weather-card background based on the condition
    const weatherCard = document.querySelector('.weather-card');
    weatherCard.classList.remove('weather-clear', 'weather-clouds', 'weather-rain', 'weather-thunderstorm', 'weather-mist');
    const newWeatherClass = getWeatherBackgroundClass(forecasts.conditionCode);
    weatherCard.classList.add(newWeatherClass);

    // Optionally, adjust text color for better readability
    const weatherInfo = document.querySelector('.weather-info');
    if (newWeatherClass === 'weather-thunderstorm' || newWeatherClass === 'weather-rain') {
        weatherInfo.style.color = 'white';
        weatherInfo.style.textShadow = '1px 1px 3px rgba(0,0,0,0.8)';
    } else {
        weatherInfo.style.color = 'black';
        weatherInfo.style.textShadow = '1px 1px 3px rgba(255,255,255,0.8)';
    }
}

async function getForecastForTime(timeOfDay) {
    const response = await fetch(
        `https://api.openweathermap.org/data/2.5/forecast?q=${API.city}&units=metric&appid=${API.key}`
    );
    const data = await response.json();

    const today = new Date().toISOString().split('T')[0];
    const times = {
        morning: '09:00:00',
        afternoon: '15:00:00',
        night: '21:00:00'
    };

    if (timeOfDay === 'now') {
        const currentWeather = await getCurrentWeather();
        return {
            temp: currentWeather.main.temp,
            condition: currentWeather.weather[0].main,
            conditionCode: currentWeather.weather[0].id
        };
    }

    const forecast = data.list.find(item =>
        item.dt_txt.includes(today) &&
        item.dt_txt.includes(times[timeOfDay])
    ) || data.list[0];

    return {
        temp: forecast.main.temp,
        condition: forecast.weather[0].main,
        conditionCode: forecast.weather[0].id
    };
}


    async function getCurrentWeather() {
        const response = await fetch(
            `https://api.openweathermap.org/data/2.5/weather?q=${API.city}&units=metric&appid=${API.key}`
        );
        return await response.json();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const e = document.querySelector('[data-bs-target="#forecast"]');
        e && e.addEventListener("shown.bs.tab", getForecastData), getWeatherData(), setInterval(getWeatherData,
            3e5)
    });
</script>

<style>
    .warning-card {
        position: relative;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .severity-indicator {
        height: 4px;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .severity-high {
        background-color: #ff0000;
    }

    .severity-medium {
        background-color: #ff8c00;
    }

    .severity-low {
        background-color: #ffd700;
    }

    .warning-map {
        border: 1px solid #ddd;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 200px !important;
    }
</style>

<script>
    // Initialize individual maps for each warning
    document.querySelectorAll('.warning-map').forEach(mapContainer => {
        const lat = parseFloat(mapContainer.dataset.lat) || 4.2105;
        const lng = parseFloat(mapContainer.dataset.lng) || 101.9758;
        const severity = mapContainer.dataset.severity;
        const affectedArea = mapContainer.dataset.area;

        // Create map
        const map = L.map(mapContainer.id, {
            zoomControl: false,
            dragging: false,
            scrollWheelZoom: false
        }).setView([lat, lng], 10);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker for location
        L.marker([lat, lng]).addTo(map);

        // Get color based on severity
        function getSeverityColor(severity) {
            switch (severity.toLowerCase()) {
                case 'high': return '#ff0000';
                case 'medium': return '#ff8c00';
                case 'low': return '#ffd700';
                default: return '#ff0000';
            }
        }
        if (affectedArea && affectedArea !== 'null') {
            try {
                const geoJSON = JSON.parse(affectedArea);
                const areaLayer = L.geoJSON(geoJSON, {
                    style: {
                        color: getSeverityColor(severity),
                        fillColor: getSeverityColor(severity),
                        weight: 2,
                        opacity: 0.8,
                        fillOpacity: 0.35
                    }
                }).addTo(map);

                // Fit bounds to affected area
                map.fitBounds(areaLayer.getBounds(), {
                    padding: [10, 10]
                });
            } catch (e) {
                console.error('Error parsing affected area:', e);
            }
        }

        // Add click handler to make map interactive
        mapContainer.addEventListener('click', () => {
            map.dragging.enable();
            map.scrollWheelZoom.enable();
            map.zoomControl.addTo(map);
            map.invalidateSize();
        });
    });
</script>

<?php $conn->close(); ?>

<?php require('footer.php') ?>
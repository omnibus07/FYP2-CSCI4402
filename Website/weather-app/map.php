<?php require('header.php') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .main-content {
        max-width: 1200px;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        border-radius: 15px;
    }

    .map-container {
        position: relative;
        width: 100%;
        height: 600px;
        border-radius: 15px;
        overflow: hidden;
    }

    #weatherMap,
    #eastMalaysiaMap {
        height: 100%;
        width: 100%;
        border-radius: 15px;
        z-index: 1;
    }

    .weather-details-floating {
        position: absolute;
        top: 120px;
        left: 20px;
        width: 280px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .weather-details-floating .card-body {
        padding: 15px;
    }

    .weather-details-floating .card-title {
        font-size: 1rem;
        color: #2c3e50;
        margin-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 8px;
    }

    .weather-details-content {
        font-size: 0.9rem;
    }

    .weather-details-content h5 {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .weather-details-content h5 i {
        color: #3498db;
    }

    .weather-details-content p {
        margin: 5px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .weather-details-content i {
        width: 20px;
        text-align: center;
        color: #7f8c8d;
    }

    .weather-marker {
        background: white;
        border-radius: 50%;
        padding: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        width: 60px;
        height: 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .weather-marker:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .weather-marker i {
        display: block;
        text-align: center;
        font-size: 1.2rem;
        margin-bottom: 2px;
    }

    .weather-marker span {
        display: block;
        text-align: center;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .nav-pills .nav-link {
        color: #2c3e50;
        border-radius: 10px;
        padding: 8px 20px;
        transition: all 0.3s ease;
        margin-right: 10px;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        background-color: #3498db;
        color: white;
        box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
    }

    .weather-condition {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        background: #f0f3f6;
        color: #2c3e50;
        margin-top: 5px;
    }

    .temperature-value {
        font-size: 1.2rem;
        font-weight: bold;
        color: #e74c3c;
    }

    .feels-like-value {
        color: #3498db;
    }

    /* Animation for weather details */
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .weather-details-floating {
        animation: slideIn 0.3s ease-out;
    }
</style>

<div class="main-content my-4">
    <ul class="nav nav-pills mb-3" id="weatherTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#peninsular">
                <i class="fas fa-map-marked-alt me-2"></i>Peninsular
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#eastMalaysia">
                <i class="fas fa-map-marked-alt me-2"></i>Sabah & Sarawak
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Peninsular Malaysia Tab -->
        <div class="tab-pane fade show active" id="peninsular">
            <div class="map-container">
                <div class="weather-details-floating card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="fas fa-cloud-sun me-2"></i>Weather Details
                        </h4>
                        <div id="peninsularWeather">
                            <p><i class="fas fa-map-marker-alt"></i>Click on a location to see weather details</p>
                        </div>
                    </div>
                </div>
                <div id="weatherMap"></div>
            </div>
        </div>

        <!-- Sabah & Sarawak Tab -->
        <div class="tab-pane fade" id="eastMalaysia">
            <div class="map-container">
                <div class="weather-details-floating card">
                    <div class="card-body">
                        <h4 class="card-title">
                            <i class="fas fa-cloud-sun me-2"></i>Weather Details
                        </h4>
                        <div id="eastMalaysiaWeather">
                            <p><i class="fas fa-map-marker-alt"></i>Click on a location to see weather details</p>
                        </div>
                    </div>
                </div>
                <div id="eastMalaysiaMap"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const API = { key: '3cae79ace9f209e67c57f2376ce5dfeb' };

    const peninsularCities = [
        { name: "Kuala Lumpur", lat: 3.1390, lng: 101.6869 },
        { name: "George Town", lat: 5.4141, lng: 100.3288 },
        { name: "Ipoh", lat: 4.5975, lng: 101.0901 },
        { name: "Johor Bahru", lat: 1.4927, lng: 103.7414 },
        { name: "Melaka", lat: 2.1896, lng: 102.2501 },
        { name: "Petaling Jaya", lat: 3.1067, lng: 101.6056 },
        { name: "Shah Alam", lat: 3.0733, lng: 101.5185 },
        { name: "Kuantan", lat: 3.8168, lng: 103.3317 },
        { name: "Alor Setar", lat: 6.1264, lng: 100.3673 },
        { name: "Kuala Terengganu", lat: 5.3302, lng: 103.1408 },
        { name: "Perlis", lat: 6.4414, lng: 100.1986 },
        { name: "Kelantan", lat: 6.1333, lng: 102.2386 },
        { name: "Negeri Sembilan", lat: 2.7297, lng: 101.9381 }
    ];

    const eastMalaysiaCities = [
        { name: "Kota Kinabalu", lat: 5.9804, lng: 116.0735 },
        { name: "Kuching", lat: 1.5535, lng: 110.3593 },
        // { name: "Miri", lat: 4.3995, lng: 113.9914 },
        // { name: "Sandakan", lat: 5.8402, lng: 118.1179 },
        // { name: "Sibu", lat: 2.2873, lng: 111.8307 },
        // { name: "Bintulu", lat: 3.1714, lng: 113.0374 },
        // { name: "Tawau", lat: 4.2498, lng: 117.8871 },
        // { name: "Lahad Datu", lat: 5.0269, lng: 118.3271 },
        // { name: "Kota Samarahan", lat: 1.4590, lng: 110.4273 },
        // { name: "Labuan", lat: 5.2831, lng: 115.2308 }
    ];

    let peninsularMap, eastMalaysiaMap;
    let peninsularMarkers = [], eastMalaysiaMarkers = [];

    function getWeatherIcon(code) {
        return code >= 200 && code < 300 ? '<i class="fas fa-bolt text-warning"></i>'
            : code >= 300 && code < 600 ? '<i class="fas fa-cloud-rain text-info"></i>'
                : code >= 600 && code < 700 ? '<i class="fas fa-snowflake text-primary"></i>'
                    : code >= 700 && code < 800 ? '<i class="fas fa-smog text-secondary"></i>'
                        : code === 800 ? '<i class="fas fa-sun text-warning"></i>'
                            : '<i class="fas fa-cloud text-secondary"></i>';
    }

    function initMaps() {
        peninsularMap = L.map('weatherMap').setView([4.2105, 101.9758], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(peninsularMap);
        peninsularCities.forEach(city => getWeatherForCity(city, 'peninsular'));

        eastMalaysiaMap = L.map('eastMalaysiaMap').setView([3.7833, 115.4833], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(eastMalaysiaMap);
    }

    async function getWeatherForCity(city, region) {
        try {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${city.lat}&lon=${city.lng}&units=metric&appid=${API.key}`);
            const data = await response.json();

            const markerHtml = `
            <div class="weather-marker">
                ${getWeatherIcon(data.weather[0].id)}
                <span>${Math.round(data.main.temp)}°C</span>
            </div>`;

            const icon = L.divIcon({
                html: markerHtml,
                className: 'weather-marker-container'
            });

            const marker = L.marker([city.lat, city.lng], { icon })
                .addTo(region === 'peninsular' ? peninsularMap : eastMalaysiaMap)
                .on('click', () => showWeatherDetails(city.name, data, region));

            if (region === 'peninsular') {
                peninsularMarkers.push(marker);
            } else {
                eastMalaysiaMarkers.push(marker);
            }
        } catch (error) {
            console.error(`Error fetching weather for ${city.name}:`, error);
        }
    }

    function showWeatherDetails(cityName, weatherData, region) {
        const detailsContainer = region === 'peninsular' ? 'peninsularWeather' : 'eastMalaysiaWeather';
        const details = document.getElementById(detailsContainer);
        details.innerHTML = `
        <div class="weather-details-content">
            <h5>
                <i class="fas fa-map-marker-alt"></i>
                <a href="dashboard.php?location=${cityName}" style="text-decoration:none">${cityName}</a>
            </h5>
            <p>
                <i class="fas fa-thermometer-half"></i>
                <span class="temperature-value">${Math.round(weatherData.main.temp)}°C</span>
            </p>
            <p>
                <i class="fas fa-temperature-low"></i>
                <span class="feels-like-value">Feels like: ${Math.round(weatherData.main.feels_like)}°C</span>
            </p>
            <p>
                <i class="fas fa-tint"></i>
                Humidity: ${weatherData.main.humidity}%
            </p>
            <p>
                <i class="fas fa-wind"></i>
                Wind: ${weatherData.wind.speed} m/s
            </p>
            <div class="weather-condition">
                ${getWeatherIcon(weatherData.weather[0].id)}
                ${weatherData.weather[0].description}
            </div>
        </div>`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMaps();

        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function () {
                if (this.getAttribute('data-bs-target') === '#peninsular') {
                    peninsularMap.invalidateSize();
                    peninsularMarkers.forEach(marker => marker.remove());
                    peninsularMarkers = [];
                    peninsularCities.forEach(city => getWeatherForCity(city, 'peninsular'));
                } else {
                    eastMalaysiaMap.invalidateSize();
                    eastMalaysiaMarkers.forEach(marker => marker.remove());
                    eastMalaysiaMarkers = [];
                    eastMalaysiaCities.forEach(city => getWeatherForCity(city, 'eastMalaysia'));
                }
            });
        });

        setInterval(() => {
            peninsularMarkers.forEach(marker => marker.remove());
            eastMalaysiaMarkers.forEach(marker => marker.remove());
            peninsularMarkers = [];
            eastMalaysiaMarkers = [];
            peninsularCities.forEach(city => getWeatherForCity(city, 'peninsular'));
            eastMalaysiaCities.forEach(city => getWeatherForCity(city, 'eastMalaysia'));
        }, 300000);
    });
</script>
<?php require('footer.php'); ?>
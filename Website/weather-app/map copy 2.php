<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>MYRamalanCuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary: #0f0558;
            --secondary: #0d47a1
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5
        }

        .navbar {
            padding: 1rem 6rem;
            border-bottom: 1px solid #eee
        }

        .navbar-brand {
            color: var(--primary) !important
        }

        .navbar-brand img {
            width: 50px;
            height: 50px
        }

        .search-box {
            background: var(--primary);
            border-radius: 25px;
            padding: .5rem 1rem;
            width: 300px;
            display: flex;
            align-items: center
        }

        .search-box input {
            background: 0 0;
            border: none;
            color: #fff;
            width: calc(100% - 20px)
        }

        .search-box input::placeholder {
            color: rgba(255, 255, 255, .7)
        }

        .search-box input:focus {
            outline: 0
        }

        .side-nav {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #fff;
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1)
        }

        .side-nav a {
            display: block;
            color: var(--primary);
            text-decoration: none;
            margin: 1rem 0;
            text-align: center
        }

        footer {
            background: var(--primary);
            color: #fff;
            padding: 2rem 0;
            margin-top: 2rem
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 15px
        }

        .weather-marker {
            background: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2)
        }

        .weather-marker i {
            display: block;
            text-align: center
        }

        .weather-marker span {
            display: block;
            text-align: center;
            margin-top: 5px;
            font-weight: bold
        }
    </style>
</head>

<body>
    <nav class="navbar"><a class="navbar-brand d-flex align-items-center" href="#"><img src="./images/logo.svg"
                alt="Logo" class="me-2">
            <div>
                <h4 class="mb-0">MYRamalanCuaca</h4><small>Malaysia Weather Dashboard</small>
            </div>
        </a>
        <div class="d-flex align-items-center gap-3">
            <div class="search-box"><input type="search" placeholder="Search" class="text-white"><i
                    class="fas fa-search text-white"></i></div>
        </div>
    </nav>
    <div class="side-nav"><a href="#" title="Home"><i class="fas fa-home fa-lg"></i></a><a href="#" title="Map"><i
                class="fas fa-map-marker-alt fa-lg"></i></a><a href="#" title="Education"><i
                class="fas fa-graduation-cap fa-lg"></i></a><a href="#" title="Analysis"><i
                class="fas fa-chart-line fa-lg"></i></a><a href="#" title="Settings"><i
                class="fas fa-cog fa-lg"></i></a></div>
    <div class="main-content my-4">
        <ul class="nav nav-pills mb-3" id="weatherTabs">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#map">Weather
                    Map</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#forecast">7-day
                    forecast</button></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="map">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <div id="weatherMap" style="height:500px;border-radius:15px"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="weather-details card">
                            <div class="card-body">
                                <h4 class="card-title">Weather Details</h4>
                                <div id="selectedLocationWeather">
                                    <p>Click on a location to see weather details</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="forecast">
                <div class="row">
                    <div class="col-8 mb-3">
                        <div class="d-flex g-3 overflow-auto" id="forecastContainer"
                            style="border-radius:15px;background:url('./images/7-days-forecast-background.png')"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 style="color:#EE921C">LINK</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">myGovernment</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Open Data Portal</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 style="color:#EE921C">CONTACT US</h5>
                    <ul class="list-unstyled">
                        <li>Address</li>
                        <li>Location Map</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>const API = { key: '3cae79ace9f209e67c57f2376ce5dfeb' }; const cities = [{ name: "Kuala Lumpur", lat: 3.1390, lng: 101.6869 }, { name: "George Town", lat: 5.4141, lng: 100.3288 }, { name: "Ipoh", lat: 4.5975, lng: 101.0901 }, { name: "Johor Bahru", lat: 1.4927, lng: 103.7414 }, { name: "Kota Kinabalu", lat: 5.9804, lng: 116.0735 }, { name: "Kuching", lat: 1.5535, lng: 110.3593 }, { name: "Melaka", lat: 2.1896, lng: 102.2501 }, { name: "Miri", lat: 4.3995, lng: 113.9914 }, { name: "Petaling Jaya", lat: 3.1067, lng: 101.6056 }, { name: "Shah Alam", lat: 3.0733, lng: 101.5185 }]; let map, markers = []; function getWeatherIcon(e) { return e >= 200 && e < 300 ? '<i class="fas fa-bolt fa-2x text-warning"></i>' : e >= 300 && e < 600 ? '<i class="fas fa-cloud-rain fa-2x text-info"></i>' : e >= 600 && e < 700 ? '<i class="fas fa-snowflake fa-2x text-primary"></i>' : e >= 700 && e < 800 ? '<i class="fas fa-smog fa-2x text-secondary"></i>' : 800 === e ? '<i class="fas fa-sun fa-2x text-warning"></i>' : '<i class="fas fa-cloud fa-2x text-secondary"></i>' } function initMap() { map = L.map('weatherMap').setView([4.2105, 101.9758], 6); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map); cities.forEach(city => getWeatherForCity(city)) } async function getWeatherForCity(city) { try { const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${city.lat}&lon=${city.lng}&units=metric&appid=${API.key}`); const data = await response.json(); const markerHtml = `<div class="weather-marker">${getWeatherIcon(data.weather[0].id)}<span>${Math.round(data.main.temp)}°C</span></div>`; const icon = L.divIcon({ html: markerHtml, className: 'weather-marker-container' }); const marker = L.marker([city.lat, city.lng], { icon }).addTo(map).on('click', () => showWeatherDetails(city.name, data)); markers.push(marker) } catch (error) { console.error(`Error fetching weather for ${city.name}:`, error) } } function showWeatherDetails(cityName, weatherData) { const details = document.getElementById('selectedLocationWeather'); details.innerHTML = `<h5>${cityName}</h5><div class="weather-details-content"><p><strong>Temperature:</strong> ${Math.round(weatherData.main.temp)}°C</p><p><strong>Feels Like:</strong> ${Math.round(weatherData.main.feels_like)}°C</p><p><strong>Humidity:</strong> ${weatherData.main.humidity}%</p><p><strong>Wind Speed:</strong> ${weatherData.wind.speed} m/s</p><p><strong>Conditions:</strong> ${weatherData.weather[0].description}</p></div>` } async function getForecastData() { try { const e = await (await fetch(`https://api.openweathermap.org/data/2.5/forecast?q=Kuala Lumpur,MY&units=metric&appid=${API.key}`)).json(), t = {}; e.list.forEach(e => { const a = new Date(1e3 * e.dt).toLocaleDateString("en-US", { weekday: "long" }); t[a] ? t[a].temp_max = Math.max(t[a].temp_max, e.main.temp_max) : t[a] = { temp_max: e.main.temp_max, temp_min: e.main.temp_min, weather: e.weather[0] } }), document.getElementById("forecastContainer").innerHTML = Object.entries(t).slice(0, 7).map(([e, t]) => `<div class="col-md-3 m-3"><div class="card h-100 border-0 shadow-sm"><div class="card-body text-center px-0"><h5 class="mb-3">${e}</h5><div class="mb-3">${getWeatherIcon(t.weather.id)}</div><div class="temperature-range"><span class="h4 text-danger">${Math.round(t.temp_max)}°C</span><span class="mx-2">/</span><span class="h5 text-primary">${Math.round(t.temp_min)}°C</span></div><p class="mt-2 text-muted">${t.weather.main}</p></div></div></div>`).join("") } catch (e) { console.error("Error:", e) } } document.addEventListener('DOMContentLoaded', function () { initMap(); const e = document.querySelector('[data-bs-target="#forecast"]'); e && e.addEventListener("shown.bs.tab", getForecastData); setInterval(() => { markers.forEach(marker => marker.remove()); markers = []; cities.forEach(city => getWeatherForCity(city)) }, 300000) });</script>
</body>

</html>
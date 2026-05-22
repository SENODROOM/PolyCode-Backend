<?php
// Weather App - Main Application

// Configuration
define('APP_NAME', 'Weather App');
define('APP_VERSION', '1.0.0');
define('WEATHER_API_KEY', 'demo_key'); // Replace with actual OpenWeatherMap API key
define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5');
define('GEO_API_URL', 'https://api.openweathermap.org/geo/1.0');
define('CACHE_DIR', __DIR__ . '/cache');

// Start session
session_start();

// Cache class
class Cache {
    private $cacheDir;
    private $defaultTTL;
    
    public function __construct() {
        $this->cacheDir = CACHE_DIR;
        $this->defaultTTL = 600; // 10 minutes default
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function set($key, $data, $ttl = null) {
        $ttl = $ttl ?? $this->defaultTTL;
        $filename = $this->getFilename($key);
        $expiry = time() + $ttl;
        
        $cacheData = [
            'data' => $data,
            'expiry' => $expiry,
            'created' => time()
        ];
        
        return file_put_contents($filename, serialize($cacheData)) !== false;
    }
    
    public function get($key) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data === false) {
            return null;
        }
        
        if (time() > $data['expiry']) {
            $this->delete($key);
            return null;
        }
        
        return $data['data'];
    }
    
    public function isExpired($key) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return true;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data === false) {
            return true;
        }
        
        return time() > $data['expiry'];
    }
    
    private function getFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
}

// Weather class
class Weather {
    private $apiKey;
    private $cache;
    
    public function __construct() {
        $this->apiKey = WEATHER_API_KEY;
        $this->cache = new Cache();
    }
    
    public function getCurrentWeather($location, $units = 'metric', $lang = 'en') {
        $cacheKey = "current_{$location}_{$units}_{$lang}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            return array_merge($cached, ['cached' => true]);
        }
        
        // Make API request
        $url = WEATHER_API_URL . "/weather";
        $params = [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => $units,
            'lang' => $lang
        ];
        
        $response = $this->makeAPIRequest($url, $params);
        
        if ($response['success']) {
            // Cache the response
            $this->cache->set($cacheKey, $response, 600); // Cache for 10 minutes
        }
        
        return array_merge($response, ['cached' => false]);
    }
    
    public function getForecast($location, $days = 5, $units = 'metric', $lang = 'en') {
        $cacheKey = "forecast_{$location}_{$days}_{$units}_{$lang}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            return array_merge($cached, ['cached' => true]);
        }
        
        // Make API request
        $url = WEATHER_API_URL . "/forecast";
        $params = [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => $units,
            'lang' => $lang
        ];
        
        $response = $this->makeAPIRequest($url, $params);
        
        if ($response['success']) {
            // Process forecast data
            $processedForecast = $this->processForecastData($response['data'], $days);
            
            $finalResponse = [
                'success' => true,
                'data' => $processedForecast,
                'timestamp' => date('c')
            ];
            
            // Cache the response
            $this->cache->set($cacheKey, $finalResponse, 1800); // Cache for 30 minutes
            
            return array_merge($finalResponse, ['cached' => false]);
        }
        
        return array_merge($response, ['cached' => false]);
    }
    
    public function searchLocations($query, $limit = 5) {
        $cacheKey = "search_{$query}_{$limit}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            return array_merge($cached, ['cached' => true]);
        }
        
        // Make API request
        $url = GEO_API_URL . "/direct";
        $params = [
            'q' => $query,
            'limit' => $limit,
            'appid' => $this->apiKey
        ];
        
        $response = $this->makeAPIRequest($url, $params);
        
        if ($response['success']) {
            // Cache the response
            $this->cache->set($cacheKey, $response, 3600); // Cache for 1 hour
        }
        
        return array_merge($response, ['cached' => false]);
    }
    
    private function makeAPIRequest($url, $params) {
        $fullUrl = $url . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'WeatherApp/1.0');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'Network error',
                'message' => $error
            ];
        }
        
        if ($httpCode !== 200) {
            return [
                'success' => false,
                'error' => 'API error',
                'message' => "HTTP status: $httpCode"
            ];
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'Data error',
                'message' => 'Invalid JSON response'
            ];
        }
        
        // Check for API errors
        if (isset($data['cod']) && $data['cod'] !== 200) {
            return [
                'success' => false,
                'error' => 'API error',
                'message' => $data['message'] ?? 'Unknown API error'
            ];
        }
        
        return [
            'success' => true,
            'data' => $data,
            'timestamp' => date('c')
        ];
    }
    
    private function processForecastData($data, $days) {
        $forecast = [];
        $dailyData = [];
        
        // Group forecast data by day
        foreach ($data['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);
            
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [];
            }
            
            $dailyData[$date][] = $item;
        }
        
        // Process each day
        $dayCount = 0;
        foreach ($dailyData as $date => $dayItems) {
            if ($dayCount >= $days) {
                break;
            }
            
            $dayData = $this->processDayData($dayItems);
            $dayData['date'] = $date;
            $forecast[] = $dayData;
            
            $dayCount++;
        }
        
        return [
            'location' => [
                'name' => $data['city']['name'],
                'country' => $data['city']['country'],
                'lat' => $data['city']['coord']['lat'],
                'lon' => $data['city']['coord']['lon']
            ],
            'forecast' => $forecast
        ];
    }
    
    private function processDayData($dayItems) {
        $temps = [];
        $humidity = [];
        $windSpeeds = [];
        $weatherCounts = [];
        
        foreach ($dayItems as $item) {
            $temps[] = $item['main']['temp'];
            $humidity[] = $item['main']['humidity'];
            $windSpeeds[] = $item['wind']['speed'];
            
            $weather = $item['weather'][0]['main'];
            if (!isset($weatherCounts[$weather])) {
                $weatherCounts[$weather] = 0;
            }
            $weatherCounts[$weather]++;
        }
        
        // Get most common weather
        arsort($weatherCounts);
        $mostCommonWeather = key($weatherCounts);
        
        // Find the weather item that matches the most common weather
        $representativeItem = null;
        foreach ($dayItems as $item) {
            if ($item['weather'][0]['main'] === $mostCommonWeather) {
                $representativeItem = $item;
                break;
            }
        }
        
        return [
            'temperature' => [
                'min' => min($temps),
                'max' => max($temps),
                'avg' => array_sum($temps) / count($temps)
            ],
            'weather' => [
                'main' => $representativeItem['weather'][0]['main'],
                'description' => $representativeItem['weather'][0]['description'],
                'icon' => $representativeItem['weather'][0]['icon']
            ],
            'humidity' => array_sum($humidity) / count($humidity),
            'wind' => [
                'speed' => array_sum($windSpeeds) / count($windSpeeds),
                'direction' => $representativeItem['wind']['deg'] ?? 0
            ],
            'precipitation' => [
                'probability' => ($representativeItem['pop'] ?? 0) * 100,
                'amount' => ($representativeItem['rain']['3h'] ?? 0)
            ]
        ];
    }
}

// Initialize classes
$weather = new Weather();

// Handle requests
$action = $_GET['action'] ?? 'home';
$message = '';
$error = '';

// Handle API requests
if ($action === 'api') {
    header('Content-Type: application/json');
    
    $apiAction = $_GET['api_action'] ?? '';
    
    switch ($apiAction) {
        case 'current':
            $location = $_GET['location'] ?? 'New York';
            $units = $_GET['units'] ?? 'metric';
            $response = $weather->getCurrentWeather($location, $units);
            echo json_encode($response);
            break;
            
        case 'forecast':
            $location = $_GET['location'] ?? 'New York';
            $days = isset($_GET['days']) ? (int)$_GET['days'] : 5;
            $units = $_GET['units'] ?? 'metric';
            $response = $weather->getForecast($location, $days, $units);
            echo json_encode($response);
            break;
            
        case 'search':
            $query = $_GET['q'] ?? '';
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $response = $weather->searchLocations($query, $limit);
            echo json_encode($response);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid API action']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0984e3;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: 1.1rem;
        }

        .search-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #0984e3;
        }

        .btn {
            padding: 15px 25px;
            background: #0984e3;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #0770c4;
        }

        .btn-secondary {
            background: #636e72;
        }

        .btn-secondary:hover {
            background: #556270;
        }

        .weather-display {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .weather-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .current-weather {
            grid-column: span 2;
        }

        .weather-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .temperature {
            font-size: 3rem;
            font-weight: bold;
            color: #0984e3;
            margin-bottom: 10px;
        }

        .weather-description {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 20px;
            text-transform: capitalize;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .detail-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .detail-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0984e3;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .forecast-day {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .forecast-day:hover {
            transform: translateY(-5px);
        }

        .forecast-date {
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .forecast-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .forecast-temp {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0984e3;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: white;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .popular-cities {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .city-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .city-btn {
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .city-btn:hover {
            background: #0984e3;
            color: white;
            border-color: #0984e3;
        }

        @media (max-width: 768px) {
            .weather-display {
                grid-template-columns: 1fr;
            }
            
            .current-weather {
                grid-column: span 1;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .city-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">🌤️ <?= APP_NAME ?></div>
            <div class="subtitle">Real-time weather information and forecasts</div>
        </header>

        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <div class="search-section">
            <form method="get" class="search-form">
                <input type="hidden" name="action" value="home">
                <input type="text" name="location" class="search-input" placeholder="Enter city name..." value="<?= htmlspecialchars($_GET['location'] ?? '') ?>" required>
                <select name="units" style="padding: 15px; border: 2px solid #e1e8ed; border-radius: 10px;">
                    <option value="metric" <?= ($_GET['units'] ?? 'metric') === 'metric' ? 'selected' : '' ?>>°C</option>
                    <option value="imperial" <?= ($_GET['units'] ?? '') === 'imperial' ? 'selected' : '' ?>>°F</option>
                </select>
                <button type="submit" class="btn">Get Weather</button>
            </form>
            
            <div id="searchSuggestions" style="display: none;">
                <!-- Search suggestions will be populated here -->
            </div>
        </div>

        <div id="weatherContent">
            <?php
            $location = $_GET['location'] ?? null;
            $units = $_GET['units'] ?? 'metric';
            
            if ($location) {
                // Get current weather
                $currentWeather = $weather->getCurrentWeather($location, $units);
                $forecast = $weather->getForecast($location, 5, $units);
                
                if ($currentWeather['success'] && $forecast['success']) {
                    include 'views/weather_display.php';
                } else {
                    echo '<div class="message error">' . htmlspecialchars($currentWeather['message'] ?? 'Failed to get weather data') . '</div>';
                }
            } else {
                include 'views/home.php';
            }
            ?>
        </div>

        <div class="popular-cities">
            <h3>Popular Cities</h3>
            <div class="city-buttons">
                <button class="city-btn" onclick="getWeather('New York')">New York</button>
                <button class="city-btn" onclick="getWeather('London')">London</button>
                <button class="city-btn" onclick="getWeather('Tokyo')">Tokyo</button>
                <button class="city-btn" onclick="getWeather('Paris')">Paris</button>
                <button class="city-btn" onclick="getWeather('Sydney')">Sydney</button>
                <button class="city-btn" onclick="getWeather('Mumbai')">Mumbai</button>
                <button class="city-btn" onclick="getWeather('Beijing')">Beijing</button>
                <button class="city-btn" onclick="getWeather('Moscow')">Moscow</button>
            </div>
        </div>
    </div>

    <script>
        function getWeather(location) {
            const units = document.querySelector('select[name="units"]').value;
            window.location.href = `?action=home&location=${encodeURIComponent(location)}&units=${units}`;
        }

        function searchLocations(query) {
            if (query.length < 2) {
                document.getElementById('searchSuggestions').style.display = 'none';
                return;
            }

            fetch(`?action=api&api_action=search&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        let suggestions = '<div style="background: white; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 10px;">';
                        
                        data.data.forEach(location => {
                            suggestions += `<div style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;" onclick="selectLocation('${location.name}, ${location.country}')">`;
                            suggestions += `<strong>${location.name}</strong>, ${location.country}`;
                            suggestions += `</div>`;
                        });
                        
                        suggestions += '</div>';
                        document.getElementById('searchSuggestions').innerHTML = suggestions;
                        document.getElementById('searchSuggestions').style.display = 'block';
                    } else {
                        document.getElementById('searchSuggestions').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function selectLocation(location) {
            document.querySelector('input[name="location"]').value = location;
            document.getElementById('searchSuggestions').style.display = 'none';
        }

        // Add search input event listener
        document.querySelector('input[name="location"]').addEventListener('input', function(e) {
            searchLocations(e.target.value);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.matches('input[name="location"]') && !e.target.closest('#searchSuggestions')) {
                document.getElementById('searchSuggestions').style.display = 'none';
            }
        });
    </script>
</body>
</html>

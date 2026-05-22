# Project 5: Weather App 🌤️

A weather application that integrates with external APIs to display weather information and forecasts with caching capabilities.

## 🎯 Learning Objectives

After completing this project, you will:
- Integrate with external APIs
- Handle JSON data processing
- Implement caching mechanisms
- Create responsive weather displays
- Handle API rate limiting
- Build location-based services
- Implement error handling for external services

## 🛠️ Features

### Weather Features
- ✅ Current weather display
- ✅ 5-day weather forecast
- ✅ Weather by city name
- ✅ Weather by coordinates
- ✅ Favorite locations
- ✅ Weather alerts and warnings
- ✅ Historical weather data

### Location Features
- ✅ Geolocation support
- ✅ City search with autocomplete
- ✅ Recent locations
- ✅ Location favorites
- ✅ GPS-based weather

### Data Features
- ✅ API integration
- ✅ Data caching
- ✅ Offline mode
- ✅ Data refresh intervals
- ✅ Error handling
- ✅ Rate limiting

## 📁 Project Structure

```
weather-app/
├── README.md           # This file
├── index.php          # Main application
├── config/
│   ├── api.php        # API configuration
│   └── cache.php      # Cache configuration
├── classes/
│   ├── Weather.php    # Weather API class
│   ├── Location.php   # Location services
│   ├── Cache.php      # Caching system
│   └── API.php        # API handler
├── api/
│   ├── weather.php    # Weather API endpoints
│   └── locations.php  # Location API endpoints
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   ├── js/
│   │   ├── app.js     # Main JavaScript
│   │   └── weather.js # Weather functionality
│   └── images/       # Weather icons
├── cache/             # Cache directory
└── logs/              # Log files
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache, Nginx)
- cURL extension
- JSON extension
- File system write permissions

### API Configuration

1. **Get Weather API Key**
   - Sign up at [OpenWeatherMap](https://openweathermap.org/api)
   - Get your free API key

2. **Configure API**
   Edit `config/api.php`:
   ```php
   define('WEATHER_API_KEY', 'your_api_key_here');
   define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5');
   define('GEO_API_URL', 'https://api.openweathermap.org/geo/1.0');
   ```

### Running the Application

1. **Navigate to project directory**
   ```bash
   cd php-learning-guide/08-projects/weather-app
   ```

2. **Create cache directory**
   ```bash
   mkdir cache logs
   chmod 755 cache logs
   ```

3. **Start PHP server**
   ```bash
   php -S localhost:8000
   ```

4. **Access the application**
   - Main site: `http://localhost:8000`
   - API: `http://localhost:8000/api`

## 📖 API Integration

### Weather API Endpoints

#### Current Weather
```
GET /weather/current
Parameters:
- q: City name (required)
- units: Units (metric, imperial, kelvin)
- lang: Language code
```

#### Weather Forecast
```
GET /weather/forecast
Parameters:
- q: City name (required)
- days: Number of days (1-5)
- units: Units (metric, imperial, kelvin)
```

#### Geocoding
```
GET /geo/search
Parameters:
- q: Search query (required)
- limit: Number of results (1-5)
```

### API Response Format
```json
{
    "success": true,
    "data": {
        "location": {
            "name": "New York",
            "country": "US",
            "lat": 40.7128,
            "lon": -74.0060
        },
        "current": {
            "temperature": 22.5,
            "feels_like": 24.1,
            "humidity": 65,
            "pressure": 1013,
            "visibility": 10000,
            "uv_index": 6,
            "wind": {
                "speed": 5.2,
                "direction": 180,
                "gust": 8.1
            },
            "weather": {
                "main": "Clear",
                "description": "clear sky",
                "icon": "01d"
            },
            "sun": {
                "sunrise": "06:30",
                "sunset": "19:45"
            }
        },
        "forecast": [
            {
                "date": "2024-03-25",
                "temperature": {
                    "min": 18.2,
                    "max": 26.8,
                    "morning": 19.5,
                    "day": 24.3,
                    "evening": 22.1,
                    "night": 20.8
                },
                "weather": {
                    "main": "Clouds",
                    "description": "few clouds",
                    "icon": "02d"
                },
                "precipitation": {
                    "probability": 20,
                    "amount": 0
                },
                "wind": {
                    "speed": 4.5,
                    "direction": 200
                },
                "humidity": 70
            }
        ]
    },
    "cached": false,
    "timestamp": "2024-03-25T10:30:00Z"
}
```

## 🔧 Core Classes

### Weather Class
```php
<?php
class Weather {
    private $apiKey;
    private $apiUrl;
    private $cache;
    private $logger;
    
    public function __construct() {
        $this->apiKey = WEATHER_API_KEY;
        $this->apiUrl = WEATHER_API_URL;
        $this->cache = new Cache();
        $this->logger = new Logger();
    }
    
    public function getCurrentWeather($location, $units = 'metric', $lang = 'en') {
        $cacheKey = "current_{$location}_{$units}_{$lang}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            $this->logger->info("Cache hit for current weather: $location");
            return json_decode($cached, true);
        }
        
        // Make API request
        $url = "{$this->apiUrl}/weather";
        $params = [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => $units,
            'lang' => $lang
        ];
        
        $response = $this->makeAPIRequest($url, $params);
        
        if ($response['success']) {
            // Cache the response
            $this->cache->set($cacheKey, json_encode($response), 600); // Cache for 10 minutes
            $this->logger->info("Cached current weather for: $location");
        }
        
        return $response;
    }
    
    public function getForecast($location, $days = 5, $units = 'metric', $lang = 'en') {
        $cacheKey = "forecast_{$location}_{$days}_{$units}_{$lang}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            $this->logger->info("Cache hit for forecast: $location");
            return json_decode($cached, true);
        }
        
        // Make API request
        $url = "{$this->apiUrl}/forecast";
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
                'cached' => false,
                'timestamp' => date('c')
            ];
            
            // Cache the response
            $this->cache->set($cacheKey, json_encode($finalResponse), 1800); // Cache for 30 minutes
            $this->logger->info("Cached forecast for: $location");
            
            return $finalResponse;
        }
        
        return $response;
    }
    
    public function searchLocations($query, $limit = 5) {
        $cacheKey = "search_{$query}_{$limit}";
        
        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached && !$this->cache->isExpired($cacheKey)) {
            $this->logger->info("Cache hit for location search: $query");
            return json_decode($cached, true);
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
            $this->cache->set($cacheKey, json_encode($response), 3600); // Cache for 1 hour
            $this->logger->info("Cached location search for: $query");
        }
        
        return $response;
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
            $this->logger->error("cURL error: $error");
            return [
                'success' => false,
                'error' => 'Network error',
                'message' => $error
            ];
        }
        
        if ($httpCode !== 200) {
            $this->logger->error("HTTP error: $httpCode");
            return [
                'success' => false,
                'error' => 'API error',
                'message' => "HTTP status: $httpCode"
            ];
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error("JSON error: " . json_last_error_msg());
            return [
                'success' => false,
                'error' => 'Data error',
                'message' => 'Invalid JSON response'
            ];
        }
        
        // Check for API errors
        if (isset($data['cod']) && $data['cod'] !== 200) {
            $this->logger->error("API error: " . ($data['message'] ?? 'Unknown error'));
            return [
                'success' => false,
                'error' => 'API error',
                'message' => $data['message'] ?? 'Unknown API error'
            ];
        }
        
        return [
            'success' => true,
            'data' => $data,
            'cached' => false,
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
                'probability' => $representativeItem['pop'] * 100,
                'amount' => $representativeItem['rain']['3h'] ?? 0
            ]
        ];
    }
}
?>
```

### Cache Class
```php
<?php
class Cache {
    private $cacheDir;
    private $defaultTTL;
    
    public function __construct() {
        $this->cacheDir = CACHE_DIR;
        $this->defaultTTL = 3600; // 1 hour default
        
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
        
        $result = file_put_contents($filename, serialize($cacheData));
        
        if ($result === false) {
            error_log("Failed to write cache file: $filename");
            return false;
        }
        
        return true;
    }
    
    public function get($key) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        if ($data === false) {
            error_log("Failed to read cache file: $filename");
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
    
    public function delete($key) {
        $filename = $this->getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }
    
    public function clear() {
        $files = glob($this->cacheDir . '*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }
        
        return $deleted;
    }
    
    public function cleanup() {
        $files = glob($this->cacheDir . '*');
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $data = unserialize(file_get_contents($file));
                
                if ($data !== false && time() > $data['expiry']) {
                    if (unlink($file)) {
                        $cleaned++;
                    }
                }
            }
        }
        
        return $cleaned;
    }
    
    public function getStats() {
        $files = glob($this->cacheDir . '*');
        $totalSize = 0;
        $expiredCount = 0;
        $validCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $data = unserialize(file_get_contents($file));
                
                if ($data !== false) {
                    if (time() > $data['expiry']) {
                        $expiredCount++;
                    } else {
                        $validCount++;
                    }
                }
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $validCount,
            'expired_files' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize)
        ];
    }
    
    private function getFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
?>
```

### Location Class
```php
<?php
class Location {
    private $weather;
    private $cache;
    
    public function __construct() {
        $this->weather = new Weather();
        $this->cache = new Cache();
    }
    
    public function getCurrentLocation() {
        // This would typically use browser geolocation
        // For demo purposes, we'll return a default location
        return [
            'name' => 'New York',
            'country' => 'US',
            'lat' => 40.7128,
            'lon' => -74.0060
        ];
    }
    
    public function searchCities($query, $limit = 5) {
        $response = $this->weather->searchLocations($query, $limit);
        
        if ($response['success']) {
            return array_map(function($location) {
                return [
                    'name' => $location['name'],
                    'country' => $location['country'],
                    'state' => $location['state'] ?? '',
                    'lat' => $location['lat'],
                    'lon' => $location['lon']
                ];
            }, $response['data']);
        }
        
        return [];
    }
    
    public function getPopularCities() {
        $popularCities = [
            ['name' => 'New York', 'country' => 'US'],
            ['name' => 'London', 'country' => 'GB'],
            ['name' => 'Tokyo', 'country' => 'JP'],
            ['name' => 'Paris', 'country' => 'FR'],
            ['name' => 'Sydney', 'country' => 'AU'],
            ['name' => 'Mumbai', 'country' => 'IN'],
            ['name' => 'Beijing', 'country' => 'CN'],
            ['name' => 'Moscow', 'country' => 'RU']
        ];
        
        return $popularCities;
    }
    
    public function formatLocation($location) {
        if (isset($location['state']) && $location['state']) {
            return "{$location['name']}, {$location['state']}, {$location['country']}";
        }
        
        return "{$location['name']}, {$location['country']}";
    }
    
    public function validateCoordinates($lat, $lon) {
        return is_numeric($lat) && is_numeric($lon) &&
               $lat >= -90 && $lat <= 90 &&
               $lon >= -180 && $lon <= 180;
    }
    
    public function getWeatherByCoordinates($lat, $lon, $units = 'metric') {
        $location = "{$lat},{$lon}";
        return $this->weather->getCurrentWeather($location, $units);
    }
}
?>
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Weather Maps**: Add weather map integration
2. **Weather Alerts**: Implement severe weather alerts
3. **Unit Conversion**: Add unit conversion tools
4. **Weather History**: Show historical weather data

### Intermediate Challenges
1. **Multiple APIs**: Integrate multiple weather providers
2. **Weather Comparison**: Compare weather between cities
3. **Weather Widgets**: Create embeddable widgets
4. **Email Notifications**: Email weather updates

### Advanced Challenges
1. **Machine Learning**: Weather prediction algorithms
2. **Real-time Updates**: WebSocket integration
3. **Mobile App**: Native mobile application
4. **Weather Analytics**: Advanced weather analytics

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] Current weather display
- [ ] Weather forecast
- [ ] Location search
- [ ] Geolocation support
- [ ] Caching functionality
- [ ] Error handling
- [ ] API rate limiting
- [ ] Responsive design

### API Testing
- [ ] GET /api/weather/current
- [ ] GET /api/weather/forecast
- [ ] GET /api/geo/search
- [ ] Cache hit/miss testing
- [ ] Error response testing

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ API integration
- ✅ JSON data handling
- ✅ Caching mechanisms
- ✅ Error handling
- ✅ cURL usage
- ✅ Data processing
- ✅ Geolocation services
- ✅ Rate limiting
- ✅ External service integration

## 🚀 Next Steps

1. **Add More APIs**: Integrate additional weather services
2. **Mobile App**: Create React Native or Flutter app
3. **Real-time Data**: WebSocket integration
4. **Analytics**: Weather usage analytics
5. **Premium Features**: Paid weather data services

---

**Ready for the next project?** ➡️ [Quiz System](../quiz-system/README.md)

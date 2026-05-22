<?php
// Home page view
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
?>

<div class="weather-card">
    <h2>Welcome to <?= APP_NAME ?></h2>
    <p>Get real-time weather information and 5-day forecasts for any city worldwide.</p>
    
    <div style="margin-top: 30px;">
        <h3>Features</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <div style="font-size: 2rem; margin-bottom: 10px;">🌡️</div>
                <h4>Current Weather</h4>
                <p>Real-time weather data including temperature, humidity, wind speed, and more.</p>
            </div>
            
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <div style="font-size: 2rem; margin-bottom: 10px;">📅</div>
                <h4>5-Day Forecast</h4>
                <p>Detailed weather forecasts for the next 5 days with daily summaries.</p>
            </div>
            
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <div style="font-size: 2rem; margin-bottom: 10px;">🔍</div>
                <h4>City Search</h4>
                <p>Search for any city worldwide with autocomplete suggestions.</p>
            </div>
            
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <div style="font-size: 2rem; margin-bottom: 10px;">💾</div>
                <h4>Smart Caching</h4>
                <p>Intelligent caching system for faster load times and reduced API calls.</p>
            </div>
        </div>
    </div>
</div>

<div class="weather-card">
    <h3>How to Use</h3>
    <div style="margin-top: 20px;">
        <ol style="line-height: 1.8;">
            <li><strong>Enter a city name</strong> in the search box above</li>
            <li><strong>Select your preferred units</strong> (Celsius or Fahrenheit)</li>
            <li><strong>Click "Get Weather"</strong> to see current conditions and forecast</li>
            <li><strong>Use the quick city buttons</strong> for popular locations</li>
            <li><strong>Enable location services</strong> on your device for weather based on your location</li>
        </ol>
    </div>
</div>

<div class="weather-card">
    <h3>Weather Data Provided</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <div>
            <h4>🌡️ Temperature</h4>
            <ul style="list-style: none; padding: 0;">
                <li>Current temperature</li>
                <li>Feels like temperature</li>
                <li>Daily min/max</li>
                <li>Temperature trends</li>
            </ul>
        </div>
        
        <div>
            <h4>💧 Atmospheric</h4>
            <ul style="list-style: none; padding: 0;">
                <li>Humidity levels</li>
                <li>Atmospheric pressure</li>
                <li>Visibility distance</li>
                <li>Precipitation chance</li>
            </ul>
        </div>
        
        <div>
            <h4>💨 Wind</h4>
            <ul style="list-style: none; padding: 0;">
                <li>Wind speed</li>
                <li>Wind direction</li>
                <li>Wind gusts</li>
                <li>Beaufort scale</li>
            </ul>
        </div>
        
        <div>
            <h4>☀️ Sun & Moon</h4>
            <ul style="list-style: none; padding: 0;">
                <li>Sunrise time</li>
                <li>Sunset time</li>
                <li>Daylight duration</li>
                <li>Solar position</li>
            </ul>
        </div>
    </div>
</div>

<div class="weather-card">
    <h3>API Information</h3>
    <p>This weather application uses the OpenWeatherMap API to provide accurate and up-to-date weather information.</p>
    
    <div style="margin-top: 15px;">
        <h4>API Features</h4>
        <ul style="line-height: 1.8;">
            <li>Real-time weather data from thousands of weather stations</li>
            <li>5-day weather forecasts with 3-hour intervals</li>
            <li>Geocoding API for city search and autocomplete</li>
            <li>Global coverage with data for over 200,000 cities</li>
            <li>Weather data updated every 10 minutes</li>
        </ul>
    </div>
    
    <div style="margin-top: 15px;">
        <h4>Data Accuracy</h4>
        <ul style="line-height: 1.8;">
            <li>Temperature accuracy: ±1°C</li>
            <li>Humidity accuracy: ±5%</li>
            <li>Wind speed accuracy: ±1 m/s</li>
            <li>Forecast accuracy decreases with time</li>
        </ul>
    </div>
</div>

<div class="weather-card">
    <h3>Quick Start</h3>
    <p>Try searching for one of these popular cities to get started:</p>
    
    <div class="city-buttons" style="margin-top: 20px;">
        <?php foreach ($popularCities as $city): ?>
            <button class="city-btn" onclick="getWeather('<?= htmlspecialchars($city['name']) ?>')">
                <?= htmlspecialchars($city['name']) ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<div class="weather-card">
    <h3>Tips & Tricks</h3>
    <div style="margin-top: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div>
                <h4>💡 Search Tips</h4>
                <ul style="line-height: 1.6;">
                    <li>Use full city names for better results</li>
                    <li>Include country name for ambiguous cities</li>
                    <li>Try alternative spellings for international cities</li>
                    <li>Use the autocomplete suggestions for accuracy</li>
                </ul>
            </div>
            
            <div>
                <h4>📱 Mobile Usage</h4>
                <ul style="line-height: 1.6;">
                    <li>The app is fully responsive for mobile devices</li>
                    <li>Bookmark the page for quick weather access</li>
                    <li>Add to home screen for app-like experience</li>
                    <li>Enable location services for local weather</li>
                </ul>
            </div>
            
            <div>
                <h4>🔄 Data Updates</h4>
                <ul style="line-height: 1.6;">
                    <li>Weather data is cached for 10 minutes</li>
                    <li>Forecasts are cached for 30 minutes</li>
                    <li>Click refresh button for latest data</li>
                    <li>Page auto-refreshes every 10 minutes</li>
                </ul>
            </div>
            
            <div>
                <h4>🌍 Global Coverage</h4>
                <ul style="line-height: 1.6;">
                    <li>Coverage for over 200,000 cities worldwide</li>
                    <li>Support for multiple languages</li>
                    <li>Metric and imperial units available</li>
                    <li>Timezone-aware weather display</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Animate the feature cards on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    document.querySelectorAll('.weather-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Add hover effects to city buttons
    document.querySelectorAll('.city-btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

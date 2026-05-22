<?php
// Weather display view
$currentData = $currentWeather['data'];
$forecastData = $forecast['data'];
$units = $_GET['units'] ?? 'metric';
$tempUnit = $units === 'metric' ? '°C' : '°F';
$windUnit = $units === 'metric' ? 'm/s' : 'mph';
?>

<div class="weather-display">
    <!-- Current Weather -->
    <div class="weather-card current-weather">
        <h2>Current Weather in <?= htmlspecialchars($currentData['name']) ?>, <?= htmlspecialchars($currentData['sys']['country']) ?></h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
            <div>
                <div class="weather-icon">
                    <?= $this->getWeatherIcon($currentData['weather'][0]['icon']) ?>
                </div>
                <div class="temperature">
                    <?= round($currentData['main']['temp']) ?><?= $tempUnit ?>
                </div>
                <div class="weather-description">
                    <?= htmlspecialchars($currentData['weather'][0]['description']) ?>
                </div>
                <div style="margin-top: 10px; color: #666;">
                    Feels like <?= round($currentData['main']['feels_like']) ?><?= $tempUnit ?>
                </div>
            </div>
            
            <div class="weather-details">
                <div class="detail-item">
                    <div class="detail-value"><?= round($currentData['main']['temp_min']) ?><?= $tempUnit ?></div>
                    <div class="detail-label">Min Temp</div>
                </div>
                <div class="detail-item">
                    <div class="detail-value"><?= round($currentData['main']['temp_max']) ?><?= $tempUnit ?></div>
                    <div class="detail-label">Max Temp</div>
                </div>
                <div class="detail-item">
                    <div class="detail-value"><?= $currentData['main']['humidity'] ?>%</div>
                    <div class="detail-label">Humidity</div>
                </div>
                <div class="detail-item">
                    <div class="detail-value"><?= $currentData['wind']['speed'] ?> <?= $windUnit ?></div>
                    <div class="detail-label">Wind Speed</div>
                </div>
                <div class="detail-item">
                    <div class="detail-value"><?= $currentData['main']['pressure'] ?> hPa</div>
                    <div class="detail-label">Pressure</div>
                </div>
                <div class="detail-item">
                    <div class="detail-value"><?= ($currentData['visibility'] / 1000) ?> km</div>
                    <div class="detail-label">Visibility</div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <strong>Sunrise:</strong> <?= date('H:i', $currentData['sys']['sunrise']) ?>
                </div>
                <div>
                    <strong>Sunset:</strong> <?= date('H:i', $currentData['sys']['sunset']) ?>
                </div>
                <div>
                    <strong>Coordinates:</strong> <?= round($currentData['coord']['lat'], 2) ?>, <?= round($currentData['coord']['lon'], 2) ?>
                </div>
                <div>
                    <strong>Data cached:</strong> <?= $currentWeather['cached'] ? 'Yes' : 'No' ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5-Day Forecast -->
<div class="weather-card">
    <h2>5-Day Forecast</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <?php foreach ($forecastData['forecast'] as $day): ?>
            <div class="forecast-day">
                <div class="forecast-date">
                    <?= date('D, M j', strtotime($day['date'])) ?>
                </div>
                <div class="forecast-icon">
                    <?= $this->getWeatherIcon($day['weather']['icon']) ?>
                </div>
                <div class="forecast-temp">
                    <?= round($day['temperature']['min']) ?>° / <?= round($day['temperature']['max']) ?>°
                </div>
                <div style="margin-top: 10px; color: #666; font-size: 0.9rem;">
                    <?= htmlspecialchars($day['weather']['description']) ?>
                </div>
                <div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 5px; font-size: 0.8rem; color: #666;">
                    <div>💧 <?= $day['humidity'] ?>%</div>
                    <div>💨 <?= round($day['wind']['speed']) ?> <?= $windUnit ?></div>
                    <?php if ($day['precipitation']['probability'] > 0): ?>
                        <div>🌧️ <?= $day['precipitation']['probability'] ?>%</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Additional Information -->
<div class="weather-card">
    <h3>Weather Details</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div>
            <h4>Atmospheric Conditions</h4>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Pressure:</strong> <?= $currentData['main']['pressure'] ?> hPa</li>
                <li><strong>Humidity:</strong> <?= $currentData['main']['humidity'] ?>%</li>
                <li><strong>Visibility:</strong> <?= ($currentData['visibility'] / 1000) ?> km</li>
                <li><strong>UV Index:</strong> Not available (requires UV API)</li>
            </ul>
        </div>
        
        <div>
            <h4>Wind Conditions</h4>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Speed:</strong> <?= $currentData['wind']['speed'] ?> <?= $windUnit ?></li>
                <li><strong>Direction:</strong> <?= $currentData['wind']['deg'] ?>°</li>
                <li><strong>Gust:</strong> <?= ($currentData['wind']['gust'] ?? 'N/A') ?> <?= $windUnit ?></li>
            </ul>
        </div>
        
        <div>
            <h4>Sun & Moon</h4>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Sunrise:</strong> <?= date('H:i', $currentData['sys']['sunrise']) ?></li>
                <li><strong>Sunset:</strong> <?= date('H:i', $currentData['sys']['sunset']) ?></li>
                <li><strong>Daylight:</strong> <?= $this->calculateDaylight($currentData['sys']['sunrise'], $currentData['sys']['sunset']) ?></li>
            </ul>
        </div>
        
        <div>
            <h4>Location Info</h4>
            <ul style="list-style: none; padding: 0;">
                <li><strong>City:</strong> <?= htmlspecialchars($currentData['name']) ?></li>
                <li><strong>Country:</strong> <?= htmlspecialchars($currentData['sys']['country']) ?></li>
                <li><strong>Coordinates:</strong> <?= round($currentData['coord']['lat'], 2) ?>°N, <?= round($currentData['coord']['lon'], 2) ?>°W</li>
                <li><strong>Timezone:</strong> UTC<?= date('P', $currentData['dt']) ?></li>
            </ul>
        </div>
    </div>
</div>

<?php
// Helper function for weather icons (would normally be in a class)
function getWeatherIcon($iconCode) {
    $iconMap = [
        '01d' => '☀️', '01n' => '🌙',
        '02d' => '⛅', '02n' => '☁️',
        '03d' => '☁️', '03n' => '☁️',
        '04d' => '☁️', '04n' => '☁️',
        '09d' => '🌧️', '09n' => '🌧️',
        '10d' => '🌦️', '10n' => '🌧️',
        '11d' => '⛈️', '11n' => '⛈️',
        '13d' => '❄️', '13n' => '❄️',
        '50d' => '🌫️', '50n' => '🌫️'
    ];
    
    return $iconMap[$iconCode] ?? '🌤️';
}

function calculateDaylight($sunrise, $sunset) {
    $daylight = $sunset - $sunrise;
    $hours = floor($daylight / 3600);
    $minutes = floor(($daylight % 3600) / 60);
    return "{$hours}h {$minutes}m";
}
?>

<script>
// Auto-refresh every 10 minutes
setTimeout(() => {
    if (confirm('Would you like to refresh the weather data?')) {
        window.location.reload();
    }
}, 600000);

// Add refresh button
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.createElement('button');
    refreshBtn.className = 'btn btn-secondary';
    refreshBtn.textContent = 'Refresh';
    refreshBtn.style.position = 'fixed';
    refreshBtn.style.bottom = '20px';
    refreshBtn.style.right = '20px';
    refreshBtn.onclick = () => window.location.reload();
    document.body.appendChild(refreshBtn);
});
</script>

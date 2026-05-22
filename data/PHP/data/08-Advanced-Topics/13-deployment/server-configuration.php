<?php
/**
 * Server Configuration and Setup
 * 
 * This file demonstrates web server configuration, PHP-FPM setup,
 * and server optimization for production environments.
 */

// Web Server Configuration Generator
class WebServerConfig {
    private string $serverType;
    private array $config = [];
    private array $sites = [];
    
    public function __construct(string $serverType = 'nginx') {
        $this->serverType = $serverType;
        $this->initializeDefaults();
    }
    
    private function initializeDefaults(): void {
        $this->config = [
            'nginx' => [
                'worker_processes' => 'auto',
                'worker_connections' => 1024,
                'keepalive_timeout' => 65,
                'client_max_body_size' => '100M',
                'gzip' => 'on',
                'gzip_types' => 'text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript',
                'ssl_protocols' => 'TLSv1.2 TLSv1.3',
                'ssl_ciphers' => 'ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384',
                'ssl_prefer_server_ciphers' => 'off'
            ],
            'apache' => [
                'ServerTokens' => 'Prod',
                'ServerSignature' => 'Off',
                'KeepAlive' => 'On',
                'KeepAliveTimeout' => 5,
                'MaxKeepAliveRequests' => 100,
                'LimitRequestFieldSize' => 16380,
                'LimitRequestLine' => 8190,
                'Timeout' => 30,
                'SSLProtocol' => 'all -SSLv3 -TLSv1 -TLSv1.1',
                'SSLCipherSuite' => 'ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384'
            ]
        ];
    }
    
    public function addSite(string $domain, array $config): void {
        $this->sites[$domain] = array_merge([
            'domain' => $domain,
            'root' => "/var/www/{$domain}",
            'index' => 'index.php index.html',
            'ssl' => true,
            'php_fpm' => true,
            'access_log' => "/var/log/nginx/{$domain}.access.log",
            'error_log' => "/var/log/nginx/{$domain}.error.log",
            'cache' => true,
            'security' => [
                'hide_php_version' => true,
                'block_common_exploits' => true,
                'xss_protection' => true,
                'content_type_nosniff' => true,
                'frame_options' => 'DENY'
            ]
        ], $config);
    }
    
    public function generateNginxConfig(): string {
        $config = "# Nginx Configuration\n";
        $config .= "# Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        // Main nginx.conf
        $config .= "user www-data;\n";
        $config .= "worker_processes {$this->config['nginx']['worker_processes']};\n";
        $config .= "pid /run/nginx.pid;\n";
        $config .= "include /etc/nginx/modules-enabled/*.conf;\n\n";
        
        $config .= "events {\n";
        $config .= "    worker_connections {$this->config['nginx']['worker_connections']};\n";
        $config .= "    multi_accept on;\n";
        $config .= "}\n\n";
        
        $config .= "http {\n";
        $config .= "    sendfile on;\n";
        $config .= "    tcp_nopush on;\n";
        $config .= "    tcp_nodelay on;\n";
        $config .= "    keepalive_timeout {$this->config['nginx']['keepalive_timeout']};\n";
        $config .= "    types_hash_max_size 2048;\n";
        $config .= "    client_max_body_size {$this->config['nginx']['client_max_body_size']};\n\n";
        
        // Gzip configuration
        $config .= "    gzip {$this->config['nginx']['gzip']};\n";
        $config .= "    gzip_vary on;\n";
        $config .= "    gzip_proxied any;\n";
        $config .= "    gzip_comp_level 6;\n";
        $config .= "    gzip_types {$this->config['nginx']['gzip_types']};\n\n";
        
        // SSL configuration
        $config .= "    ssl_protocols {$this->config['nginx']['ssl_protocols']};\n";
        $config .= "    ssl_ciphers {$this->config['nginx']['ssl_ciphers']};\n";
        $config .= "    ssl_prefer_server_ciphers {$this->config['nginx']['ssl_prefer_server_ciphers']};\n";
        $config .= "    ssl_session_cache shared:SSL:10m;\n";
        $config .= "    ssl_session_timeout 10m;\n\n";
        
        // Include site configs
        $config .= "    include /etc/nginx/conf.d/*.conf;\n";
        $config .= "    include /etc/nginx/sites-enabled/*;\n";
        $config .= "}\n\n";
        
        // Generate site configurations
        foreach ($this->sites as $domain => $site) {
            $config .= $this->generateNginxSiteConfig($domain, $site);
        }
        
        return $config;
    }
    
    private function generateNginxSiteConfig(string $domain, array $site): string {
        $config = "# Site configuration for {$domain}\n";
        $config .= "server {\n";
        $config .= "    listen 80;\n";
        $config .= "    server_name {$domain} www.{$domain};\n";
        
        if ($site['ssl']) {
            $config .= "    listen 443 ssl http2;\n";
            $config .= "    ssl_certificate /etc/ssl/certs/{$domain}.crt;\n";
            $config .= "    ssl_certificate_key /etc/ssl/private/{$domain}.key;\n";
        }
        
        $config .= "    root {$site['root']};\n";
        $config .= "    index {$site['index']};\n\n";
        
        // Logging
        $config .= "    access_log {$site['access_log']};\n";
        $config .= "    error_log {$site['error_log']};\n\n";
        
        // Security headers
        if ($site['security']['xss_protection']) {
            $config .= "    add_header X-XSS-Protection \"1; mode=block\" always;\n";
        }
        if ($site['security']['content_type_nosniff']) {
            $config .= "    add_header X-Content-Type-Options nosniff always;\n";
        }
        if ($site['security']['frame_options']) {
            $config .= "    add_header X-Frame-Options {$site['security']['frame_options']} always;\n";
        }
        $config .= "    add_header Referrer-Policy \"strict-origin-when-cross-origin\" always;\n\n";
        
        // PHP-FPM configuration
        if ($site['php_fpm']) {
            $config .= "    location ~ \.php$ {\n";
            $config .= "        include snippets/fastcgi-php.conf;\n";
            $config .= "        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;\n";
            $config .= "        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n";
            $config .= "        include fastcgi_params;\n";
            $config .= "    }\n\n";
        }
        
        // Static file caching
        if ($site['cache']) {
            $config .= "    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {\n";
            $config .= "        expires 1y;\n";
            $config .= "        add_header Cache-Control \"public, immutable\";\n";
            $config .= "    }\n\n";
        }
        
        // Block common exploits
        if ($site['security']['block_common_exploits']) {
            $config .= "    location ~ /\. {\n";
            $config .= "        deny all;\n";
            $config .= "    }\n\n";
            
            $config .= "    location ~ ^/(wp-config|wp-content|wp-includes)/ {\n";
            $config .= "        deny all;\n";
            $config .= "    }\n\n";
        }
        
        // Hide PHP version
        if ($site['security']['hide_php_version']) {
            $config .= "    location ~ ^/\. {\n";
            $config .= "        deny all;\n";
            $config .= "    }\n\n";
        }
        
        // Default location
        $config .= "    location / {\n";
        $config .= "        try_files \$uri \$uri/ /index.php?\$query_string;\n";
        $config .= "    }\n\n";
        
        $config .= "}\n\n";
        
        return $config;
    }
    
    public function generateApacheConfig(): string {
        $config = "# Apache Configuration\n";
        $config .= "# Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        // Main apache2.conf
        $config .= "<IfModule mod_unixd.c>\n";
        $config .= "User www-data\n";
        $config .= "Group www-data\n";
        $config .= "</IfModule>\n\n";
        
        $config .= "<IfModule dir_module>\n";
        $config .= "    DirectoryIndex index.html index.cgi index.pl index.php index.xhtml index.htm\n";
        $config .= "</IfModule>\n\n";
        
        // Security settings
        $config .= "ServerTokens {$this->config['apache']['ServerTokens']}\n";
        $config .= "ServerSignature {$this->config['apache']['ServerSignature']}\n\n";
        
        // Performance settings
        $config .= "<IfModule mpm_prefork_module>\n";
        $config .= "    StartServers 5\n";
        $config .= "    MinSpareServers 5\n";
        $config .= "    MaxSpareServers 10\n";
        $config .= "    MaxRequestWorkers 150\n";
        $config .= "    MaxConnectionsPerChild 0\n";
        $config .= "</IfModule>\n\n";
        
        // Generate site configurations
        foreach ($this->sites as $domain => $site) {
            $config .= $this->generateApacheSiteConfig($domain, $site);
        }
        
        return $config;
    }
    
    private function generateApacheSiteConfig(string $domain, array $site): string {
        $config = "<VirtualHost *:80>\n";
        $config .= "    ServerName {$domain}\n";
        $config .= "    ServerAlias www.{$domain}\n";
        $config .= "    DocumentRoot {$site['root']}\n\n";
        
        $config .= "    ErrorLog {$site['error_log']}\n";
        $config .= "    CustomLog {$site['access_log']} combined\n\n";
        
        if ($site['php_fpm']) {
            $config .= "    <FilesMatch \.php$>\n";
            $config .= "        SetHandler \"proxy:unix:/var/run/php/php8.1-fpm.sock|fcgi://localhost/\"\n";
            $config .= "    </FilesMatch>\n\n";
        }
        
        // Security headers
        if ($site['security']['xss_protection']) {
            $config .= "    Header always set X-XSS-Protection \"1; mode=block\"\n";
        }
        if ($site['security']['content_type_nosniff']) {
            $config .= "    Header always set X-Content-Type-Options nosniff\n";
        }
        if ($site['security']['frame_options']) {
            $config .= "    Header always set X-Frame-Options {$site['security']['frame_options']}\n";
        }
        
        $config .= "    <Directory {$site['root']}>\n";
        $config .= "        Options -Indexes +FollowSymLinks\n";
        $config .= "        AllowOverride All\n";
        $config .= "        Require all granted\n";
        $config .= "    </Directory>\n\n";
        
        if ($site['cache']) {
            $config .= "    <IfModule mod_expires.c>\n";
            $config .= "        ExpiresActive On\n";
            $config .= "        ExpiresByType text/css \"access plus 1 year\"\n";
            $config .= "        ExpiresByType application/javascript \"access plus 1 year\"\n";
            $config .= "        ExpiresByType image/png \"access plus 1 year\"\n";
            $config .= "        ExpiresByType image/jpg \"access plus 1 year\"\n";
            $config .= "        ExpiresByType image/gif \"access plus 1 year\"\n";
            $config .= "        ExpiresByType image/ico \"access plus 1 year\"\n";
            $config .= "    </IfModule>\n\n";
        }
        
        $config .= "</VirtualHost>\n\n";
        
        if ($site['ssl']) {
            $config .= "<VirtualHost *:443>\n";
            $config .= "    ServerName {$domain}\n";
            $config .= "    ServerAlias www.{$domain}\n";
            $config .= "    DocumentRoot {$site['root']}\n\n";
            
            $config .= "    SSLEngine on\n";
            $config .= "    SSLCertificateFile /etc/ssl/certs/{$domain}.crt\n";
            $config .= "    SSLCertificateKeyFile /etc/ssl/private/{$domain}.key\n";
            $config .= "    SSLProtocol {$this->config['apache']['SSLProtocol']}\n";
            $config .= "    SSLCipherSuite {$this->config['apache']['SSLCipherSuite']}\n\n";
            
            $config .= "</VirtualHost>\n\n";
        }
        
        return $config;
    }
}

// PHP-FPM Configuration Generator
class PHPFPMConfig {
    private array $config = [];
    
    public function __construct() {
        $this->initializeDefaults();
    }
    
    private function initializeDefaults(): void {
        $this->config = [
            'global' => [
                'pid' => '/run/php/php8.1-fpm.pid',
                'error_log' => '/var/log/php8.1-fpm.log',
                'daemonize' => 'yes'
            ],
            'www' => [
                'user' => 'www-data',
                'group' => 'www-data',
                'listen' => '/run/php/php8.1-fpm.sock',
                'listen.owner' => 'www-data',
                'listen.group' => 'www-data',
                'listen.mode' => '0660',
                'pm' => 'dynamic',
                'pm.max_children' => 50,
                'pm.start_servers' => 5,
                'pm.min_spare_servers' => 5,
                'pm.max_spare_servers' => 35,
                'pm.max_requests' => 500,
                'request_terminate_timeout' => 300,
                'request_slowlog_timeout' => 30,
                'slowlog' => '/var/log/php8.1-fpm-slow.log',
                'php_admin_value[memory_limit] => '256M',
                'php_admin_value[max_execution_time] => '300',
                'php_admin_value[upload_max_filesize] => '100M',
                'php_admin_value[post_max_size] => '100M',
                'php_admin_value[max_input_vars] => '3000',
                'php_admin_value[session.save_handler] => 'files',
                'php_admin_value[session.save_path] => '/var/lib/php/sessions',
                'php_admin_value[opcache.enable] => '1',
                'php_admin_value[opcache.memory_consumption] => '128',
                'php_admin_value[opcache.interned_strings_buffer] => '8',
                'php_admin_value[opcache.max_accelerated_files] => '4000',
                'php_admin_value[opcache.revalidate_freq] => '2',
                'php_admin_value[opcache.fast_shutdown] => '1'
            ]
        ];
    }
    
    public function generateConfig(): string {
        $config = "; PHP-FPM Configuration\n";
        $config .= "; Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        // Global section
        $config .= "[global]\n";
        foreach ($this->config['global'] as $key => $value) {
            $config .= "{$key} = {$value}\n";
        }
        $config .= "\n";
        
        // Pool configuration
        $config .= "[www]\n";
        foreach ($this->config['www'] as $key => $value) {
            $config .= "{$key} = {$value}\n";
        }
        
        return $config;
    }
    
    public function updateSetting(string $section, string $key, string $value): void {
        $this->config[$section][$key] = $value;
    }
    
    public function optimizeForHighTraffic(): void {
        $this->config['www']['pm.max_children'] = 100;
        $this->config['www']['pm.start_servers'] = 10;
        $this->config['www']['pm.min_spare_servers'] = 10;
        $this->config['www']['pm.max_spare_servers'] = 70;
        $this->config['www']['pm.max_requests'] = 1000;
        $this->config['www']['php_admin_value[memory_limit']'] = '512M';
        $this->config['www']['php_admin_value[opcache.memory_consumption']'] = '256';
        $this->config['www']['php_admin_value[opcache.max_accelerated_files']'] = '10000';
    }
    
    public function optimizeForLowMemory(): void {
        $this->config['www']['pm.max_children'] = 20;
        $this->config['www']['pm.start_servers'] = 2;
        $this->config['www']['pm.min_spare_servers'] = 2;
        $this->config['www']['pm.max_spare_servers'] = 10;
        $this->config['www']['php_admin_value[memory_limit']'] = '128M';
        $this->config['www']['php_admin_value[opcache.memory_consumption']'] = '64';
        $this->config['www']['php_admin_value[opcache.max_accelerated_files'] = '2000';
    }
}

// SSL Certificate Generator
class SSLCertificateGenerator {
    private array $config = [];
    
    public function __construct() {
        $this->config = [
            'country' => 'US',
            'state' => 'California',
            'locality' => 'San Francisco',
            'organization' => 'Example Company',
            'organizational_unit' => 'IT Department',
            'email' => 'admin@example.com',
            'key_size' => 2048,
            'days' => 365
        ];
    }
    
    public function generateCSR(string $domain): string {
        $config = "[req]\n";
        $config .= "distinguished_name = req_distinguished_name\n";
        $config .= "req_extensions = v3_req\n";
        $config .= "prompt = no\n\n";
        
        $config .= "[req_distinguished_name]\n";
        $config .= "C = {$this->config['country']}\n";
        $config .= "ST = {$this->config['state']}\n";
        $config .= "L = {$this->config['locality']}\n";
        $config .= "O = {$this->config['organization']}\n";
        $config .= "OU = {$this->config['organizational_unit']}\n";
        $config .= "CN = {$domain}\n";
        $config .= "emailAddress = {$this->config['email']}\n\n";
        
        $config .= "[v3_req]\n";
        $config .= "basicConstraints = CA:FALSE\n";
        $config .= "keyUsage = nonRepudiation, digitalSignature, keyEncipherment\n";
        $config .= "subjectAltName = @alt_names\n\n";
        
        $config .= "[alt_names]\n";
        $config .= "DNS.1 = {$domain}\n";
        $config .= "DNS.2 = www.{$domain}\n";
        
        return $config;
    }
    
    public function generateSelfSignedCert(string $domain): array {
        // This would typically use OpenSSL commands
        // For demo purposes, we'll generate the commands
        
        $configFile = "/tmp/{$domain}.conf";
        $keyFile = "/etc/ssl/private/{$domain}.key";
        $certFile = "/etc/ssl/certs/{$domain}.crt";
        $csrFile = "/tmp/{$domain}.csr";
        
        $commands = [
            // Generate private key
            "openssl genrsa -out {$keyFile} {$this->config['key_size']}",
            
            // Generate CSR
            "openssl req -new -key {$keyFile} -out {$csrFile} -config {$configFile}",
            
            // Generate self-signed certificate
            "openssl x509 -req -days {$this->config['days']} -in {$csrFile} -signkey {$keyFile} -out {$certFile} -extensions v3_req -extfile {$configFile}",
            
            // Set permissions
            "chmod 600 {$keyFile}",
            "chmod 644 {$certFile}"
        ];
        
        return [
            'config_file' => $this->generateCSR($domain),
            'commands' => $commands,
            'key_file' => $keyFile,
            'cert_file' => $certFile,
            'csr_file' => $csrFile
        ];
    }
    
    public function generateLetsEncryptCommands(string $domain): array {
        return [
            // Install Certbot
            "apt-get update && apt-get install -y certbot python3-certbot-nginx",
            
            // Generate certificate
            "certbot --nginx -d {$domain} -d www.{$domain} --email {$this->config['email']} --agree-tos --non-interactive",
            
            // Set up auto-renewal
            "echo '0 12 * * * /usr/bin/certbot renew --quiet' | crontab -"
        ];
    }
}

// Server Performance Monitor
class ServerPerformanceMonitor {
    private array $metrics = [];
    private array $thresholds = [];
    
    public function __construct() {
        $this->initializeThresholds();
    }
    
    private function initializeThresholds(): void {
        $this->thresholds = [
            'cpu_usage' => 80.0,
            'memory_usage' => 85.0,
            'disk_usage' => 90.0,
            'load_average' => 4.0,
            'connections' => 1000,
            'response_time' => 2.0
        ];
    }
    
    public function collectMetrics(): array {
        $this->metrics = [
            'timestamp' => time(),
            'cpu_usage' => $this->getCpuUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'load_average' => $this->getLoadAverage(),
            'connections' => $this->getConnections(),
            'response_time' => $this->getResponseTime(),
            'php_processes' => $this->getPHPProcesses()
        ];
        
        return $this->metrics;
    }
    
    private function getCpuUsage(): float {
        // Simulate CPU usage
        return rand(20, 95);
    }
    
    private function getMemoryUsage(): float {
        // Simulate memory usage
        return rand(30, 90);
    }
    
    private function getDiskUsage(): float {
        // Simulate disk usage
        return rand(10, 95);
    }
    
    private function getLoadAverage(): float {
        // Simulate load average
        return rand(0.1, 8.0);
    }
    
    private function getConnections(): int {
        // Simulate connection count
        return rand(100, 1500);
    }
    
    private function getResponseTime(): float {
        // Simulate response time
        return rand(50, 5000) / 1000; // Convert to seconds
    }
    
    private function getPHPProcesses(): int {
        // Simulate PHP-FPM process count
        return rand(5, 50);
    }
    
    public function checkThresholds(): array {
        $alerts = [];
        
        foreach ($this->metrics as $metric => $value) {
            if (isset($this->thresholds[$metric])) {
                $threshold = $this->thresholds[$metric];
                
                if ($value > $threshold) {
                    $alerts[] = [
                        'metric' => $metric,
                        'value' => $value,
                        'threshold' => $threshold,
                        'severity' => $this->getSeverity($metric, $value, $threshold),
                        'message' => $this->generateAlertMessage($metric, $value, $threshold)
                    ];
                }
            }
        }
        
        return $alerts;
    }
    
    private function getSeverity(string $metric, float $value, float $threshold): string {
        $percentage = ($value / $threshold) * 100;
        
        if ($percentage >= 120) {
            return 'CRITICAL';
        } elseif ($percentage >= 110) {
            return 'HIGH';
        } elseif ($percentage >= 100) {
            return 'MEDIUM';
        } else {
            return 'LOW';
        }
    }
    
    private function generateAlertMessage(string $metric, float $value, float $threshold): string {
        $metricNames = [
            'cpu_usage' => 'CPU Usage',
            'memory_usage' => 'Memory Usage',
            'disk_usage' => 'Disk Usage',
            'load_average' => 'Load Average',
            'connections' => 'Active Connections',
            'response_time' => 'Response Time'
        ];
        
        $name = $metricNames[$metric] ?? $metric;
        $unit = in_array($metric, ['cpu_usage', 'memory_usage', 'disk_usage']) ? '%' : '';
        
        return "{$name} is {$value}{$unit} (threshold: {$threshold}{$unit})";
    }
    
    public function getMetrics(): array {
        return $this->metrics;
    }
    
    public function getRecommendations(): array {
        $recommendations = [];
        
        if ($this->metrics['cpu_usage'] > 70) {
            $recommendations[] = "Consider scaling up CPU resources or optimizing application code";
        }
        
        if ($this->metrics['memory_usage'] > 80) {
            $recommendations[] = "Consider increasing memory or optimizing memory usage";
        }
        
        if ($this->metrics['disk_usage'] > 85) {
            $recommendations[] = "Consider cleaning up disk space or expanding storage";
        }
        
        if ($this->metrics['load_average'] > 3.0) {
            $recommendations[] = "Consider scaling up or optimizing application performance";
        }
        
        if ($this->metrics['connections'] > 800) {
            $recommendations[] = "Consider increasing connection limits or implementing connection pooling";
        }
        
        if ($this->metrics['response_time'] > 1.5) {
            $recommendations[] = "Consider implementing caching or optimizing database queries";
        }
        
        return $recommendations;
    }
}

// Server Configuration Examples
class ServerConfigurationExamples {
    private WebServerConfig $webServer;
    private PHPFPMConfig $phpFpm;
    private SSLCertificateGenerator $ssl;
    private ServerPerformanceMonitor $monitor;
    
    public function __construct() {
        $this->webServer = new WebServerConfig('nginx');
        $this->phpFpm = new PHPFPMConfig();
        $this->ssl = new SSLCertificateGenerator();
        $this->monitor = new ServerPerformanceMonitor();
    }
    
    public function demonstrateNginxConfiguration(): void {
        echo "Nginx Configuration Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Add sample sites
        $this->webServer->addSite('example.com', [
            'root' => '/var/www/example.com/public',
            'ssl' => true,
            'cache' => true,
            'security' => [
                'hide_php_version' => true,
                'block_common_exploits' => true,
                'xss_protection' => true,
                'content_type_nosniff' => true,
                'frame_options' => 'DENY'
            ]
        ]);
        
        $this->webServer->addSite('api.example.com', [
            'root' => '/var/www/api.example.com/public',
            'ssl' => true,
            'cache' => false,
            'php_fpm' => true,
            'security' => [
                'hide_php_version' => true,
                'block_common_exploits' => true,
                'xss_protection' => true,
                'content_type_nosniff' => true,
                'frame_options' => 'SAMEORIGIN'
            ]
        ]);
        
        $config = $this->webServer->generateNginxConfig();
        
        echo "Generated Nginx configuration (first 1000 characters):\n";
        echo substr($config, 0, 1000) . "...\n\n";
        
        echo "Configuration includes:\n";
        echo "- Main nginx.conf with performance settings\n";
        echo "- SSL/TLS configuration\n";
        echo "- Gzip compression\n";
        echo "- Security headers\n";
        echo "- PHP-FPM integration\n";
        echo "- Static file caching\n";
        echo "- Site-specific configurations\n";
    }
    
    public function demonstrateApacheConfiguration(): void {
        echo "\nApache Configuration Example\n";
        echo str_repeat("-", 30) . "\n";
        
        // Switch to Apache
        $apacheConfig = new WebServerConfig('apache');
        
        $apacheConfig->addSite('example.com', [
            'root' => '/var/www/example.com/public',
            'ssl' => true,
            'cache' => true,
            'php_fpm' => true
        ]);
        
        $config = $apacheConfig->generateApacheConfig();
        
        echo "Generated Apache configuration (first 1000 characters):\n";
        echo substr($config, 0, 1000) . "...\n\n";
        
        echo "Configuration includes:\n";
        echo "- Main apache2.conf with security settings\n";
        echo "- MPM configuration\n";
        echo "- SSL/TLS configuration\n";
        echo "- PHP-FPM integration via proxy\n";
        echo "- Security headers\n";
        echo "- Static file caching\n";
        echo "- VirtualHost configurations\n";
    }
    
    public function demonstratePHPFPMConfiguration(): void {
        echo "\nPHP-FPM Configuration Example\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "Default configuration:\n";
        $config = $this->phpFpm->generateConfig();
        echo substr($config, 0, 800) . "...\n\n";
        
        echo "High traffic optimization:\n";
        $this->phpFpm->optimizeForHighTraffic();
        $optimizedConfig = $this->phpFpm->generateConfig();
        echo substr($optimizedConfig, 0, 800) . "...\n\n";
        
        echo "Key optimizations for high traffic:\n";
        echo "- Increased max_children to 100\n";
        echo "- Increased memory limit to 512M\n";
        echo "- Optimized OPcache settings\n";
        echo "- Increased max_requests to 1000\n";
    }
    
    public function demonstrateSSLConfiguration(): void {
        echo "\nSSL Certificate Configuration\n";
        echo str_repeat("-", 35) . "\n";
        
        $domain = 'example.com';
        
        echo "Self-signed certificate generation:\n";
        $certConfig = $this->ssl->generateSelfSignedCert($domain);
        
        echo "OpenSSL configuration:\n";
        echo substr($certConfig['config_file'], 0, 500) . "...\n\n";
        
        echo "Commands to execute:\n";
        foreach ($certConfig['commands'] as $command) {
            echo "  $command\n";
        }
        
        echo "\nLet's Encrypt setup:\n";
        $letsEncryptCommands = $this->ssl->generateLetsEncryptCommands($domain);
        foreach ($letsEncryptCommands as $command) {
            echo "  $command\n";
        }
        
        echo "\nSSL certificate files:\n";
        echo "- Private key: {$certConfig['key_file']}\n";
        echo "- Certificate: {$certConfig['cert_file']}\n";
        echo "- CSR: {$certConfig['csr_file']}\n";
    }
    
    public function demonstratePerformanceMonitoring(): void {
        echo "\nServer Performance Monitoring\n";
        echo str_repeat("-", 35) . "\n";
        
        // Collect metrics
        $metrics = $this->monitor->collectMetrics();
        
        echo "Current Server Metrics:\n";
        echo "CPU Usage: {$metrics['cpu_usage']}%\n";
        echo "Memory Usage: {$metrics['memory_usage']}%\n";
        echo "Disk Usage: {$metrics['disk_usage']}%\n";
        echo "Load Average: {$metrics['load_average']}\n";
        echo "Active Connections: {$metrics['connections']}\n";
        echo "Response Time: {$metrics['response_time']}s\n";
        echo "PHP Processes: {$metrics['php_processes']}\n\n";
        
        // Check thresholds
        $alerts = $this->monitor->checkThresholds();
        
        if (!empty($alerts)) {
            echo "Performance Alerts:\n";
            foreach ($alerts as $alert) {
                $severityIcon = match($alert['severity']) {
                    'CRITICAL' => '🔴',
                    'HIGH' => '🟠',
                    'MEDIUM' => '🟡',
                    'LOW' => '🔵'
                };
                echo "  {$severityIcon} {$alert['message']}\n";
            }
        } else {
            echo "✅ All metrics within normal thresholds\n";
        }
        
        // Get recommendations
        $recommendations = $this->monitor->getRecommendations();
        
        if (!empty($recommendations)) {
            echo "\nRecommendations:\n";
            foreach ($recommendations as $rec) {
                echo "  • $rec\n";
            }
        }
    }
    
    public function demonstrateServerOptimization(): void {
        echo "\nServer Optimization Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Web Server Optimization:\n";
        echo "   • Enable gzip compression\n";
        echo "   • Configure static file caching\n";
        echo "   • Optimize worker processes\n";
        echo "   • Enable HTTP/2\n";
        echo "   • Configure SSL/TLS properly\n\n";
        
        echo "2. PHP-FPM Optimization:\n";
        echo "   • Tune process manager settings\n";
        echo "   • Optimize memory limits\n";
        echo "   • Configure OPcache\n";
        echo "   • Set appropriate timeouts\n";
        echo "   • Enable slow log monitoring\n\n";
        
        echo "3. Database Optimization:\n";
        echo "   • Configure connection pooling\n";
        echo "   • Optimize query cache\n";
        echo "   • Set appropriate buffer sizes\n";
        echo "   • Enable query logging\n";
        echo "   • Monitor slow queries\n\n";
        
        echo "4. System Optimization:\n";
        echo "   • Configure kernel parameters\n";
        echo "   • Optimize file descriptors\n";
        echo "   • Configure swap space\n";
        echo "   • Enable monitoring\n";
        echo "   • Set up log rotation\n";
    }
    
    public function runAllExamples(): void {
        echo "Server Configuration Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateNginxConfiguration();
        $this->demonstrateApacheConfiguration();
        $this->demonstratePHPFPMConfiguration();
        $this->demonstrateSSLConfiguration();
        $this->demonstratePerformanceMonitoring();
        $this->demonstrateServerOptimization();
    }
}

// Server Configuration Best Practices
function printServerConfigurationBestPractices(): void {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Server Configuration Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Web Server Configuration:\n";
    echo "   • Use appropriate worker processes\n";
    echo "   • Enable compression for text content\n";
    echo "   • Configure proper caching headers\n";
    echo "   • Implement security headers\n";
    echo "   • Use SSL/TLS with strong ciphers\n\n";
    
    echo "2. PHP-FPM Configuration:\n";
    echo "   • Tune process manager to traffic\n";
    echo "   • Set appropriate memory limits\n";
    echo "   • Enable OPcache for performance\n";
    echo "   • Configure slow logging\n";
    echo "   • Monitor process health\n\n";
    
    echo "3. Security Hardening:\n";
    echo "   • Hide server signatures\n";
    echo "   • Disable unnecessary modules\n";
    echo "   • Implement access controls\n";
    echo "   • Use secure file permissions\n";
    echo "   • Regular security updates\n\n";
    
    echo "4. Performance Optimization:\n";
    echo "   • Enable HTTP/2 and HTTP/3\n";
    echo "   • Configure connection keep-alive\n";
    echo "   • Optimize timeout values\n";
    echo "   • Use load balancing\n";
    echo "   • Implement CDN caching\n\n";
    
    echo "5. Monitoring & Logging:\n";
    echo "   • Configure comprehensive logging\n";
    echo "   • Monitor server metrics\n";
    echo "   • Set up alerting\n";
    echo "   • Implement log rotation\n";
    echo "   • Use centralized logging\n\n";
    
    echo "6. SSL/TLS Management:\n";
    echo "   • Use Let's Encrypt for certificates\n";
    echo "   • Implement auto-renewal\n";
    echo "   • Use strong cipher suites\n";
    echo "   • Enable HSTS headers\n";
    echo "   • Regular certificate monitoring";
}

// Main execution
function runServerConfigurationDemo(): void {
    $examples = new ServerConfigurationExamples();
    $examples->runAllExamples();
    printServerConfigurationBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runServerConfigurationDemo();
}
?>

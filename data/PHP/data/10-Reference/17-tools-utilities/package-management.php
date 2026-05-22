<?php
/**
 * PHP Package Management and Composer
 * 
 * This file demonstrates Composer usage, package development,
 * dependency management, and package publishing.
 */

// Composer Configuration Manager
class ComposerConfigurationManager
{
    private array $config;
    private array $packages = [];
    private array $devPackages = [];
    private array $scripts = [];
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'name' => 'vendor/project',
            'description' => 'Project description',
            'type' => 'project',
            'license' => 'MIT',
            'minimum_stability' => 'stable',
            'prefer_stable' => true,
            'require' => [],
            'require_dev' => [],
            'autoload' => [
                'psr-4' => [],
                'psr-0' => [],
                'classmap' => [],
                'files' => []
            ],
            'autoload_dev' => [
                'psr-4' => [],
                'psr-0' => [],
                'classmap' => [],
                'files' => []
            ],
            'scripts' => [],
            'config' => [
                'optimize-autoloader' => true,
                'sort-packages' => true,
                'process-timeout' => 200
            ]
        ], $config);
    }
    
    /**
     * Set project information
     */
    public function setProjectInfo(string $name, string $description, string $type = 'project', string $license = 'MIT'): void
    {
        $this->config['name'] = $name;
        $this->config['description'] = $description;
        $this->config['type'] = $type;
        $this->config['license'] = $license;
    }
    
    /**
     * Add package requirement
     */
    public function requirePackage(string $package, string $version, bool $dev = false): void
    {
        if ($dev) {
            $this->config['require_dev'][$package] = $version;
        } else {
            $this->config['require'][$package] = $version;
        }
    }
    
    /**
     * Add autoloading
     */
    public function addAutoload(string $type, string $namespace, string $path, bool $dev = false): void
    {
        $autoloadKey = $dev ? 'autoload_dev' : 'autoload';
        $this->config[$autoloadKey][$type][$namespace] = $path;
    }
    
    /**
     * Add script
     */
    public function addScript(string $name, string|array $command): void
    {
        $this->config['scripts'][$name] = $command;
    }
    
    /**
     * Generate composer.json
     */
    public function generateComposerJson(): string
    {
        return json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Parse composer.json
     */
    public function parseComposerJson(string $json): array
    {
        $config = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        
        $this->config = $config;
        return $config;
    }
    
    /**
     * Validate configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];
        $warnings = [];
        
        // Check required fields
        if (empty($this->config['name'])) {
            $errors[] = 'Project name is required';
        }
        
        if (empty($this->config['description'])) {
            $warnings[] = 'Project description is recommended';
        }
        
        // Validate package name format
        if (!empty($this->config['name']) && !preg_match('/^[a-z0-9_.-]+\/[a-z0-9_.-]+$/', $this->config['name'])) {
            $errors[] = 'Package name must be in format "vendor/project"';
        }
        
        // Check autoloading
        if (empty($this->config['autoload']['psr-4']) && empty($this->config['autoload']['psr-0'])) {
            $warnings[] = 'PSR autoloading is recommended';
        }
        
        // Check stability
        if ($this->config['minimum_stability'] === 'dev') {
            $warnings[] = 'Dev stability is not recommended for production';
        }
        
        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'valid' => empty($errors)
        ];
    }
    
    /**
     * Get dependency tree
     */
    public function getDependencyTree(): array
    {
        return [
            'direct' => array_keys($this->config['require']),
            'dev' => array_keys($this->config['require_dev']),
            'total' => count($this->config['require']) + count($this->config['require_dev'])
        ];
    }
}

// Package Dependency Analyzer
class PackageDependencyAnalyzer
{
    private array $packages = [];
    private array $dependencies = [];
    private array $conflicts = [];
    private array $replacements = [];
    
    public function __construct()
    {
        $this->initializePackages();
    }
    
    /**
     * Initialize common packages
     */
    private function initializePackages(): void
    {
        $this->packages = [
            'php' => [
                'name' => 'PHP',
                'version' => '^8.0',
                'type' => 'php',
                'description' => 'PHP programming language'
            ],
            'ext-pdo' => [
                'name' => 'PDO Extension',
                'version' => '*',
                'type' => 'extension',
                'description' => 'PHP Data Objects'
            ],
            'ext-mbstring' => [
                'name' => 'MBString Extension',
                'version' => '*',
                'type' => 'extension',
                'description' => 'Multibyte string functions'
            ],
            'symfony/console' => [
                'name' => 'Symfony Console',
                'version' => '^6.0',
                'type' => 'package',
                'description' => 'Symfony Console component',
                'dependencies' => ['php:>=8.0', 'symfony/polyfill-ctype:~1.0']
            ],
            'guzzlehttp/guzzle' => [
                'name' => 'Guzzle HTTP',
                'version' => '^7.0',
                'type' => 'package',
                'description' => 'HTTP client library',
                'dependencies' => ['php:>=7.2.5', 'ext-json:*', 'psr/http-message:^1.0']
            ],
            'monolog/monolog' => [
                'name' => 'Monolog',
                'version' => '^2.0',
                'type' => 'package',
                'description' => 'Logging library',
                'dependencies' => ['php:>=7.2', 'psr/log:^1.0']
            ],
            'doctrine/orm' => [
                'name' => 'Doctrine ORM',
                'version' => '^2.10',
                'type' => 'package',
                'description' => 'Object-Relational Mapper',
                'dependencies' => ['php:>=7.4', 'doctrine/dbal:^2.10', 'doctrine/persistence:^2.0']
            ],
            'laravel/framework' => [
                'name' => 'Laravel Framework',
                'version' => '^9.0',
                'type' => 'package',
                'description' => 'PHP web framework',
                'dependencies' => ['php:>=8.0.2', 'illuminate/database:9.*', 'illuminate/http:9.*']
            ],
            'phpunit/phpunit' => [
                'name' => 'PHPUnit',
                'version' => '^9.5',
                'type' => 'package',
                'description' => 'Testing framework',
                'dependencies' => ['php:>=7.3', 'ext-dom:*', 'ext-json:*', 'ext-mbstring:*']
            ]
        ];
    }
    
    /**
     * Analyze dependencies
     */
    public function analyzeDependencies(array $requirements): array
    {
        $analysis = [
            'packages' => [],
            'conflicts' => [],
            'missing' => [],
            'versions' => []
        ];
        
        foreach ($requirements as $package => $version) {
            if (!isset($this->packages[$package])) {
                $analysis['missing'][] = $package;
                continue;
            }
            
            $pkg = $this->packages[$package];
            $analysis['packages'][$package] = $pkg;
            $analysis['versions'][$package] = [
                'required' => $version,
                'available' => $pkg['version'],
                'compatible' => $this->isVersionCompatible($version, $pkg['version'])
            ];
            
            // Analyze sub-dependencies
            if (isset($pkg['dependencies'])) {
                foreach ($pkg['dependencies'] as $dep => $depVersion) {
                    if (!isset($requirements[$dep])) {
                        $analysis['missing'][] = $dep;
                    }
                }
            }
        }
        
        return $analysis;
    }
    
    /**
     * Check version compatibility
     */
    private function isVersionCompatible(string $required, string $available): bool
    {
        // Simplified version checking
        if ($required === '*') {
            return true;
        }
        
        if (str_starts_with($required, '^')) {
            $requiredMajor = substr($required, 1);
            $availableMajor = $this->getMajorVersion($available);
            return $requiredMajor === $availableMajor;
        }
        
        if (str_starts_with($required, '~')) {
            $requiredMajor = substr($required, 1);
            $availableMajor = $this->getMajorVersion($available);
            return $requiredMajor === $availableMajor;
        }
        
        if (str_starts_with($required, '>=')) {
            return version_compare($available, substr($required, 2), '>=');
        }
        
        return version_compare($available, $required, '>=');
    }
    
    /**
     * Get major version
     */
    private function getMajorVersion(string $version): string
    {
        $parts = explode('.', $version);
        return $parts[0];
    }
    
    /**
     * Generate dependency graph
     */
    public function generateDependencyGraph(array $requirements): array
    {
        $graph = [];
        
        foreach ($requirements as $package => $version) {
            $graph[$package] = $this->buildDependencyTree($package, $version);
        }
        
        return $graph;
    }
    
    /**
     * Build dependency tree
     */
    private function buildDependencyTree(string $package, string $version, array $visited = []): array
    {
        if (in_array($package, $visited)) {
            return ['circular' => true];
        }
        
        $visited[] = $package;
        
        $tree = [
            'package' => $package,
            'version' => $version,
            'dependencies' => []
        ];
        
        if (isset($this->packages[$package]['dependencies'])) {
            foreach ($this->packages[$package]['dependencies'] as $dep => $depVersion) {
                $tree['dependencies'][$dep] = $this->buildDependencyTree($dep, $depVersion, $visited);
            }
        }
        
        return $tree;
    }
    
    /**
     * Check for conflicts
     */
    public function checkConflicts(array $requirements): array
    {
        $conflicts = [];
        
        // Check for version conflicts
        foreach ($requirements as $package => $version) {
            if (isset($this->packages[$package])) {
                $available = $this->packages[$package]['version'];
                if (!$this->isVersionCompatible($version, $available)) {
                    $conflicts[] = [
                        'package' => $package,
                        'required' => $version,
                        'available' => $available,
                        'type' => 'version_conflict'
                    ];
                }
            }
        }
        
        return $conflicts;
    }
    
    /**
     * Suggest alternatives
     */
    public function suggestAlternatives(string $package): array
    {
        $alternatives = [
            'guzzlehttp/guzzle' => ['symfony/http-client', 'react/http-client'],
            'monolog/monolog' => ['psr/log'],
            'doctrine/orm' => ['illuminate/database', 'eloquent'],
            'phpunit/phpunit' => ['pestphp/pest', 'codeception/codeception'],
            'symfony/console' => ['laravel/framework', 'zend/console']
        ];
        
        return $alternatives[$package] ?? [];
    }
}

// Package Publisher
class PackagePublisher
{
    private array $packageInfo = [];
    private array $files = [];
    private array $metadata = [];
    
    public function __construct(array $packageInfo = [])
    {
        $this->packageInfo = array_merge([
            'name' => 'vendor/package',
            'description' => 'Package description',
            'version' => '1.0.0',
            'type' => 'library',
            'license' => 'MIT',
            'authors' => [],
            'keywords' => [],
            'homepage' => '',
            'support' => []
        ], $packageInfo);
    }
    
    /**
     * Set package information
     */
    public function setPackageInfo(array $info): void
    {
        $this->packageInfo = array_merge($this->packageInfo, $info);
    }
    
    /**
     * Add file to package
     */
    public function addFile(string $path, string $content): void
    {
        $this->files[$path] = $content;
    }
    
    /**
     * Generate package structure
     */
    public function generatePackageStructure(): array
    {
        $structure = [
            'composer.json' => $this->generateComposerJson(),
            'README.md' => $this->generateReadme(),
            'LICENSE' => $this->generateLicense(),
            'CHANGELOG.md' => $this->generateChangelog(),
            'CONTRIBUTING.md' => $this->generateContributing()
        ];
        
        // Add custom files
        foreach ($this->files as $path => $content) {
            $structure[$path] = $content;
        }
        
        return $structure;
    }
    
    /**
     * Generate composer.json for package
     */
    private function generateComposerJson(): string
    {
        $composer = [
            'name' => $this->packageInfo['name'],
            'description' => $this->packageInfo['description'],
            'version' => $this->packageInfo['version'],
            'type' => $this->packageInfo['type'],
            'license' => $this->packageInfo['license'],
            'authors' => $this->packageInfo['authors'],
            'keywords' => $this->packageInfo['keywords'],
            'homepage' => $this->packageInfo['homepage'],
            'support' => $this->packageInfo['support'],
            'require' => [
                'php' => '^8.0'
            ],
            'require-dev' => [
                'phpunit/phpunit' => '^9.5',
                'phpstan/phpstan' => '^1.0'
            ],
            'autoload' => [
                'psr-4' => [
                    str_replace('/', '\\', $this->packageInfo['name']) . '\\' => 'src/'
                ]
            ],
            'autoload-dev' => [
                'psr-4' => [
                    str_replace('/', '\\', $this->packageInfo['name']) . '\\Tests\\' => 'tests/'
                ]
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true
        ];
        
        return json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Generate README
     */
    private function generateReadme(): string
    {
        $name = $this->packageInfo['name'];
        $description = $this->packageInfo['description'];
        
        $readme = "# {$name}\n\n";
        $readme .= "{$description}\n\n";
        $readme .= "## Installation\n\n";
        $readme .= "```bash\n";
        $readme .= "composer require {$name}\n";
        $readme .= "```\n\n";
        $readme .= "## Usage\n\n";
        $readme .= "```php\n";
        $readme .= "use " . str_replace('/', '\\', $name) . "\\ClassName;\n\n";
        $readme .= "\$instance = new ClassName();\n";
        $readme .= "```\n\n";
        $readme .= "## Testing\n\n";
        $readme .= "```bash\n";
        $readme .= "composer test\n";
        $readme .= "```\n\n";
        $readme .= "## License\n\n";
        $readme .= "This package is licensed under the {$this->packageInfo['license']} License.\n";
        
        return $readme;
    }
    
    /**
     * Generate LICENSE
     */
    private function generateLicense(): string
    {
        $license = "MIT License\n\n";
        $license .= "Copyright (c) " . date('Y') . " " . ($this->packageInfo['authors'][0]['name'] ?? 'Author') . "\n\n";
        $license .= "Permission is hereby granted, free of charge, to any person obtaining a copy\n";
        $license .= "of this software and associated documentation files (the \"Software\"), to deal\n";
        $license .= "in the Software without restriction, including without limitation the rights\n";
        $license .= "to use, copy, modify, merge, publish, distribute, sublicense, and/or sell\n";
        $license .= "copies of the Software, and to permit persons to whom the Software is\n";
        $license .= "furnished to do so, subject to the following conditions:\n\n";
        $license .= "The above copyright notice and this permission notice shall be included in all\n";
        $license .= "copies or substantial portions of the Software.\n\n";
        $license .= "THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR\n";
        $license .= "IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,\n";
        $license .= "FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE\n";
        $license .= "AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER\n";
        $license .= "LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,\n";
        $license .= "OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE\n";
        $license .= "SOFTWARE.\n";
        
        return $license;
    }
    
    /**
     * Generate CHANGELOG
     */
    private function generateChangelog(): string
    {
        $changelog = "# Changelog\n\n";
        $changelog .= "All notable changes to this project will be documented in this file.\n\n";
        $changelog .= "The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),\n";
        $changelog .= "and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).\n\n";
        $changelog .= "## [Unreleased]\n\n";
        $changelog .= "### Added\n";
        $changelog .= "- Initial release\n\n";
        $changelog .= "## [{$this->packageInfo['version']}] - " . date('Y-m-d') . "\n\n";
        $changelog .= "### Added\n";
        $changelog .= "- Initial functionality\n";
        $changelog .= "- Basic features\n\n";
        
        return $changelog;
    }
    
    /**
     * Generate CONTRIBUTING
     */
    private function generateContributing(): string
    {
        $contributing = "# Contributing to {$this->packageInfo['name']}\n\n";
        $contributing .= "Thank you for considering contributing to this project!\n\n";
        $contributing .= "## How to Contribute\n\n";
        $contributing .= "1. Fork the repository\n";
        $contributing .= "2. Create a feature branch\n";
        $contributing .= "3. Make your changes\n";
        $contributing .= "4. Add tests\n";
        $contributing .= "5. Run the test suite\n";
        $contributing .= "6. Submit a pull request\n\n";
        $contributing .= "## Development Setup\n\n";
        $contributing .= "```bash\n";
        $contributing .= "git clone <your-fork>\n";
        $contributing .= "cd {$this->packageInfo['name']}\n";
        $contributing .= "composer install\n";
        $contributing .= "```\n\n";
        $contributing .= "## Testing\n\n";
        $contributing .= "```bash\n";
        $contributing .= "composer test\n";
        $contributing .= "```\n\n";
        
        return $contributing;
    }
    
    /**
     * Validate package
     */
    public function validatePackage(): array
    {
        $errors = [];
        $warnings = [];
        
        // Check required fields
        if (empty($this->packageInfo['name'])) {
            $errors[] = 'Package name is required';
        }
        
        if (empty($this->packageInfo['description'])) {
            $errors[] = 'Package description is required';
        }
        
        if (empty($this->packageInfo['license'])) {
            $warnings[] = 'Package license is recommended';
        }
        
        // Validate package name
        if (!empty($this->packageInfo['name']) && !preg_match('/^[a-z0-9_.-]+\/[a-z0-9_.-]+$/', $this->packageInfo['name'])) {
            $errors[] = 'Package name must be in format "vendor/package"';
        }
        
        // Check for source files
        $hasSource = false;
        foreach ($this->files as $path => $content) {
            if (str_starts_with($path, 'src/')) {
                $hasSource = true;
                break;
            }
        }
        
        if (!$hasSource) {
            $warnings[] = 'No source files found in src/ directory';
        }
        
        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'valid' => empty($errors)
        ];
    }
    
    /**
     * Simulate package publishing
     */
    public function publish(string $repository = 'packagist'): array
    {
        $validation = $this->validatePackage();
        
        if (!$validation['valid']) {
            return [
                'status' => 'error',
                'errors' => $validation['errors']
            ];
        }
        
        echo "Publishing package to $repository...\n";
        
        return [
            'status' => 'success',
            'package' => $this->packageInfo['name'],
            'version' => $this->packageInfo['version'],
            'repository' => $repository,
            'url' => "https://$repository.org/packages/{$this->packageInfo['name']}",
            'published_at' => date('Y-m-d H:i:s')
        ];
    }
}

// Version Manager
class VersionManager
{
    private string $currentVersion;
    private array $versionHistory = [];
    
    public function __construct(string $initialVersion = '1.0.0')
    {
        $this->currentVersion = $initialVersion;
        $this->versionHistory[] = [
            'version' => $initialVersion,
            'type' => 'initial',
            'date' => date('Y-m-d H:i:s'),
            'changes' => ['Initial release']
        ];
    }
    
    /**
     * Get current version
     */
    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }
    
    /**
     * Bump major version
     */
    public function bumpMajor(array $changes = []): string
    {
        $parts = explode('.', $this->currentVersion);
        $parts[0] = (int)$parts[0] + 1;
        $parts[1] = 0;
        $parts[2] = 0;
        
        $this->currentVersion = implode('.', $parts);
        
        $this->versionHistory[] = [
            'version' => $this->currentVersion,
            'type' => 'major',
            'date' => date('Y-m-d H:i:s'),
            'changes' => $changes ?: ['Major version bump']
        ];
        
        return $this->currentVersion;
    }
    
    /**
     * Bump minor version
     */
    public function bumpMinor(array $changes = []): string
    {
        $parts = explode('.', $this->currentVersion);
        $parts[1] = (int)$parts[1] + 1;
        $parts[2] = 0;
        
        $this->currentVersion = implode('.', $parts);
        
        $this->versionHistory[] = [
            'version' => $this->currentVersion,
            'type' => 'minor',
            'date' => date('Y-m-d H:i:s'),
            'changes' => $changes ?: ['Minor version bump']
        ];
        
        return $this->currentVersion;
    }
    
    /**
     * Bump patch version
     */
    public function bumpPatch(array $changes = []): string
    {
        $parts = explode('.', $this->currentVersion);
        $parts[2] = (int)$parts[2] + 1;
        
        $this->currentVersion = implode('.', $parts);
        
        $this->versionHistory[] = [
            'version' => $this->currentVersion,
            'type' => 'patch',
            'date' => date('Y-m-d H:i:s'),
            'changes' => $changes ?: ['Patch version bump']
        ];
        
        return $this->currentVersion;
    }
    
    /**
     * Add pre-release suffix
     */
    public function addPreRelease(string $suffix): string
    {
        $this->currentVersion .= '-' . $suffix;
        
        $this->versionHistory[] = [
            'version' => $this->currentVersion,
            'type' => 'pre-release',
            'date' => date('Y-m-d H:i:s'),
            'changes' => ["Pre-release: $suffix"]
        ];
        
        return $this->currentVersion;
    }
    
    /**
     * Get version history
     */
    public function getVersionHistory(): array
    {
        return $this->versionHistory;
    }
    
    /**
     * Compare versions
     */
    public function compareVersions(string $version1, string $version2): int
    {
        return version_compare($version1, $version2);
    }
    
    /**
     * Validate version format
     */
    public function validateVersion(string $version): bool
    {
        return preg_match('/^\d+\.\d+\.\d+(-[a-zA-Z0-9]+)?$/', $version);
    }
    
    /**
     * Get next version
     */
    public function getNextVersion(string $type): string
    {
        $parts = explode('.', $this->currentVersion);
        
        switch ($type) {
            case 'major':
                return ((int)$parts[0] + 1) . '.0.0';
            case 'minor':
                return $parts[0] . '.' . ((int)$parts[1] + 1) . '.0';
            case 'patch':
                return $parts[0] . '.' . $parts[1] . '.' . ((int)$parts[2] + 1);
            default:
                return $this->currentVersion;
        }
    }
}

// Package Management Examples
class PackageManagementExamples
{
    private ComposerConfigurationManager $composer;
    private PackageDependencyAnalyzer $analyzer;
    private PackagePublisher $publisher;
    private VersionManager $versionManager;
    
    public function __construct()
    {
        $this->composer = new ComposerConfigurationManager();
        $this->analyzer = new PackageDependencyAnalyzer();
        $this->publisher = new PackagePublisher();
        $this->versionManager = new VersionManager();
    }
    
    public function demonstrateComposerConfiguration(): void
    {
        echo "Composer Configuration Examples\n";
        echo str_repeat("-", 35) . "\n";
        
        // Set up project
        $this->composer->setProjectInfo(
            'example/my-project',
            'A sample PHP project',
            'project',
            'MIT'
        );
        
        // Add requirements
        $this->composer->requirePackage('php', '^8.0');
        $this->composer->requirePackage('guzzlehttp/guzzle', '^7.0');
        $this->composer->requirePackage('monolog/monolog', '^2.0');
        $this->composer->requirePackage('phpunit/phpunit', '^9.5', true);
        
        // Add autoloading
        $this->composer->addAutoload('psr-4', 'Example\\MyProject\\', 'src/');
        $this->composer->addAutoload('psr-4', 'Example\\MyProject\\Tests\\', 'tests/', true);
        
        // Add scripts
        $this->composer->addScript('test', 'vendor/bin/phpunit');
        $this->composer->addScript('lint', 'vendor/bin/phpstan analyse');
        $this->composer->addScript('fix', 'vendor/bin/php-cs-fixer fix');
        
        // Generate composer.json
        $composerJson = $this->composer->generateComposerJson();
        
        echo "Generated composer.json:\n";
        echo substr($composerJson, 0, 500) . "...\n\n";
        
        // Validate configuration
        $validation = $this->composer->validateConfiguration();
        echo "Configuration Validation:\n";
        echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
        
        if (!empty($validation['errors'])) {
            echo "Errors:\n";
            foreach ($validation['errors'] as $error) {
                echo "  - $error\n";
            }
        }
        
        if (!empty($validation['warnings'])) {
            echo "Warnings:\n";
            foreach ($validation['warnings'] as $warning) {
                echo "  - $warning\n";
            }
        }
        
        // Show dependency tree
        $tree = $this->composer->getDependencyTree();
        echo "\nDependency Tree:\n";
        echo "Direct dependencies: " . count($tree['direct']) . "\n";
        echo "Dev dependencies: " . count($tree['dev']) . "\n";
        echo "Total dependencies: " . $tree['total'] . "\n";
    }
    
    public function demonstrateDependencyAnalysis(): void
    {
        echo "\nDependency Analysis Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Analyze dependencies
        $requirements = [
            'php' => '^8.0',
            'guzzlehttp/guzzle' => '^7.0',
            'monolog/monolog' => '^2.0',
            'symfony/console' => '^6.0',
            'phpunit/phpunit' => '^9.5',
            'nonexistent/package' => '^1.0'
        ];
        
        $analysis = $this->analyzer->analyzeDependencies($requirements);
        
        echo "Dependency Analysis:\n";
        echo "Packages found: " . count($analysis['packages']) . "\n";
        echo "Missing packages: " . count($analysis['missing']) . "\n\n";
        
        echo "Version Compatibility:\n";
        foreach ($analysis['versions'] as $package => $info) {
            $status = $info['compatible'] ? '✓' : '✗';
            echo "  $status $package: {$info['required']} (available: {$info['available']})\n";
        }
        
        if (!empty($analysis['missing'])) {
            echo "\nMissing Packages:\n";
            foreach ($analysis['missing'] as $package) {
                echo "  - $package\n";
            }
        }
        
        // Check conflicts
        $conflicts = $this->analyzer->checkConflicts($requirements);
        if (!empty($conflicts)) {
            echo "\nConflicts:\n";
            foreach ($conflicts as $conflict) {
                echo "  - {$conflict['package']}: required {$conflict['required']}, available {$conflict['available']}\n";
            }
        }
        
        // Generate dependency graph
        $graph = $this->analyzer->generateDependencyGraph(['guzzlehttp/guzzle', 'symfony/console']);
        echo "\nDependency Graph:\n";
        echo json_encode($graph, JSON_PRETTY_PRINT) . "\n";
        
        // Suggest alternatives
        $alternatives = $this->analyzer->suggestAlternatives('guzzlehttp/guzzle');
        echo "\nAlternatives to guzzlehttp/guzzle:\n";
        foreach ($alternatives as $alt) {
            echo "  - $alt\n";
        }
    }
    
    public function demonstratePackagePublishing(): void
    {
        echo "\nPackage Publishing Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        // Set up package info
        $this->publisher->setPackageInfo([
            'name' => 'example/utility-package',
            'description' => 'A utility package for common PHP functions',
            'version' => '1.0.0',
            'type' => 'library',
            'license' => 'MIT',
            'authors' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john@example.com'
                ]
            ],
            'keywords' => ['utility', 'helper', 'php'],
            'homepage' => 'https://github.com/example/utility-package'
        ]);
        
        // Add source files
        $this->publisher->addFile('src/UtilityClass.php', "<?php\nnamespace Example\\UtilityPackage;\n\nclass UtilityClass {\n    public static function formatText(string \$text): string {\n        return ucfirst(strtolower(trim(\$text)));\n    }\n}\n");
        
        $this->publisher->addFile('tests/UtilityClassTest.php', "<?php\nnamespace Example\\UtilityPackage\\Tests;\n\nuse PHPUnit\\Framework\\TestCase;\nuse Example\\UtilityPackage\\UtilityClass;\n\nclass UtilityClassTest extends TestCase {\n    public function testFormatText() {\n        \$this->assertEquals('Hello', UtilityClass::formatText('hello'));\n    }\n}\n");
        
        // Generate package structure
        $structure = $this->publisher->generatePackageStructure();
        
        echo "Package Structure:\n";
        foreach (array_keys($structure) as $file) {
            echo "  - $file\n";
        }
        
        // Validate package
        $validation = $this->publisher->validatePackage();
        echo "\nPackage Validation:\n";
        echo "Valid: " . ($validation['valid'] ? 'Yes' : 'No') . "\n";
        
        if (!empty($validation['errors'])) {
            echo "Errors:\n";
            foreach ($validation['errors'] as $error) {
                echo "  - $error\n";
            }
        }
        
        if (!empty($validation['warnings'])) {
            echo "Warnings:\n";
            foreach ($validation['warnings'] as $warning) {
                echo "  - $warning\n";
            }
        }
        
        // Publish package
        $publishResult = $this->publisher->publish();
        echo "\nPublish Result:\n";
        echo "Status: {$publishResult['status']}\n";
        if ($publishResult['status'] === 'success') {
            echo "Package: {$publishResult['package']}\n";
            echo "Version: {$publishResult['version']}\n";
            echo "URL: {$publishResult['url']}\n";
            echo "Published at: {$publishResult['published_at']}\n";
        }
    }
    
    public function demonstrateVersionManagement(): void
    {
        echo "\nVersion Management Examples\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "Current version: " . $this->versionManager->getCurrentVersion() . "\n\n";
        
        // Bump versions
        echo "Bumping patch version...\n";
        $patchVersion = $this->versionManager->bumpPatch(['Fixed bug in utility function']);
        echo "New version: $patchVersion\n\n";
        
        echo "Bumping minor version...\n";
        $minorVersion = $this->versionManager->bumpMinor(['Added new utility functions']);
        echo "New version: $minorVersion\n\n";
        
        echo "Bumping major version...\n";
        $majorVersion = $this->versionManager->bumpMajor(['Breaking changes to API']);
        echo "New version: $majorVersion\n\n";
        
        // Add pre-release
        echo "Adding pre-release...\n";
        $preReleaseVersion = $this->versionManager->addPreRelease('alpha.1');
        echo "New version: $preReleaseVersion\n\n";
        
        // Show version history
        $history = $this->versionManager->getVersionHistory();
        echo "Version History:\n";
        foreach ($history as $entry) {
            echo "  {$entry['version']} ({$entry['type']}) - {$entry['date']}\n";
            foreach ($entry['changes'] as $change) {
                echo "    - $change\n";
            }
            echo "\n";
        }
        
        // Compare versions
        echo "Version Comparisons:\n";
        echo "1.0.0 vs 1.1.0: " . $this->versionManager->compareVersions('1.0.0', '1.1.0') . "\n";
        echo "2.0.0 vs 1.9.9: " . $this->versionManager->compareVersions('2.0.0', '1.9.9') . "\n";
        echo "1.2.3 vs 1.2.3: " . $this->versionManager->compareVersions('1.2.3', '1.2.3') . "\n\n";
        
        // Get next versions
        echo "Next versions:\n";
        echo "Major: " . $this->versionManager->getNextVersion('major') . "\n";
        echo "Minor: " . $this->versionManager->getNextVersion('minor') . "\n";
        echo "Patch: " . $this->versionManager->getNextVersion('patch') . "\n";
        
        // Validate versions
        echo "\nVersion Validation:\n";
        $versions = ['1.0.0', '2.1.3', '1.0.0-alpha', 'invalid', '1.2', '1.2.3.4'];
        foreach ($versions as $version) {
            $valid = $this->versionManager->validateVersion($version);
            echo "  $version: " . ($valid ? 'Valid' : 'Invalid') . "\n";
        }
    }
    
    public function demonstratePackageWorkflows(): void
    {
        echo "\nPackage Development Workflow\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Package Development:\n";
        echo "   - Create package structure\n";
        echo "   - Write source code\n";
        echo "   - Add tests\n";
        echo "   - Configure autoloading\n";
        echo "   - Write documentation\n\n";
        
        echo "2. Dependency Management:\n";
        echo "   - Define requirements in composer.json\n";
        echo "   - Use semantic versioning\n";
        echo "   - Check compatibility\n";
        echo "   - Update dependencies regularly\n";
        echo "   - Use lock files for reproducibility\n\n";
        
        echo "3. Testing:\n";
        echo "   - Write unit tests\n";
        echo "   - Use continuous integration\n";
        echo "   - Test against multiple PHP versions\n";
        echo "   - Check code quality\n";
        echo "   - Ensure test coverage\n\n";
        
        echo "4. Publishing:\n";
        echo "   - Validate package structure\n";
        echo "   - Tag releases\n";
        echo "   - Publish to Packagist\n";
        echo "   - Update documentation\n";
        echo "   - Announce release\n\n";
        
        echo "5. Maintenance:\n";
        echo "   - Monitor issues and pull requests\n";
        echo "   - Fix bugs promptly\n";
        echo "   - Update dependencies\n";
        echo "   - Release new versions\n";
        echo "   - Maintain backward compatibility";
    }
    
    public function runAllExamples(): void
    {
        echo "PHP Package Management Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstrateComposerConfiguration();
        $this->demonstrateDependencyAnalysis();
        $this->demonstratePackagePublishing();
        $this->demonstrateVersionManagement();
        $this->demonstratePackageWorkflows();
    }
}

// Package Management Best Practices
function printPackageManagementBestPractices(): void
{
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Package Management Best Practices\n";
    echo str_repeat("=", 50) . "\n";
    
    echo "1. Composer Configuration:\n";
    echo "   • Use semantic versioning\n";
    echo "   • Define clear dependencies\n";
    echo "   • Use PSR autoloading\n";
    echo "   • Include development dependencies\n";
    echo "   • Configure scripts for common tasks\n\n";
    
    echo "2. Dependency Management:\n";
    echo "   • Keep dependencies minimal\n";
    echo "   • Use stable versions\n";
    echo "   • Check compatibility\n";
    echo "   • Update regularly\n";
    echo "   • Use lock files\n\n";
    
    echo "3. Package Development:\n";
    echo "   • Follow PSR standards\n";
    echo "   • Write comprehensive tests\n";
    echo "   • Document your code\n";
    echo "   • Use semantic versioning\n";
    echo "   • Include examples\n\n";
    
    echo "4. Version Management:\n";
    echo "   • Use semantic versioning\n";
    echo "   • Tag releases properly\n";
    echo "   • Maintain changelog\n";
    echo "   • Communicate changes\n";
    echo "   • Support multiple versions\n\n";
    
    echo "5. Publishing:\n";
    echo "   • Validate before publishing\n";
    echo "   • Use proper package naming\n";
    echo "   • Include metadata\n";
    echo "   • Set up continuous integration\n";
    echo "   • Monitor package usage\n\n";
    
    echo "6. Security:\n";
    echo "   • Scan for vulnerabilities\n";
    echo "   • Update dependencies\n";
    echo "   • Use private packages\n";
    echo "   • Implement access controls\n";
    echo "   • Monitor security advisories";
}

// Main execution
function runPackageManagementDemo(): void
{
    $examples = new PackageManagementExamples();
    $examples->runAllExamples();
    printPackageManagementBestPractices();
}

// Run the demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runPackageManagementDemo();
}
?>

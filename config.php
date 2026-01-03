<?php
// AgriTrust Protocol - Universal Configuration
// Works on Cloud PaaS (Render) and Shared Hosting (InfinityFree/cPanel)

// Load environment variables
require_once __DIR__ . '/core/EnvLoader.php';
EnvLoader::load(__DIR__ . '/.env');

// Debug mode from environment
define('DEBUG_MODE', EnvLoader::get('DEBUG_MODE', 'false') === 'true');

// Database Configuration
function getDbConfig() {
    return [
        'host' => EnvLoader::get('DB_HOST', 'localhost'),
        'username' => EnvLoader::get('DB_USERNAME', ''),
        'password' => EnvLoader::get('DB_PASSWORD', ''),
        'database' => EnvLoader::get('DB_NAME', ''),
        'port' => EnvLoader::get('DB_PORT', 3306)
    ];
}

// Database Connection
function getDbConnection() {
    $config = getDbConfig();
    
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die("Database connection failed: " . $e->getMessage());
        }
        die("Database connection failed");
    }
}

// JSON Response Helper (Fixes CORS issues on free hosts)
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    echo json_encode($data);
    exit;
}

// Error handling
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Base URL detection
$baseUrl = EnvLoader::get('BASE_URL');
if (!$baseUrl) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . $host . $script;
}
define('BASE_URL', $baseUrl);

// Security functions
function getJwtSecret() {
    return EnvLoader::get('JWT_SECRET', 'default_secret_change_this');
}

function getEncryptionKey() {
    return EnvLoader::get('ENCRYPTION_KEY', 'default_key_change_this_32_chars');
}
?>
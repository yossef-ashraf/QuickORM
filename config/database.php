<?php

function findDotEnv($startDir) {
    $dir = $startDir;
    $attempts = 0;
    $maxAttempts = 10;
    
    while (!file_exists($dir . '/.env')) {
        $parent = dirname($dir);
        if ($parent === $dir || $attempts >= $maxAttempts) {
            return null;
        }
        $dir = $parent;
        $attempts++;
    }
    return $dir . '/.env';
}

$envPath = findDotEnv(__DIR__);

if ($envPath !== null && file_exists($envPath)) {
    $envVariables = parse_ini_file($envPath);
} else {
    $envVariables = [];
}

return [
    'driver' => $envVariables['DB_DRIVER'] ?? 'mysql', 
    'host' => $envVariables['DB_HOST'] ?? '127.0.0.1',
    'port' => $envVariables['DB_PORT'] ?? 3306, 
    'dbname' => $envVariables['DB_NAME'] ?? 'dbname',
    'user' => $envVariables['DB_USER'] ?? 'root',
    'password' => $envVariables['DB_PASSWORD'] ?? '',
    'charset' => $envVariables['DB_CHARSET'] ?? 'utf8mb4', 
];
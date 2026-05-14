<?php
// Simple test version - Step by step debugging
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Step 1: Can we start session?
try {
    session_start();
    $step1 = 'OK';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'step' => 1, 'error' => $e->getMessage()]);
    exit;
}

// Step 2: Can we read input?
try {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    $step2 = 'OK';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'step' => 2, 'error' => $e->getMessage()]);
    exit;
}

// Step 3: Can we find database config?
$configPath = __DIR__ . '/../config/database.php';
$step3 = file_exists($configPath) ? 'OK' : 'File not found: ' . $configPath;

// Step 4: Can we require it?
try {
    if (file_exists($configPath)) {
        require_once $configPath;
        $step4 = 'OK';
    } else {
        $step4 = 'Config not found';
    }
} catch (Exception $e) {
    $step4 = 'Error: ' . $e->getMessage();
}

// Step 5: Can we create database connection?
try {
    if (class_exists('Database')) {
        $db = new Database();
        $pdo = $db->getConnection();
        $step5 = 'OK - Connected';
    } else {
        $step5 = 'Database class not found';
    }
} catch (Exception $e) {
    $step5 = 'Error: ' . $e->getMessage();
}

echo json_encode([
    'success' => true,
    'message' => 'Diagnostic complete',
    'steps' => [
        'session_start' => $step1,
        'read_input' => $step2,
        'find_config' => $step3,
        'require_config' => $step4,
        'db_connection' => $step5
    ],
    'input_received' => $input ?? null,
    'config_path' => $configPath
]);

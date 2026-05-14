<?php
// api/auth/driver-login-get.php - Driver Login API (GET method workaround)

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Accept GET with query parameter (base64 encoded for security)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    session_start();
    require_once '../config/database.php';

    // Get driver code from query parameter
    $driverCode = trim($_GET['code'] ?? '');

    if (empty($driverCode)) {
        throw new Exception('Driver code is required');
    }

    // Decode if base64 encoded
    if (preg_match('/^[A-Za-z0-9+\/=]+$/', $driverCode) && strlen($driverCode) % 4 === 0) {
        $decoded = base64_decode($driverCode, true);
        if ($decoded !== false) {
            $driverCode = $decoded;
        }
    }

    $db = new Database();
    $pdo = $db->getConnection();

    $sql = "SELECT id, username, name, phone_number, status, code
            FROM drivers
            WHERE code = :driver_code OR id = :driver_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':driver_code' => $driverCode,
        ':driver_id' => is_numeric($driverCode) ? intval($driverCode) : 0
    ]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$driver) {
        throw new Exception('Invalid driver code');
    }

    if ($driver['status'] !== 'active') {
        throw new Exception('Your account is inactive. Please contact administrator.');
    }

    // Create session
    $_SESSION['driver_id'] = $driver['id'];
    $_SESSION['driver_name'] = $driver['name'];
    $_SESSION['driver_username'] = $driver['username'];
    $_SESSION['role'] = 'driver';
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    unset($driver['password']);

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'driver' => $driver,
            'session_id' => session_id()
        ]
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

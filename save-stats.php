<?php
/**
 * Save reading statistics to MySQL
 * RSVP Rychločtečka v1.1.2
 */

require_once 'config.php';

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required = ['text', 'wpm', 'total_time_seconds', 'words_read', 'time_saved_seconds', 'current_speed', 'language', 'is_demo'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }
    
    // Connect to database
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    // Hash IP address for privacy
    $ip = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown';
    $ip_hash = hash('sha256', $ip . IP_HASH_SALT);
    
    // Get referer
    $referer = $_SERVER['HTTP_REFERER'] ?? null;
    
    // Truncate text if too long (store first 10000 chars)
    $text = mb_substr($input['text'], 0, 10000);
    
    // Prepare and execute insert
    $sql = "INSERT INTO reading_stats 
            (created_at, text_content, text_length, wpm_achieved, total_time_seconds, 
             words_read, time_saved_seconds, current_speed, language, is_demo, 
             referer, ip_hash)
            VALUES 
            (NOW(), :text, :text_length, :wpm, :total_time, 
             :words_read, :time_saved, :current_speed, :language, :is_demo,
             :referer, :ip_hash)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':text' => $text,
        ':text_length' => mb_strlen($input['text']),
        ':wpm' => (int)$input['wpm'],
        ':total_time' => (int)$input['total_time_seconds'],
        ':words_read' => (int)$input['words_read'],
        ':time_saved' => (int)$input['time_saved_seconds'],
        ':current_speed' => (int)$input['current_speed'],
        ':language' => $input['language'] === 'en' ? 'en' : 'cs',
        ':is_demo' => $input['is_demo'] ? 1 : 0,
        ':referer' => $referer,
        ':ip_hash' => $ip_hash
    ]);
    
    $id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'id' => $id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}

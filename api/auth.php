<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'login') {
        handleLogin($input);
    } elseif ($action === 'signup') {
        handleSignup($input);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} elseif ($method === 'DELETE') {
    handleLogout();
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function handleLogin($input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }
    
    // Use Supabase Auth API for login
    $authUrl = SUPABASE_URL . '/auth/v1/token?grant_type=password';
    
    $data = [
        'email' => $email,
        'password' => $password
    ];
    
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $authUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        // Set HTTP-only cookie with the access token
        setcookie(
            'auth_token',
            $result['access_token'],
            [
                'expires' => time() + (7 * 24 * 60 * 60), // 7 days
                'path' => '/',
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]
        );
        
        echo json_encode(['success' => true, 'user' => $result['user']]);
    } else {
        $error = json_decode($response, true);
        http_response_code(401);
        echo json_encode(['error' => $error['error_description'] ?? 'Login failed']);
    }
}

function handleSignup($input) {
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $fullName = $input['fullName'] ?? '';
    
    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }
    
    // Use Supabase Auth API for signup
    $authUrl = SUPABASE_URL . '/auth/v1/signup';
    
    $data = [
        'email' => $email,
        'password' => $password,
        'data' => [
            'full_name' => $fullName
        ]
    ];
    
    $headers = [
        'apikey: ' . SUPABASE_SERVICE_KEY,
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $authUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        // Set HTTP-only cookie with the access token
        setcookie(
            'auth_token',
            $result['access_token'],
            [
                'expires' => time() + (7 * 24 * 60 * 60), // 7 days
                'path' => '/',
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']),
                'samesite' => 'Lax'
            ]
        );
        
        echo json_encode(['success' => true, 'user' => $result['user']]);
    } else {
        $error = json_decode($response, true);
        http_response_code(400);
        echo json_encode(['error' => $error['error_description'] ?? 'Signup failed']);
    }
}

function handleLogout() {
    // Clear the auth cookie
    setcookie(
        'auth_token',
        '',
        [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax'
        ]
    );
    
    echo json_encode(['success' => true]);
}
?>
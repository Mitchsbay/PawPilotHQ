<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Verify authentication
$authToken = $_COOKIE['auth_token'] ?? null;
if (!$authToken) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$supabase = new SupabaseClient();
$user = $supabase->verifyToken($authToken);

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid authentication']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$petId = $_GET['pet_id'] ?? null;

if ($method === 'GET') {
    getHealthRecords($supabase, $user, $authToken, $petId);
} elseif ($method === 'POST') {
    addHealthRecord($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getHealthRecords($supabase, $user, $authToken, $petId) {
    $userId = $user['id'];
    
    if ($petId) {
        // Get records for specific pet, but verify ownership first
        $petOwnership = $supabase->makeRequest("pets?id=eq.$petId&user_id=eq.$userId", 'GET', null, $authToken);
        if ($petOwnership['status'] !== 200 || empty($petOwnership['data'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        $query = "health_records?pet_id=eq.$petId&select=*&order=created_at.desc";
    } else {
        // Get records for all user's pets
        $query = "health_records?pet_id=in.(select id from pets where user_id=eq.$userId)&select=*,pet:pets(name)&order=created_at.desc";
    }
    
    $result = $supabase->makeRequest($query, 'GET', null, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data']);
    } else {
        echo json_encode([]);
    }
}

function addHealthRecord($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $user['id'];
    $petId = $input['pet_id'] ?? null;
    
    if (!$petId) {
        http_response_code(400);
        echo json_encode(['error' => 'Pet ID is required']);
        return;
    }
    
    // Verify pet ownership
    $petOwnership = $supabase->makeRequest("pets?id=eq.$petId&user_id=eq.$userId", 'GET', null, $authToken);
    if ($petOwnership['status'] !== 200 || empty($petOwnership['data'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        return;
    }
    
    $recordData = [
        'pet_id' => $petId,
        'title' => $input['title'] ?? '',
        'description' => $input['description'] ?? '',
        'type' => $input['type'] ?? 'general',
        'date' => $input['date'] ?? date('Y-m-d'),
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('health_records', 'POST', $recordData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to add health record']);
    }
}
?>
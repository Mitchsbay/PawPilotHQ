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

if ($method === 'GET') {
    getPets($supabase, $user, $authToken);
} elseif ($method === 'POST') {
    addPet($supabase, $user, $authToken);
} elseif ($method === 'PUT') {
    updatePet($supabase, $user, $authToken);
} elseif ($method === 'DELETE') {
    deletePet($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getPets($supabase, $user, $authToken) {
    $userId = $user['id'];
    $result = $supabase->makeRequest("pets?user_id=eq.$userId&select=*", 'GET', null, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data']);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to fetch pets']);
    }
}

function addPet($supabase, $user, $authToken) {
    $userId = $user['id'];
    
    // Handle file upload if present
    $avatarUrl = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // In a real implementation, you would upload to Supabase Storage
        $avatarUrl = 'images/pets/' . uniqid() . '_' . $_FILES['avatar']['name'];
    }
    
    $petData = [
        'user_id' => $userId,
        'name' => $_POST['name'] ?? '',
        'breed' => $_POST['breed'] ?? null,
        'age' => $_POST['age'] ? intval($_POST['age']) : null,
        'gender' => $_POST['gender'] ?? null,
        'description' => $_POST['description'] ?? null,
        'avatar_url' => $avatarUrl,
        'health_score' => 70, // Default health score
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('pets', 'POST', $petData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to add pet']);
    }
}

function updatePet($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $petId = $input['id'] ?? null;
    $userId = $user['id'];
    
    if (!$petId) {
        http_response_code(400);
        echo json_encode(['error' => 'Pet ID is required']);
        return;
    }
    
    // Verify ownership
    $ownership = $supabase->makeRequest("pets?id=eq.$petId&user_id=eq.$userId", 'GET', null, $authToken);
    if ($ownership['status'] !== 200 || empty($ownership['data'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        return;
    }
    
    $updateData = array_intersect_key($input, array_flip(['name', 'breed', 'age', 'gender', 'description']));
    $updateData['updated_at'] = date('c');
    
    $result = $supabase->makeRequest("pets?id=eq.$petId", 'PATCH', $updateData, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to update pet']);
    }
}

function deletePet($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $petId = $input['id'] ?? null;
    $userId = $user['id'];
    
    if (!$petId) {
        http_response_code(400);
        echo json_encode(['error' => 'Pet ID is required']);
        return;
    }
    
    // Verify ownership
    $ownership = $supabase->makeRequest("pets?id=eq.$petId&user_id=eq.$userId", 'GET', null, $authToken);
    if ($ownership['status'] !== 200 || empty($ownership['data'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        return;
    }
    
    $result = $supabase->makeRequest("pets?id=eq.$petId", 'DELETE', null, $authToken);
    
    if ($result['status'] === 204) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to delete pet']);
    }
}
?>
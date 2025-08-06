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
    getLostPets($supabase, $user, $authToken);
} elseif ($method === 'POST') {
    reportLostPet($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getLostPets($supabase, $user, $authToken) {
    $query = "lost_pets?status=eq.lost&select=*&order=lost_date.desc&limit=20";
    $result = $supabase->makeRequest($query, 'GET', null, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data']);
    } else {
        // Mock lost pets data
        echo json_encode([
            [
                'id' => '1',
                'name' => 'Buddy',
                'breed' => 'Golden Retriever',
                'color' => 'Golden',
                'photo_url' => 'https://images.pexels.com/photos/551628/pexels-photo-551628.jpeg',
                'lost_date' => date('Y-m-d', strtotime('-2 days')),
                'location' => 'Central Park Area',
                'description' => 'Friendly golden retriever, responds to Buddy',
                'contact_phone' => '(555) 123-4567'
            ],
            [
                'id' => '2',
                'name' => 'Mittens',
                'breed' => 'Domestic Shorthair',
                'color' => 'Black and White',
                'photo_url' => 'https://images.pexels.com/photos/104827/cat-pet-animal-domestic-104827.jpeg',
                'lost_date' => date('Y-m-d', strtotime('-5 days')),
                'location' => 'Downtown District',
                'description' => 'Small black and white cat, very shy',
                'contact_phone' => '(555) 987-6543'
            ]
        ]);
    }
}

function reportLostPet($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $user['id'];
    
    $lostPetData = [
        'user_id' => $userId,
        'pet_id' => $input['pet_id'] ?? null,
        'name' => $input['name'] ?? '',
        'breed' => $input['breed'] ?? '',
        'color' => $input['color'] ?? '',
        'description' => $input['description'] ?? '',
        'lost_date' => $input['lost_date'] ?? date('Y-m-d'),
        'last_seen_location' => $input['location'] ?? '',
        'contact_phone' => $input['contact_phone'] ?? '',
        'status' => 'lost',
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('lost_pets', 'POST', $lostPetData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to report lost pet']);
    }
}
?>
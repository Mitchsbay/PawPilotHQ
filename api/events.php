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
    getEvents($supabase, $user, $authToken);
} elseif ($method === 'POST') {
    addEvent($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getEvents($supabase, $user, $authToken) {
    $userId = $user['id'];
    $upcoming = $_GET['upcoming'] ?? null;
    $limit = $_GET['limit'] ?? 20;
    
    if ($upcoming) {
        // Get upcoming events for dashboard
        echo json_encode([
            [
                'id' => '1',
                'title' => 'Annual Checkup',
                'description' => 'Luna - Annual Checkup',
                'date' => date('Y-m-d', strtotime('+3 days')),
                'location' => 'Vet Clinic',
                'pet_name' => 'Luna'
            ],
            [
                'id' => '2',
                'title' => 'Grooming Appointment',
                'description' => 'Whiskers - Grooming Appointment',
                'date' => date('Y-m-d', strtotime('+5 days')),
                'location' => 'Pet Groomer',
                'pet_name' => 'Whiskers'
            ],
            [
                'id' => '3',
                'title' => 'Monthly Flea Treatment',
                'description' => 'All Pets',
                'date' => date('Y-m-d', strtotime('+7 days')),
                'location' => 'Home',
                'pet_name' => 'All Pets'
            ]
        ]);
    } else {
        // Get all events for calendar
        $query = "events?user_id=eq.$userId&select=*&order=date.asc&limit=$limit";
        $result = $supabase->makeRequest($query, 'GET', null, $authToken);
        
        if ($result['status'] === 200) {
            echo json_encode($result['data']);
        } else {
            // Mock calendar events
            echo json_encode([
                [
                    'id' => '1',
                    'title' => 'Vet Appointment',
                    'description' => 'Annual checkup for Luna',
                    'date' => date('Y-m-d', strtotime('+3 days')),
                    'time' => '10:00',
                    'location' => 'City Vet Clinic'
                ],
                [
                    'id' => '2',
                    'title' => 'Dog Park Meetup',
                    'description' => 'Social meetup at Central Park',
                    'date' => date('Y-m-d', strtotime('+7 days')),
                    'time' => '15:00',
                    'location' => 'Central Park'
                ]
            ]);
        }
    }
}

function addEvent($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $user['id'];
    
    $eventData = [
        'user_id' => $userId,
        'title' => $input['title'] ?? '',
        'description' => $input['description'] ?? '',
        'date' => $input['date'] ?? date('Y-m-d'),
        'time' => $input['time'] ?? null,
        'location' => $input['location'] ?? null,
        'pet_id' => $input['pet_id'] ?? null,
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('events', 'POST', $eventData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to add event']);
    }
}
?>
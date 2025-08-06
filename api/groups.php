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
    getGroups($supabase, $user, $authToken);
} elseif ($method === 'POST') {
    handleGroupAction($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function getGroups($supabase, $user, $authToken) {
    $userId = $user['id'];
    $my = $_GET['my'] ?? null;
    $discover = $_GET['discover'] ?? null;
    
    if ($my) {
        // Get user's groups
        $query = "groups?id=in.(select group_id from group_members where user_id=eq.$userId)&select=*";
        $result = $supabase->makeRequest($query, 'GET', null, $authToken);
        
        if ($result['status'] === 200) {
            echo json_encode($result['data']);
        } else {
            echo json_encode([]);
        }
    } elseif ($discover) {
        // Get suggested groups (not joined by user)
        $query = "groups?id=not.in.(select group_id from group_members where user_id=eq.$userId)&select=*&limit=10";
        $result = $supabase->makeRequest($query, 'GET', null, $authToken);
        
        if ($result['status'] === 200) {
            echo json_encode($result['data']);
        } else {
            // Mock data for discover groups
            echo json_encode([
                [
                    'id' => '1',
                    'name' => 'Golden Retriever Lovers',
                    'description' => 'A community for Golden Retriever enthusiasts',
                    'avatar_url' => 'images/groups/golden-retriever.jpg',
                    'members_count' => 1247,
                    'posts_count' => 89
                ],
                [
                    'id' => '2',
                    'name' => 'Cat Parents Unite',
                    'description' => 'Support and advice for cat owners',
                    'avatar_url' => 'images/groups/cat-parents.jpg',
                    'members_count' => 892,
                    'posts_count' => 156
                ],
                [
                    'id' => '3',
                    'name' => 'Local Dog Park Friends',
                    'description' => 'Connect with local dog owners in your area',
                    'avatar_url' => 'images/groups/dog-park.jpg',
                    'members_count' => 423,
                    'posts_count' => 67
                ]
            ]);
        }
    } else {
        // Get all groups
        $result = $supabase->makeRequest('groups?select=*', 'GET', null, $authToken);
        
        if ($result['status'] === 200) {
            echo json_encode($result['data']);
        } else {
            echo json_encode([]);
        }
    }
}

function handleGroupAction($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $groupId = $input['group_id'] ?? null;
    $userId = $user['id'];
    
    if ($action === 'join' && $groupId) {
        $memberData = [
            'user_id' => $userId,
            'group_id' => $groupId,
            'joined_at' => date('c')
        ];
        
        $result = $supabase->makeRequest('group_members', 'POST', $memberData, $authToken);
        
        if ($result['status'] === 201) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code($result['status']);
            echo json_encode(['error' => 'Failed to join group']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action or missing group ID']);
    }
}
?>
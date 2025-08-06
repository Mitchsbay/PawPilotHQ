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
    handleGetRequest($supabase, $user, $authToken);
} elseif ($method === 'POST') {
    handlePostRequest($supabase, $user, $authToken);
} elseif ($method === 'PATCH') {
    handlePatchRequest($supabase, $user, $authToken);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

function handleGetRequest($supabase, $user, $authToken) {
    $filter = $_GET['filter'] ?? 'all';
    $limit = $_GET['limit'] ?? 20;
    $comments = $_GET['comments'] ?? null;
    $stats = $_GET['stats'] ?? null;
    $feed = $_GET['feed'] ?? null;
    $highlights = $_GET['highlights'] ?? null;
    
    if ($comments) {
        getPostComments($supabase, $comments, $authToken);
    } elseif ($stats) {
        getUserStats($supabase, $user, $authToken);
    } elseif ($feed === 'recent') {
        getRecentActivity($supabase, $user, $authToken, $limit);
    } elseif ($highlights) {
        getCommunityHighlights($supabase, $authToken, $limit);
    } else {
        getPosts($supabase, $user, $authToken, $filter, $limit);
    }
}

function getPosts($supabase, $user, $authToken, $filter, $limit) {
    $userId = $user['id'];
    $query = "posts?select=*,author:profiles(name,avatar_url)&order=created_at.desc&limit=$limit";
    
    if ($filter === 'friends') {
        // In a real implementation, you would filter by friends
        $query .= "&visibility=eq.friends";
    } elseif ($filter === 'public') {
        $query .= "&visibility=eq.public";
    } elseif ($filter === 'liked') {
        // In a real implementation, you would join with likes table
        $query .= "&liked_by_user=eq.$userId";
    }
    
    $result = $supabase->makeRequest($query, 'GET', null, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data']);
    } else {
        echo json_encode([]);
    }
}

function getPostComments($supabase, $postId, $authToken) {
    $query = "comments?post_id=eq.$postId&select=*,author:profiles(name,avatar_url)&order=created_at.asc";
    $result = $supabase->makeRequest($query, 'GET', null, $authToken);
    
    if ($result['status'] === 200) {
        echo json_encode($result['data']);
    } else {
        echo json_encode([]);
    }
}

function getUserStats($supabase, $user, $authToken) {
    $userId = $user['id'];
    
    // Mock data for now - in real implementation, you would query actual stats
    echo json_encode([
        'posts_count' => 12,
        'likes_received' => 256,
        'comments_count' => 48,
        'followers_count' => 89
    ]);
}

function getRecentActivity($supabase, $user, $authToken, $limit) {
    // Mock activity data
    echo json_encode([
        [
            'id' => '1',
            'user_name' => 'Luna received her annual vaccination',
            'user_avatar' => 'images/default-avatar.png',
            'action' => 'received her annual vaccination',
            'created_at' => date('c', strtotime('-2 hours')),
            'type' => 'health'
        ],
        [
            'id' => '2',
            'user_name' => 'You received 5 likes on your post about Whiskers',
            'user_avatar' => 'images/default-avatar.png',
            'action' => 'received 5 likes on your post about Whiskers',
            'created_at' => date('c', strtotime('-5 hours')),
            'type' => 'social'
        ]
    ]);
}

function getCommunityHighlights($supabase, $authToken, $limit) {
    // Mock highlights data
    echo json_encode([
        [
            'id' => '1',
            'title' => 'Puppy Training Tips',
            'excerpt' => 'Great discussion on house training techniques...',
            'likes' => 24,
            'comments' => 8
        ],
        [
            'id' => '2',
            'title' => 'Best Cat Food Reviews',
            'excerpt' => 'Community reviews of the latest cat nutrition...',
            'likes' => 18,
            'comments' => 12
        ]
    ]);
}

function handlePostRequest($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'add_comment') {
        addComment($supabase, $user, $authToken, $input);
    } else {
        createPost($supabase, $user, $authToken, $input);
    }
}

function createPost($supabase, $user, $authToken, $input) {
    $userId = $user['id'];
    
    $postData = [
        'user_id' => $userId,
        'content' => $input['content'] ?? '',
        'image_url' => $input['image_url'] ?? null,
        'visibility' => $input['visibility'] ?? 'public',
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('posts', 'POST', $postData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to create post']);
    }
}

function addComment($supabase, $user, $authToken, $input) {
    $userId = $user['id'];
    
    $commentData = [
        'user_id' => $userId,
        'post_id' => $input['post_id'] ?? '',
        'content' => $input['content'] ?? '',
        'created_at' => date('c')
    ];
    
    $result = $supabase->makeRequest('comments', 'POST', $commentData, $authToken);
    
    if ($result['status'] === 201) {
        echo json_encode($result['data'][0]);
    } else {
        http_response_code($result['status']);
        echo json_encode(['error' => 'Failed to add comment']);
    }
}

function handlePatchRequest($supabase, $user, $authToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'toggle_like') {
        toggleLike($supabase, $user, $authToken, $input);
    }
}

function toggleLike($supabase, $user, $authToken, $input) {
    $userId = $user['id'];
    $postId = $input['post_id'] ?? '';
    
    // Check if user already liked this post
    $existingLike = $supabase->makeRequest("likes?user_id=eq.$userId&post_id=eq.$postId", 'GET', null, $authToken);
    
    if ($existingLike['status'] === 200 && !empty($existingLike['data'])) {
        // Unlike
        $result = $supabase->makeRequest("likes?user_id=eq.$userId&post_id=eq.$postId", 'DELETE', null, $authToken);
    } else {
        // Like
        $likeData = [
            'user_id' => $userId,
            'post_id' => $postId,
            'created_at' => date('c')
        ];
        $result = $supabase->makeRequest('likes', 'POST', $likeData, $authToken);
    }
    
    echo json_encode(['success' => true]);
}
?>
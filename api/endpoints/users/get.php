<?php
// Endpoint: GET /api/users/{id}
$auth_user = $auth->requireAuth();

$user = new User($db);
if($user->getById($user_id)) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role,
            'department' => $user->department,
            'is_active' => $user->is_active
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
}
?>

<?php
// Endpoint: POST /api/auth/logout
$result = $auth->logout();
http_response_code(200);
echo json_encode($result);
?>

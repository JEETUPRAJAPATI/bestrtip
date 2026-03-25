<?php
// webhook.php - Place this file on your server
file_put_contents('webhook_log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);

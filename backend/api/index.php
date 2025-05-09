<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/Auth.php';
require_once __DIR__ . '/../models/Resource.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$endpoint = $request[0] ?? '';

// Initialize response array
$response = ['success' => false, 'message' => 'Invalid endpoint'];

// Handle authentication
$auth = new Auth();
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

// Public endpoints that don't require authentication
$public_endpoints = ['login', 'register'];

if (!in_array($endpoint, $public_endpoints) && !$auth->validateToken($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Route the request
switch($endpoint) {
    case 'login':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $response = $auth->login($data['username'], $data['password']);
        }
        break;

    case 'register':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $response = $auth->register(
                $data['username'],
                $data['email'],
                $data['password'],
                $data['full_name']
            );
        }
        break;

    case 'resources':
        $resource = new Resource();
        $id = $request[1] ?? null;

        switch($method) {
            case 'GET':
                if ($id) {
                    $response = $resource->getById($id);
                } else {
                    $page = $_GET['page'] ?? 1;
                    $limit = $_GET['limit'] ?? 10;
                    $response = $resource->getAll($page, $limit);
                }
                break;

            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                $response = $resource->create(
                    $data['title'],
                    $data['description'],
                    $data['type'],
                    $data['file_path'],
                    $data['created_by'],
                    $data['categories'] ?? []
                );
                break;

            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $response = $resource->update(
                        $id,
                        $data['title'],
                        $data['description'],
                        $data['type'],
                        $data['categories'] ?? []
                    );
                }
                break;

            case 'DELETE':
                if ($id) {
                    $response = $resource->delete($id);
                }
                break;
        }
        break;

    default:
        $response = ['success' => false, 'message' => 'Invalid endpoint'];
}

// Send response
http_response_code($response['success'] ? 200 : 400);
echo json_encode($response);
?> 
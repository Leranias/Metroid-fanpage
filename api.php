<?php
// api.php die soll der einzige ansprechpartner für java sein

require_once 'game_logic.php';

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    switch ($action) {
        case 'collect_crystal':
            if (isset($_POST['crystal_id'])) {
                collect_crystal((int)$_POST['crystal_id']);
                $response = get_game_state();
            } else {
                http_response_code(400); // Bad Request
                $response = ['error' => 'crystal_id not provided'];
            }
            break;
            
        case 'reset_game':
            initialize_game();
            $response = get_game_state();
            break;

        default:
            http_response_code(400); // Bad Request
            $response = ['error' => 'Invalid action'];
            break;
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response = ['error' => 'Only POST method is allowed'];
}

echo json_encode($response);
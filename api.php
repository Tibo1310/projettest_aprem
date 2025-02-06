<?php
header('Content-Type: application/json');
require_once 'UserManager.php';

// Ajout de logs
error_log("Requête reçue : " . $_SERVER['REQUEST_METHOD']);

$userManager = new UserManager();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    error_log("GET request");
    echo json_encode($userManager->getUsers());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request");
    $rawData = file_get_contents('php://input');
    error_log("Données reçues : " . $rawData);
    
    $data = json_decode($rawData, true);
    
    try {
        switch ($data['action']) {
            case 'add':
                error_log("Action: add");
                $userManager->addUser($data['name'], $data['email']);
                break;
            case 'update':
                error_log("Action: update");
                $userManager->updateUser($data['id'], $data['name'], $data['email']);
                break;
            case 'delete':
                error_log("Action: delete");
                $userManager->removeUser($data['id']);
                break;
        }
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log("Erreur : " . $e->getMessage());
        http_response_code(400);
        echo $e->getMessage();
    }
    exit;
}
?>

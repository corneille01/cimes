<?php
// include/session_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Vérification basique : si l'utilisateur n'est pas connecté, on renvoie 403
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

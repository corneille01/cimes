<?php
session_start();
header('Content-Type: application/json');

// Assurez-vous que l'utilisateur est connecté
if (isset($_SESSION['firstname']) && isset($_SESSION['lastname']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    echo json_encode([
        'firstname' => $_SESSION['firstname'],
        'lastname' => $_SESSION['lastname']
    ]);
} else {
    echo json_encode([
        'firstname' => null,
        'lastname' => null
    ]);
}

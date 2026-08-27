<?php

ob_start();
session_start();

header('Content-Type: application/json');

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);

    exit();
}

try {

    $conn = getConnexion();

    /* =========================
       1. RÉCUPÉRATION DONNÉES
    ========================== */

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $role = "user";

    /* =========================
       2. VALIDATION CHAMPS
    ========================== */

    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        echo json_encode([
            'success' => false,
            'message' => 'Tous les champs sont obligatoires.'
        ]);

        exit();
    }

    /* =========================
       3. VALIDATION EMAIL
    ========================== */

    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Format email invalide.'
    //     ]);

    //     exit();
    // }

    /* =========================
       4. VALIDATION PASSWORD
    ========================== */

    // if (strlen($password) < 8) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Mot de passe trop court (8 caractères minimum).'
    //     ]);

    //     exit();
    // }

    // if (!preg_match('/[A-Z]/', $password)) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Le mot de passe doit contenir une majuscule.'
    //     ]);

    //     exit();
    // }

    // if (!preg_match('/[a-z]/', $password)) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Le mot de passe doit contenir une minuscule.'
    //     ]);

    //     exit();
    // }

    // if (!preg_match('/[0-9]/', $password)) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Le mot de passe doit contenir un chiffre.'
    //     ]);

    //     exit();
    // }

    // if (!preg_match('/[\W_]/', $password)) {

    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Ajoute au moins un caractère spécial.'
    //     ]);

    //     exit();
    // }

    /* =========================
       5. CONFIRM PASSWORD
    ========================== */

    if ($password !== $confirm_password) {

        echo json_encode([
            'success' => false,
            'message' => 'Les mots de passe ne correspondent pas.'
        ]);

        exit();
    }

    /* =========================
       6. CHECK EMAIL EXISTE
    ========================== */

    $sql = "SELECT id 
            FROM cimes_utilisateurs 
            WHERE email = :email 
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':email', $email);

    $stmt->execute();

    $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userExists) {

        echo json_encode([
            'success' => false,
            'message' => 'Cet email est déjà utilisé.'
        ]);

        exit();
    }

    /* =========================
       7. HASH PASSWORD
    ========================== */

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    /* =========================
       8. INSERT USER
    ========================== */

    $sql = "INSERT INTO cimes_utilisateurs
            (lastname, firstname, email, password, role)
            VALUES
            (:lastname, :firstname, :email, :password, :role)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);

    $success = $stmt->execute();

    if (!$success) {

        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de l’inscription.'
        ]);

        exit();
    }







    /* =========================
       10. SUCCESS RESPONSE
    ========================== */

    echo json_encode([
        'success' => true,
        'message' => 'Inscription réussie.'
    ]);
} catch (PDOException $e) {

    error_log($e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Une erreur serveur est survenue.'
    ]);
}

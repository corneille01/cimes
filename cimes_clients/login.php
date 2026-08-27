<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json'); // ← important

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $conn = getConnexion();

        // ---------- Vérifier ADMIN ----------
        $sql = "SELECT * FROM cimes_admin WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['id'] = $admin['id'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = 'admin';
            $_SESSION['firstname'] = $admin['firstname'];
            $_SESSION['lastname'] = $admin['lastname'];

            echo json_encode(['success' => true, 'role' => 'admin']);
            exit();
        }

        // ---------- Vérifier USER ----------
        $sql = "SELECT * FROM cimes_utilisateurs WHERE email = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'user';
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];

            echo json_encode(['success' => true, 'role' => 'user']);
            exit();
        }

        // ---------- Échec ----------
        echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur.']);
        exit();
    }
}

<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $conn = getConnexion();
        $sql = "SELECT * FROM cimes_admin WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstname_admin'] = $user['nom'];
            $_SESSION['lastname_admin'] = $user['prénom'];
            header("Location: ../cimes_admin/navbar.php");
            exit();
        } else {
            echo "Email ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>

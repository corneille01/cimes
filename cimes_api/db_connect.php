
<?php
function getConnexion()
{
    try {
        return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}
?>
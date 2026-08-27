
<?php 
      $servername = "localhost";
      $username = "tandjaouidb_user";
      $password = "HYA*65bsi";
      $dbname = "tandjaoui_db";

try {
    // Crée une connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configure PDO pour lever des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    
    

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
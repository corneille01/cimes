<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html')?>
<?php include('include/header.html') ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une recherche</title>
    <style>
        /* Conteneur principal */
.container {
    max-width: 800px;
    margin: 0 auto;
    margin-top:150px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Styles des titres */
h1 {
    text-align: center;
    color: #112d3a;
    margin-bottom: 20px;
}

/* Styles des labels */
label {
    display: block;
    margin-bottom: 5px;
    color: #333;
}

/* Styles des champs de saisie */
input[type="text"],
input[type="date"],
textarea,
select,
input[type="file"] {
    width: calc(100% - 22px); /* Ajuste la largeur pour inclure le padding et les bordures */
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box; /* Inclut le padding et la bordure dans la largeur totale */
}

/* Styles du bouton */
button {
    display: block;
    width: 100%;
    padding: 15px;
    background-color: #112d3a;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

/* Styles du bouton au survol */
button:hover {
    background-color: #236898;
}
    </style>
</head>
<body>
    <div class="container">
    <h1>Ajouter une recherche</h1>
    <form id="recherche-form" enctype="multipart/form-data">
        <label for="thematique">Thématique:</label>
        <select id="thematique" name="thematique" required>
            <!-- Options des thematiques seront chargées via JS -->
        </select><br>

        <label for="titre">Titre:</label>
        <input type="text" id="titre" name="titre" required><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br>

        <label for="texte">Texte:</label>
        <textarea id="texte" name="texte" required></textarea><br>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*"><br>

        <button type="submit">Ajouter</button>
    </form>
    </div>
    <script src="js/ajoute_recherche_user.js"></script>
</body>
</html>

<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../cimes_clients/espace_admin.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de modification des publications</title>
</head>
<body>
<form class="formulaire" id="formPub">
        <h1>Modification d'une publication</h1>
        <div class="mb-3">
            <label for="thematique" class="form-label">Thématique :</label>
            <input type="text" class="form-control" id="thematique" required>
        </div>
        <div class="mb-3">
            <label for="titre" class="form-label">Titre :</label>
            <input type="text" class="form-control" id="titre" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="auteur">Auteur :</label>
            <input type="text" class="form-control" id="auteur" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="date">Date :</label>
            <input type="date" class="form-control" id="date" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="image">Image :</label>
            <input type="file" id="image" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="texte">Texte :</label>
            <textarea class="form-control" id="texte" required></textarea>
        </div>
        <input type="hidden" id="massif" value="<?php echo htmlspecialchars($_GET['id'] ?? '', ENT_QUOTES); ?>">
        
        <button type="submit" class="btn btn-primary" id="envoyer">Modifier la publication</button>
        <div id="erreur" class="alerte"></div>
    </form>
    <?php echo '<script> let id=' . $_GET['quelpubli'] . '</script>'; ?>
    <script>let lien='modif_publication';</script>
    <script src="js/value_publication.js" async defer></script>
    <script src="js/creer_publication.js" async defer></script>
</body>
</html>

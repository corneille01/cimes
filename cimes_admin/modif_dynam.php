<?php include('include/head.html'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification d'un évènement</title>
    <meta name="description" content="">
</head>
<body>
    <form class="formulaire">
        <h1 class="titre_admin">Modification d'un évènement<a href="navbar.php"><i class="fa-solid fa-house"></i></a></h1>
        <div class="mb-3">
            <label for="thematique" class="form-label">Rentrez la thematique</label>
            <input type="text" class="form-control" id="thematique">
        </div>
        <div class="mb-3">
            <label for="titre" class="form-label">Rentrez le titre</label>
            <input type="text" class="form-control" id="titre">
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Rentrez la date</label>
            <input type="date" class="form-control" id="date">
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Insérez une image</label>
            <input type="file" id="image" class="form-control">
        </div>
        <div class="mb-3">
            <label for="texte" class="form-label">Insérez un texte</label>
            <input type="text" id="texte" class="form-control">
        </div>
        
        <?php $parent_id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>
        <input type="hidden" id="parent_id" value="<?php echo $parent_id; ?>">
        <button type="submit" class="btn btn-primary" id="modifier">Modifier</button>
        <div id="erreur" class="alerte"></div>
    </form>
    
    <script src="js/modif_dynam.js" async defer></script>
</body>
</html>

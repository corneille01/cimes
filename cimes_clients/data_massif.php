
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Les massifs</title>
    <meta name="description" content="">
    <!-- Inclure Bootstrap CSS pour rendre le tableau responsive -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style></style>
    <body>
   
   <section class="ajouter">
       <div class="container">
           <h2>Administration de la gouvernance</h2>
           <a href="ajoute_massif.php" class="bouton_ajouter"><i class="fa-solid fa-plus"></i> Ajouter </a>
       </div>
   </section>
   <section class="container">
       <h2>Modifier ou supprimer élément à la base de données</h2>
       <div class="table-responsive">
           <table class="table">
               <thead>
                   <tr>
                       <th scope="col">Id</th>
                       <th scope="col">Nom </th>
                       <th scope="col">Chaine </th>
                       <th scope="col">Région </th>
                       <th scope="col">Pays </th>
                       <th scope="col">Modifier</th>
                       <th scope="col">Supprimer</th>
                       <th scope="col">Accéder à la page</th>
                   </tr>
               </thead>
               <tbody id="corp_tab"></tbody>
           </table>
       </div>
   </section>
   
    
    <!-- Modale pour la suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" id="deleteClose">&times;</span>
            <p class="texte_supp">Etes-vous certain de vouloir supprimer cet élément?</p>
            <div class="btn_supp">
                <button class="btn_oui_non" id="confirmDelete">Oui</button>
                <button class="btn_oui_non" id="cancelDelete">Non</button>
            </div>
        </div>
    </div>
   <!-- Inclure jQuery et Bootstrap JS -->
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <script src="js/code_massif.js" async defer></script>
</body>
</html>

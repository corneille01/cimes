<?php
session_start();
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes publications</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <style>
        h1 {
            text-align: center;
        }

        .table {
            margin-top: 50px;
        }

        .ajouter {
            margin: 100px 30px;
        }

        .ajouter .bouton_ajouter {
            display: flex;
            justify-content: center;
            align-items: center;
            /* Pour centrer le contenu verticalement */
            margin-top: 50px;
            background-color: #112d3a;
            color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 250px;
            /* Limite la largeur maximale du bouton */
            margin-left: auto;
            /* Centre le bouton horizontalement */
            margin-right: auto;
            /* Centre le bouton horizontalement */
        }

        .ajouter .bouton_ajouter:hover {
            background: #236898;
        }
    </style>
</head>

<body>

    <section class="ajouter">
        <h1>Mes publications</h1>
        <a href="ajoute_pub_user.php" id="ajoutPub" class="bouton_ajouter"> Ajouter une publication</a>
    </section>
    <section class="container">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">thématique</th>
                    <th scope="col">Publication</th>
                    <th scope="col">date</th>
                    <th scope="col">massif</th>
                    <th scope="col">Modifier</th>
                    <th scope="col">Supprimer</th>
                </tr>
            </thead>
            <tbody id="corp_tab"></tbody>
        </table>
    </section>
    <section class="panneau_supp" id="panneausup">
        <p class="texte_supp">Etes-vous certain de vouloir supprimer cette publication ?</p>
        <div class="btn_supp">
            <a href="javascript:void(0);" class="btn_oui_non" id="oui">oui</a>
            <a href="javascript:void(0);" class="btn_oui_non" id="non">non</a>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
            window.userId = userId; // Rendre l'ID utilisateur disponible pour les scripts externes

            // Charger les publications
            loadPublications();
        });
    </script>
    <script src="js/mes_publications.js" async defer></script>
</body>

</html>
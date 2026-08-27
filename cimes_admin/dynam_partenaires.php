<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des partenaires</title>
    <style>
        :root {
            --header-height: 80px;
            --green-dark: #0F6E56;
            --green: #1D9E75;
            --green-light: #eef5f2;
            --surface: #ffffff;
            --surface-alt: #f7f9f8;
            --border: rgba(0, 0, 0, 0.10);
            --border-md: rgba(0, 0, 0, 0.18);
            --text: #1e2420;
            --text-muted: #5f6b65;
            --red: #A32D2D;
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        body {
            background: #f2f5f3;
            font-family: system-ui, sans-serif;
            margin: 0;
        }

        .dynam-wrapper {
            max-width: 1400px;
            margin: calc(var(--header-height) + 20px) auto 40px;
            padding: 0 24px;
        }

        .dynam-toolbar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .bouton_retour,
        .bouton_ajouter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
        }

        .bouton_retour {
            background: #64748b;
            color: white;
        }

        .bouton_ajouter {
            background: var(--green-dark);
            color: white;
            cursor: pointer;
        }

        .filtre-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filtre-bar label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .filtre-select {
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: white;
        }

        .dynam-container {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 20px;
            overflow-x: auto;
        }

        .dynam-container h2 {
            margin-top: 0;
            margin-bottom: 16px;
            color: var(--green-dark);
            font-size: 1.2rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .table th {
            background: var(--surface-alt);
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .table a {
            margin: 0 4px;
            display: inline-block;
            padding: 6px 10px;
            border-radius: 8px;
            color: var(--green-dark);
        }

        .table a.supprimer-lien {
            color: var(--red);
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content2 {
            background: white;
            padding: 32px;
            border-radius: 16px;
            max-width: 400px;
            text-align: center;
        }

        .btn_oui_non {
            padding: 8px 20px;
            border-radius: 30px;
            background: #e2e8f0;
            color: #1e293b;
            text-decoration: none;
            margin: 0 5px;
            cursor: pointer;
        }

        #oui {
            background: var(--red);
            color: white;
        }

        #non {
            background: #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="dynam-wrapper">
        <div class="dynam-toolbar">
            <a href="navbar.php" class="bouton_retour"><i class="fas fa-arrow-left"></i> Retour</a>
            <a href="ajout_partenaires.php" class="bouton_ajouter"><i class="fas fa-plus"></i> Ajouter</a>
        </div>

        <!-- Filtre par catégorie -->
        <div class="filtre-bar">
            <label>Filtrer par catégorie :</label>
            <select id="filtre-categorie" class="filtre-select">
                <option value="">Toutes les catégories</option>
            </select>
        </div>

        <div class="dynam-container">
            <h2>Liste des partenaires</h2>
            <table class="table">
                <thead>
                    <tr>

                        <th>Nom du partenaire</th>
                        <th>Rôle</th>
                        <th>Catégorie</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody id="corp_tab"></tbody>
            </table>
        </div>
    </div>

    <div id="panneausup" class="modal">
        <div class="modal-content2">
            <span class="close">&times;</span>
            <p class="texte_supp">Êtes-vous certain de vouloir supprimer ?</p>
            <div class="btn_supp">
                <a href="javascript:void(0);" class="btn_oui_non" id="oui">Oui</a>
                <a href="javascript:void(0);" class="btn_oui_non" id="non">Non</a>
            </div>
        </div>
    </div>

    <script src="js/dynam_partenaires.js"></script>
    <?php include('include/footer.html'); ?>
</body>

</html>
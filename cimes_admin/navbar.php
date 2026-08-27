<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <meta name="description" content="">
    <style>
        /* ═══════════════════════════════════════════
           VARIABLES
        ═══════════════════════════════════════════ */
        :root {
            --header-height: 70px;
            --green-dark: #0F6E56;
            --green: #1D9E75;
            --green-light: #eef5f2;
            --green-ring: rgba(29, 158, 117, 0.15);
            --surface: #ffffff;
            --surface-alt: #f7f9f8;
            --surface-hover: #f0f5f3;
            --border: rgba(0, 0, 0, 0.10);
            --border-md: rgba(0, 0, 0, 0.18);
            --text: #1e2420;
            --text-muted: #5f6b65;
            --text-hint: #9aada6;
            --red: #A32D2D;
            --red-light: #fcebeb;
            --red-border: #F09595;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        /* ═══════════════════════════════════════════
           RESET & BASE
        ═══════════════════════════════════════════ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            background: #f2f5f3;
            color: var(--text);
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            /* empêche tout scroll du body */
            display: flex;
            flex-direction: column;
        }

        /* ═══════════════════════════════════════════
           CONTENEUR PRINCIPAL FIXÉ SOUS LE HEADER
        ═══════════════════════════════════════════ */
        .admin-wrapper {
            position: fixed;
            top: var(--header-height);
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
            background: #f2f5f3;

            display: flex;
            flex-direction: column;
            overflow: hidden;

            max-width: 1160px;
            margin: 0 auto;
            padding: 40px 32px 0 32px;
            /* padding haut et côtés, pas de padding bas ici */
        }

        /* ── Header admin figé ── */
        .admin-header {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-bottom: 30px;
        }

        .admin-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: var(--green-dark);
            text-align: center;
            letter-spacing: -0.02em;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--green-dark);
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 600;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(15, 110, 86, 0.20);
        }

        .dashboard-btn:hover {
            background: var(--green);
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(15, 110, 86, 0.28);
            color: #fff;
        }

        .dashboard-btn:active {
            transform: scale(0.98);
        }

        /* ═══════════════════════════════════════════
           BARRE D'ACTIONS (bouton centré) – optionnelle, conservée
        ═══════════════════════════════════════════ */
        .admin-toolbar {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
            flex-shrink: 0;
        }

        #addButton {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--green-dark);
            color: #fff;
            padding: 12px 26px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.01em;
            box-shadow: 0 2px 8px rgba(15, 110, 86, 0.25);
            white-space: nowrap;
        }

        #addButton::before {
            content: "+";
            font-size: 1.3rem;
            font-weight: 400;
            line-height: 1;
        }

        #addButton:hover {
            background-color: var(--green);
            box-shadow: 0 6px 14px rgba(15, 110, 86, 0.35);
            transform: translateY(-1px);
        }

        #addButton:active {
            transform: scale(0.98);
        }

        /* ═══════════════════════════════════════════
           CONTENEUR DU TABLEAU (remplit l’espace restant)
        ═══════════════════════════════════════════ */
        .navadmin-container {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            /* le conteneur lui-même ne défile pas */
            display: flex;
            flex-direction: column;

            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            margin-bottom: 40px;
            /* espace en bas */
        }

        /* ═══ Zone de défilement du tableau ═══ */
        .table-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            /* arrondis en bas si besoin */
        }

        /* ═══════════════════════════════════════════
           TABLEAU
        ═══════════════════════════════════════════ */
        .table {
            width: 100%;
            min-width: 720px;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        /* En-tête collant */
        .table thead {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--surface-alt);
            border-bottom: 1px solid var(--border-md);
        }

        .table thead th {
            padding: 14px 20px;
            text-align: center;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-hint);
            white-space: nowrap;
            background: var(--surface-alt);
        }

        /* Corps */
        .table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background: var(--surface-hover);
        }

        .table tbody th,
        .table tbody td {
            padding: 16px 20px;
            vertical-align: middle;
            text-align: center;
        }

        /* Colonne "Onglets" alignée à gauche */
        .table tbody th {
            text-align: left;
            white-space: nowrap;
        }

        tr.menu {
            background: #f9fcfa;
        }

        tr.menu th {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 0.92rem;
        }

        tr.sous-menu th {
            padding-left: 44px;
            font-weight: 400;
            color: var(--text-muted);
            font-size: 0.87rem;
            position: relative;
        }

        tr.sous-menu th::before {
            content: "";
            position: absolute;
            left: 28px;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 1px;
            background: var(--text-hint);
            opacity: 0.7;
        }

        /* Icônes d'action */
        .table a {
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: var(--radius-sm);
            transition: all 0.15s;
            border: 1px solid transparent;
        }

        .table a:hover {
            color: var(--green-dark);
            background: var(--green-light);
            border-color: var(--border-md);
        }

        .table a i {
            font-size: 0.95rem;
        }

        /* ── Drag & drop (inchangé) ── */
        .drag-handle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            margin-right: 4px;
            cursor: grab;
            color: var(--text-hint);
            border-radius: 4px;
            transition: color 0.15s, background 0.15s;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .drag-handle:hover {
            color: var(--green-dark);
            background: var(--green-light);
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .drag-handle i {
            font-size: 0.75rem;
            pointer-events: none;
        }

        tr.dragging {
            opacity: 0.2;
        }

        tr.drop-indicator td {
            height: 3px;
            padding: 0;
            background: var(--green);
            border-radius: 2px;
            box-shadow: 0 0 0 2px var(--green-light);
        }

        .drag-ghost-table {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            border-collapse: collapse;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
        }

        .accordion-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0 4px;
            color: var(--text-hint);
            vertical-align: middle;
            transition: color 0.15s;
        }

        .accordion-toggle:hover {
            color: var(--green-dark);
        }

        .accordion-toggle i {
            transition: transform 0.25s ease;
            font-size: 0.75rem;
        }

        tr.sub-hidden {
            display: none;
        }

        /* ═══════════════════════════════════════════
           MODALES (inchangées)
        ═══════════════════════════════════════════ */
        .navadmin-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 25, 20, 0.50);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .navadmin-modal[style*="display: block"] {
            display: flex !important;
        }

        .navadmin-modal-content {
            position: relative;
            background: var(--surface);
            padding: 36px 32px 32px;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 460px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-modal);
            border: 0.5px solid var(--border);
            animation: modal-in 0.18s ease;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .navadmin-close {
            position: absolute;
            top: 14px;
            right: 16px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--text-hint);
            cursor: pointer;
            background: transparent;
            border: none;
            transition: background 0.15s, color 0.15s;
        }

        .navadmin-close:hover {
            background: var(--red-light);
            color: var(--red);
        }

        .navadmin-modal-content h2 {
            margin: 0 0 24px;
            color: var(--green-dark);
            font-size: 1.2rem;
            font-weight: 600;
        }

        .navadmin-modal-content form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .navadmin-modal-content label {
            font-weight: 500;
            font-size: 0.75rem;
            color: var(--text-hint);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .navadmin-modal-content input,
        .navadmin-modal-content select,
        .navadmin-modal-content textarea {
            padding: 10px 12px;
            border: 0.5px solid var(--border-md);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            width: 100%;
            background: var(--surface-alt);
            color: var(--text);
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            outline: none;
            font-family: inherit;
        }

        .navadmin-modal-content input:focus,
        .navadmin-modal-content select:focus,
        .navadmin-modal-content textarea:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px var(--green-ring);
            background: var(--surface);
        }

        .navadmin-modal-content button[type="submit"] {
            margin-top: 4px;
            background: var(--green-dark);
            color: #fff;
            padding: 11px 20px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
        }

        .navadmin-modal-content button[type="submit"]:hover {
            background: var(--green);
        }

        .navadmin-modal-content button[type="submit"]:active {
            transform: scale(0.98);
        }

        .delete-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--red-light);
            color: var(--red);
            font-size: 1.4rem;
            margin: 0 auto 16px;
        }

        .texte_supp {
            font-size: 0.95rem;
            color: var(--text);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .texte_supp strong {
            display: block;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .btn_supp {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn_oui_non {
            padding: 10px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            transition: background 0.18s, transform 0.1s;
        }

        #confirmDelete {
            background: var(--red);
            color: #fff;
        }

        #confirmDelete:hover {
            background: #791F1F;
        }

        #cancelDelete {
            background: var(--surface-alt);
            color: var(--text-muted);
            border: 0.5px solid var(--border-md);
        }

        #cancelDelete:hover {
            background: var(--surface-hover);
            color: var(--text);
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .admin-wrapper {
                top: 60px;
                /* hauteur réduite du header sur mobile */
                padding: 20px 16px 0 16px;
            }

            .navadmin-container {
                margin-bottom: 20px;
                border-radius: 12px;
            }

            .table {
                min-width: 600px;
            }
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- <div class="admin-toolbar">
            <button id="addButton">Ajouter une nouvelle page</button>
        </div> -->

        <div class="admin-header">
            <h1 class="admin-title">
                Gestion des pages et du menu
            </h1>
            <a href="../cimes_clients/dashboard.php" class="dashboard-btn">
                ← Retour au tableau de bord
            </a>
        </div>

        <section class="navadmin-container">
            <!-- ⚠️ AJOUT DE LA DIV SCROLLABLE AUTOUR DU TABLEAU -->
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Onglets</th>
                            <th scope="col">Modifier</th>
                            <th scope="col">Supprimer</th>
                            <th scope="col">Visibilité</th>
                            <th scope="col">Accéder</th>
                        </tr>
                    </thead>
                    <tbody id="corp_tab"></tbody>
                </table>
            </div>
            <!-- FIN DE LA DIV SCROLLABLE -->
        </section>

    </div><!-- /.admin-wrapper -->

    <!-- MODALES (inchangées) -->
    <div id="addContentModal" class="navadmin-modal">
        <div class="navadmin-modal-content">
            <button class="navadmin-close" aria-label="Fermer">&times;</button>
            <h2>Ajouter du contenu</h2>
            <form id="addContentForm">
                <input type="hidden" id="contentParentId" name="parent_id">
                <div class="form-group">
                    <label for="thematique">Thématique</label>
                    <input type="text" id="thematique" name="thematique" required>
                </div>
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="texte">Texte</label>
                    <textarea id="texte" name="texte" required></textarea>
                </div>
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="navadmin-modal">
        <div class="navadmin-modal-content">
            <button class="navadmin-close" aria-label="Fermer">&times;</button>
            <h2>Modifier le nom</h2>
            <form id="editForm">
                <div class="form-group">
                    <label for="editName">Nom de la page</label>
                    <input type="text" id="editName" name="name" required>
                </div>
                <input type="hidden" id="editId" name="id">
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="navadmin-modal">
        <div class="navadmin-modal-content">
            <button class="navadmin-close" id="deleteClose" aria-label="Fermer">&times;</button>
            <div class="delete-icon">&#9888;</div>
            <p class="texte_supp">
                <strong>Supprimer cet élément ?</strong>
                Cette action est irréversible et supprimera également les sous-pages associées.
            </p>
            <div class="btn_supp">
                <button class="btn_oui_non" id="confirmDelete">Supprimer</button>
                <button class="btn_oui_non" id="cancelDelete">Annuler</button>
            </div>
        </div>
    </div>

    <div id="addSubMenuModal" class="navadmin-modal">
        <div class="navadmin-modal-content">
            <button class="navadmin-close" aria-label="Fermer">&times;</button>
            <h2>Ajouter une page</h2>
            <form id="addSubMenuForm">
                <div class="form-group">
                    <label for="parentSelect">Onglet principal</label>
                    <select id="parentSelect" name="parent_id" required></select>
                </div>
                <div class="form-group">
                    <label for="subMenuName">Nom de la page</label>
                    <input type="text" id="subMenuName" name="name" required>
                </div>
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <script src="js/navbar.js" defer></script>
</body>

</html>
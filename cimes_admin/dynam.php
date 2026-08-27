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
   VARIABLES (communes aux deux pages admin)
═══════════════════════════════════════════ */
        :root {
            --header-height: 80px;
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
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ═══ Wrapper principal : fixé sous le header, occupe tout l'espace restant ═══ */
        .dynam-wrapper {
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
        }

        /* La section titre + toolbar reste figée en haut du wrapper */
        .ajouter {
            flex-shrink: 0;
        }

        #titre {
            text-align: center;
            color: var(--green-dark);
            margin: 0 0 24px 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .dynam-toolbar {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .bouton_retour {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--text-muted);
            color: #fff;
            padding: 12px 26px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.01em;
            text-decoration: none;
            white-space: nowrap;
        }

        .bouton_retour:hover {
            background-color: #4a5550;
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
        }

        .bouton_retour:active {
            transform: scale(0.98);
        }

        .bouton_ajouter {
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
            text-decoration: none;
            white-space: nowrap;
        }

        .bouton_ajouter:hover {
            background-color: var(--green);
            box-shadow: 0 6px 14px rgba(15, 110, 86, 0.35);
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
        }

        .bouton_ajouter:active {
            transform: scale(0.98);
        }

        /* ═══ Conteneur principal de la liste (fixe en hauteur, ne défile pas) ═══ */
        .dynam-container {
            flex: 1 1 auto;
            min-height: 0;
            overflow: hidden;
            /* EMPÊCHE le scroll du conteneur lui-même */
            display: flex;
            flex-direction: column;

            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            padding: 24px;
            margin-bottom: 40px;
            width: 100%;
        }

        /* Les éléments au‑dessus du tableau restent fixes */
        .dynam-container>#breadcrumb-container,
        .dynam-container>h2 {
            flex-shrink: 0;
        }

        .dynam-container h2 {
            margin: 0 0 16px 0;
            color: var(--text);
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
        }

        #breadcrumb-container {
            margin-bottom: 16px;
            font-size: 0.85rem;
            color: var(--text-hint);
            text-align: center;
        }

        /* ═══ Zone de défilement du tableau ═══ */
        .table-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid var(--border);
            /* optionnel : ligne de séparation subtile */
            border-radius: var(--radius-sm);
        }

        /* ═══ Table elle‑même ═══ */
        .table {
            width: 100%;
            min-width: 600px;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        /* En‑tête collant pendant le défilement du tbody */
        .table thead {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--surface-alt);
            border-bottom: 1px solid var(--border-md);
        }

        .table thead th {
            padding: 14px 16px;
            text-align: center;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-hint);
            white-space: nowrap;
            background: var(--surface-alt);
        }

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

        .table tbody td,
        .table tbody th {
            padding: 14px 16px;
            vertical-align: middle;
            text-align: center;
        }

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

        .table a.supprimer-lien:hover {
            color: var(--red);
            background: var(--red-light);
            border-color: var(--red-border);
        }

        .table a i {
            font-size: 0.95rem;
        }

        /* ═══ Modales (inchangées) ═══ */
        .modal {
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

        .modal[style*="display: block"] {
            display: flex !important;
        }

        .modal-content2 {
            position: relative;
            background: var(--surface);
            padding: 36px 32px 32px;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 420px;
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

        .close {
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

        .close:hover {
            background: var(--red-light);
            color: var(--red);
        }

        .texte_supp {
            font-size: 0.95rem;
            color: var(--text);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 24px;
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        #oui {
            background: var(--red);
            color: #fff;
        }

        #oui:hover {
            background: #791F1F;
        }

        #non {
            background: var(--surface-alt);
            color: var(--text-muted);
            border: 0.5px solid var(--border-md);
        }

        #non:hover {
            background: var(--surface-hover);
            color: var(--text);
        }

        /* ═══ Adaptation mobile ═══ */
        @media (max-width: 768px) {
            .dynam-wrapper {
                top: 60px;
                padding: 20px 16px 0 16px;
            }

            .dynam-container {
                padding: 16px;
                margin-bottom: 20px;
                border-radius: 12px;
            }

            .table {
                min-width: 500px;
            }

            .table-scroll {
                border-radius: var(--radius-sm);
            }
        }
    </style>
</head>

<body>

    <div class="dynam-wrapper">
        <section class="ajouter">
            <h2 id="titre"></h2>
            <div class="dynam-toolbar">
                <a href="navbar.php" class="bouton_retour">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="#" id="ajout" class="bouton_ajouter">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
            </div>
        </section>
        <section class="dynam-container">
            <div id="breadcrumb-container"></div>
            <h2>Modifier ou supprimer un élément</h2>

            <!-- ⚠️ AJOUT DE LA DIV SCROLLABLE AUTOUR DU TABLEAU -->
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>

                            <th scope="col">titre</th>
                            <th scope="col">modifier</th>
                            <th scope="col">supprimer</th>
                        </tr>
                    </thead>
                    <tbody id="corp_tab"></tbody>
                </table>
            </div>
            <!-- FIN DE LA DIV SCROLLABLE -->

        </section>
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

    <?php $id = htmlspecialchars($_GET['id'] ?? ''); ?>
    <input type="hidden" id="main-id" value="<?php echo $id; ?>">

    <script src="js/dynam.js" async defer></script>
</body>

</html>
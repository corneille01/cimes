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
    <title>Administration – Carrousel</title>
    <meta name="description" content="Gestion des slides du carrousel">
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
        }

        .dynam-wrapper {
            max-width: 1160px;
            margin: 0 auto;
            padding: 40px 32px;
            margin-top: var(--header-height);
            scroll-margin-top: var(--header-height);
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

        .dynam-container {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            padding: 24px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .dynam-container h2 {
            margin: 0 0 16px 0;
            color: var(--text);
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
        }

        /* ── Miniature image ── */
        .slide-thumb {
            width: 80px;
            height: 50px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--border);
            background: var(--surface-alt);
        }

        .slide-thumb-placeholder {
            width: 80px;
            height: 50px;
            border-radius: var(--radius-sm);
            background: var(--surface-alt);
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-hint);
            font-size: 0.7rem;
        }

        .table {
            width: 100%;
            min-width: 700px;
            margin: 0 auto;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        .table thead {
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

        .table .col-id {
            width: 60px;
        }

        .table .col-image {
            width: 100px;
        }

        .table .col-titre {
            text-align: left;
            max-width: 250px;
        }

        .table .col-soustitre {
            text-align: left;
            max-width: 250px;
        }

        .table .col-modifier {
            width: 80px;
        }

        .table .col-supprimer {
            width: 80px;
        }

        .table .text-truncate {
            display: block;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        @media (max-width: 768px) {
            .dynam-wrapper {
                margin-top: 60px;
                padding: 20px 16px 30px;
            }

            .dynam-container {
                border-radius: 12px;
                padding: 16px;
            }

            .table {
                min-width: 600px;
            }
        }
    </style>
</head>

<body>

    <div class="dynam-wrapper">
        <section class="ajouter">
            <h2 id="titre">Gestion du Carrousel</h2>
            <div class="dynam-toolbar">
                <a href="../cimes_clients/dashboard.php" class="bouton_retour">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <!-- Pas de bouton Ajouter pour le carrousel (4 slides fixes) -->
            </div>
        </section>
        <section class="dynam-container">
            <h2>Les diapositives de la page d'accueil</h2>
            <table class="table">
                <thead>
                    <tr>

                        <th scope="col" class="col-image">image</th>
                        <th scope="col" class="col-titre">titre</th>
                        <th scope="col" class="col-soustitre">sous-titre</th>
                        <th scope="col" class="col-modifier">modifier</th>
                        <th scope="col" class="col-supprimer">supprimer</th>
                    </tr>
                </thead>
                <tbody id="corp_tab"></tbody>
            </table>
        </section>
    </div>

    <div id="panneausup" class="modal">
        <div class="modal-content2">
            <span class="close">&times;</span>
            <p class="texte_supp">Êtes-vous certain de vouloir supprimer ce slide ?</p>
            <div class="btn_supp">
                <a href="javascript:void(0);" class="btn_oui_non" id="oui">Oui</a>
                <a href="javascript:void(0);" class="btn_oui_non" id="non">Non</a>
            </div>
        </div>
    </div>

    <script>
        /* ══════════════════════════════════════════════
           Gestion du carrousel (inspiré de dynam.js)
        ══════════════════════════════════════════════ */
        let contenu_tableau = '';

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function cree_tableau() {
            fetch('../cimes_api/index_api.php?query=carrousel')
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    const tbody = document.querySelector('#corp_tab');
                    contenu_tableau = '';

                    if (!data || data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px;color:var(--text-hint);">
                                    <i class="fa-solid fa-image" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                    Aucun slide trouvé dans le carrousel.
                                </td>
                            </tr>`;
                        return;
                    }

                    data.forEach(slide => {
                        const titre = escapeHtml(slide.titre || '—');
                        const soustitre = escapeHtml(slide.sous_titre || '—');

                        const imageCell = slide.image ?
                            `<img src="../cimes_clients/img/${escapeHtml(slide.image)}" 
                                   alt="${titre}" 
                                   class="slide-thumb"
                                   onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                               <span class="slide-thumb-placeholder" style="display:none;">
                                   <i class="fa-solid fa-image"></i>
                               </span>` :
                            `<span class="slide-thumb-placeholder">
                                   <i class="fa-solid fa-image"></i>
                               </span>`;

                        contenu_tableau += `
                            <tr>
                                
                                <td class="col-image">${imageCell}</td>
                                <td class="col-titre">
                                    <span class="text-truncate" title="${titre}">${titre}</span>
                                </td>
                                <td class="col-soustitre">
                                    <span class="text-truncate" title="${soustitre}">${soustitre}</span>
                                </td>
                                <td class="col-modifier">
                                    <a href="modif_carrousel.php?id=${slide.id}" title="Modifier ce slide">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </td>
                                <td class="col-supprimer">
                                    <a href="javascript:void(0);" 
                                       class="supprimer-lien" 
                                       data-id="${slide.id}" 
                                       data-table="cimes_carrousel"
                                       title="Supprimer ce slide">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>`;
                    });

                    tbody.innerHTML = contenu_tableau;
                })
                .catch(error => {
                    console.error('Erreur chargement carrousel :', error);
                    document.getElementById('corp_tab').innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align:center;padding:40px;color:var(--red);">
                                <i class="fa-solid fa-triangle-exclamation" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                                Impossible de charger les slides du carrousel.
                            </td>
                        </tr>`;
                });
        }

        // ═══════════════════════════════════════
        // RECHARGEMENT INTELLIGENT
        // ═══════════════════════════════════════

        function checkReload() {
            const reloadNeeded = sessionStorage.getItem('reload_needed_carrousel');
            if (reloadNeeded) {
                sessionStorage.removeItem('reload_needed_carrousel');
                cree_tableau();
            }
        }

        // Chargement initial
        cree_tableau();

        // Vérifier immédiatement si un rechargement est demandé
        checkReload();

        // Vérifier quand la page s'affiche (bouton retour, etc.)
        window.addEventListener('pageshow', function() {
            checkReload();
        });

        // Vérifier quand l'onglet redevient visible
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                checkReload();
            }
        });

        // Secours : focus sur la fenêtre
        window.addEventListener('focus', function() {
            checkReload();
        });

        // ═══════════════════════════════════════
        // GESTION DE LA SUPPRESSION
        // ═══════════════════════════════════════
        let quelid = "";
        let quelQuery = "";

        // Délégation pour les liens "supprimer"
        document.getElementById('corp_tab').addEventListener('click', function(e) {
            const target = e.target.closest('.supprimer-lien');
            if (target) {
                e.preventDefault();
                const supprId = target.getAttribute('data-id');
                const supprTable = target.getAttribute('data-table');
                ouvrepanneau(supprId, supprTable);
            }
        });

        function ouvrepanneau(id, table) {
            const modal = document.querySelector("#panneausup");
            modal.style.display = "block";
            quelid = id;
            quelQuery = `supp_${table}`;
        }

        document.querySelector("#oui").addEventListener('click', function() {
            fetch(`../cimes_api/index_api.php?query=${quelQuery}&id=${quelid}`)
                .then(reponse => reponse.text())
                .then(data => {
                    if (data.length == 2) {
                        document.querySelector("#corp_tab").innerHTML = '';
                        contenu_tableau = '';
                        cree_tableau();
                    }
                })
                .catch(err => console.error('Erreur suppression :', err));
            document.querySelector("#panneausup").style.display = "none";
        });

        document.querySelector("#non").addEventListener('click', function() {
            document.querySelector("#panneausup").style.display = "none";
        });

        document.querySelector(".close").addEventListener('click', function() {
            document.querySelector("#panneausup").style.display = "none";
        });

        window.addEventListener('click', function(event) {
            const modal = document.querySelector("#panneausup");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>

</body>

</html>
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
    <title>Gestion des logos partenaires</title>
    <style>
        :root {
            --header-height: 80px;
            --green-dark: #0F6E56;
            --surface: #ffffff;
            --surface-alt: #f7f9f8;
            --border: rgba(0, 0, 0, 0.1);
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
            color: var(--green-dark);
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

        .logo-thumb {
            max-height: 40px;
            max-width: 80px;
        }
    </style>
</head>

<body>
    <div class="dynam-wrapper">
        <div class="dynam-toolbar">
            <a href="navbar.php" class="bouton_retour"><i class="fas fa-arrow-left"></i> Retour</a>
            <a href="ajout_logo_partenaire.php" class="bouton_ajouter"><i class="fas fa-plus"></i> Ajouter</a>
        </div>

        <div class="dynam-container">
            <h2>Liste des logos partenaires</h2>
            <table class="table">
                <thead>
                    <tr>

                        <th>Aperçu</th>
                        <th>Texte alternatif</th>
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
            <p class="texte_supp">Êtes-vous certain de vouloir supprimer ce logo ?</p>
            <div class="btn_supp">
                <a href="javascript:void(0);" class="btn_oui_non" id="oui">Oui</a>
                <a href="javascript:void(0);" class="btn_oui_non" id="non">Non</a>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../cimes_api/index_api.php';
        let supprId = null;

        function chargerTable() {
            fetch(`${API_URL}?query=logo_partenaire`)
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('corp_tab');
                    if (!Array.isArray(data) || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5">Aucun logo</td></tr>';
                        return;
                    }
                    let html = '';
                    data.reverse().forEach(item => {
                        html += `
                    <tr>
                       
                        <td><img src="../cimes_clients/img/${item.logo}" class="logo-thumb" alt="${escapeHtml(item.alt)}"></td>
                        <td>${escapeHtml(item.alt)}</td>
                        <td><a href="modif_logo_partenaire.php?id=${item.id}"><i class="fas fa-pen"></i></a></td>
                        <td><a href="javascript:void(0);" class="supprimer-lien" data-id="${item.id}"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>`;
                    });
                    tbody.innerHTML = html;
                })
                .catch(err => console.error(err));
        }

        document.body.addEventListener('click', e => {
            if (e.target.closest('.supprimer-lien')) {
                supprId = e.target.closest('.supprimer-lien').dataset.id;
                document.getElementById('panneausup').style.display = 'flex';
            }
        });

        document.getElementById('oui').addEventListener('click', () => {
            if (supprId) {
                fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            lien: 'supprimer_logo_partenaire',
                            id: parseInt(supprId)
                        })
                    })
                    .then(r => r.text())
                    .then(res => {
                        if (res === 'ok') chargerTable();
                        else alert('Erreur suppression');
                        document.getElementById('panneausup').style.display = 'none';
                        supprId = null;
                    });
            }
        });

        document.getElementById('non').addEventListener('click', () => {
            document.getElementById('panneausup').style.display = 'none';
        });
        document.querySelector('.close').addEventListener('click', () => {
            document.getElementById('panneausup').style.display = 'none';
        });

        function escapeHtml(str) {
            return String(str).replace(/[&<>]/g, m => (m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;'));
        }

        chargerTable();
    </script>
</body>

</html>
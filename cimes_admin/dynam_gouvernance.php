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
    <title>Gestion de la gouvernance</title>
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
            position: sticky;
            top: 0;
            background: #f2f5f3;
            z-index: 100;
            padding: 12px 0;
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .filtre-bar {
            position: sticky;
            top: 70px;
            background: #f2f5f3;
            z-index: 99;
            padding-bottom: 16px;
            margin-bottom: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
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
            margin-bottom: 40px;
        }

        .table-scroll {
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            border-top: 1px solid var(--border);
            margin-top: 12px;
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

        .table thead {
            position: sticky;
            top: 0;
            background: var(--surface-alt);
            z-index: 2;
        }

        .table th {
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
            <a href="#" id="btn-ajout-principal" class="bouton_ajouter"><i class="fas fa-plus"></i> Ajouter un membre</a>
        </div>

        <div class="filtre-bar">
            <label>Filtrer les membres par catégorie :</label>
            <select id="filtre-type" class="filtre-select">
                <option value="">Tous</option>
            </select>
        </div>

        <!-- BLOC MEMBRES -->
        <div class="dynam-container">
            <h2>Liste des membres de la gouvernance</h2>
            <div class="table-scroll" id="membres-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody id="corp_tab"></tbody>
                </table>
            </div>
        </div>

        <!-- BLOC SCHEMA -->
        <div class="dynam-container">
            <h2>Schéma de gouvernance</h2>
            <div class="table-scroll" id="schema-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Fréquence</th>
                            <th>Ordre</th>
                            <th>Modifier</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody id="schema_tab"></tbody>
                </table>
            </div>
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

    <script>
        const API_URL = '../cimes_api/index_api.php';
        let currentSuppression = {
            type: null,
            id: null
        };

        function chargerTypes() {
            fetch(`${API_URL}?query=gouvernance_types`)
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById('filtre-type');
                    if (!select) return;
                    select.innerHTML = '<option value="">Tous</option>';
                    const labels = {
                        'presidence': 'Présidence',
                        'direction': 'Direction',
                        'conseil_groupement': 'Conseil de groupement',
                        'bureau': 'Bureau',
                        'conseil_scientifique': 'Conseil scientifique',
                        'comite_orientation': 'Comité d\'orientation'
                    };
                    data.forEach(item => {
                        const type = item.type;
                        const label = labels[type] || type;
                        select.innerHTML += `<option value="${type}">${label}</option>`;
                    });
                })
                .catch(err => console.error(err));
        }

        function chargerMembres(type = '') {
            const tbody = document.getElementById('corp_tab');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="5">Chargement...</td></tr>';
            let url = `${API_URL}?query=gouvernance`;
            if (type) url += `&type=${encodeURIComponent(type)}`;
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (!Array.isArray(data)) {
                        tbody.innerHTML = '<tr><td colspan="5">Erreur format données</td></tr>';
                        return;
                    }
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5">Aucune entrée</td></tr>';
                        return;
                    }
                    const labels = {
                        'presidence': 'Présidence',
                        'direction': 'Direction',
                        'conseil_groupement': 'Conseil de groupement',
                        'bureau': 'Bureau',
                        'conseil_scientifique': 'Conseil scientifique',
                        'comite_orientation': 'Comité d\'orientation'
                    };
                    let html = '';
                    data.forEach(item => {
                        const typeLabel = labels[item.type] || item.type;
                        const nomComplet = item.prenom ? `${item.prenom} ${item.nom}` : item.nom;
                        html += `
                        <tr>
                            <td>${escapeHtml(typeLabel)}</td>
                            <td>${escapeHtml(nomComplet)}</td>
                            <td>${escapeHtml(item.role)}</td>
                            <td><a href="modif_gouvernance.php?id=${item.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                            <td><a href="javascript:void(0);" class="supprimer-lien" data-type="membre" data-id="${item.id}"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                    `;
                    });
                    tbody.innerHTML = html;
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="5">Erreur : ${err.message}</td></tr>`;
                });
        }

        function chargerSchema() {
            const tbody = document.getElementById('schema_tab');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="6">Chargement...</td></tr>';
            fetch(`${API_URL}?query=gouvernance_schema`)
                .then(r => r.json())
                .then(data => {
                    if (!Array.isArray(data)) {
                        tbody.innerHTML = '<tr><td colspan="6">Erreur format données</td></tr>';
                        return;
                    }
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6">Aucune entrée</td></tr>';
                        return;
                    }
                    let html = '';
                    data.forEach(item => {
                        html += `
                        <tr>
                            <td>${escapeHtml(item.titre)}</td>
                            <td>${escapeHtml(item.description)}</td>
                            <td>${escapeHtml(item.reunion)}</td>
                            <td>${item.ordre}</td>
                            <td><a href="modif_gouvernance_schema.php?id=${item.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                            <td><a href="javascript:void(0);" class="supprimer-lien" data-type="schema" data-id="${item.id}"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                    `;
                    });
                    tbody.innerHTML = html;
                })
                .catch(err => {
                    tbody.innerHTML = `<tr><td colspan="6">Erreur : ${err.message}</td></tr>`;
                });
        }

        function ouvrirModalSuppression(type, id) {
            currentSuppression = {
                type,
                id
            };
            document.getElementById('panneausup').style.display = 'flex';
        }

        document.body.addEventListener('click', (e) => {
            const target = e.target.closest('.supprimer-lien');
            if (target) {
                e.preventDefault();
                const type = target.getAttribute('data-type');
                const id = target.getAttribute('data-id');
                ouvrirModalSuppression(type, id);
            }
        });

        document.getElementById('oui')?.addEventListener('click', () => {
            if (currentSuppression.id) {
                const payload = (currentSuppression.type === 'membre') ? {
                    lien: 'supprimer_gouvernance_entite',
                    id: parseInt(currentSuppression.id)
                } : {
                    lien: 'supprimer_gouvernance_schema',
                    id: parseInt(currentSuppression.id)
                };
                fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.text())
                    .then(res => {
                        if (res === 'ok') {
                            if (currentSuppression.type === 'membre') {
                                const filtre = document.getElementById('filtre-type')?.value || '';
                                chargerMembres(filtre);
                            } else {
                                chargerSchema();
                            }
                        } else {
                            alert('Erreur lors de la suppression');
                        }
                        document.getElementById('panneausup').style.display = 'none';
                        currentSuppression = {
                            type: null,
                            id: null
                        };
                    })
                    .catch(err => alert('Erreur réseau'));
            }
        });

        document.getElementById('non')?.addEventListener('click', () => {
            document.getElementById('panneausup').style.display = 'none';
            currentSuppression = {
                type: null,
                id: null
            };
        });
        document.querySelector('.close')?.addEventListener('click', () => {
            document.getElementById('panneausup').style.display = 'none';
        });

        document.getElementById('filtre-type')?.addEventListener('change', (e) => {
            chargerMembres(e.target.value);
        });

        document.getElementById('btn-ajout-principal').addEventListener('click', (e) => {
            e.preventDefault();
            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '10000';
            modal.innerHTML = `
            <div style="background:white; padding:30px; border-radius:20px; max-width:400px;">
                <h3>Choisir le type de membre</h3>
                <select id="type-select-ajout" style="width:100%; padding:10px; margin:15px 0;">
                    <option value="">-- Sélectionnez --</option>
                    <option value="presidence">Présidence</option>
                    <option value="direction">Direction</option>
                    <option value="conseil_groupement">Conseil de groupement</option>
                    <option value="bureau">Bureau</option>
                    <option value="conseil_scientifique">Conseil scientifique</option>
                    <option value="comite_orientation">Comité d'orientation</option>
                </select>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button id="modal-annuler" style="padding:8px 16px;">Annuler</button>
                    <button id="modal-valider" style="background:#0F6E56; color:white; padding:8px 16px; border:none; border-radius:8px;">Ajouter</button>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
            document.getElementById('modal-annuler').addEventListener('click', () => modal.remove());
            document.getElementById('modal-valider').addEventListener('click', () => {
                const selected = document.getElementById('type-select-ajout').value;
                if (selected) window.location.href = `ajout_gouvernance.php?type=${selected}`;
                else alert('Veuillez sélectionner un type');
                modal.remove();
            });
        });

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
        }

        chargerTypes();
        chargerMembres();
        chargerSchema();
    </script>

</body>

</html>
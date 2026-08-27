<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Ajout d'une personne – Annuaire</title>
<meta name="description" content="Formulaire d'ajout d'une personne à l'annuaire">

</head>

<body>

    <style>
        /* ═══════════════════════════ Styles communs (identiques à la page de modification) ═══════════════════════════ */
        :root {
            --header-height: 80px;
            --green-dark: #0F6E56;
            --green: #1D9E75;
            --green-light: #eef5f2;
            --green-ring: rgba(29, 158, 117, 0.20);
            --surface: #ffffff;
            --surface-alt: #f7f9f8;
            --border: rgba(0, 0, 0, 0.10);
            --border-md: rgba(0, 0, 0, 0.16);
            --text: #1e2420;
            --text-muted: #5f6b65;
            --text-hint: #9aada6;
            --red: #A32D2D;
            --red-light: #fcebeb;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-card: 0 4px 16px rgba(0, 0, 0, 0.06);
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

        .form-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 40px 24px 60px;
            margin-top: var(--header-height);
        }

        .form-page-title {
            text-align: center;
            color: var(--green-dark);
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 6px;
        }

        .form-page-sub {
            text-align: center;
            color: var(--text-hint);
            font-size: 0.88rem;
            margin: 0 0 32px;
        }

        .form-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            padding: 28px 28px 24px;
            margin-bottom: 20px;
        }

        .form-card__title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--green-dark);
            margin: 0 0 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-card__title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-row.three {
            grid-template-columns: 1fr 1fr 1fr;
        }

        @media (max-width: 640px) {

            .form-row,
            .form-row.three {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px 16px;
            }

            .form-wrapper {
                padding: 24px 12px 48px;
                margin-top: 60px;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.01em;
        }

        .form-group label .required {
            color: var(--red);
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-md);
            border-radius: var(--radius-sm);
            font-size: 0.88rem;
            color: var(--text);
            background: var(--surface);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px var(--green-ring);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 90px;
            line-height: 1.55;
        }

        .field-hint {
            font-size: 0.75rem;
            color: var(--text-hint);
            margin-top: 2px;
        }

        /* ── Upload photo ── */
        .upload-zone {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px;
            border: 1.5px dashed var(--border-md);
            border-radius: var(--radius-md);
            background: var(--surface-alt);
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .upload-zone:hover {
            border-color: var(--green);
            background: var(--green-light);
        }

        .upload-zone input[type="file"] {
            display: none;
        }

        .upload-preview {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: var(--green-light);
            color: var(--green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 600;
            overflow: hidden;
            flex-shrink: 0;
        }

        .upload-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-info {
            flex: 1;
        }

        .upload-info strong {
            display: block;
            font-size: 0.85rem;
            color: var(--text);
        }

        .upload-info small {
            font-size: 0.75rem;
            color: var(--text-hint);
        }

        /* ── Publications dynamiques ── */
        .publi-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .publi-item {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 10px;
            align-items: start;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface-alt);
        }

        @media (max-width: 640px) {
            .publi-item {
                grid-template-columns: 1fr;
            }
        }

        .publi-remove {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--red-light);
            color: var(--red);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 0.15s;
            align-self: center;
        }

        .publi-remove:hover {
            background: #f5c6c6;
        }

        .btn-add-publi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            padding: 8px 16px;
            background: var(--green-light);
            color: var(--green-dark);
            border: 1px solid rgba(29, 158, 117, 0.25);
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-add-publi:hover {
            background: #d5ece4;
        }

        /* ── Actions ── */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }

        .btn-cancel {
            padding: 11px 24px;
            background: var(--surface);
            border: 1px solid var(--border-md);
            border-radius: var(--radius-md);
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .btn-cancel:hover {
            background: var(--surface-alt);
            color: var(--text);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 28px;
            background: var(--green-dark);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.01em;
            box-shadow: 0 2px 8px rgba(15, 110, 86, 0.30);
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            text-decoration: none;
        }

        .btn-submit:hover {
            background: var(--green);
            box-shadow: 0 6px 16px rgba(15, 110, 86, 0.38);
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ── Feedback ── */
        .alerte {
            margin-top: 14px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            min-height: 22px;
        }

        .alerte.success {
            color: var(--green-dark);
        }

        .alerte.error {
            color: var(--red);
        }
    </style>

    <div class="form-wrapper">
        <h1 class="form-page-title">Ajout d'une personne</h1>
        <p class="form-page-sub">Remplissez les champs ci-dessous pour ajouter un membre à l'annuaire.</p>

        <!-- ── Identité ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-user"></i> Identité</p>

            <!-- Photo -->
            <div class="form-group" style="margin-bottom:18px;">
                <label>Photo de profil</label>
                <label class="upload-zone" for="photo_annuaire">
                    <input type="file" id="photo_annuaire" accept="image/*">
                    <div class="upload-preview" id="photo-preview">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour importer une photo</strong>
                        <small>JPG, PNG, WebP — 13 Mo max. Optionnel.</small>
                    </div>
                </label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nom_annuaire">Nom <span class="required">*</span></label>
                    <input type="text" class="form-control" id="nom_annuaire" placeholder="ex. Dupont" autocomplete="family-name">
                </div>
                <div class="form-group">
                    <label for="prenom_annuaire">Prénom <span class="required">*</span></label>
                    <input type="text" class="form-control" id="prenom_annuaire" placeholder="ex. Jean" autocomplete="given-name">
                </div>
            </div>

            <div class="form-row" style="margin-top:14px;">
                <div class="form-group">
                    <label for="email_annuaire">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email_annuaire" placeholder="ex. jean.dupont@exemple.fr" autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="fonction_annuaire">Fonction / Titre</label>
                    <input type="text" class="form-control" id="fonction_annuaire" placeholder="ex. Chercheur associé">
                </div>
            </div>
        </div>

        <!-- ── Affiliation académique ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-building-columns"></i> Affiliation académique</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="discipline_annuaire">Discipline</label>
                    <input type="text" class="form-control" id="discipline_annuaire" placeholder="ex. Biologie, Chimie…">
                </div>
                <div class="form-group">
                    <label for="etablis_annuaire">Établissement</label>
                    <input type="text" class="form-control" id="etablis_annuaire" placeholder="ex. Université de Paris">
                </div>
            </div>

            <div class="form-row" style="margin-top:14px;">
                <div class="form-group">
                    <label for="universite_annuaire">Université</label>
                    <input type="text" class="form-control" id="universite_annuaire" placeholder="ex. Sorbonne Université">
                </div>
                <div class="form-group">
                    <label for="id_hal_annuaire">IdHAL</label>
                    <input type="text" class="form-control" id="id_hal_annuaire" placeholder="identifiant HAL">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="page_web_annuaire">Page web / Profil</label>
                    <input type="url" class="form-control" id="page_web_annuaire" placeholder="https://…">
                </div>
            </div>
        </div>

        <!-- ── Recherche / mots-clés ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-magnifying-glass"></i> Domaines & mots-clés</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="terrain_recherche_annuaire">Terrains de recherche</label>
                    <input type="text" class="form-control" id="terrain_recherche_annuaire" placeholder="ex. Massif central, Himalaya">
                    <span class="field-hint">Régions ou zones géographiques d'activité, séparées par des virgules.</span>
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="mots_cles_annuaire">Mots-clés</label>
                    <input type="text" class="form-control" id="mots_cles_annuaire" placeholder="ex. climatologie, archéologie">
                    <span class="field-hint">Mots-clés séparés par des virgules.</span>
                </div>
            </div>
        </div>

        <!-- ── Publications ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-book-open"></i> Publications</p>
            <div id="publications-container">
                <div class="publi-list" id="publi-list"></div>
                <button type="button" class="btn-add-publi" id="btn-add-publi">
                    <i class="fa-solid fa-plus"></i> Ajouter une publication
                </button>
                <span class="field-hint" style="display:block; margin-top:6px;">Ajoutez jusqu'à 3 publications (titre + lien).</span>
            </div>
        </div>

        <!-- ── Actions ── -->
        <div class="form-actions">
            <a href="javascript:history.back();" class="btn-cancel">Annuler</a>
            <a href="#" class="btn-submit" id="envoyer">
                <i class="fa-solid fa-floppy-disk"></i> Créer la fiche
            </a>
        </div>

        <div id="erreur" class="alerte"></div>
    </div>

    <script>
        /* ── variable attendue par creer_modif_annuaire_personnes.js ── */
        let lien = 'cree_annuaire';

        /* ─── Prévisualisation photo ─── */
        document.getElementById('photo_annuaire').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}" alt="Aperçu">`;
            };
            reader.readAsDataURL(file);
        });

        /* ─── Gestion des publications ─── */
        const publiList = document.getElementById('publi-list');
        const btnAddPubli = document.getElementById('btn-add-publi');
        const MAX_PUBLI = 3;

        function createPubliItem(titre = '', lien = '') {
            const div = document.createElement('div');
            div.className = 'publi-item';
            div.innerHTML = `
                <div class="form-group">
                    <label>Titre</label>
                    <input type="text" class="form-control publi-titre" value="${titre.replace(/"/g, '&quot;')}" placeholder="Titre de la publication">
                </div>
                <div class="form-group">
                    <label>Lien (URL)</label>
                    <input type="url" class="form-control publi-lien" value="${lien}" placeholder="https://…">
                </div>
                <button type="button" class="publi-remove" title="Supprimer">×</button>
            `;
            div.querySelector('.publi-remove').addEventListener('click', () => {
                div.remove();
                updateAddButtonState();
            });
            return div;
        }

        function updateAddButtonState() {
            btnAddPubli.style.display = publiList.children.length >= MAX_PUBLI ? 'none' : '';
        }

        btnAddPubli.addEventListener('click', () => {
            if (publiList.children.length >= MAX_PUBLI) return;
            publiList.appendChild(createPubliItem());
            updateAddButtonState();
        });

        function collectPublications() {
            const items = publiList.querySelectorAll('.publi-item');
            const pubs = [];
            items.forEach(item => {
                const titre = item.querySelector('.publi-titre')?.value.trim() || '';
                const lien = item.querySelector('.publi-lien')?.value.trim() || '';
                if (titre) pubs.push({
                    titre,
                    lien
                });
            });
            return pubs.length ? JSON.stringify(pubs) : '';
        }

        /* Exposer les données pour creer_modif_annuaire_personnes.js */
        window.getFormData = function() {
            return {
                nom: document.getElementById('nom_annuaire').value.trim(),
                prenom: document.getElementById('prenom_annuaire').value.trim(),
                mail: document.getElementById('email_annuaire').value.trim(),
                fonction: document.getElementById('fonction_annuaire').value.trim(),
                discipline: document.getElementById('discipline_annuaire').value.trim(),
                etablissement: document.getElementById('etablis_annuaire').value.trim(),
                universite: document.getElementById('universite_annuaire').value.trim(),
                page_web: document.getElementById('page_web_annuaire').value.trim(),
                id_hal: document.getElementById('id_hal_annuaire').value.trim(),
                terrain_recherche: document.getElementById('terrain_recherche_annuaire').value.trim(),
                mots_cles: document.getElementById('mots_cles_annuaire').value.trim(),
                publications: collectPublications()
            };
        };

        // Initialisation : vide
        updateAddButtonState();
    </script>

    <script src="js/creer_modif_annuaire_personnes.js" async defer></script>

</body>

</html>
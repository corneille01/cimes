<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Ajout d'une structure – Annuaire</title>
<meta name="description" content="Formulaire d'ajout d'une structure à l'annuaire">

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

        @media (max-width: 640px) {
            .form-row {
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

        .alerte-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .alerte-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 22px;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
            white-space: nowrap;
        }

        .alerte-btn--outline {
            background: var(--surface);
            color: var(--green-dark);
            border: 1.5px solid var(--green-dark);
        }

        .alerte-btn--outline:hover {
            background: var(--green-light);
            transform: translateY(-1px);
        }

        .alerte-btn--primary {
            background: var(--green-dark);
            color: #fff;
            border: 1.5px solid transparent;
            box-shadow: 0 2px 8px rgba(15, 110, 86, 0.28);
        }

        .alerte-btn--primary:hover {
            background: var(--green);
            box-shadow: 0 4px 14px rgba(15, 110, 86, 0.36);
            transform: translateY(-1px);
        }
    </style>

    <div class="form-wrapper">
        <h1 class="form-page-title">Ajout d'une structure</h1>
        <p class="form-page-sub">Remplissez les champs ci-dessous pour ajouter une structure à l'annuaire.</p>

        <!-- ── Identité ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-building"></i> Identité</p>

            <div class="form-group" style="margin-bottom:18px;">
                <label>Logo / Photo de la structure</label>
                <label class="upload-zone" for="photo_annuaire">
                    <input type="file" id="photo_annuaire" accept="image/*">
                    <div class="upload-preview" id="photo-preview">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour importer une image</strong>
                        <small>JPG, PNG, WebP — 13 Mo max. Optionnel.</small>
                    </div>
                </label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="etablissement_annuaire">Nom de la structure <span class="required">*</span></label>
                    <input type="text" class="form-control" id="etablissement_annuaire"
                        placeholder="ex. Laboratoire ABCD" autocomplete="organization">
                </div>
                <div class="form-group">
                    <label for="responsable_annuaire">Responsable (s)</label>
                    <input type="text" class="form-control" id="responsable_annuaire"
                        placeholder="ex. Pr. Martin">
                </div>
            </div>
        </div>

        <!-- ── Recherche & Affiliation ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-flask"></i> Recherche &amp; Affiliation</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="discipline_annuaire">Discipline (s)</label>
                    <input type="text" class="form-control" id="discipline_annuaire"
                        placeholder="ex. Biologie, Chimie…">
                </div>
                <div class="form-group">
                    <label for="domaine_recherche_annuaire">Domaine(s) de recherche</label>
                    <input type="text" class="form-control" id="domaine_recherche_annuaire"
                        placeholder="ex. Neurosciences, Écologie…">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="tutelles_annuaire">Tutelle(s)</label>
                    <input type="text" class="form-control" id="tutelles_annuaire"
                        placeholder="ex. CNRS / Sorbonne Université">
                    <span class="field-hint">Plusieurs tutelles séparées par « / »</span>
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="annee_creation_annuaire">Année de création</label>
                    <input type="text" class="form-control" id="annee_creation_annuaire"
                        placeholder="ex. 2010">
                </div>
            </div>
        </div>

        <!-- ── Coordonnées ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-location-dot"></i> Coordonnées</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="adresse_annuaire">Adresse complète</label>
                    <input type="text" class="form-control" id="adresse_annuaire"
                        placeholder="ex. 12 rue de la Sorbonne, 75005 Paris, France">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="site_web_annuaire">Site web</label>
                    <input type="url" class="form-control" id="site_web_annuaire"
                        placeholder="https://…">
                </div>
            </div>
        </div>

        <!-- ── Présentation ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-align-left"></i> Présentation</p>
            <div class="form-group">
                <label for="presentation_annuaire">Texte de présentation</label>
                <textarea class="form-control" id="presentation_annuaire" rows="4"
                    placeholder="Courte description de la structure, missions, historique…"></textarea>
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
        /* ── Variable attendue par creer_modif_annuaire_structures.js ── */
        let lien = 'cree_structure';

        /* ─── Prévisualisation du logo ─── */
        document.getElementById('photo_annuaire').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}" alt="Aperçu">`;
            };
            reader.readAsDataURL(file);
        });

        /* ─── Données envoyées au script commun ─── */
        window.getFormData = function() {
            return {
                etablissement: document.getElementById('etablissement_annuaire').value.trim(),
                responsable: document.getElementById('responsable_annuaire').value.trim(),
                discipline: document.getElementById('discipline_annuaire').value.trim(),
                domaine_recherche: document.getElementById('domaine_recherche_annuaire').value.trim(),
                tutelles: document.getElementById('tutelles_annuaire').value.trim(),
                annee_creation: document.getElementById('annee_creation_annuaire').value.trim(),
                adresse: document.getElementById('adresse_annuaire').value.trim(),
                site_web: document.getElementById('site_web_annuaire').value.trim(),
                presentation: document.getElementById('presentation_annuaire').value.trim()
            };
        };
    </script>

    <script src="js/creer_modif_annuaire_structures.js" async defer></script>

</body>

</html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modification d'une mission</title>
<meta name="description" content="Formulaire de modification d'une mission du CIMES">

</head>

<body>

    <style>
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
            min-height: 140px;
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
            width: 100px;
            height: 70px;
            border-radius: var(--radius-sm);
            background: var(--green-light);
            color: var(--green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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

        .skeleton {
            background: linear-gradient(90deg, #e8eeeb 25%, #d8e5df 50%, #e8eeeb 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 4px;
            color: transparent !important;
            pointer-events: none;
            user-select: none;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>

    <div class="form-wrapper">
        <h1 class="form-page-title">Modification d'une mission</h1>
        <p class="form-page-sub">Modifiez les champs ci-dessous puis enregistrez.</p>

        <!-- ── Image de la mission ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-image"></i> Image de la mission</p>

            <div class="form-group">
                <label>Image d'illustration</label>
                <label class="upload-zone" for="image_mission">
                    <input type="file" id="image_mission" accept="image/*">
                    <div class="upload-preview" id="image-preview">
                        <i class="fa-solid fa-mountain"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour changer l'image</strong>
                        <small>JPG, PNG, WebP — 13 Mo max. Laissez vide pour conserver l'image actuelle.</small>
                    </div>
                </label>
            </div>
        </div>

        <!-- ── Contenu de la mission ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-bullseye"></i> Contenu de la mission</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="nom_mission">Nom de la mission <span class="required">*</span></label>
                    <input type="text" class="form-control" id="nom_mission" placeholder="ex. Observer et comprendre les dynamiques des territoires de montagne">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="texte_mission">Texte de la mission <span class="required">*</span></label>
                    <textarea class="form-control" id="texte_mission" rows="8"
                        placeholder="Description détaillée de la mission, ses objectifs, ses actions…"></textarea>
                    <span class="field-hint">Décrivez la mission en détail : contexte, objectifs, actions menées, résultats attendus.</span>
                </div>
            </div>
        </div>

        <!-- ── Actions ── -->
        <div class="form-actions">
            <a href="javascript:history.back();" class="btn-cancel">Annuler</a>
            <a href="#" class="btn-submit" id="envoyer">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
            </a>
        </div>

        <div id="erreur" class="alerte"></div>
    </div>

    <?php $id_mission = intval($_GET['id'] ?? $_GET['quelmission'] ?? 0); ?>
    <script>
        const id = <?php echo $id_mission; ?>;
        let lien = 'modif_mission';

        /* ── Prévisualisation image ── */
        document.getElementById('image_mission').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('image-preview').innerHTML =
                    `<img src="../cimes_clients/img/${e.target.result}" alt="Aperçu">`;
            };
            reader.readAsDataURL(file);
        });

        /* ── Données du formulaire exposées pour creer_modif_mission.js ── */
        window.getFormData = function() {
            return {
                id,
                nom: document.getElementById('nom_mission').value.trim(),
                texte: document.getElementById('texte_mission').value.trim(),
            };
        };

        /* ══════════════════════════════════════════════
           Pré-remplissage depuis l'API
        ══════════════════════════════════════════════ */
        const CHAMPS = [
            ['#nom_mission', 'nom'],
            ['#texte_mission', 'texte'],
        ];

        /* Activation skeleton */
        CHAMPS.forEach(([sel]) => {
            const el = document.querySelector(sel);
            if (el) el.classList.add('skeleton');
        });

        fetch(`../cimes_api/index_api.php?query=mission&id=${id}`)
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                /* Retrait skeleton */
                CHAMPS.forEach(([sel]) => {
                    const el = document.querySelector(sel);
                    if (el) el.classList.remove('skeleton');
                });

                const mission = data[0];
                if (!mission) {
                    console.warn('Aucune donnée reçue pour id =', id);
                    return;
                }

                /* Remplissage des champs */
                CHAMPS.forEach(([sel, cle]) => {
                    const el = document.querySelector(sel);
                    if (el && mission[cle] != null) el.value = mission[cle];
                });

                /* Image existante */
                if (mission.image) {
                    const src = mission.image.startsWith('http') ? mission.image : `../cimes_clients/img/${mission.image}`;
                    document.getElementById('image-preview').innerHTML =
                        `<img src="${src}" alt="Image actuelle">`;
                }

                /* Titre dynamique */
                if (mission.nom) {
                    document.title = `Modifier – ${mission.nom}`;
                    const h1 = document.querySelector('.form-page-title');
                    if (h1) h1.textContent = `Modification de la mission : ${mission.nom}`;
                }
            })
            .catch(err => {
                CHAMPS.forEach(([sel]) => {
                    const el = document.querySelector(sel);
                    if (el) el.classList.remove('skeleton');
                });
                console.error('Erreur chargement mission :', err);
                const errEl = document.querySelector('#erreur');
                if (errEl) {
                    errEl.className = 'alerte error';
                    errEl.textContent = 'Impossible de charger les données de la mission.';
                }
            });
    </script>

    <script src="js/creer_modif_mission.js" async defer></script>

</body>

</html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
$id_carrousel = intval($_GET['id'] ?? 0);
if ($id_carrousel <= 0) {
    die("Identifiant de slide invalide.");
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modification d'un slide – Carrousel</title>
<meta name="description" content="Formulaire de modification d'un slide du carrousel">

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
            outline: none;
            font-family: inherit;
        }

        .form-control:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px var(--green-ring);
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
            width: 160px;
            height: 90px;
            border-radius: var(--radius-sm);
            background: var(--green-light);
            color: var(--green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
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
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(15, 110, 86, 0.30);
            transition: background 0.2s, transform 0.1s;
            text-decoration: none;
        }

        .btn-submit:hover {
            background: var(--green);
            transform: translateY(-1px);
            color: #fff;
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
        <h1 class="form-page-title">Modification d'un slide</h1>
        <p class="form-page-sub">Modifiez les champs ci-dessous puis enregistrez.</p>

        <!-- ── Image du slide ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-image"></i> Image du slide</p>

            <div class="form-group">
                <label>Image d'illustration</label>
                <label class="upload-zone" for="image_carrousel">
                    <input type="file" id="image_carrousel" accept="image/*">
                    <div class="upload-preview" id="image-preview">
                        <i class="fa-solid fa-image fa-2x"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour changer l'image</strong>
                        <small>JPG, PNG, WebP — 13 Mo max. Laissez vide pour conserver l'image actuelle.</small>
                    </div>
                </label>
            </div>
        </div>

        <!-- ── Contenu du slide ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-sliders"></i> Contenu du slide</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="titre_carrousel">Titre <span class="required">*</span></label>
                    <input type="text" class="form-control" id="titre_carrousel" placeholder="ex. Centre International des Montagnes du Sud">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="soustitre_carrousel">Sous-titre</label>
                    <input type="text" class="form-control" id="soustitre_carrousel" placeholder="ex. Groupement d'Intérêt Scientifique qui fédère les recherches...">
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

    <!-- Variables pour le JS -->
    <script>
        const id = <?php echo $id_carrousel; ?>;
        let lien = 'modif_carrousel';
    </script>

    <!-- Chargement des données existantes -->
    <script>
        (function() {
            const champs = [{
                    selector: '#titre_carrousel',
                    key: 'titre'
                },
                {
                    selector: '#soustitre_carrousel',
                    key: 'sous_titre'
                },
            ];

            // Skeleton le temps du chargement
            champs.forEach(({
                selector
            }) => {
                const el = document.querySelector(selector);
                if (el) el.classList.add('skeleton');
            });

            fetch(`../cimes_api/index_api.php?query=carrousel&id=${id}`)
                .then(r => r.ok ? r.json() : Promise.reject('Erreur réseau'))
                .then(data => {
                    const slide = Array.isArray(data) ? data[0] : data;
                    if (!slide) {
                        document.querySelector('#erreur').innerHTML = 'Aucun slide trouvé.';
                        return;
                    }

                    champs.forEach(({
                        selector,
                        key
                    }) => {
                        const el = document.querySelector(selector);
                        if (el) {
                            el.classList.remove('skeleton');
                            if (slide[key] !== undefined) el.value = slide[key];
                        }
                    });

                    // Prévisualisation de l'image existante
                    if (slide.image) {
                        const src = slide.image.startsWith('http') ? slide.image : `../cimes_clients/img/${slide.image}`;
                        document.getElementById('image-preview').innerHTML =
                            `<img src="${src}" alt="Image actuelle">`;
                    }

                    document.title = `Modifier – ${slide.titre || 'Slide'}`;
                    document.querySelector('.form-page-title').textContent =
                        `Modification : ${slide.titre || 'Slide'}`;
                })
                .catch(err => {
                    champs.forEach(({
                        selector
                    }) => {
                        const el = document.querySelector(selector);
                        if (el) el.classList.remove('skeleton');
                    });
                    document.querySelector('#erreur').innerHTML = 'Impossible de charger le slide.';
                    console.error(err);
                });

            // Prévisualisation de la nouvelle image
            document.getElementById('image_carrousel').addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('image-preview').innerHTML =
                        `<img src="${e.target.result}" alt="Aperçu">`;
                };
                reader.readAsDataURL(file);
            });

            // Exposition des données pour le script d'envoi
            window.getFormData = function() {
                return {
                    id: id,
                    titre: document.getElementById('titre_carrousel').value.trim(),
                    sous_titre: document.getElementById('soustitre_carrousel').value.trim(),
                };
            };
        })();
    </script><?php
                session_start();
                if (!isset($_SESSION['id'])) {
                    header("Location: ../cimes_clients/espace_personnel.php");
                    exit();
                }
                ?>
    <?php include('include/head.html'); ?>
    <?php include('include/header.html'); ?>
    <title>Administration – Gouvernance</title>
    <style>
        /* Force le footer en bas */
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        body {
            flex: 1;
            background: #f2f5f3;
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        .gov-wrapper {
            flex: 1;
            max-width: 1400px;
            margin: 100px auto 0;
            padding: 0 24px;
        }

        .gov-section {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            margin-bottom: 48px;
            overflow: hidden;
        }

        .gov-section-header {
            background: #eef5f2;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .gov-section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0F6E56;
            margin: 0;
        }

        .gov-btn-add {
            background: #0F6E56;
            color: white;
            padding: 6px 16px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .gov-items-container {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .gov-item-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            background: #fff;
        }

        .gov-item-fields {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px 20px;
            margin-bottom: 16px;
        }

        .gov-field-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .gov-field-group label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #5f6b65;
        }

        .gov-field-group input,
        .gov-field-group textarea {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 0.85rem;
            background: #f9fafb;
            font-family: inherit;
        }

        .gov-readonly-fields input,
        .gov-readonly-fields textarea {
            background: #f8fafc;
            color: #334155;
            cursor: default;
        }

        .gov-item-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .gov-btn-edit,
        .gov-btn-delete,
        .gov-btn-save,
        .gov-btn-cancel {
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .gov-btn-edit {
            background: #e2e8f0;
            color: #1e293b;
        }

        .gov-btn-delete {
            background: #fff5f5;
            color: #A32D2D;
            border: 1px solid #fed7d7;
        }

        .gov-btn-save {
            background: #0F6E56;
            color: white;
        }

        .gov-btn-cancel {
            background: #e2e8f0;
            color: #475569;
        }

        .gov-loader {
            text-align: center;
            padding: 40px;
            color: #0F6E56;
        }

        .gov-error {
            text-align: center;
            padding: 40px;
            color: #A32D2D;
            background: #fff5f5;
            border-radius: 14px;
        }

        .gov-empty {
            text-align: center;
            padding: 20px;
            color: #94a3b8;
        }
    </style>
    </head>

    <body>
        <div class="gov-wrapper">
            <div id="gouvernance-root">
                <div class="gov-loader">Chargement des données...</div>
            </div>
        </div>
        <script src="creer_modif_gouvernance.js"></script>
        <?php include('include/footer.html'); ?>
    </body>

    </html>

    <!-- Script de soumission -->
    <script src="js/modif_carrousel.js" async defer></script>

</body>

</html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modification de la présentation – CIMES</title>
<meta name="description" content="Formulaire de modification de la section présentation du GIS-CIMES">

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

        .form-row.full {
            grid-template-columns: 1fr;
            display: grid;
        }

        @media (max-width: 640px) {
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
            min-height: 180px;
            line-height: 1.65;
        }

        .field-hint {
            font-size: 0.75rem;
            color: var(--text-hint);
            margin-top: 2px;
        }

        .char-counter {
            font-size: 0.75rem;
            color: var(--text-hint);
            text-align: right;
            margin-top: 4px;
            transition: color 0.2s;
        }

        .char-counter.warn {
            color: #b45309;
        }

        .char-counter.over {
            color: var(--red);
            font-weight: 600;
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
            width: 88px;
            height: 60px;
            border-radius: var(--radius-sm);
            background: var(--green-light);
            color: var(--green-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
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
        <h1 class="form-page-title">Modification de la présentation</h1>
        <p class="form-page-sub">Modifiez les champs ci-dessous puis enregistrez.</p>

        <!-- ── Contenu textuel ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-align-left"></i> Contenu textuel</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="texte_presentation">Corps du texte <span class="required">*</span></label>
                    <textarea class="form-control" id="texte_presentation" rows="8"
                        placeholder="Rédigez la présentation du GIS-CIMES…"></textarea>
                    <div class="char-counter" id="char-count">0 caractère</div>
                    <span class="field-hint">Le texte sera affiché tel quel sur la page d'accueil.</span>
                </div>
            </div>
        </div>

        <!-- ── Image illustrative ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-image"></i> Image illustrative</p>

            <div class="form-group">
                <label>Illustration (côté droit)</label>
                <label class="upload-zone" for="image_presentation">
                    <input type="file" id="image_presentation" accept="image/*">
                    <div class="upload-preview" id="image-preview">
                        <i class="fa-solid fa-mountain-sun"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour changer l'image</strong>
                        <small>JPG, PNG, WebP — 10 Mo max. Laissez vide pour conserver l'image actuelle.</small>
                    </div>
                </label>
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

    <script>
        /* ── Compteur de caractères ── */
        const charCountEl = document.getElementById('char-count');

        function updateCharCount(n) {
            charCountEl.textContent = n.toLocaleString('fr-FR') + ' caractère' + (n > 1 ? 's' : '');
            charCountEl.className = 'char-counter' + (n > 3000 ? ' over' : n > 2000 ? ' warn' : '');
        }

        document.getElementById('texte_presentation').addEventListener('input', function() {
            updateCharCount(this.value.length);
        });

        /* ── Prévisualisation image ── */
        document.getElementById('image_presentation').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('image-preview').innerHTML =
                    `<img src="${e.target.result}" alt="Aperçu">`;
            };
            reader.readAsDataURL(file);
        });

        /* ── Données exposées pour modif_presentation.js ── */
        window.getFormData = function() {
            return {
                texte: document.getElementById('texte_presentation').value.trim(),
            };
        };

        /* ── Pré-remplissage depuis l'API (ligne unique, pas d'id) ── */
        const CHAMPS = [
            ['#texte_presentation', 'texte'],
        ];

        CHAMPS.forEach(([sel]) => {
            const el = document.querySelector(sel);
            if (el) el.classList.add('skeleton');
        });

        fetch('../cimes_api/index_api.php?query=presentation')
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                CHAMPS.forEach(([sel]) => {
                    const el = document.querySelector(sel);
                    if (el) el.classList.remove('skeleton');
                });

                const section = data[0];
                if (!section) {
                    console.warn('Aucune donnée reçue');
                    return;
                }

                CHAMPS.forEach(([sel, cle]) => {
                    const el = document.querySelector(sel);
                    if (el && section[cle] != null) el.value = section[cle];
                });

                /* Compteur initial */
                if (section.texte) updateCharCount(section.texte.length);

                /* Image existante */
                if (section.image) {
                    document.getElementById('image-preview').innerHTML =
                        `<img src="../cimes_clients/img/${section.image}" alt="Image actuelle">`;
                }
            })
            .catch(err => {
                CHAMPS.forEach(([sel]) => {
                    const el = document.querySelector(sel);
                    if (el) el.classList.remove('skeleton');
                });
                console.error('Erreur chargement présentation :', err);
                const errEl = document.getElementById('erreur');
                if (errEl) {
                    errEl.className = 'alerte error';
                    errEl.textContent = 'Impossible de charger les données de la section.';
                }
            });
    </script>

    <script src="js/modif_presentation.js" async defer></script>

</body>

</html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
$id_activites = intval($_GET['id'] ?? 0);
if ($id_activites <= 0) {
    die("Identifiant d'activité invalide.");
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modification d'une activité</title>
<meta name="description" content="Formulaire de modification d'une activité">

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

    textarea.form-control {
        resize: vertical;
        min-height: 90px;
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
        width: 80px;
        height: 80px;
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

    .upload-info strong {
        display: block;
        font-size: 0.85rem;
    }

    .upload-info small {
        font-size: 0.75rem;
        color: var(--text-hint);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
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
        text-decoration: none;
        transition: background 0.15s;
    }

    .btn-cancel:hover {
        background: var(--surface-alt);
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
    }

    .btn-submit:hover {
        background: var(--green);
        transform: translateY(-1px);
    }

    .alerte {
        margin-top: 14px;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
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

</head>

<body>

    <div class="form-wrapper">
        <h1 class="form-page-title">Modification d'une activité</h1>
        <p class="form-page-sub">Modifiez les champs ci-dessous puis enregistrez.</p>

        <!-- ── Informations générales ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-person-hiking"></i> Détails</p>

            <div class="form-row">
                <div class="form-group">
                    <label for="titre_activites">Titre <span class="required">*</span></label>
                    <input type="text" class="form-control" id="titre_activites" placeholder="Titre de l'activité">
                </div>
                <div class="form-group">
                    <label for="date_activites">Date</label>
                    <input type="date" class="form-control" id="date_activites">
                </div>
            </div>

            <div class="form-row full" style="margin-top:14px;">
                <div class="form-group">
                    <label for="lieu_activites">Lieu</label>
                    <input type="text" class="form-control" id="lieu_activites" placeholder="ex. Chamonix, Massif du Mont-Blanc">
                </div>
            </div>
        </div>

        <!-- ── Descriptions ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-align-left"></i> Contenu</p>

            <div class="form-group" style="margin-bottom:16px;">
                <label for="description_courte_activites">Description courte (résumé)</label>
                <textarea class="form-control" id="description_courte_activites" rows="2"
                    placeholder="Résumé en une ou deux phrases…"></textarea>
            </div>

            <div class="form-group">
                <label for="description_longue_activites">Description détaillée</label>
                <textarea class="form-control" id="description_longue_activites" rows="6"
                    placeholder="Description complète de l'activité…"></textarea>
            </div>
        </div>

        <!-- ── Image d'illustration ── -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-image"></i> Illustration</p>
            <div class="form-group">
                <label>Image de l'activité</label>
                <label class="upload-zone" for="image_activites">
                    <input type="file" id="image_activites" accept="image/*">
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

        <!-- ── Actions ── -->
        <div class="form-actions">
            <a href="javascript:history.back();" class="btn-cancel">Annuler</a>
            <button class="btn-submit" id="envoyer">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
            </button>
        </div>

        <div id="erreur" class="alerte"></div>
    </div>

    <script>
        const idActivites = <?php echo $id_activites; ?>;
        let lien = 'modif_activites';
    </script>

    <script>
        (function() {
            const champs = [{
                    selector: '#titre_activites',
                    key: 'titre'
                },
                {
                    selector: '#date_activites',
                    key: 'date'
                },
                {
                    selector: '#lieu_activites',
                    key: 'lieu'
                },
                {
                    selector: '#description_courte_activites',
                    key: 'description_courte'
                },
                {
                    selector: '#description_longue_activites',
                    key: 'description_longue'
                }
            ];

            champs.forEach(({
                selector
            }) => {
                const el = document.querySelector(selector);
                if (el) el.classList.add('skeleton');
            });

            fetch(`../cimes_api/index_api.php?query=activites&id=${idActivites}`)
                .then(r => r.ok ? r.json() : Promise.reject('Erreur réseau'))
                .then(data => {
                    const activite = Array.isArray(data) ? data[0] : data;
                    if (!activite) {
                        document.querySelector('#erreur').innerHTML = 'Activité introuvable.';
                        return;
                    }

                    champs.forEach(({
                        selector,
                        key
                    }) => {
                        const el = document.querySelector(selector);
                        if (el) {
                            el.classList.remove('skeleton');
                            if (activite[key] !== undefined) el.value = activite[key];
                        }
                    });

                    if (activite.image) {
                        document.getElementById('image-preview').innerHTML =
                            `<img src="../cimes_clients/img/${activite.image}" alt="Image actuelle">`;
                    }

                    document.title = `Modifier – ${activite.titre || 'Activité'}`;
                    document.querySelector('.form-page-title').textContent =
                        `Modification : ${activite.titre || 'Activité'}`;
                })
                .catch(err => {
                    champs.forEach(({
                        selector
                    }) => {
                        const el = document.querySelector(selector);
                        if (el) el.classList.remove('skeleton');
                    });
                    document.querySelector('#erreur').innerHTML = 'Impossible de charger l’activité.';
                    console.error(err);
                });

            document.getElementById('image_activites').addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('image-preview').innerHTML =
                        `<img src="../cimes_clients/img/${e.target.result}" alt="Aperçu">`;
                };
                reader.readAsDataURL(file);
            });

            window.getFormData = function() {
                return {
                    id: idActivites,
                    titre: document.getElementById('titre_activites').value.trim(),
                    date: document.getElementById('date_activites').value.trim(),
                    lieu: document.getElementById('lieu_activites').value.trim(),
                    description_courte: document.getElementById('description_courte_activites').value.trim(),
                    description_longue: document.getElementById('description_longue_activites').value.trim()
                };
            };
        })();
    </script>

    <script src="js/creer_modif_activites.js" async defer></script>

</body>

</html>
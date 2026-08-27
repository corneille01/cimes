<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
$id_logo = (int)($_GET['id'] ?? 0);
if ($id_logo <= 0) die('ID invalide.');
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modifier un logo partenaire</title>
<style>
    /* Variables et styles identiques à ajout_actu.php (gardez le même bloc) */
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

    * {
        box-sizing: border-box;
    }

    body {
        background: #f2f5f3;
        color: var(--text);
        font-family: system-ui, sans-serif;
        margin: 0;
    }

    .form-wrapper {
        max-width: 820px;
        margin: calc(var(--header-height) + 20px) auto 40px;
        padding: 0 24px;
    }

    .form-page-title {
        text-align: center;
        color: var(--green-dark);
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .form-page-sub {
        text-align: center;
        color: var(--text-hint);
        font-size: 0.88rem;
        margin-bottom: 32px;
    }

    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-card);
        padding: 28px;
        margin-bottom: 20px;
    }

    .form-card__title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--green-dark);
        margin-bottom: 20px;
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

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        font-size: 0.88rem;
        outline: none;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px var(--green-ring);
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
        transition: .2s;
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
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .upload-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
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
        text-decoration: none;
        font-weight: 600;
    }

    .btn-cancel:hover {
        background: var(--surface-alt);
    }

    .btn-submit {
        padding: 11px 28px;
        background: var(--green-dark);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(15, 110, 86, 0.30);
    }

    .btn-submit:hover {
        background: var(--green);
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

    .alerte-btn--primary {
        background: var(--green-dark);
        color: #fff;
        border: 1.5px solid transparent;
        box-shadow: 0 2px 8px rgba(15, 110, 86, 0.28);
    }

    .alerte-btn--outline:hover {
        background: var(--green-light);
    }

    .alerte-btn--primary:hover {
        background: var(--green);
    }
</style>
</head>

<body>

    <div class="form-wrapper">
        <h1 class="form-page-title">Modifier un logo partenaire</h1>
        <p class="form-page-sub">Modifiez le texte alternatif ou remplacez le logo.</p>

        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-image"></i> Logo actuel</p>
            <div class="form-group">
                <div id="current-logo" style="margin-bottom:12px;"></div>
                <label>Nouveau logo (laisser vide pour conserver l'actuel)</label>
                <label class="upload-zone" for="logo_file">
                    <input type="file" id="logo_file" accept="image/*">
                    <div class="upload-preview" id="image-preview">
                        <i class="fa-solid fa-image fa-2x"></i>
                    </div>
                    <div class="upload-info">
                        <strong>Cliquez pour changer l'image</strong>
                        <small>JPG, PNG, WebP — 13 Mo max.</small>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-tag"></i> Texte alternatif</p>
            <div class="form-group">
                <label for="alt_text">Texte alternatif <span style="color:var(--red);">*</span></label>
                <input type="text" id="alt_text" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="dynam_logo_partenaire.php" class="btn-cancel">Annuler</a>
            <button id="envoyer" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
        </div>
        <div id="erreur" class="alerte"></div>
    </div>

    <script>
        const id = <?= $id_logo ?>;
        let lien = 'modif_logo_partenaire';

        // Prévisualisation nouveau fichier
        document.getElementById('logo_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                document.getElementById('image-preview').innerHTML = `<img src="${ev.target.result}" alt="Aperçu">`;
            };
            reader.readAsDataURL(file);
        });

        // Récupération des données existantes
        fetch(`../cimes_api/index_api.php?query=logo_partenaire`)
            .then(r => r.json())
            .then(data => {
                const item = data.find(l => l.id == id);
                if (!item) {
                    document.getElementById('erreur').textContent = 'Logo introuvable.';
                    return;
                }
                document.getElementById('alt_text').value = item.alt || '';
                if (item.logo) {
                    document.getElementById('current-logo').innerHTML = `<img src="../cimes_clients/img/${item.logo}" style="max-height:80px;">`;
                    document.getElementById('image-preview').innerHTML = `<img src="../cimes_clients/img/${item.logo}" alt="Logo actuel">`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('erreur').textContent = 'Erreur chargement.';
            });

        window.getFormData = function() {
            return {
                id: id,
                alt: document.getElementById('alt_text').value.trim()
            };
        };
    </script>
    <script src="js/creer_modif_logo_partenaire.js"></script>

</body>

</html>
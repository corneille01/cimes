<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>
<title>Ajouter un partenaire</title>
<meta name="description" content="Formulaire d'ajout d'un partenaire du CIMES">
<style>
    /* (même CSS que pour modif_partenaires, voir ci-dessous) */
    :root {
        --header-height: 80px;
        --green-dark: #0F6E56;
        --green: #1D9E75;
        --green-light: #eef5f2;
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

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 16px;
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
        background: var(--surface);
        outline: none;
        font-family: inherit;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.2);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
        line-height: 1.55;
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
        margin-top: 8px;
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
        object-fit: cover;
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
        transition: background 0.15s, transform 0.1s;
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
    }

    .alerte-btn--primary:hover {
        background: var(--green);
        transform: translateY(-1px);
    }
</style>
</head>

<body>
    <div class="form-wrapper">
        <h1 class="form-page-title">Ajouter un partenaire</h1>
        <p class="form-page-sub">Remplissez les champs ci-dessous pour créer un nouveau partenaire.</p>
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-handshake"></i> Informations</p>
            <div class="form-group"><label>Titre <span class="required">*</span></label><input type="text" id="titre" class="form-control"></div>
            <div class="form-group"><label>Rôle (ex: Université, CNRS, Collectivité)</label><input type="text" id="role" class="form-control"></div>
            <div class="form-group">
                <label>Catégorie</label>
                <select id="categorie" class="form-control">
                    <option value="academique">Académique</option>
                    <option value="territorial">Territorial</option>
                    <option value="reseau">Réseau associé</option>
                </select>
            </div>
            <div class="form-group"><label>Description</label><textarea id="description" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Lien du site web</label><input type="url" id="lien_site" class="form-control" placeholder="https://..."></div>
            <div class="form-group">
                <label>Logo</label>
                <label class="upload-zone" for="image">
                    <input type="file" id="image" accept="image/jpeg,image/png,image/webp">
                    <div class="upload-preview" id="preview"><i class="fa-solid fa-camera"></i></div>
                    <div class="upload-info"><strong>Cliquez pour importer un logo</strong><small>JPG, PNG, WebP – 13 Mo max. Optionnel.</small></div>
                </label>
            </div>
        </div>
        <div class="form-actions">
            <a href="dynam_partenaires.php" class="btn-cancel">Annuler</a>
            <button id="envoyer" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Créer</button>
        </div>
        <div id="erreur" class="alerte"></div>
    </div>
    <script>
        const lien = 'cree_partenaire';
        // Prévisualisation
        const fileInput = document.getElementById('image');
        const previewDiv = document.getElementById('preview');
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    previewDiv.innerHTML = `<img src="${ev.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        window.getFormData = function() {
            function normalizeUrl(url) {
                if (!url) return '';
                url = url.trim();
                if (/^https?:\/\//i.test(url)) return url;
                if (url.startsWith('www.')) return 'https://' + url;
                return 'https://' + url;
            }
            return {
                titre: document.getElementById('titre').value.trim(),
                role: document.getElementById('role').value.trim(),
                categorie: document.getElementById('categorie').value,
                description: document.getElementById('description').value.trim(),
                lien_site: normalizeUrl(document.getElementById('lien_site').value.trim())
            };
        };
    </script>
    <script src="js/creer_modif_partenaires.js"></script>
</body>

</html>
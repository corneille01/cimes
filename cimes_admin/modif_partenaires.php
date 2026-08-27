<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("ID invalide");
?>
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>
<title>Modifier un partenaire</title>
<meta name="description" content="Formulaire de modification d’un partenaire du CIMES">
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

    .current-photo {
        margin-bottom: 10px;
    }

    .current-photo img {
        max-width: 150px;
        max-height: 100px;
        border-radius: 8px;
        border: 1px solid var(--border);
        padding: 4px;
        background: white;
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
        transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
    }

    .btn-submit:hover {
        background: var(--green);
        transform: translateY(-1px);
    }

    .alerte {
        margin-top: 20px;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
    }

    .alerte.success {
        background: #eef5f2;
        color: var(--green-dark);
        border: 1px solid var(--green);
    }

    .alerte.error {
        background: #fcebeb;
        color: var(--red);
        border: 1px solid #F09595;
    }

    .alerte-actions {
        margin-top: 12px;
    }

    .alerte-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 20px;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 600;
        background: white;
        color: var(--green-dark);
        border: 1px solid var(--green-dark);
    }

    .alerte-btn:hover {
        background: var(--green-light);
    }
</style>
</head>

<body>
    <div class="form-wrapper">
        <h1 class="form-page-title">Modifier un partenaire</h1>
        <p class="form-page-sub">Modifiez les informations du partenaire puis enregistrez.</p>
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-handshake"></i> Informations</p>
            <input type="hidden" id="id" value="<?= $id ?>">
            <div class="form-group"><label>Titre <span class="required">*</span></label><input type="text" id="titre" class="form-control"></div>
            <div class="form-group"><label>Rôle</label><input type="text" id="role" class="form-control"></div>
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
                <label>Logo actuel</label>
                <div id="current-photo" class="current-photo"></div>
                <label class="upload-zone" for="image">
                    <input type="file" id="image" accept="image/jpeg,image/png,image/webp">
                    <div class="upload-preview" id="preview"><i class="fa-solid fa-camera"></i></div>
                    <div class="upload-info"><strong>Cliquez pour changer le logo</strong><small>JPG, PNG, WebP – 13 Mo max. Laissez vide pour conserver.</small></div>
                </label>
            </div>
        </div>
        <div class="form-actions">
            <a href="dynam_partenaires.php" class="btn-cancel">Annuler</a>
            <button id="envoyer" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
        </div>
        <div id="erreur" class="alerte"></div>
    </div>
    <script>
        const lien = 'modif_partenaire';
        const id = <?= $id ?>;
        // Chargement des données
        fetch(`../cimes_api/index_api.php?query=partenaire&id=${id}`)
            .then(r => r.json())
            .then(data => {
                const p = Array.isArray(data) ? data[0] : data;
                if (!p) return;
                document.getElementById('titre').value = p.titre || '';
                document.getElementById('role').value = p.role || '';
                document.getElementById('categorie').value = p.categorie || 'academique';
                document.getElementById('description').value = p.description || '';
                document.getElementById('lien_site').value = p.lien_site || '';
                if (p.image) document.getElementById('current-photo').innerHTML = `<img src="../cimes_clients/img/${p.image}" alt="Logo" style="max-width:150px;">`;
            })
            .catch(err => console.error("Erreur chargement :", err));

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
                lien_site: normalizeUrl(document.getElementById('lien_site').value.trim()),
                id: id
            };
        };
    </script>
    <script src="js/creer_modif_partenaires.js"></script>
</body>

</html>
<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}

$id_contact = intval($_GET['id'] ?? 0);
if ($id_contact <= 0) {
    die("Identifiant de contact invalide.");
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Modification d'un contact</title>
<meta name="description" content="Formulaire de modification d'un contact">

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

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-md);
        border-radius: var(--radius-sm);
        font-size: 0.88rem;
        color: var(--text);
        background: var(--surface);
        outline: none;
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
        <h1 class="form-page-title">Modification d'un contact</h1>
        <p class="form-page-sub">Modifiez les champs ci-dessous puis enregistrez.</p>

        <!-- Coordonnées -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-address-card"></i> Coordonnées</p>

            <div class="form-row full">
                <div class="form-group">
                    <label for="nom_contact">Nom (facultatif)</label>
                    <input type="text" class="form-control" id="nom_contact" placeholder="ex. Siège CIMES">
                </div>
            </div>

            <div class="form-row" style="margin-top:14px;">
                <div class="form-group">
                    <label for="email_contact">Email</label>
                    <input type="email" class="form-control" id="email_contact" placeholder="mailcimes@gmail.com">
                </div>
                <div class="form-group">
                    <label for="telephone_contact">Téléphone</label>
                    <input type="text" class="form-control" id="telephone_contact" placeholder="+33 5 11 22 33 44">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <a href="javascript:history.back();" class="btn-cancel">Annuler</a>
            <button class="btn-submit" id="envoyer">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
            </button>
        </div>

        <div id="erreur" class="alerte"></div>
    </div>

    <script>
        const id = <?= $id_contact ?>;
        let lien = 'modif_contact';

        window.getFormData = function() {
            return {
                id: id,
                nom: document.getElementById('nom_contact').value.trim(),
                email: document.getElementById('email_contact').value.trim(),
                telephone: document.getElementById('telephone_contact').value.trim()
            };
        };

        // Pré-remplissage depuis l'API
        const champs = [{
                selector: '#nom_contact',
                key: 'nom'
            },
            {
                selector: '#email_contact',
                key: 'email'
            },
            {
                selector: '#telephone_contact',
                key: 'telephone'
            }
        ];

        champs.forEach(({
            selector
        }) => {
            const el = document.querySelector(selector);
            if (el) el.classList.add('skeleton');
        });

        fetch(`../cimes_api/index_api.php?query=contact&id=${id}`)
            .then(r => r.ok ? r.json() : Promise.reject('Erreur réseau'))
            .then(data => {
                champs.forEach(({
                    selector
                }) => {
                    const el = document.querySelector(selector);
                    if (el) el.classList.remove('skeleton');
                });

                const contact = Array.isArray(data) ? data[0] : data;
                if (!contact) {
                    document.querySelector('#erreur').innerHTML = 'Aucun contact trouvé.';
                    return;
                }

                champs.forEach(({
                    selector,
                    key
                }) => {
                    const el = document.querySelector(selector);
                    if (el && contact[key] !== undefined && contact[key] !== null) {
                        el.value = contact[key];
                    }
                });

                if (contact.nom) {
                    document.title = `Modifier – ${contact.nom}`;
                    const h1 = document.querySelector('.form-page-title');
                    if (h1) h1.textContent = `Modification du contact : ${contact.nom}`;
                }
            })
            .catch(err => {
                champs.forEach(({
                    selector
                }) => {
                    const el = document.querySelector(selector);
                    if (el) el.classList.remove('skeleton');
                });
                document.querySelector('#erreur').innerHTML = 'Impossible de charger le contact.';
                console.error(err);
            });
    </script>

    <script src="js/creer_modif_contact.js" async defer></script>

</body>

</html>
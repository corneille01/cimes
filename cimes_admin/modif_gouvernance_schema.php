<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: dynam_gouvernance.php');
    exit();
}
?>
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier le schéma de gouvernance</title>
    <style>
        :root {
            --green-dark: #0F6E56;
            --surface: #ffffff;
            --border: rgba(0, 0, 0, 0.12);
            --text: #1e2420;
            --text-muted: #5f6b65;
            --red: #A32D2D;
            --radius-md: 10px;
            --radius-lg: 14px;
        }

        body {
            background: #f2f5f3;
            font-family: system-ui, sans-serif;
            margin: 0;
        }

        .dynam-wrapper {
            max-width: 700px;
            margin: 100px auto 40px;
            padding: 0 24px;
        }

        .dynam-toolbar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .bouton_retour {
            background: #64748b;
            color: white;
            padding: 10px 24px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
        }

        .form-container {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 32px;
        }

        h2 {
            margin-top: 0;
            color: var(--text);
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 1rem;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: var(--green-dark);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            font-size: 1rem;
            margin-bottom: 24px;
            /* espace avant le message */
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .alerte {
            padding: 16px;
            border-radius: var(--radius-md);
            /* margin-top: 24px; */
            /* sera géré par le flux */
        }

        .alerte.success {
            background: #d1fae5;
            color: #065f46;
        }

        .alerte.error {
            background: #fee2e2;
            color: var(--red);
        }

        .alerte-actions {
            margin-top: 12px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .alerte-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
        }

        .alerte-btn--outline {
            border: 2px solid var(--green-dark);
            color: var(--green-dark);
            background: transparent;
        }

        .alerte-btn--outline:hover {
            background: var(--green-dark);
            color: white;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: var(--text-muted);
        }
    </style>
</head>

<body>
    <div class="dynam-wrapper">
        <div class="dynam-toolbar">
            <a href="dynam_gouvernance.php" class="bouton_retour">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <div class="form-container">
            <h2>Modifier le schéma de gouvernance</h2>

            <!-- Chargement masqué quand le formulaire s'affiche -->
            <div id="loading" class="loading">Chargement des données...</div>

            <form id="form-schema" style="display: none;">
                <input type="hidden" id="record-id" value="<?= $id ?>">

                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="reunion">Fréquence des réunions</label>
                    <input type="text" id="reunion" name="reunion" placeholder="Ex. : 1 réunion / an">
                </div>

                <div class="form-group">
                    <label for="ordre">Ordre d'affichage</label>
                    <input type="number" id="ordre" name="ordre" min="0" step="1">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </form>

            <!-- Message d'erreur / succès placé APRÈS le formulaire -->
            <div id="message"></div>
        </div>
    </div>

    <script>
        const API_URL = '../cimes_api/index_api.php';
        const id = <?= $id ?>;
        const LABEL_BTN_DEFAUT = '<i class="fas fa-save"></i> Enregistrer les modifications';

        function afficherErreur(msg) {
            const el = document.getElementById('message');
            el.className = 'alerte error';
            el.textContent = msg;
        }

        function afficherSucces() {
            const el = document.getElementById('message');
            el.className = 'alerte success';
            el.innerHTML = `
                <span>Les modifications ont bien été enregistrées.</span>
                <div class="alerte-actions">
                    <a href="dynam_gouvernance.php" class="alerte-btn alerte-btn--outline">
                        <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>`;
            sessionStorage.setItem('reload_needed_dynam', Date.now());
        }

        async function chargerSchema() {
            try {
                const response = await fetch(API_URL + '?query=gouvernance_schema');
                const data = await response.json();
                const record = data.find(item => item.id == id);

                document.getElementById('loading').style.display = 'none';

                if (!record) {
                    afficherErreur("Aucun schéma trouvé avec l'ID " + id);
                    return;
                }

                document.getElementById('titre').value = record.titre || '';
                document.getElementById('description').value = record.description || '';
                document.getElementById('reunion').value = record.reunion || '';
                document.getElementById('ordre').value = record.ordre || 0;

                document.getElementById('form-schema').style.display = 'block';
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                afficherErreur('Erreur réseau ou serveur : ' + error.message);
            }
        }

        document.getElementById('form-schema').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = this.querySelector('.btn-submit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement…';

            const payload = {
                lien: 'modif_gouvernance_schema',
                id: id,
                titre: document.getElementById('titre').value.trim(),
                description: document.getElementById('description').value.trim(),
                reunion: document.getElementById('reunion').value.trim(),
                ordre: parseInt(document.getElementById('ordre').value) || 0
            };

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.text();
                btn.disabled = false;
                btn.innerHTML = LABEL_BTN_DEFAUT;

                if (result === 'ok') {
                    afficherSucces();
                } else {
                    afficherErreur('Erreur : ' + result);
                }
            } catch (error) {
                btn.disabled = false;
                btn.innerHTML = LABEL_BTN_DEFAUT;
                afficherErreur('Problème de réseau. Veuillez réessayer.');
            }
        });

        chargerSchema();
    </script>

    <?php include('include/footer.html'); ?>
</body>

</html>
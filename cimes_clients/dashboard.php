<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: espace_personnel.php");
    exit();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isUser  = (isset($_SESSION['role']) && $_SESSION['role'] === 'user');

if (!$isAdmin && !$isUser) {
    die('<div style="text-align:center; margin-top:2rem;"><h3>🔒 Accès refusé</h3></div>');
}

$userId    = $_SESSION['id'];
$firstname = $_SESSION['firstname'] ?? '';
$lastname  = $_SESSION['lastname'] ?? '';
$email     = $_SESSION['email'] ?? '';
$initials  = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
?>
<?php include('include/head.html'); ?>
<?php include('include/header.html'); ?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <main>

        <!-- WELCOME -->
        <section class="dash-welcome">
            <h1 class="dash-welcome__title">
                Bienvenue, <?= htmlspecialchars($firstname . ' ' . $lastname) ?> !
            </h1>
            <p class="dash-welcome__subtitle">
                <?= $isAdmin
                    ? 'Vous êtes administrateur. Gérez la plateforme depuis ce tableau de bord.'
                    : 'Bienvenue dans votre espace personnel. Retrouvez vos projets et vos outils.' ?>
            </p>
            <a href="logout.php" class="dash-logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
            </a>
        </section>

        <div class="dash-wrapper">

            <div class="dash-overlay" id="dash-overlay"></div>

            <div class="dash-grid">

                <!-- ===== PROFIL ===== -->
                <div class="dash-card-wrap">
                    <div class="dash-panel" id="dash-panel-profil">
                        <div class="dash-panel__head">
                            <h2 class="dash-panel__head-title">
                                <i class="fas fa-user" style="color:var(--vert);margin-right:5px;font-size:11px;"></i> Mon profil
                            </h2>
                            <button class="dash-panel__close" data-dash-close="profil">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="dash-panel__body">
                            <div class="dash-profile-top">
                                <div class="dash-avatar" id="dash-avatar-initials">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div class="dash-profile-meta">
                                    <strong class="dash-profile-meta__name">
                                        <?= htmlspecialchars($firstname . ' ' . $lastname) ?>
                                    </strong>
                                    <span class="dash-profile-meta__role">
                                        <?= $isAdmin ? 'Administrateur' : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div id="profil-message" class="dash-message"></div>
                            <form id="dash-profil-form" method="POST">
                                <input type="hidden" name="user_id" value="<?= $userId ?>">
                                <div class="dash-form-grid">
                                    <div class="dash-form-row">
                                        <label class="dash-form-row__label" for="dash-f-nom">Nom</label>
                                        <input class="dash-form-row__input" type="text" id="dash-f-nom" name="nom"
                                            value="<?= htmlspecialchars($lastname) ?>" readonly>
                                    </div>
                                    <div class="dash-form-row">
                                        <label class="dash-form-row__label" for="dash-f-prenom">Prénom</label>
                                        <input class="dash-form-row__input" type="text" id="dash-f-prenom" name="prenom"
                                            value="<?= htmlspecialchars($firstname) ?>" readonly>
                                    </div>
                                    <div class="dash-form-row">
                                        <label class="dash-form-row__label" for="dash-f-email">Email</label>
                                        <input class="dash-form-row__input" type="text" id="dash-f-email" name="mail"
                                            value="<?= htmlspecialchars($email) ?>" readonly>
                                    </div>
                                    <hr class="dash-form-divider">
                                    <div class="dash-form-row">

                                        <label class="dash-form-row__label" for="dash-f-pwd">
                                            Mot de passe
                                        </label>

                                        <small class="dash-password-help">
                                            Laissez vide si vous ne souhaitez pas le modifier.
                                        </small>

                                        <input
                                            class="dash-form-row__input"
                                            type="text"
                                            id="dash-f-pwd"
                                            name="password"
                                            placeholder="Ex : MonPass@123"
                                            readonly>
                                    </div>
                                </div>

                                <!-- Vue : bouton Modifier -->
                                <div class="dash-actions" id="dash-btn-view">
                                    <button type="button" class="dash-btn" id="dash-edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                </div>

                                <!-- Édition : Enregistrer + Annuler -->
                                <div class="dash-actions" id="dash-btn-edit" style="display:none;">
                                    <button type="submit" class="dash-btn dash-btn--primary">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                    <button type="button" class="dash-btn dash-btn--danger" id="dash-cancel-btn">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="dash-card" data-dash-target="profil">
                        <div class="dash-card__icon"><i class="fas fa-user"></i></div>
                        <h3 class="dash-card__title">Mon profil</h3>
                        <p class="dash-card__desc">Consultez et modifiez vos informations personnelles.</p>
                    </div>
                </div>

                <?php if ($isUser): ?>



                    <!-- ===== AJOUTER UN PROJET ===== -->

                    <a href="user_dynam_projets.php" style="text-decoration:none;color:inherit;">
                        <div class="dash-card" data-dash-target="projets">
                            <div class="dash-card__icon"><i class="fas fa-briefcase"></i></div>
                            <h3 class="dash-card__title">Mes projets</h3>
                            <p class="dash-card__desc">Gérez vos projets de recherche et collaborations.</p>
                        </div>
                    </a>



            </div>

        <?php elseif ($isAdmin): ?>
            <!-- ===== ADMIN ===== -->
            <div class="dash-card-wrap">
                <div class="dash-panel" id="dash-panel-adminPanel">
                    <div class="dash-panel__head">
                        <h2 class="dash-panel__head-title">
                            <i class="fas fa-crown" style="color:var(--vert);margin-right:5px;font-size:11px;"></i> Administration
                        </h2>
                        <button class="dash-panel__close" data-dash-close="adminPanel">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="dash-panel__body">
                        <div class="dash-admin-grid">
                            <a href="../cimes_admin/navbar.php" class="dash-admin-entry">
                                <i class="fas fa-th-large"></i>Barre de navigation et pages associées
                            </a>
                            <a href="../cimes_admin/modif_visibilite_sections_page_accueil.php" class="dash-admin-entry">
                                <i class="fas fa-home"></i> Visibilité des sections de la page d'accueil
                            </a>

                            <a href="../cimes_admin/dynam_carrousel.php" class="dash-admin-entry">
                                <i class="fas fa-images"></i> Modifier les images des diapositives de la page d'accueil
                            </a>
                            <a href="../cimes_admin/dynam_programme.php" class="dash-admin-entry">
                                <i class="fas fa-images"></i> Ajouter/Modifier les programmes
                            </a>
                            <a href="../cimes_admin/dynam_logo_partenaire.php" class="dash-admin-entry">
                                <i class="fas fa-images"></i> Ajouter/Modifier les logos des partenaires sur la pages d'accueil
                            </a>
                            <a href="../cimes_admin/dynam_contact.php" class="dash-admin-entry">
                                <i class="fas fa-images"></i> Ajouter/Modifier les contacts dans le pied de page
                            </a>

                        </div>
                    </div>
                </div>

                <div class="dash-card" data-dash-target="adminPanel">
                    <div class="dash-card__icon"><i class="fas fa-crown"></i></div>
                    <h3 class="dash-card__title">Panel admin</h3>
                    <p class="dash-card__desc">Interface complète d'administration de la plateforme.</p>
                </div>
            </div>
        <?php endif; ?>

        </div><!-- /dash-grid -->
        </div><!-- /dash-wrapper -->
    </main>

    <?php echo '<script>let id = ' . json_encode($_SESSION['id']) . ';</script>'; ?>

    <script>
        (function() {
            'use strict';

            const NAV_H = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-height')) || 80;
            const GAP = 8;
            const overlay = document.getElementById('dash-overlay');

            /* ---- Fermer tous les panneaux ---- */
            function closeAll() {
                document.querySelectorAll('.dash-panel').forEach(p => {
                    p.classList.remove('dash-panel--open');
                    p.style.bottom = '';
                    p.style.maxHeight = '';
                    p.style.overflowY = '';
                });
                document.querySelectorAll('.dash-card').forEach(c => c.classList.remove('dash-card--active'));
                overlay.classList.remove('dash-overlay--active');
            }

            /* ---- Ouvrir un panneau ---- */
            function openPanel(panel, card) {
                closeAll();
                panel.classList.add('dash-panel--open');
                card.classList.add('dash-card--active');
                overlay.classList.add('dash-overlay--active');

                requestAnimationFrame(() => {
                    const rect = panel.getBoundingClientRect();
                    const limit = NAV_H + GAP;
                    if (rect.top < limit) {
                        const overflow = limit - rect.top;
                        const available = rect.height - overflow - 10;
                        if (available > 120) {
                            panel.style.maxHeight = available + 'px';
                            panel.style.overflowY = 'auto';
                        } else {
                            const cur = parseFloat(getComputedStyle(panel).bottom) || 0;
                            panel.style.bottom = (cur - overflow) + 'px';
                        }
                    }
                });
            }

            /* ---- Ouverture au clic sur une carte ---- */
            document.querySelectorAll('[data-dash-target]').forEach(card => {
                card.addEventListener('click', () => {
                    const panel = document.getElementById('dash-panel-' + card.dataset.dashTarget);
                    if (panel.classList.contains('dash-panel--open')) {
                        closeAll();
                        return;
                    }
                    openPanel(panel, card);
                });
            });

            /* ---- Boutons de fermeture (×) ---- */
            document.querySelectorAll('[data-dash-close]').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    closeAll();
                });
            });

            /* ---- Overlay ---- */
            overlay.addEventListener('click', closeAll);

            /* ---- Édition profil ---- */
            const editBtn = document.getElementById('dash-edit-btn');
            const cancelBtn = document.getElementById('dash-cancel-btn');
            const rowView = document.getElementById('dash-btn-view');
            const rowEdit = document.getElementById('dash-btn-edit');
            const form = document.getElementById('dash-profil-form');
            const origVals = {};

            function getInputs() {
                return form ? form.querySelectorAll('.dash-form-row__input') : [];
            }

            if (editBtn) {
                editBtn.addEventListener('click', () => {
                    getInputs().forEach(inp => {
                        origVals[inp.name] = inp.value;
                        inp.readOnly = false;
                    });
                    rowView.style.display = 'none';
                    rowEdit.style.display = 'flex';
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    getInputs().forEach(inp => {
                        inp.value = origVals[inp.name] ?? inp.value;
                        inp.readOnly = true;
                    });
                    rowView.style.display = 'flex';
                    rowEdit.style.display = 'none';
                });
            }

            /* ---- Upload photo ---- */
            const editPhotoBtn = document.getElementById('dash-edit-photo-btn');
            if (editPhotoBtn) {
                editPhotoBtn.addEventListener('click', () => {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';
                    input.onchange = () => {
                        const fd = new FormData();
                        fd.append('profile_photo', input.files[0]);
                        fd.append('user_id', '<?= $userId ?>');
                        fetch('../cimes_api/index_api.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('dash-avatar-initials').innerHTML =
                                        `<img src="uploads/${data.filename}?t=${Date.now()}" alt="photo">`;
                                } else {
                                    alert('Erreur upload : ' + data.error);
                                }
                            })
                            .catch(() => alert('Erreur technique'));
                    };
                    input.click();
                });
            }

        })();

        const form = document.getElementById("dash-profil-form");

        form.addEventListener("submit", async (e) => {

            e.preventDefault();

            // rôle venant du PHP
            const isAdmin = <?= json_encode($isAdmin) ?>;
            const isUser = <?= json_encode($isUser) ?>;

            let data = {};

            // =========================
            // ADMIN
            // =========================
            if (isAdmin) {

                data = {
                    lien: "modif_profil_admin",

                    id: id,

                    lastname: document.getElementById("dash-f-nom").value,
                    firstname: document.getElementById("dash-f-prenom").value,
                    email: document.getElementById("dash-f-email").value,
                    password: document.getElementById("dash-f-pwd").value
                };
            }

            // =========================
            // USER
            // =========================
            if (isUser) {

                data = {
                    lien: "modif_profil_user",

                    id: id,

                    name: document.getElementById("dash-f-prenom").value + " " +
                        document.getElementById("dash-f-nom").value,

                    email: document.getElementById("dash-f-email").value,
                    password: document.getElementById("dash-f-pwd").value
                };
            }

            try {

                const response = await fetch("../cimes_api/index_api.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                const messageBox = document.getElementById("profil-message");

                messageBox.textContent = result.message;

                if (result.status === "success") {

                    messageBox.className = "dash-message success";

                    // remet les champs en readonly
                    document.querySelectorAll('.dash-form-row__input').forEach(inp => {
                        inp.readOnly = true;
                    });

                    document.getElementById('dash-btn-view').style.display = 'flex';
                    document.getElementById('dash-btn-edit').style.display = 'none';

                } else {

                    messageBox.className = "dash-message error";

                }

            } catch (error) {

                console.error(error);

                const messageBox = document.getElementById("profil-message");

                messageBox.textContent = "Erreur serveur";
                messageBox.className = "dash-message error";

            }

        });
    </script>
</body>

</html>
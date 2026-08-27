/* ─────────────────────────────────────────────
   creer_modif_programme.js
   Gère la création (lien = 'cree_programme')
   et la modification (lien = 'modif_programme')
   ───────────────────────────────────────────── */
'use strict';

const estModif = (lien === 'modif_programme');

/* Libellé du bouton selon le mode */
const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer le programme';

if (!estModif) {
    document.title = 'Ajouter un programme';
}

document.querySelector('#envoyer').addEventListener('click', function (e) {
    e.preventDefault();
    envoi_donnees();
});

/* ══════════════════════════════════════════════
   Fonction principale d'envoi
══════════════════════════════════════════════ */
function envoi_donnees() {

    /* Récupération des données via getFormData() ou fallback direct */
    let formData;
    if (typeof window.getFormData === 'function') {
        formData = window.getFormData();
    } else {
        formData = {
            titre: document.getElementById('titre_programme')?.value.trim() ?? '',
            texte: document.getElementById('texte_programme')?.value.trim() ?? '',
        };
        /* En mode modif, id est disponible en variable globale */
        if (estModif && typeof id !== 'undefined') {
            formData.id = id;
        }
    }

    /* Validation minimale */
    if (!formData.titre || !formData.texte) {
        afficherErreur('Le titre et la description sont obligatoires.');
        return;
    }

    /* Vérification poids image côté client */
    const fileInput = document.getElementById('image_programme');
    const file = fileInput?.files[0];

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (13 Mo max). Veuillez choisir une image plus légère.');
        return;
    }

    /* Construction du payload */
    const buildPayload = (imageBase64 = null, imageMime = null) => {
        const payload = { lien, ...formData };
        if (imageBase64) {
            payload.image_base64 = imageBase64;
            payload.image_mime   = imageMime;
        }
        return payload;
    };

    /* Reset formulaire (ajout uniquement) */
    const resetFormulaire = () => {
        ['titre_programme', 'texte_programme'].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
        const imageInput = document.getElementById('image_programme');
        if (imageInput) imageInput.value = '';
        const imagePreview = document.getElementById('image-preview');
        if (imagePreview) imagePreview.innerHTML = '<i class="fa-solid fa-mountain"></i>';
    };

    /* Envoi fetch */
    const send = (payload) => {
        const btn = document.querySelector('#envoyer');
        btn.style.opacity       = '0.6';
        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

        fetch('../cimes_api/index_api.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload),
        })
        .then(r => r.text())
        .then(response => {
            const r = response.trim();
            console.log('Server response:', r);

            btn.style.opacity       = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;

            if (r === 'ok') {
                sessionStorage.setItem('reload_needed_dynam', Date.now());
                if (!estModif) resetFormulaire();

                const errEl = document.querySelector('#erreur');
                errEl.className = 'alerte success';

                if (estModif) {
                    errEl.innerHTML = `
                        <span>Les modifications ont bien été enregistrées.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </a>
                        </div>`;
                } else {
                    errEl.innerHTML = `
                        <span>Le programme a bien été enregistré.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour aux programmes
                            </a>
                            <a href="ajout_programme.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter un autre programme
                            </a>
                        </div>`;
                }

            } else if (r === 'ko_doublon') {
                const titre = document.getElementById('titre_programme').value.trim();
                afficherErreur(`Un programme nommé "${titre}" existe déjà. Veuillez modifier le titre.`);

            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (13 Mo max).');

            } else if (r === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP uniquement).");

            } else if (r === 'ko_write') {
                afficherErreur("Impossible d'écrire l'image sur le serveur. Vérifiez les permissions du dossier.");

            } else if (r === 'ko_base64') {
                afficherErreur("Erreur lors de l'encodage de l'image. Réessayez.");

            } else {
                afficherErreur('Erreur serveur : ' + r);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            btn.style.opacity       = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;
            afficherErreur('Problème de réseau. Veuillez réessayer.');
        });
    };

    /* Traitement image : resize + base64 si fichier sélectionné */
    if (file) {
        const img       = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(objectUrl);

            const MAX = 1200;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else       { w = Math.round(w * MAX / h); h = MAX; }
            }

            const canvas  = document.createElement('canvas');
            canvas.width  = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            const outputMime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
            const quality    = outputMime === 'image/jpeg' ? 0.82 : undefined;

            canvas.toBlob(blob => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const [header, b64] = e.target.result.split(',');
                    const mime = header.match(/:(.*?);/)[1];
                    send(buildPayload(b64, mime));
                };
                reader.onerror = () => afficherErreur('Impossible de lire le fichier image.');
                reader.readAsDataURL(blob);
            }, outputMime, quality);
        };

        img.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            afficherErreur('Fichier image invalide ou corrompu.');
        };

        img.src = objectUrl;

    } else {
        send(buildPayload());
    }
}

/* ── Affichage erreur ── */
function afficherErreur(msg) {
    const el = document.querySelector('#erreur');
    el.className  = 'alerte error';
    el.textContent = msg;
}
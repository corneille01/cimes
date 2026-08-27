/* ─────────────────────────────────────────────
   creer_modif_activites.js
   Gère la création (lien = 'cree_activites')
   et la modification (lien = 'modif_activites')
   ───────────────────────────────────────────── */
'use strict';

const estModif = (typeof lien !== 'undefined' && lien === 'modif_activites');

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer l\'activité';

if (!estModif) {
    document.title = 'Ajouter une activité';
}

document.querySelector('#envoyer').addEventListener('click', function (e) {
    e.preventDefault();
    envoi_donnees();
});

function envoi_donnees() {
    let formData;
    if (typeof window.getFormData === 'function') {
        formData = window.getFormData();
    } else {
        formData = {
            titre: document.getElementById('titre_activites')?.value.trim() ?? '',
            date: document.getElementById('date_activites')?.value.trim() ?? '',
            lieu: document.getElementById('lieu_activites')?.value.trim() ?? '',
            description_courte: document.getElementById('description_courte_activites')?.value.trim() ?? '',
            description_longue: document.getElementById('description_longue_activites')?.value.trim() ?? ''
        };
        if (estModif && typeof idActivites !== 'undefined') {
            formData.id = idActivites;
        }
    }

    if (!formData.titre) {
        afficherErreur('Le titre est obligatoire.');
        return;
    }

    const fileInput = document.getElementById('image_activites');
    const file = fileInput?.files[0];

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (13 Mo max).');
        return;
    }

    const buildPayload = (photoBase64 = null, photoMime = null) => {
        const payload = { lien, ...formData };
        if (photoBase64) {
            payload.image_base64 = photoBase64;
            payload.image_mime   = photoMime;
        }
        return payload;
    };

    const resetFormulaire = () => {
        [
            'titre_activites', 'date_activites', 'lieu_activites',
            'description_courte_activites', 'description_longue_activites'
        ].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
        const imgInput = document.getElementById('image_activites');
        if (imgInput) imgInput.value = '';
        const preview = document.getElementById('image-preview');
        if (preview) preview.innerHTML = '<i class="fa-solid fa-image fa-2x"></i>';
    };

    const send = (payload) => {
        const btn = document.querySelector('#envoyer');
        btn.style.opacity = '0.6';
        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

        fetch('../cimes_api/index_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(r => r.text())
        .then(response => {
            const r = response.trim();
            btn.style.opacity = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;

            if (r === 'ok') {
                if (!estModif) resetFormulaire();
                sessionStorage.setItem('reload_needed_dynam', Date.now());

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
                        <span>L'activité a bien été créée.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour aux activités
                            </a>
                            <a href="ajout_activites.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter une autre activité
                            </a>
                        </div>`;
                }
            } else if (r === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP uniquement).");
            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (13 Mo max).');
            } else if (r === 'ko_write') {
                afficherErreur("Impossible d'écrire l'image sur le serveur.");
            } else if (r === 'ko_base64') {
                afficherErreur("Erreur lors de l'encodage de l'image.");
            } else {
                afficherErreur('Erreur : ' + r);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            btn.style.opacity = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;
            afficherErreur('Problème de réseau. Veuillez réessayer.');
        });
    };

    if (file) {
        const img = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(objectUrl);

            const MAX = 1200;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else       { w = Math.round(w * MAX / h); h = MAX; }
            }

            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            const outputMime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
            const quality = outputMime === 'image/jpeg' ? 0.82 : undefined;

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

function afficherErreur(msg) {
    const el = document.querySelector('#erreur');
    el.className = 'alerte error';
    el.textContent = msg;
}
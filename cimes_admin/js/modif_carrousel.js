/* ─────────────────────────────────────────────
   modif_carrousel.js
   Gère la modification d'un slide du carrousel
   (lien = 'modif_carrousel')
   ───────────────────────────────────────────── */
'use strict';

const estModif = (lien === 'modif_carrousel');

const LABEL_BTN_DEFAUT = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications';

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
            id: (typeof id !== 'undefined') ? id : 0,
            titre: document.getElementById('titre_carrousel')?.value.trim() ?? '',
            sous_titre: document.getElementById('soustitre_carrousel')?.value.trim() ?? '',
        };
    }

    if (!formData.titre) {
        afficherErreur('Le titre est obligatoire.');
        return;
    }

    const fileInput = document.getElementById('image_carrousel');
    const file = fileInput?.files[0];

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (13 Mo max). Veuillez choisir une image plus légère.');
        return;
    }

    const buildPayload = (imageBase64 = null, imageMime = null) => {
        const payload = { lien, ...formData };
        if (imageBase64) {
            payload.image_base64 = imageBase64;
            payload.image_mime   = imageMime;
        }
        return payload;
    };

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
                // Marquer qu'un rechargement est nécessaire pour dynam_carrousel.php
                sessionStorage.setItem('reload_needed_carrousel', Date.now());

                const errEl = document.querySelector('#erreur');
                errEl.className = 'alerte success';
                errEl.innerHTML = `
                    <span>Le slide a bien été mis à jour.</span>
                    <div class="alerte-actions">
                        <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                    </div>`;
            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (13 Mo max).');
            } else if (r === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP uniquement).");
            } else if (r === 'ko_write') {
                afficherErreur("Impossible d'écrire l'image sur le serveur.");
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

    if (file) {
        const img       = new Image();
        const objectUrl = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(objectUrl);

            const MAX = 1920;
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
            const quality    = outputMime === 'image/jpeg' ? 0.85 : undefined;

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
    el.className  = 'alerte error';
    el.textContent = msg;
}
/* ─────────────────────────────────────────────
   modif_presentation.js
   Gère la modification de la section Présentation
   ───────────────────────────────────────────── */
'use strict';

const LABEL_BTN_DEFAUT = '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications';

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
            texte: document.getElementById('texte_presentation')?.value.trim() ?? '',
        };
    }

    /* Validation minimale */
    if (!formData.texte) {
        afficherErreur('Le corps du texte est obligatoire.');
        return;
    }

    /* Vérification poids image côté client */
    const fileInput = document.getElementById('image_presentation');
    const file = fileInput?.files[0];

    if (file && file.size > 10 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (10 Mo max). Veuillez choisir une image plus légère.');
        return;
    }

    /* Construction du payload */
    const buildPayload = (imageBase64 = null, imageMime = null) => {
        const payload = { lien: 'modif_presentation', ...formData };
        if (imageBase64) {
            payload.image_base64 = imageBase64;
            payload.image_mime   = imageMime;
        }
        return payload;
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
                const errEl = document.querySelector('#erreur');
                errEl.className = 'alerte success';
                errEl.innerHTML = `
                    <span>Les modifications ont bien été enregistrées.</span>
                    <div class="alerte-actions">
                        <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                    </div>`;

            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (10 Mo max).');

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

            const MAX = 1600;
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
    el.className   = 'alerte error';
    el.textContent = msg;
}
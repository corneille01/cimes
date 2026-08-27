/* ── Gère création (cree_logo_partenaire) et modification (modif_logo_partenaire) ── */
'use strict';

const estModif = (typeof lien !== 'undefined' && lien === 'modif_logo_partenaire');
const LABEL_BTN = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer le logo';

document.querySelector('#envoyer').addEventListener('click', function(e) {
    e.preventDefault();
    envoi();
});

function envoi() {
    const formData = window.getFormData ? window.getFormData() : {};
    const fileInput = document.getElementById('logo_file');
    const file = fileInput?.files[0];

    // Validation
    if (!estModif && !file) {
        afficherErreur('Le logo est obligatoire.');
        return;
    }
    if (!formData.alt && formData.alt !== '') { // alt peut être vide ? Nous exigeons au moins un caractère
        afficherErreur('Le texte alternatif est obligatoire.');
        return;
    }

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur("L'image est trop lourde (13 Mo max).");
        return;
    }

    const buildPayload = (logoBase64, logoMime) => {
        const payload = { lien, ...formData };
        if (estModif && !payload.id) payload.id = (typeof id !== 'undefined') ? id : null;
        if (logoBase64) {
            payload.logo_base64 = logoBase64;
            payload.logo_mime = logoMime;
        }
        return payload;
    };

    const send = (payload) => {
        const btn = document.querySelector('#envoyer');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

        fetch('../cimes_api/index_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.text())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = LABEL_BTN;
            if (res === 'ok') {
                const el = document.getElementById('erreur');
                el.className = 'alerte success';
                if (estModif) {
                    el.innerHTML = `<span>Modifications enregistrées.</span>
                        <div class="alerte-actions"><a href="dynam_logo_partenaire.php" class="alerte-btn alerte-btn--outline"><i class="fa-solid fa-arrow-left"></i> Retour</a></div>`;
                } else {
                    el.innerHTML = `<span>Logo créé avec succès.</span>
                        <div class="alerte-actions">
                            <a href="dynam_logo_partenaire.php" class="alerte-btn alerte-btn--outline"><i class="fa-solid fa-arrow-left"></i> Retour à la liste</a>
                            <a href="ajout_logo_partenaire.php" class="alerte-btn alerte-btn--primary"><i class="fa-solid fa-plus"></i> Ajouter un autre</a>
                        </div>`;
                    // Réinitialiser le formulaire
                    document.getElementById('alt_text').value = '';
                    fileInput.value = '';
                    document.getElementById('image-preview').innerHTML = '<i class="fa-solid fa-image fa-2x"></i>';
                }
            } else {
                afficherErreur('Erreur : ' + res);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = LABEL_BTN;
            afficherErreur('Problème réseau.');
        });
    };

    if (file) {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const MAX = 1200;
            let w = img.width, h = img.height;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else { w = Math.round(w * MAX / h); h = MAX; }
            }
            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            const mime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
            canvas.toBlob(blob => {
                const reader = new FileReader();
                reader.onload = e => {
                    const [header, b64] = e.target.result.split(',');
                    const mimeType = header.match(/:(.*?);/)[1];
                    send(buildPayload(b64, mimeType));
                };
                reader.readAsDataURL(blob);
            }, mime, 0.82);
        };
        img.src = url;
    } else {
        send(buildPayload());
    }
}

function afficherErreur(msg) {
    const el = document.getElementById('erreur');
    el.className = 'alerte error';
    el.textContent = msg;
}
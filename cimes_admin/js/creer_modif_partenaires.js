'use strict';

/* ─────────────────────────────────────────────────────────────
   Fichier : creer_modif_partenaires.js
   Utilisation commune pour ajout_partenaires.php et modif_partenaires.php
   Détecte automatiquement le mode via la variable globale "lien"
───────────────────────────────────────────────────────────── */

const estModif = (lien === 'modif_partenaire');

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer le partenaire';

if (!estModif) {
    document.title = 'Ajouter un partenaire';
}

document.querySelector('#envoyer').addEventListener('click', function(e) {
    e.preventDefault();
    envoi_donnees();
});

/* ════════════════════════════════════════════════════════════
   Fonction principale d'envoi
════════════════════════════════════════════════════════════ */
function envoi_donnees() {

    // Normalisation de l'URL (ajout de https:// si nécessaire)
    function normalizeUrl(url) {
        if (!url) return '';
        url = url.trim();
        if (/^https?:\/\//i.test(url)) return url;
        if (url.startsWith('www.')) return 'https://' + url;
        return 'https://' + url;
    }

    // Récupération des données via getFormData() ou fallback direct
    let formData;
    if (typeof window.getFormData === 'function') {
        formData = window.getFormData();
    } else {
        const rawLien = document.getElementById('lien_site')?.value.trim() || '';
        formData = {
            titre:       document.getElementById('titre')?.value.trim() ?? '',
            role:        document.getElementById('role')?.value.trim() ?? '',
            description: document.getElementById('description')?.value.trim() ?? '',
            lien_site:   normalizeUrl(rawLien),
        };
        if (estModif && typeof id !== 'undefined') {
            formData.id = id;
        }
    }

    // Validation minimale
    if (!formData.titre) {
        afficherErreur('Le titre est obligatoire.');
        return;
    }

    // Vérification du fichier image
    const fileInput = document.getElementById('image');
    const file = fileInput?.files[0];

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (13 Mo max).');
        return;
    }

    // Construction du payload (selon le mode)
    const buildPayload = (imageBase64 = null, imageMime = null) => {
        const payload = { lien, ...formData };
        if (imageBase64) {
            payload.image_base64 = imageBase64;
            payload.image_mime   = imageMime;
        }
        return payload;
    };

    // Réinitialisation du formulaire (uniquement en création)
    const resetFormulaire = () => {
        ['titre', 'role', 'description', 'lien_site'].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
        const imageInput = document.getElementById('image');
        if (imageInput) imageInput.value = '';
        const preview = document.getElementById('preview');
        if (preview) preview.innerHTML = '<i class="fa-solid fa-camera"></i>';
        const currentPhoto = document.getElementById('current-photo');
        if (currentPhoto) currentPhoto.innerHTML = '';
    };

    // Envoi de la requête
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
                        <span>Le partenaire a bien été créé.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                            </a>
                            <a href="ajout_partenaires.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter un autre partenaire
                            </a>
                        </div>`;
                }

            } else if (r === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP uniquement).");
            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (13 Mo max).');
            } else if (r === 'ko_write') {
                afficherErreur("Impossible d'écrire l'image sur le serveur. Vérifiez les permissions.");
            } else if (r === 'ko_base64') {
                afficherErreur("Erreur lors de l'encodage de l'image. Réessayez.");
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

    // Traitement de l'image avec redimensionnement
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

/* ── Affichage des erreurs ── */
function afficherErreur(msg) {
    const el = document.querySelector('#erreur');
    el.className = 'alerte error';
    el.textContent = msg;
}
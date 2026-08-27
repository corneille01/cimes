'use strict';

// Détection du mode (création ou modification)
const estModif = (typeof id !== 'undefined' && id > 0) || (document.getElementById('gouv-id') !== null);
const lien = estModif ? 'modif_gouvernance_entite' : 'cree_gouvernance_entite';

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer';

if (!estModif) {
    document.title = 'Ajouter un membre de la gouvernance';
}

document.querySelector('#envoyer')?.addEventListener('click', function(e) {
    e.preventDefault();
    envoiDonnees();
});

/* =====================================================
   Fonction principale d'envoi
   ===================================================== */
function envoiDonnees() {
    // Récupération des données via getFormData() ou fallback direct
    let formData;
    if (typeof window.getFormData === 'function') {
        formData = window.getFormData();
    } else {
        const fields = Array.from(document.querySelectorAll('#fields-container .form-group input, #fields-container .form-group textarea'))
            .map(el => el.id.replace('field-', ''));
        formData = {};
        fields.forEach(field => {
            const input = document.getElementById(`field-${field}`);
            if (input) formData[field] = input.value.trim();
        });
        formData.type = document.getElementById('type-select')?.value || '';
        if (estModif) {
            formData.id = document.getElementById('gouv-id')?.value || (typeof id !== 'undefined' ? id : null);
        }
    }

    if (!formData.type) {
        afficherErreur('Veuillez sélectionner un type de membre.');
        return;
    }
    if (!formData.nom) {
        afficherErreur('Le champ "nom" est obligatoire.');
        return;
    }
    if (estModif && (!formData.id || formData.id <= 0)) {
        afficherErreur('ID invalide.');
        return;
    }

    const fileInput = document.getElementById('photo');
    const file = fileInput?.files[0];
    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image ne doit pas dépasser 13 Mo.');
        return;
    }

    const buildPayload = (imageBase64 = null, imageMime = null) => {
        const payload = { lien, type: formData.type };
        if (estModif) payload.id = parseInt(formData.id);
        for (const key in formData) {
            if (key !== 'type' && key !== 'id' && key !== 'photo_base64' && key !== 'photo_mime') {
                payload[key] = formData[key];
            }
        }
        if (imageBase64) {
            payload.photo_base64 = imageBase64;
            payload.photo_mime = imageMime;
        }
        return payload;
    };

    const envoyerRequete = (finalPayload) => {
        const btn = document.querySelector('#envoyer');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';

        fetch('../cimes_api/index_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(finalPayload)
        })
        .then(r => r.text())
        .then(response => {
            const res = response.trim();
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (res === 'ok') {
                sessionStorage.setItem('reload_needed_dynam', Date.now());
                if (estModif) {
                    afficherSucces('Modification enregistrée.', 'dynam_gouvernance.php');
                } else {
                    afficherSucces('Entité créée avec succès.', 'dynam_gouvernance.php');
                    reinitialiserFormulaire();
                }
            } else if (res === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP).");
            } else if (res === 'ko_size') {
                afficherErreur("Image trop volumineuse côté serveur (13 Mo max).");
            } else if (res === 'ko_write') {
                afficherErreur("Impossible d'écrire l'image sur le serveur.");
            } else {
                afficherErreur('Erreur : ' + res);
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = originalText;
            afficherErreur('Problème de réseau. Veuillez réessayer.');
        });
    };

    if (file) {
        const img = new Image();
        const objectUrl = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(objectUrl);
            let w = img.width, h = img.height;
            const MAX = 1200;
            if (w > MAX || h > MAX) {
                if (w > h) { h = Math.round(h * MAX / w); w = MAX; }
                else       { w = Math.round(w * MAX / h); h = MAX; }
            }
            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            const outputMime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
            const quality = outputMime === 'image/jpeg' ? 0.85 : undefined;
            canvas.toBlob(blob => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const [header, b64] = e.target.result.split(',');
                    const mime = header.match(/:(.*?);/)[1];
                    envoyerRequete(buildPayload(b64, mime));
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
        envoyerRequete(buildPayload());
    }
}

function afficherErreur(msg) {
    const errDiv = document.getElementById('erreur');
    if (errDiv) {
        errDiv.className = 'alerte error';
        errDiv.innerHTML = `<span>${msg}</span>`;
    } else {
        alert(msg);
    }
}

function afficherSucces(msg, redirectUrl) {
    const errDiv = document.getElementById('erreur');
    if (errDiv) {
        errDiv.className = 'alerte success';
        let boutons = `<div class="alerte-actions">
            <a href="${redirectUrl}" class="alerte-btn alerte-btn--outline">
                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
            </a>`;
        if (!estModif) {
            boutons += `<a href="ajout_gouvernance.php" class="alerte-btn alerte-btn--primary">
                <i class="fa-solid fa-plus"></i> Ajouter un autre
            </a>`;
        }
        boutons += `</div>`;
        errDiv.innerHTML = `<span>${msg}</span>${boutons}`;
    } else {
        alert(msg);
    }
}

function reinitialiserFormulaire() {
    const typeSelect = document.getElementById('type-select');
    const container = document.getElementById('fields-container');
    const fileInput = document.getElementById('photo');
    const preview = document.getElementById('preview');

    // 1. Garder le type actuel (ou le laisser vide s’il n’y en a pas)
    const currentType = typeSelect ? typeSelect.value : '';

    // 2. Régénérer les champs pour ce type (cela recrée le contenu du container)
    if (typeof genererChamps === 'function' && currentType) {
        genererChamps(currentType);
        // 3. Vider toutes les valeurs des inputs/textarea
        const inputs = container.querySelectorAll('input, textarea');
        inputs.forEach(el => el.value = '');
    } else {
        // Si aucun type n’est sélectionné, on vide juste le container
        if (container) container.innerHTML = '';
    }

    // 4. Vider l’input file et l’aperçu photo
    if (fileInput) fileInput.value = '';
    if (preview) preview.innerHTML = '<i class="fa-solid fa-camera"></i>';
}
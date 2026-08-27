/* ─────────────────────────────────────────────
   creer_modif_annuaire_personnes.js
   Gère la création (lien = 'cree_annuaire')
   et la modification (lien = 'modif_annuaire')
   Adapté à la table cimes_annuaire_personnes
   ───────────────────────────────────────────── */
'use strict';

const estModif = (lien === 'modif_annuaire');

/* Libellé du bouton selon le mode */
const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer la fiche';

if (!estModif) {
    document.title = 'Ajouter une personne';
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
        // Fallback basique (ne couvre pas les publications)
        formData = {
            nom: document.getElementById('nom_annuaire')?.value.trim() ?? '',
            prenom: document.getElementById('prenom_annuaire')?.value.trim() ?? '',
            mail: document.getElementById('email_annuaire')?.value.trim() ?? '',
            fonction: document.getElementById('fonction_annuaire')?.value.trim() ?? '',
            discipline: document.getElementById('discipline_annuaire')?.value.trim() ?? '',
            etablissement: document.getElementById('etablis_annuaire')?.value.trim() ?? '',
            universite: document.getElementById('universite_annuaire')?.value.trim() ?? '',
            page_web: document.getElementById('page_web_annuaire')?.value.trim() ?? '',
            id_hal: document.getElementById('id_hal_annuaire')?.value.trim() ?? '',
            terrain_recherche: document.getElementById('terrain_recherche_annuaire')?.value.trim() ?? '',
            mots_cles: document.getElementById('mots_cles_annuaire')?.value.trim() ?? ''
        };
        if (estModif && typeof id !== 'undefined') {
            formData.id = id;
        }
    }

    /* Validation minimale */
    if (!formData.nom || !formData.prenom) {
        afficherErreur('Le nom et le prénom sont obligatoires.');
        return;
    }

    /* Vérification poids photo côté client */
    const fileInput = document.getElementById('photo_annuaire');
    const file = fileInput?.files[0];
    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('La photo est trop lourde (13 Mo max). Veuillez choisir une image plus légère.');
        return;
    }

    /* Construction du payload */
    const buildPayload = (photoBase64 = null, photoMime = null) => {
        const payload = { lien, ...formData };
        if (photoBase64) {
            payload.photo_base64 = photoBase64;
            payload.photo_mime   = photoMime;
        }
        return payload;
    };

    /* Reset formulaire (ajout uniquement) */
    const resetFormulaire = () => {
        [
            'nom_annuaire', 'prenom_annuaire', 'email_annuaire',
            'fonction_annuaire', 'discipline_annuaire', 'etablis_annuaire',
            'page_web_annuaire', 'id_hal_annuaire', 'terrain_recherche_annuaire',
            'universite_annuaire', 'mots_cles_annuaire'
        ].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
        const photoInput = document.getElementById('photo_annuaire');
        if (photoInput) photoInput.value = '';
        const photoPreview = document.getElementById('photo-preview');
        if (photoPreview) photoPreview.innerHTML = '<i class="fa-solid fa-user"></i>';

        // Réinitialiser les publications
        const publiList = document.getElementById('publi-list');
        if (publiList) publiList.innerHTML = '';
        const btnAddPubli = document.getElementById('btn-add-publi');
        if (btnAddPubli) btnAddPubli.style.display = '';
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
                        <span>La fiche a bien été enregistrée.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour à l'annuaire
                            </a>
                            <a href="ajout_annuaire_personnes.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter une autre personne
                            </a>
                        </div>`;
                }

            } else if (r === 'ko_doublon') {
                const nom    = document.getElementById('nom_annuaire').value.trim();
                const prenom = document.getElementById('prenom_annuaire').value.trim();
                afficherErreur(`${prenom} ${nom} existe déjà dans l'annuaire. Veuillez modifier le nom et/ou le prénom.`);

            } else if (r === 'ko_size') {
                afficherErreur('Image trop volumineuse côté serveur (13 Mo max).');

            } else if (r === 'ko_mime') {
                afficherErreur("Format d'image non accepté (JPG, PNG, WebP uniquement).");

            } else if (r === 'ko_write') {
                afficherErreur("Impossible d'écrire la photo sur le serveur. Vérifiez les permissions du dossier.");

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

    /* Traitement photo : resize + base64 si fichier sélectionné */
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
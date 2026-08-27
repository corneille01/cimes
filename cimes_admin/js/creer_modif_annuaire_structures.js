/* ─────────────────────────────────────────────
   creer_modif_annuaire_structures.js
   Gère la création (lien = 'cree_structure')
   et la modification (lien = 'modif_structure')
   ───────────────────────────────────────────── */
'use strict';

const estModif = (typeof lien !== 'undefined' && lien === 'modif_structure');

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer la fiche';

if (!estModif) {
    document.title = 'Ajouter une structure';
}

document.querySelector('#envoyer').addEventListener('click', function (e) {
    e.preventDefault();
    envoi_donnees();
});

/* ══════════════════════════════════════════════
   Fonction principale d'envoi
══════════════════════════════════════════════ */
function envoi_donnees() {

    let formData;
    if (typeof window.getFormData === 'function') {
        formData = window.getFormData();
    } else {
        /* Fallback si getFormData n'est pas défini (page ajout sans inline script)
           zones_intervention absent volontairement — colonne inexistante en base */
        formData = {
            etablissement:    document.getElementById('etablissement_annuaire')?.value.trim()     ?? '',
            responsable:      document.getElementById('responsable_annuaire')?.value.trim()       ?? '',
            discipline:       document.getElementById('discipline_annuaire')?.value.trim()        ?? '',
            domaine_recherche:document.getElementById('domaine_recherche_annuaire')?.value.trim() ?? '',
            tutelles:         document.getElementById('tutelles_annuaire')?.value.trim()          ?? '',
            annee_creation:   document.getElementById('annee_creation_annuaire')?.value.trim()    ?? '',
            adresse:          document.getElementById('adresse_annuaire')?.value.trim()           ?? '',
            site_web:         document.getElementById('site_web_annuaire')?.value.trim()          ?? '',
            presentation:     document.getElementById('presentation_annuaire')?.value.trim()      ?? '',
        };
        if (estModif && typeof id !== 'undefined') {
            formData.id = id;
        }
    }

    /* Validation minimale */
    if (!formData.etablissement) {
        afficherErreur('Le nom de la structure est obligatoire.');
        return;
    }

    /* Vérification poids photo côté client */
    const fileInput = document.getElementById('photo_annuaire');
    const file = fileInput?.files[0];

    if (file && file.size > 13 * 1024 * 1024) {
        afficherErreur('L\'image est trop lourde (13 Mo max). Veuillez choisir une image plus légère.');
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

    /* Reset formulaire (création uniquement) */
    const resetFormulaire = () => {
        [
            'etablissement_annuaire', 'responsable_annuaire', 'discipline_annuaire',
            'domaine_recherche_annuaire', 'tutelles_annuaire', 'annee_creation_annuaire',
            'adresse_annuaire', 'site_web_annuaire', 'presentation_annuaire'
        ].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
        const photoInput = document.getElementById('photo_annuaire');
        if (photoInput) photoInput.value = '';
        const photoPreview = document.getElementById('photo-preview');
        if (photoPreview) photoPreview.innerHTML = '<i class="fa-solid fa-building"></i>';
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
                            <a href="ajout_annuaire_structures.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter une autre structure
                            </a>
                        </div>`;
                }

            } else if (r === 'ko_doublon') {
                const nom = document.getElementById('etablissement_annuaire').value.trim();
                afficherErreur(`Une structure nommée « ${nom} » existe déjà. Veuillez modifier le nom.`);

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
    el.className   = 'alerte error';
    el.textContent = msg;
}

/* ══════════════════════════════════════════════
   CARTES CLIQUABLES — annuaire_structures.php
   Permet de déplier/replier les détails en
   cliquant n'importe où sur la carte,
   en plus du bouton flèche existant.
══════════════════════════════════════════════ */
function initCardsClickables() {
    /* On délègue sur le conteneur pour couvrir les cartes
       chargées dynamiquement après le fetch */
    const grid = document.getElementById('annuaire_structures');
    if (!grid) return;

    grid.addEventListener('click', function (e) {
        /* Ignorer les clics sur liens, boutons natifs et inputs */
        if (e.target.closest('a, button, input')) return;

        const card = e.target.closest('.member-card');
        if (!card) return;

        const extra  = card.querySelector('.card-extra');
        const btn    = card.querySelector('.card-toggle-btn');
        if (!extra || !btn) return; /* carte sans section dépliable */

        const isOpen = btn.getAttribute('aria-expanded') === 'true';

        btn.setAttribute('aria-expanded', !isOpen);
        card.classList.toggle('card-open', !isOpen);
        extra.style.maxHeight = isOpen ? '0' : extra.scrollHeight + 'px';
    });
}

/* Lancement après chargement du DOM
   (le fetch de l'annuaire est dans code_annuaire_structure.js) */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCardsClickables);
} else {
    initCardsClickables();
}
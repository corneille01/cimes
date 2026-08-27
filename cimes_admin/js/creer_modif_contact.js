'use strict';
const estModif = (typeof lien !== 'undefined' && lien === 'modif_contact');

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer le contact';

if (!estModif) document.title = 'Ajouter un contact';

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
            nom: document.getElementById('nom_contact')?.value.trim() ?? '',
            email: document.getElementById('email_contact')?.value.trim() ?? '',
            telephone: document.getElementById('telephone_contact')?.value.trim() ?? ''
        };
        if (estModif && typeof id !== 'undefined') formData.id = id;
    }

    const payload = { lien, ...formData };

    const resetFormulaire = () => {
        ['nom_contact', 'email_contact', 'telephone_contact'].forEach(fieldId => {
            const el = document.getElementById(fieldId);
            if (el) el.value = '';
        });
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
                sessionStorage.setItem('reload_needed_dynam', Date.now());
                if (!estModif) resetFormulaire();

                const errEl = document.querySelector('#erreur');
                errEl.className = 'alerte success';

                if (estModif) {
                    errEl.innerHTML = `
                        <span>Modifications enregistrées.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </a>
                        </div>`;
                } else {
                    errEl.innerHTML = `
                        <span>Contact enregistré.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour aux contacts
                            </a>
                            <a href="ajout_contact.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter un autre contact
                            </a>
                        </div>`;
                }
            } else {
                afficherErreur('Erreur serveur : ' + r);
            }
        })
        .catch(err => {
            console.error(err);
            btn.style.opacity = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;
            afficherErreur('Problème réseau.');
        });
    };

    send(payload);
}

function afficherErreur(msg) {
    const el = document.querySelector('#erreur');
    el.className = 'alerte error';
    el.textContent = msg;
}
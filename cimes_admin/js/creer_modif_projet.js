'use strict';

const estModif = (lien === 'modif_projet');

const LABEL_BTN_DEFAUT = estModif
    ? '<i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications'
    : '<i class="fa-solid fa-floppy-disk"></i> Créer le projet';

if (!estModif) {
    document.title = 'Ajouter un projet';
}

document.querySelector('#envoyer').addEventListener('click', function (e) {
    e.preventDefault();
    envoi_donnees();
});

function envoi_donnees() {
    // On appelle getFormData, qui peut retourner une promesse ou un objet
    let result;
    try {
        result = typeof window.getFormData === 'function' ? window.getFormData() : null;
    } catch (err) {
        afficherErreur('Erreur lors de la récupération des données.');
        console.error(err);
        return;
    }

    // On transforme en promesse dans tous les cas
    const formDataPromise = (result && typeof result.then === 'function')
        ? result
        : Promise.resolve(result);

    formDataPromise.then(formData => {
        // Fallback si aucun getFormData ou retour vide
        if (!formData || typeof formData !== 'object') {
            formData = {
                titre: document.getElementById('titre')?.value.trim() ?? '',
                acronyme: document.getElementById('acronyme')?.value.trim() ?? '',
                financeur: document.getElementById('financeur')?.value.trim() ?? '',
                porteur_principal: document.getElementById('porteur_principal')?.value.trim() ?? '',
                structure_rattachement: document.getElementById('structure_rattachement')?.value.trim() ?? '',
                partenaires: document.getElementById('partenaires')?.value.trim() ?? '',
                disciplines: document.getElementById('disciplines')?.value.trim() ?? '',
                massif: document.getElementById('massif')?.value.trim() ?? '',
                pays: document.getElementById('pays')?.value.trim() ?? '',
                objectif_principal: document.getElementById('objectif_principal')?.value.trim() ?? '',
                date_debut: document.getElementById('date_debut')?.value.trim() ?? '',
                date_fin: document.getElementById('date_fin')?.value.trim() ?? '',
                site_web: document.getElementById('site_web')?.value.trim() ?? '',
                site_web_porteur: document.getElementById('site_web_porteur')?.value.trim() ?? '',
    
                localisations: '[]'
            };
            if (estModif && typeof id !== 'undefined') {
                formData.id = id;
            }
        }

        if (!formData.titre) {
            afficherErreur('Le titre du projet est obligatoire.');
            return;
        }

        const payload = {
            lien,
            ...formData
        };

        if (estModif && !payload.id) {
            afficherErreur('ID du projet manquant pour la modification.');
            return;
        }

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
                const errEl = document.querySelector('#erreur');
                errEl.className = 'alerte success';
                if (estModif) {
                    errEl.innerHTML = `
                        <span>Projet modifié avec succès.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour
                            </a>
                        </div>`;
                } else {
                    if (typeof window.resetForm === 'function') {
                        window.resetForm();
                    }
                    errEl.innerHTML = `
                        <span>Projet créé avec succès.</span>
                        <div class="alerte-actions">
                            <a href="javascript:history.back();" class="alerte-btn alerte-btn--outline">
                                <i class="fa-solid fa-arrow-left"></i> Retour aux projets
                            </a>
                            <a href="./ajout_projets.php" class="alerte-btn alerte-btn--primary">
                                <i class="fa-solid fa-plus"></i> Ajouter un autre projet
                            </a>
                        </div>`;
                }
            } else {
                afficherErreur('Erreur serveur : ' + r);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            btn.style.opacity = '';
            btn.style.pointerEvents = '';
            btn.innerHTML = LABEL_BTN_DEFAUT;
            afficherErreur('Problème de réseau. Veuillez réessayer.');
        });
    }).catch(err => {
        // Si la promesse est rejetée (ex: données pas chargées)
        console.error('getFormData a échoué :', err);
        afficherErreur(typeof err === 'string' ? err : 'Impossible de préparer les données.');
    });
}

function afficherErreur(msg) {
    const el = document.querySelector('#erreur');
    el.className = 'alerte error';
    el.textContent = msg;
}
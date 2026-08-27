'use strict';

const API_URL = '../cimes_api/index_api.php';
let tousProjets = [];
let massifsSet = new Set();
let supprId = null;

// Charge tous les projets (admin voit tout)
function chargerTousProjets() {
    const tbody = document.getElementById('corp_tab');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="7">Chargement...</td></tr>';

    fetch(`${API_URL}?query=projet`)
        .then(r => r.json())
        .then(data => {
            if (!Array.isArray(data)) {
                tbody.innerHTML = '<tr><td colspan="7">Erreur format données</td></tr>';
                return;
            }

            tousProjets = data.map(item => ({
                id: item.id,
                titre: item.titre,
                acronyme: item.acronyme || '',
                massif: item.massif || '',
                porteur_principal: item.porteur_principal || '',
                structure_rattachement: item.structure_rattachement || ''
            }));

            // Récupère les massifs pour le filtre
            massifsSet.clear();
            tousProjets.forEach(p => {
                if (p.massif) {
                    p.massif.split(',').forEach(m => {
                        const nom = m.trim();
                        if (nom) massifsSet.add(nom);
                    });
                }
            });

            remplirSelect('filtre-massif', Array.from(massifsSet).sort());
            filtrerEtAfficher();
        })
        .catch(err => {
            console.error('Erreur chargement projets :', err);
            if (tbody) tbody.innerHTML = `<tr><td colspan="7">Erreur : ${err.message}</td></tr>`;
        });
}

function remplirSelect(idSelect, valeurs) {
    const select = document.getElementById(idSelect);
    if (!select) return;
    const valeurActuelle = select.value;
    select.innerHTML = '<option value="">Tous</option>';
    valeurs.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v;
        opt.textContent = v;
        select.appendChild(opt);
    });
    select.value = valeurActuelle;
}

function filtrerEtAfficher() {
    const massifFiltre = document.getElementById('filtre-massif')?.value || '';
    const projetsFiltres = tousProjets.filter(p => {
        if (massifFiltre) {
            const massifsProjet = (p.massif || '').split(',').map(s => s.trim());
            if (!massifsProjet.includes(massifFiltre)) return false;
        }
        return true;
    });
    afficherTableau(projetsFiltres);
}

function afficherTableau(projets) {
    const tbody = document.getElementById('corp_tab');
    if (!tbody) return;
    if (projets.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7">Aucun projet</td></tr>';
        return;
    }
    let html = '';
    projets.forEach(p => {
        html += `
            <tr>
                <td>${escapeHtml(p.titre)}</td>
                <td>${escapeHtml(p.acronyme)}</td>
                <td>${escapeHtml(p.massif)}</td>
                <td>${escapeHtml(p.porteur_principal)}</td>
                <td>${escapeHtml(p.structure_rattachement)}</td>
                <td><a href="modif_projet.php?id=${p.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                <td><a href="javascript:void(0);" class="supprimer-lien" data-id="${p.id}"><i class="fas fa-trash-alt"></i></a></td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

// Événement filtre
document.getElementById('filtre-massif')?.addEventListener('change', filtrerEtAfficher);

// Gestion suppression
function ouvrirModalSuppression(id) {
    supprId = id;
    document.getElementById('panneausup').style.display = 'flex';
}

document.body.addEventListener('click', (e) => {
    const target = e.target.closest('.supprimer-lien');
    if (target) {
        e.preventDefault();
        const id = target.getAttribute('data-id');
        ouvrirModalSuppression(id);
    }
});

document.getElementById('oui')?.addEventListener('click', () => {
    if (supprId) {
        fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    lien: 'supprimer_projet',
                    id: parseInt(supprId)
                })
            })
            .then(r => r.text())
            .then(res => {
                if (res === 'ok' || res === 'ko_not_found') {
                    chargerTousProjets();
                } else if (res === 'ko_unauthorized') {
                    alert("Vous n'êtes pas autorisé à supprimer ce projet.");
                } else {
                    alert('Erreur lors de la suppression (code : ' + res + ')');
                }
                document.getElementById('panneausup').style.display = 'none';
                supprId = null;
            })
            .catch(err => alert('Erreur réseau'));
    }
});

document.getElementById('non')?.addEventListener('click', () => {
    document.getElementById('panneausup').style.display = 'none';
    supprId = null;
});

document.querySelector('.close')?.addEventListener('click', () => {
    document.getElementById('panneausup').style.display = 'none';
});

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
}

// Lancement initial
chargerTousProjets();
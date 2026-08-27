'use strict';

const API_URL = '../cimes_api/index_api.php';

// Charger les types distincts pour le filtre
function chargerTypes() {
    fetch(`${API_URL}?query=gouvernance_types`)
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('filtre-type');
            if (!select) return;
            select.innerHTML = '<option value="">Tous</option>';
            data.forEach(type => {
                let label = type;
                if (type === 'direction') label = 'Direction';
                else if (type === 'conseil_groupement') label = 'Conseil de groupement';
                else if (type === 'conseil_scientifique') label = 'Conseil scientifique';
                else if (type === 'comite_orientation') label = 'Comité d\'orientation';
                select.innerHTML += `<option value="${type}">${label}</option>`;
            });
        })
        .catch(err => console.error(err));
}

// Charger les entités (avec filtre)
function chargerTable(type = '') {
    const tbody = document.getElementById('corp_tab');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="6">Chargement...</td></tr>';

    let url = `${API_URL}?query=gouvernance`;
    if (type) url += `&type=${encodeURIComponent(type)}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (!Array.isArray(data)) {
                tbody.innerHTML = '<tr><td colspan="6">Erreur format données</td></tr>';
                return;
            }
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Aucune entrée</td></tr>';
                return;
            }
            let html = '';
            data.forEach(item => {
                let typeLabel = item.type;
                if (typeLabel === 'direction') typeLabel = 'Direction';
                else if (typeLabel === 'conseil_groupement') typeLabel = 'CG';
                else if (typeLabel === 'conseil_scientifique') typeLabel = 'CS';
                else if (typeLabel === 'comite_orientation') typeLabel = 'CO';
                html += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${escapeHtml(typeLabel)}</td>
                        <td>${escapeHtml(item.nom)}</td>
                        <td>${escapeHtml(item.role)}</td>
                        <td><a href="modif_gouvernance.php?id=${item.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                        <td><a href="javascript:void(0);" class="supprimer-lien" data-id="${item.id}"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = `<td><td colspan="6">Erreur : ${err.message}</td></tr>`;
        });
}

// Bouton Ajouter (modale de choix du type)
document.getElementById('btn-ajout-principal').addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.zIndex = '10000';
    modal.innerHTML = `
        <div style="background:white; padding:30px; border-radius:20px;">
            <h3>Type de membre</h3>
            <select id="type-select" style="width:100%; padding:10px; margin:15px 0;">
                <option value="direction">Direction</option>
                <option value="conseil_groupement">Conseil de groupement</option>
                <option value="conseil_scientifique">Conseil scientifique</option>
                <option value="comite_orientation">Comité d'orientation</option>
            </select>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button id="modal-annuler">Annuler</button>
                <button id="modal-valider" style="background:#0F6E56; color:white;">Suivant</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    document.getElementById('modal-annuler').addEventListener('click', () => modal.remove());
    document.getElementById('modal-valider').addEventListener('click', () => {
        const type = document.getElementById('type-select').value;
        window.location.href = `ajout_gouvernance.php?type=${type}`;
        modal.remove();
    });
});

// Suppression
let supprId = null;
function ouvrirModalSuppression(id) {
    supprId = id;
    document.getElementById('panneausup').style.display = 'flex';
}
document.body.addEventListener('click', (e) => {
    const target = e.target.closest('.supprimer-lien');
    if (target) {
        e.preventDefault();
        supprId = target.getAttribute('data-id');
        ouvrirModalSuppression(supprId);
    }
});
document.getElementById('oui')?.addEventListener('click', () => {
    if (supprId) {
        fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lien: 'supprimer_gouvernance_entite', id: parseInt(supprId) })
        })
        .then(r => r.text())
        .then(res => {
            if (res === 'ok') {
                const filtre = document.getElementById('filtre-type')?.value || '';
                chargerTable(filtre);
            } else alert('Erreur suppression');
            document.getElementById('panneausup').style.display = 'none';
            supprId = null;
        });
    }
});
document.getElementById('non')?.addEventListener('click', () => {
    document.getElementById('panneausup').style.display = 'none';
});
document.querySelector('.close')?.addEventListener('click', () => {
    document.getElementById('panneausup').style.display = 'none';
});

// Filtre
const filtreSelect = document.getElementById('filtre-type');
if (filtreSelect) {
    filtreSelect.addEventListener('change', () => chargerTable(filtreSelect.value));
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
}

// Initialisation
chargerTypes();
chargerTable();
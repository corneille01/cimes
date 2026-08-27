'use strict';

const API_URL = '../cimes_api/index_api.php';

// Charger les catégories pour le filtre
function chargerCategories() {
    console.log('Chargement des catégories...');
    fetch(`${API_URL}?query=partenaires_categories`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Catégories reçues :', data);
            const select = document.getElementById('filtre-categorie');
            if (!select) {
                console.error("Select #filtre-categorie non trouvé");
                return;
            }
            select.innerHTML = '<option value="">Toutes les catégories</option>';
            if (Array.isArray(data)) {
                data.forEach(cat => {
                    select.innerHTML += `<option value="${cat}">${cat}</option>`;
                });
            }
        })
        .catch(err => console.error('Erreur chargement catégories :', err));
}

// Charger les partenaires (avec ou sans filtre)
function chargerTable(categorie = '') {
    const tbody = document.getElementById('corp_tab');
    if (!tbody) {
        console.error("tbody #corp_tab non trouvé");
        return;
    }
    tbody.innerHTML = '<tr><td colspan="6">Chargement...</td></table>';

    let url = `${API_URL}?query=partenaires`;
    if (categorie) url += `&categorie=${encodeURIComponent(categorie)}`;
    console.log('Chargement partenaires :', url);

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Partenaires reçus :', data);
            if (!Array.isArray(data)) {
                tbody.innerHTML = '<tr><td colspan="6">Erreur format données</td></tr>';
                return;
            }
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Aucun partenaire</td></tr>';
                return;
            }
            let html = '';
            data.forEach(item => {
                html += `
                    <tr>
                       
                        <td>${escapeHtml(item.titre)}</td>
                        <td>${escapeHtml(item.role)}</td>
                        <td>${escapeHtml(item.categorie)}</td>
                        <td><a href="modif_partenaires.php?id=${item.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                        <td><a href="javascript:void(0);" class="supprimer-lien" data-id="${item.id}"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        })
        .catch(err => {
            console.error('Erreur chargement partenaires :', err);
            tbody.innerHTML = `<tr><td colspan="6">Erreur : ${err.message}</td></tr>`;
        });
}

// Bouton Ajouter
const btnAjout = document.getElementById('btn-ajout-principal');
if (btnAjout) {
    btnAjout.addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = 'ajout_partenaires.php';
    });
}

// Filtre
const filtreSelect = document.getElementById('filtre-categorie');
if (filtreSelect) {
    filtreSelect.addEventListener('change', () => {
        chargerTable(filtreSelect.value);
    });
}

// Gestion suppression
let supprId = null;
function ouvrirModalSuppression(id) {
    supprId = id;
    const modal = document.getElementById('panneausup');
    if (modal) modal.style.display = 'flex';
}

document.body.addEventListener('click', (e) => {
    const target = e.target.closest('.supprimer-lien');
    if (target) {
        e.preventDefault();
        const id = target.getAttribute('data-id');
        ouvrirModalSuppression(id);
    }
});

const ouiBtn = document.getElementById('oui');
if (ouiBtn) {
    ouiBtn.addEventListener('click', () => {
        if (supprId) {
            fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ lien: 'supprimer_partenaire', id: parseInt(supprId) })
            })
            .then(r => r.text())
            .then(res => {
                if (res === 'ok') {
                    chargerTable(filtreSelect ? filtreSelect.value : '');
                } else {
                    alert('Erreur suppression');
                }
                document.getElementById('panneausup').style.display = 'none';
                supprId = null;
            })
            .catch(err => alert('Erreur réseau'));
        }
    });
}

const nonBtn = document.getElementById('non');
if (nonBtn) {
    nonBtn.addEventListener('click', () => {
        document.getElementById('panneausup').style.display = 'none';
        supprId = null;
    });
}

const closeBtn = document.querySelector('.close');
if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        document.getElementById('panneausup').style.display = 'none';
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
}

// Initialisation
chargerCategories();
chargerTable();
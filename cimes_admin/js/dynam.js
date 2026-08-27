const id = document.getElementById('main-id').value;
let contenu_tableau = '';

function cree_tableau() {
    fetch(`../cimes_api/api_dynam.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.title = data.name;
            if (data.error) {
                console.error('Erreur:', data.error);
                document.querySelector("#corp_tab").innerHTML = `<tr><td colspan="3">${data.error}</td></tr>`;
                return;
            }

            const contentData = data.content_data.reverse(); // On inverse pour afficher du plus récent au plus ancien
            console.log("retour api dynam", data);
            document.querySelector("#titre").innerHTML = `Ajouter un nouvel élément dans la page "${data.name}"`;

            contenu_tableau = ''; // ← on vide avant de reconstruire
            if (Array.isArray(contentData)) {
                contentData.forEach(ligne => {
                    contenu_tableau += 
                    `<tr>
                        <td>${ligne[data.display_field] ?? 'Sans titre'}</td>
                        <td><a href="modif_${data.base_slug}.php?id=${ligne.id}" class="editer-lien"><i class="fas fa-pen"></i></a></td>
                        <td><a href="javascript:void(0);" class="supprimer-lien" data-id="${ligne.id}" data-table="${data.database_name}"><i class="fas fa-trash-can"></i></a></td>
                    </tr>`;
                });
                document.querySelector("#corp_tab").innerHTML = contenu_tableau;
            } else {
                document.querySelector("#corp_tab").innerHTML = `<tr><td colspan="3">Aucune donnée trouvée.</td></tr>`;
            }

            // Redirection du bouton "Ajouter"
            const btnAjout = document.getElementById('ajout');
            if (btnAjout) {
                // Supprimer les anciens événements en clonant
                const newBtn = btnAjout.cloneNode(true);
                btnAjout.parentNode.replaceChild(newBtn, btnAjout);
                newBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    window.location.href = `ajout_${data.base_slug}.php?id=${id}`;
                });
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.querySelector("#corp_tab").innerHTML = `<tr><td colspan="3">Erreur lors de la récupération des données.</td></tr>`;
        });
}

// ═══════════════════════════════════════
// RECHARGEMENT INTELLIGENT
// ═══════════════════════════════════════

function checkReload() {
    const reloadNeeded = sessionStorage.getItem('reload_needed_dynam');
    if (reloadNeeded) {
        sessionStorage.removeItem('reload_needed_dynam');
        cree_tableau();
    }
}

// Chargement initial
cree_tableau();

// Vérifier immédiatement si un rechargement est demandé
checkReload();

// Vérifier quand la page s'affiche (bouton retour, etc.)
window.addEventListener('pageshow', function() {
    checkReload();
});

// Vérifier quand l'onglet redevient visible
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        checkReload();
    }
});

// Secours : focus sur la fenêtre
window.addEventListener('focus', function() {
    checkReload();
});

// ═══════════════════════════════════════
// GESTION DE LA SUPPRESSION
// ═══════════════════════════════════════

let quelid = "";
let quelQuery = "";

function ouvrepanneau(id, table) {
    const modal = document.querySelector("#panneausup");
    modal.style.display = "block";
    quelid = id;
    quelQuery = `supp_${table}`;
}

// Délégation pour les liens "supprimer"
document.getElementById('corp_tab').addEventListener('click', function(e) {
    const target = e.target.closest('.supprimer-lien');
    if (target) {
        e.preventDefault();
        const supprId = target.getAttribute('data-id');
        const supprTable = target.getAttribute('data-table');
        ouvrepanneau(supprId, supprTable);
    }
});

document.querySelector("#oui").addEventListener('click', function() {
    fetch(`../cimes_api/index_api.php?query=${quelQuery}&id=${quelid}`)
    .then(reponse => reponse.text())
    .then(data => {
        if (data.length == 2) {
            document.querySelector("#corp_tab").innerHTML = '';
            contenu_tableau = '';
            cree_tableau();
        }
    })
    .catch(err => console.error('Erreur suppression :', err));
    document.querySelector("#panneausup").style.display = "none";
});

document.querySelector("#non").addEventListener('click', function() {
    document.querySelector("#panneausup").style.display = "none";
});

document.querySelector(".close").addEventListener('click', function() {
    document.querySelector("#panneausup").style.display = "none";
});

window.addEventListener('click', function(event) {
    const modal = document.querySelector("#panneausup");
    if (event.target === modal) {
        modal.style.display = "none";
    }
});
let contenu_tableau = '';
const parentMap = {};

function cree_tableau() {
    fetch('../cimes_api/index_api_head.php?query=navbar_admin')
    .then(response => response.json())
    .then(data => {
        document.title  = 'Administration';
        contenu_tableau = '';
        let parentOptions = '';

        Object.keys(parentMap).forEach(k => delete parentMap[k]);

        data.forEach(ligne => {
            const hasSubItems = ligne.sub_items && ligne.sub_items.length > 0;

            parentMap[ligne.id] = {
                database_name: ligne.database_name ?? '',
                url_admin:     ligne.url_admin     ?? 'dynam.php',
            };

            let lien = '';
            if (!hasSubItems && ligne.url_admin && ligne.url_admin !== '#') {
                if (ligne.url_admin === 'dynam.php') {
                    lien = `<a href="#" onclick="redirectToPage(${ligne.id}, 'dynam.php')">
                                <i class="fa-solid fa-file-lines"></i>
                            </a>`;
                } else {
                    lien = `<a href="${ligne.url_admin}" title="Accéder à l'administration">
                                <i class="fa-solid fa-file-lines"></i>
                            </a>`;
                }
            }

            const isVisible = ligne.visible !== 0;
            const eyeIcon   = isVisible ? 'fa-eye' : 'fa-eye-slash';
            const eyeTitle  = isVisible ? 'Masquer' : 'Afficher';

            const accordionBtn = hasSubItems
                ? `<button class="accordion-toggle" data-parent-id="${ligne.id}" title="Déplier / Replier">
                       <i class="fa-solid fa-chevron-down"></i>
                   </button>`
                : '';

            contenu_tableau += `
            <tr class="menu" data-id="${ligne.id}" data-has-sub="${hasSubItems ? 1 : 0}" style="${isVisible ? '' : 'opacity:0.5'}">
                <th scope="row">
                    <span class="drag-handle" title="Déplacer"><i class="fa-solid fa-grip-vertical"></i></span>
                    ${accordionBtn}
                    ${ligne.name}
                </th>
                <td><a href="#" onclick="openEditModal(${ligne.id}, '${escHtml(ligne.name)}')"><i class="fas fa-pen"></i></a></td>
                <td></td>
                <td><a href="#" title="${eyeTitle}" onclick="toggleVisibility(${ligne.id}); return false;"><i class="fas ${eyeIcon}"></i></a></td>
                <td>${lien}</td>
            </tr>`;

            parentOptions += `<option value="${ligne.id}">${ligne.name}</option>`;

            if (hasSubItems) {
                ligne.sub_items.forEach(sub_item => {
                    const subMenuUrl  = sub_item.url_admin ?? 'dynam.php';
                    const subVisible  = sub_item.visible !== 0;
                    const subEyeIcon  = subVisible ? 'fa-eye' : 'fa-eye-slash';
                    const subEyeTitle = subVisible ? 'Masquer' : 'Afficher';

                    contenu_tableau += `
                    <tr class="sous-menu" data-id="${sub_item.id}" data-parent-id="${ligne.id}" style="${subVisible ? '' : 'opacity:0.5'}">
                        <th scope="row" style="padding-left:20px; font-weight:normal;">
                            <span class="drag-handle" title="Déplacer"><i class="fa-solid fa-grip-vertical"></i></span>
                            ${sub_item.name}
                        </th>
                        <td><a href="#" onclick="openEditModal(${sub_item.id}, '${escHtml(sub_item.name)}')"><i class="fas fa-pen"></i></a></td>
                        <td><a href="#" onclick="confirmDelete(${sub_item.id})"><i class="fas fa-window-close"></i></a></td>
                        <td><a href="#" title="${subEyeTitle}" onclick="toggleVisibility(${sub_item.id}); return false;"><i class="fas ${subEyeIcon}"></i></a></td>
                        <td><a href="#" onclick="redirectToPage(${sub_item.id}, '${subMenuUrl}')"><i class="fa-solid fa-file-lines"></i></a></td>
                    </tr>`;
                });
            }
        });

        document.querySelector('#corp_tab').innerHTML = contenu_tableau;
        document.getElementById('parentSelect').innerHTML = parentOptions;

        initAccordion();
        initDragDrop();
    })
    .catch(err => console.error('Erreur cree_tableau :', err));
}

function escHtml(str) {
    return String(str ?? '').replace(/'/g, "\\'");
}

function redirectToPage(id, url) {
    window.location.href = (url === 'dynam.php') ? `dynam.php?id=${id}` : `${url}?id=${id}`;
}


// ── ACCORDÉON ───────────────────────────────────────────────────────────────

function initAccordion() {
    document.querySelectorAll('tr.sous-menu').forEach(row => {
        row.classList.add('sub-hidden');
    });

    document.querySelectorAll('.accordion-toggle').forEach(btn => {
        const parentId = btn.dataset.parentId;
        const icon     = btn.querySelector('i');
        let open       = false;

        btn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            open = !open;
            icon.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
            document.querySelectorAll(`tr.sous-menu[data-parent-id="${parentId}"]`).forEach(row => {
                row.classList.toggle('sub-hidden', !open);
            });
        });
    });
}


// ── DRAG & DROP (mousedown/mousemove/mouseup) ────────────────────────────────

function initDragDrop() {
    const tbody = document.querySelector('#corp_tab');
    if (!tbody) return;

    let dragSrc   = null;
    let group     = [];
    let ghost     = null;
    let indicator = null;

    // Crée le fantôme visuel flottant
    function createGhost(rows, clientY) {
        const tbl = document.createElement('table');
        tbl.className = 'drag-ghost-table';
        const tableWidth = tbody.closest('table').offsetWidth;
        const tableLeft  = tbody.closest('table').getBoundingClientRect().left;
        tbl.style.cssText = `
            position:fixed; pointer-events:none; z-index:9999;
            border-collapse:collapse; opacity:0.85;
            background:#fff; border-radius:8px;
            box-shadow:0 8px 24px rgba(0,0,0,0.18);
            width:${tableWidth}px; left:${tableLeft}px; top:${clientY}px;
        `;
        rows.forEach(r => {
            const clone = r.cloneNode(true);
            clone.style.cssText = 'opacity:1; background:#fff;';
            tbl.appendChild(clone);
        });
        document.body.appendChild(tbl);
        return tbl;
    }

    // Crée la ligne indicatrice de position
    function createIndicator() {
        const tr = document.createElement('tr');
        tr.className = 'drop-indicator';
        const td = document.createElement('td');
        td.colSpan = 5;
        tr.appendChild(td);
        return tr;
    }

    // Retourne le groupe d'un parent (lui + ses sous-menus présents dans le DOM)
    function getGroup(row) {
        const rows = [row];
        if (row.classList.contains('menu')) {
            tbody.querySelectorAll(`tr.sous-menu[data-parent-id="${row.dataset.id}"]`)
                 .forEach(s => rows.push(s));
        }
        return rows;
    }

    // Retourne la meilleure ligne cible + la position (avant/après) selon clientY
    function getTargetInfo(clientY) {
        const candidates = [...tbody.querySelectorAll('tr:not(.drop-indicator)')]
            .filter(r => !group.includes(r));

        let best     = null;
        let bestDist = Infinity;

        for (const r of candidates) {
            const rect = r.getBoundingClientRect();
            const mid  = rect.top + rect.height / 2;
            const dist = Math.abs(clientY - mid);
            if (dist < bestDist) {
                bestDist = dist;
                best = { row: r, above: clientY < mid };
            }
        }
        return best;
    }

    // Vérifie la compatibilité source → cible
    function canDrop(targetRow) {
        if (!targetRow) return false;
        if (dragSrc.classList.contains('menu')) {
            return targetRow.classList.contains('menu');
        }
        if (dragSrc.classList.contains('sous-menu')) {
            return targetRow.classList.contains('sous-menu') &&
                   dragSrc.dataset.parentId === targetRow.dataset.parentId;
        }
        return false;
    }

    // ── mousedown sur les poignées ──────────────────────────────────────────
    tbody.addEventListener('mousedown', e => {
        const handle = e.target.closest('.drag-handle');
        if (!handle) return;
        e.preventDefault();

        dragSrc   = handle.closest('tr');
        group     = getGroup(dragSrc);
        indicator = createIndicator();

        // Masque visuellement les lignes source
        group.forEach(r => r.classList.add('dragging'));

        // Crée le fantôme
        ghost = createGhost(group, e.clientY);
    });

    // ── mousemove global ────────────────────────────────────────────────────
    document.addEventListener('mousemove', e => {
        if (!dragSrc) return;
        e.preventDefault();

        // Suit la souris
        ghost.style.top = e.clientY + 'px';

        // Trouve la cible
        const info = getTargetInfo(e.clientY);

        if (info && canDrop(info.row)) {
            ghost.style.opacity = '0.85';
            // Place l'indicateur avant ou après la cible
            if (info.above) {
                info.row.before(indicator);
            } else {
                // Pour un parent, place après son dernier sous-menu
                const lastOfGroup = getGroup(info.row).slice(-1)[0];
                lastOfGroup.after(indicator);
            }
        } else {
            ghost.style.opacity = '0.35';
            if (indicator.parentNode) indicator.parentNode.removeChild(indicator);
        }
    });

    // ── mouseup global ──────────────────────────────────────────────────────
    document.addEventListener('mouseup', e => {
        if (!dragSrc) return;

        const info = getTargetInfo(e.clientY);

        if (info && canDrop(info.row) && indicator.parentNode) {
            // Insère chaque ligne du groupe à la place de l'indicateur (dans l'ordre)
            group.forEach(r => indicator.before(r));
        }

        // Nettoyage
        group.forEach(r => r.classList.remove('dragging'));
        if (indicator.parentNode) indicator.parentNode.removeChild(indicator);
        if (ghost && ghost.parentNode)  ghost.parentNode.removeChild(ghost);

        const wasDragging = dragSrc !== null;
        dragSrc   = null;
        group     = [];
        ghost     = null;
        indicator = null;

        if (wasDragging) saveNewOrder();
    });
}


// ── Sauvegarde de l'ordre ────────────────────────────────────────────────────

function saveNewOrder() {
    const rows  = [...document.querySelectorAll('#corp_tab tr:not(.drop-indicator)')];
    const order = rows.map((r, i) => ({ id: parseInt(r.dataset.id), position: i }));

    fetch('../cimes_api/index_api_head.php?query=reorder_navbar', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ order })
    })
    .then(r => { if (!r.ok) throw new Error('Réseau KO'); return r.json(); })
    .then(data => {
        console.log('Ordre sauvegardé :', data);
        reloadHeaderNav(); 
    })
    .catch(err => console.error('Erreur reorder :', err));
}


// ── TOGGLE VISIBILITÉ ────────────────────────────────────────────────────────

function toggleVisibility(id) {
    fetch('../cimes_api/index_api_head.php?query=toggle_visibility', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id })
    })
    .then(r => { if (!r.ok) throw new Error('Réseau KO'); return r.json(); })
    .then(data => {
        if (!data.success) return;
        const nowVisible = data.visible === 1;
        _applyEyeState(id, nowVisible);
        if (data.affected_children > 0) {
            document.querySelectorAll(`tr.sous-menu[data-parent-id="${id}"]`).forEach(row => {
                _applyEyeState(parseInt(row.dataset.id), nowVisible);
            });
        }
        reloadHeaderNav();
    })
    .catch(err => console.error('Erreur toggle_visibility :', err));
}

function _applyEyeState(id, visible) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    if (!row) return;
    row.style.opacity = visible ? '1' : '0.5';
    const icon = row.querySelector('.fa-eye, .fa-eye-slash');
    if (icon) {
        icon.classList.toggle('fa-eye',       visible);
        icon.classList.toggle('fa-eye-slash', !visible);
    }
    const link = icon ? icon.closest('a') : null;
    if (link) link.title = visible ? 'Masquer' : 'Afficher';
}

function reloadHeaderNav() {
    Promise.all([
        fetch('../cimes_api/index_api_head.php?query=navbar').then(r => r.json()),
        fetch('../cimes_clients/user_info.php').then(r => r.json()).catch(() => ({})),
        fetch('../cimes_clients/admin_info.php').then(r => r.json()).catch(() => ({}))
    ])
    .then(([data, userInfo, adminInfo]) => {
        if (adminInfo.firstname && adminInfo.lastname) {
            data.push({ name: adminInfo.lastname, url: './navbar.php', icon: 'fa-solid fa-user' });
        } else if (userInfo.firstname && userInfo.lastname) {
            data.push({ name: userInfo.lastname, url: '../cimes_clients/dashboard.php', icon: 'fa-solid fa-user' });
        } else {
            data.push({ name: 'Se connecter', url: '../cimes_clients/espace_personnel.php', icon: 'fa-solid fa-right-to-bracket', class: 'login-link' });
        }
        generateNav(data);
    })
    .catch(err => console.error('Erreur reloadHeaderNav :', err));
}


// ── AJOUT SOUS-MENU ──────────────────────────────────────────────────────────

// function openAddSubMenuModal(preSelectedParentId = null) {
//     if (preSelectedParentId !== null) {
//         const select = document.getElementById('parentSelect');
//         const option = select.querySelector(`option[value="${preSelectedParentId}"]`);
//         if (option) select.value = preSelectedParentId;
//     }
//     document.getElementById('addSubMenuModal').style.display = 'block';
// }

// const addButton = document.getElementById('addButton');

// if (addButton) {
//     addButton.onclick = function () {
//         openAddSubMenuModal();
//     };
// }

// document.getElementById('addSubMenuForm').onsubmit = function (event) {
//     event.preventDefault();
//     const parent_id     = document.getElementById('parentSelect').value;
//     const name          = document.getElementById('subMenuName').value.trim();
//     if (!name) return;

//     const parentInfo    = parentMap[parent_id] ?? {};
//     const database_name = parentInfo.database_name ?? '';
//     const url           = parentInfo.url_admin ?? 'dynam.php';

//     fetch('../cimes_api/index_api_head.php?query=add_submenu', {
//         method:  'POST',
//         headers: { 'Content-Type': 'application/json' },
//         body:    JSON.stringify({ parent_id, name, database_name, url })
//     })
//     .then(r => { if (!r.ok) throw new Error('Réseau KO'); return r.json(); })
//     .then(data => {
//         console.log('Sous-menu ajouté :', data);
//         document.getElementById('addSubMenuModal').style.display = 'none';
//         document.getElementById('subMenuName').value = '';
//         cree_tableau();
//     })
//     .catch(err => {
//         console.error('Erreur ajout sous-menu :', err);
//         document.getElementById('addSubMenuModal').style.display = 'none';
//     });
// };


// ── MODIFICATION ─────────────────────────────────────────────────────────────

function openEditModal(id, name) {
    document.getElementById('editId').value   = id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').style.display = 'block';
}

document.getElementById('editForm').onsubmit = function (event) {
    event.preventDefault();
    const id   = document.getElementById('editId').value;
    const name = document.getElementById('editName').value.trim();

    fetch('../cimes_api/index_api_head.php?query=update_navbar', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id, name })
    })
    .then(r => { if (!r.ok) throw new Error('Réseau KO'); return r.json(); })
    .then(data => {
        console.log('Modifié :', data);
        document.getElementById('editModal').style.display = 'none';
        cree_tableau();
    })
    .catch(err => {
        console.error('Erreur modification :', err);
        document.getElementById('editModal').style.display = 'none';
    });
};


// ── SUPPRESSION ──────────────────────────────────────────────────────────────

let itemToDelete = null;

function confirmDelete(id) {
    itemToDelete = id;
    document.getElementById('deleteModal').style.display = 'block';
}

document.getElementById('confirmDelete').onclick = function () {
    fetch('../cimes_api/index_api_head.php?query=delete_navbar', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ id: itemToDelete })
    })
    .then(r => { if (!r.ok) throw new Error('Réseau KO'); return r.json(); })
    .then(data => {
        console.log('Supprimé :', data);
        document.getElementById('deleteModal').style.display = 'none';
        itemToDelete = null;
        cree_tableau();
    })
    .catch(err => {
        console.error('Erreur suppression :', err);
        document.getElementById('deleteModal').style.display = 'none';
        itemToDelete = null;
    });
};

document.getElementById('cancelDelete').onclick = function () {
    document.getElementById('deleteModal').style.display = 'none';
    itemToDelete = null;
};

document.getElementById('deleteClose').onclick = function () {
    document.getElementById('deleteModal').style.display = 'none';
    itemToDelete = null;
};


// ── FERMETURE MODALS ─────────────────────────────────────────────────────────

window.onclick = function (event) {
    ['addSubMenuModal', 'editModal', 'deleteModal', 'addContentModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal && event.target === modal) {
            modal.style.display = 'none';
            if (modalId === 'deleteModal') itemToDelete = null;
        }
    });
};

document.querySelectorAll('.navadmin-close').forEach(btn => {
    btn.onclick = function () {
        const modal = btn.closest('.navadmin-modal');
        if (modal) {
            modal.style.display = 'none';
            if (modal.id === 'deleteModal') itemToDelete = null;
        }
    };
});


// ── INIT ─────────────────────────────────────────────────────────────────────

cree_tableau();
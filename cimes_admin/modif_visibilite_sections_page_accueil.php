<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: ./cimes_clients/espace_personnel.php');
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mise en page — Accueil</title>
    <style>
        :root {
            --header-height: 70px;
            --green-dark: #0F6E56;
            --green: #1D9E75;
            --green-light: #eef5f2;
            --green-ring: rgba(29, 158, 117, .15);
            --surface: #ffffff;
            --surface-alt: #f7f9f8;
            --surface-hover: #f0f5f3;
            --border: rgba(0, 0, 0, .10);
            --border-md: rgba(0, 0, 0, .18);
            --text: #1e2420;
            --text-muted: #5f6b65;
            --text-hint: #9aada6;
            --red: #A32D2D;
            --red-light: #fcebeb;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, .15), 0 4px 16px rgba(0, 0, 0, .08);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        .table-title {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        body {
            background: #f2f5f3;
            color: var(--text);

            margin: 0;
            padding: 0;
            padding-top: var(--header-height);
        }

        .admin-wrapper {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 32px
        }

        /* ── Toolbar ── */
        .admin-toolbar {
            display: flex;
            justify-content: center;
            margin-bottom: 24px
        }

        #addButton {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--green-dark);
            color: #fff;
            padding: 12px 26px;
            border: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: .95rem;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(15, 110, 86, .25);
            transition: background .2s, transform .1s, box-shadow .2s;
        }

        #addButton::before {
            content: "+";
            font-size: 1.3rem;
            font-weight: 400;
            line-height: 1
        }

        #addButton:hover {
            background: var(--green);
            box-shadow: 0 6px 14px rgba(15, 110, 86, .35);
            transform: translateY(-1px)
        }

        /* ── Tableau ── */
        .sections-container {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow-x: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: .88rem
        }

        .table thead {
            background: var(--surface-alt);
            border-bottom: 1px solid var(--border-md)
        }

        .table thead th {
            padding: 14px 20px;
            text-align: center;
            font-weight: 600;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--text-hint);
            white-space: nowrap;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s
        }

        .table tbody tr:last-child {
            border-bottom: none
        }

        .table tbody tr:hover {
            background: var(--surface-hover)
        }

        .table tbody td,
        .table tbody th {
            padding: 16px 20px;
            vertical-align: middle;
            text-align: center
        }

        .table tbody th {
            text-align: left;
            white-space: nowrap
        }

        .table a,
        .table button.icon-btn {
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: var(--radius-sm);
            transition: all .15s;
            border: 1px solid transparent;
            background: none;
            cursor: pointer;
        }

        .table a:hover,
        .table button.icon-btn:hover {
            color: var(--green-dark);
            background: var(--green-light);
            border-color: var(--border-md)
        }

        .table a i,
        .table button.icon-btn i {
            font-size: .95rem
        }

        /* Poignée drag */
        .drag-handle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            margin-right: 6px;
            cursor: grab;
            color: var(--text-hint);
            border-radius: 4px;
            transition: color .15s, background .15s;
            vertical-align: middle;
        }

        .drag-handle:hover {
            color: var(--green-dark);
            background: var(--green-light)
        }

        .drag-handle:active {
            cursor: grabbing
        }

        .drag-handle i {
            font-size: .75rem;
            pointer-events: none
        }

        tr.dragging {
            opacity: .2
        }

        tr.drop-indicator td {
            height: 3px;
            padding: 0;
            background: var(--green);
            border-radius: 2px;
            box-shadow: 0 0 0 2px var(--green-light)
        }

        .drag-ghost-table {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            border-collapse: collapse;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
            background: #fff;
        }

        /* Badge visible/masqué */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .04em;
        }

        .badge-visible {
            background: var(--green-light);
            color: var(--green-dark)
        }

        .badge-hidden {
            background: #f3f3f3;
            color: #999
        }

        /* ── Modales ── */
        .sec-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 25, 20, .50);
            backdrop-filter: blur(3px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .sec-modal[style*="display: block"] {
            display: flex !important
        }

        .sec-modal-content {
            position: relative;
            background: var(--surface);
            padding: 36px 32px 32px;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 460px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-modal);
            border: .5px solid var(--border);
            animation: modal-in .18s ease;
        }

        @keyframes modal-in {
            from {
                opacity: 0;
                transform: translateY(8px) scale(.98)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .sec-close {
            position: absolute;
            top: 14px;
            right: 16px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--text-hint);
            cursor: pointer;
            background: transparent;
            border: none;
            transition: background .15s, color .15s;
        }

        .sec-close:hover {
            background: var(--red-light);
            color: var(--red)
        }

        .sec-modal-content h2 {
            margin: 0 0 24px;
            color: var(--green-dark);
            font-size: 1.2rem;
            font-weight: 600
        }

        .sec-modal-content form {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .sec-modal-content label {
            font-weight: 500;
            font-size: .75rem;
            color: var(--text-hint);
            text-transform: uppercase;
            letter-spacing: .06em
        }

        .sec-modal-content input {
            padding: 10px 12px;
            border: .5px solid var(--border-md);
            border-radius: var(--radius-sm);
            font-size: .9rem;
            width: 100%;
            background: var(--surface-alt);
            color: var(--text);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            font-family: inherit;
        }

        .sec-modal-content input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px var(--green-ring);
            background: var(--surface)
        }

        .sec-modal-content button[type="submit"] {
            margin-top: 4px;
            background: var(--green-dark);
            color: #fff;
            padding: 11px 20px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .9rem;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .sec-modal-content button[type="submit"]:hover {
            background: var(--green)
        }

        /* Modale suppression */
        .delete-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--red-light);
            color: var(--red);
            font-size: 1.4rem;
            margin: 0 auto 16px;
        }

        .texte_supp {
            font-size: .95rem;
            color: var(--text);
            text-align: center;
            line-height: 1.6;
            margin-bottom: 24px
        }

        .texte_supp strong {
            display: block;
            font-size: 1.05rem;
            margin-bottom: 4px
        }

        .btn_supp {
            display: flex;
            justify-content: center;
            gap: 10px
        }

        .btn_oui_non {
            padding: 10px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .88rem;
            cursor: pointer;
            transition: background .18s
        }

        #confirmDelete {
            background: var(--red);
            color: #fff
        }

        #confirmDelete:hover {
            background: #791F1F
        }

        #cancelDelete {
            background: var(--surface-alt);
            color: var(--text-muted);
            border: .5px solid var(--border-md)
        }

        #cancelDelete:hover {
            background: var(--surface-hover)
        }

        /* Hint section_key */
        .hint {
            font-size: .75rem;
            color: var(--text-hint);
            margin-top: 3px
        }

        @media(max-width:768px) {
            body {
                padding-top: 60px
            }

            .admin-wrapper {
                padding: 20px 16px 30px
            }
        }

        .table-action {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .table-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            background: var(--green-dark);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(15, 110, 86, 0.25);
        }

        .table-btn:hover {
            background: var(--green);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(15, 110, 86, 0.35);
        }

        .table-btn i {
            margin-right: 8px;
        }

        .table-btn,
        .table-btn:visited {
            color: #fff;
            text-decoration: none;
        }

        .table-btn:hover,
        .table-btn:focus,
        .table-btn:active {
            color: #fff;
            text-decoration: none;
            background: var(--green);
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <h1 class="table-title">Tableau des sections</h1>

        <div class="table-action">
            <a href="../cimes_clients/dashboard.php" class="table-btn">
                <i class="fas fa-arrow-left"></i>
                revenir sur le tableau de bord
            </a>
        </div>

        <section class="sections-container">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Section</th>

                        <th scope="col">État</th>
                        <!-- <th scope="col">Modifier</th> -->
                        <!-- <th scope="col">Supprimer</th> -->
                        <th scope="col">Visibilité</th>
                    </tr>
                </thead>
                <tbody id="corp_tab"></tbody>
            </table>
        </section>

    </div>

    <!-- MODALE : Ajouter -->
    <div id="addModal" class="sec-modal">
        <div class="sec-modal-content">
            <button class="sec-close" aria-label="Fermer">&times;</button>
            <h2>Ajouter une section</h2>
            <form id="addForm">
                <div class="form-group">
                    <label for="addLabel">Nom affiché</label>
                    <input type="text" id="addLabel" required placeholder="ex : Témoignages">
                </div>
                <div class="form-group">
                    <label for="addKey">Clé HTML <span style="font-weight:400">(data-section)</span></label>
                    <input type="text" id="addKey" required placeholder="ex : temoignages">
                    <span class="hint">Correspond à <code>data-section="…"</code> dans index.php</span>
                </div>
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- MODALE : Modifier -->
    <div id="editModal" class="sec-modal">
        <div class="sec-modal-content">
            <button class="sec-close" aria-label="Fermer">&times;</button>
            <h2>Modifier la section</h2>
            <form id="editForm">
                <input type="hidden" id="editId">
                <div class="form-group">
                    <label for="editLabel">Nom affiché</label>
                    <input type="text" id="editLabel" required>
                </div>
                <div class="form-group">
                    <label for="editKey">Clé HTML <span style="font-weight:400">(data-section)</span></label>
                    <input type="text" id="editKey" required>
                    <span class="hint">Correspond à <code>data-section="…"</code> dans index.php</span>
                </div>
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>



    <script>
        // ── Chargement & rendu ────────────────────────────────────────────────────────

        function cree_tableau() {
            fetch('../cimes_api/index_api_sections.php?query=get_sections')
                .then(r => r.json())
                .then(data => {
                    let html = '';
                    data.forEach(s => {
                        const isVisible = s.visible != 0;
                        const eyeIcon = isVisible ? 'fa-eye' : 'fa-eye-slash';
                        const eyeTitle = isVisible ? 'Masquer' : 'Afficher';
                        const badge = isVisible ?
                            '<span class="badge badge-visible">Visible</span>' :
                            '<span class="badge badge-hidden">Masqué</span>';

                        html += `
            <tr data-id="${s.id}" style="${isVisible ? '' : 'opacity:0.5'}">
                <th scope="row">
                    <span class="drag-handle" title="Déplacer"><i class="fa-solid fa-grip-vertical"></i></span>
                    ${escHtml(s.label)}
                </th>
               
                <td>${badge}</td>
               
                
                <td>
                    <button class="icon-btn" title="${eyeTitle}" onclick="toggleVisibility(${s.id})">
                        <i class="fas ${eyeIcon}"></i>
                    </button>
                </td>
            </tr>`;
                    });
                    document.getElementById('corp_tab').innerHTML = html;
                    initDragDrop();
                })
                .catch(err => console.error('Erreur cree_tableau :', err));
        }

        function escHtml(str) {
            return String(str ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        // ── Visibilité ────────────────────────────────────────────────────────────────

        function toggleVisibility(id) {
            fetch('../cimes_api/index_api_sections.php?query=toggle_section', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    const nowVis = data.visible === 1;
                    row.style.opacity = nowVis ? '1' : '0.5';
                    const icon = row.querySelector('.fa-eye,.fa-eye-slash');
                    if (icon) {
                        icon.classList.toggle('fa-eye', nowVis);
                        icon.classList.toggle('fa-eye-slash', !nowVis);
                    }
                    const badge = row.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge ' + (nowVis ? 'badge-visible' : 'badge-hidden');
                        badge.textContent = nowVis ? 'Visible' : 'Masqué';
                    }
                    const btn = icon ? icon.closest('button') : null;
                    if (btn) btn.title = nowVis ? 'Masquer' : 'Afficher';
                })
                .catch(err => console.error('Erreur toggle :', err));
        }

        // // ── Ajout ─────────────────────────────────────────────────────────────────────

        // document.getElementById('addButton').onclick = () => {
        //     document.getElementById('addModal').style.display = 'block';
        // };

        // document.getElementById('addForm').onsubmit = function(e) {
        //     e.preventDefault();
        //     const label = document.getElementById('addLabel').value.trim();
        //     const section_key = document.getElementById('addKey').value.trim();
        //     if (!label || !section_key) return;

        //     fetch('./cimes_api/index_api_sections.php?query=add_section', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json'
        //             },
        //             body: JSON.stringify({
        //                 label,
        //                 section_key
        //             })
        //         })
        //         .then(r => r.json())
        //         .then(() => {
        //             document.getElementById('addModal').style.display = 'none';
        //             document.getElementById('addLabel').value = '';
        //             document.getElementById('addKey').value = '';
        //             cree_tableau();
        //         });
        // };

        // ── Modification ──────────────────────────────────────────────────────────────

        function openEdit(id, label, key) {
            document.getElementById('editId').value = id;
            document.getElementById('editLabel').value = label;
            document.getElementById('editKey').value = key;
            document.getElementById('editModal').style.display = 'block';
        }

        document.getElementById('editForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('editId').value;
            const label = document.getElementById('editLabel').value.trim();
            const section_key = document.getElementById('editKey').value.trim();

            fetch('../cimes_api/index_api_sections.php?query=update_section', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        label,
                        section_key
                    })
                })
                .then(r => r.json())
                .then(() => {
                    document.getElementById('editModal').style.display = 'none';
                    cree_tableau();
                });
        };

        // ── Suppression ───────────────────────────────────────────────────────────────

        // let itemToDelete = null;

        // // function confirmDelete(id) {
        // //     itemToDelete = id;
        // //     document.getElementById('deleteModal').style.display = 'block';
        // // }

        // // document.getElementById('confirmDelete').onclick = function() {
        // //     fetch('../cimes_api/index_api_sections.php?query=delete_section', {
        // //             method: 'POST',
        // //             headers: {
        // //                 'Content-Type': 'application/json'
        // //             },
        // //             body: JSON.stringify({
        // //                 id: itemToDelete
        // //             })
        // //         })
        // //         .then(r => r.json())
        // //         .then(() => {
        // //             document.getElementById('deleteModal').style.display = 'none';
        // //             itemToDelete = null;
        // //             cree_tableau();
        // //         });
        // // };

        // document.getElementById('cancelDelete').onclick = () => {
        //     document.getElementById('deleteModal').style.display = 'none';
        //     itemToDelete = null;
        // };
        // document.getElementById('deleteClose').onclick = () => {
        //     document.getElementById('deleteModal').style.display = 'none';
        //     itemToDelete = null;
        // };

        // ── Drag & drop (mousedown/mousemove/mouseup) ─────────────────────────────────

        function initDragDrop() {
            const tbody = document.querySelector('#corp_tab');
            if (!tbody) return;

            let dragSrc = null,
                ghost = null,
                indicator = null;

            function createGhost(row, clientY) {
                const tbl = document.createElement('table');
                tbl.className = 'drag-ghost-table';
                const tw = tbody.closest('table').offsetWidth;
                const tl = tbody.closest('table').getBoundingClientRect().left;
                tbl.style.cssText = `width:${tw}px;left:${tl}px;top:${clientY}px;opacity:.85`;
                const clone = row.cloneNode(true);
                clone.style.cssText = 'opacity:1;background:#fff';
                tbl.appendChild(clone);
                document.body.appendChild(tbl);
                return tbl;
            }

            function createIndicator() {
                const tr = document.createElement('tr');
                tr.className = 'drop-indicator';
                const td = document.createElement('td');
                td.colSpan = 6;
                tr.appendChild(td);
                return tr;
            }

            function getTargetInfo(clientY) {
                const rows = [...tbody.querySelectorAll('tr:not(.drop-indicator)')].filter(r => r !== dragSrc);
                let best = null,
                    bestDist = Infinity;
                for (const r of rows) {
                    const rect = r.getBoundingClientRect();
                    const mid = rect.top + rect.height / 2;
                    const dist = Math.abs(clientY - mid);
                    if (dist < bestDist) {
                        bestDist = dist;
                        best = {
                            row: r,
                            above: clientY < mid
                        };
                    }
                }
                return best;
            }

            tbody.addEventListener('mousedown', e => {
                const handle = e.target.closest('.drag-handle');
                if (!handle) return;
                e.preventDefault();
                dragSrc = handle.closest('tr');
                indicator = createIndicator();
                dragSrc.classList.add('dragging');
                ghost = createGhost(dragSrc, e.clientY);
            });

            document.addEventListener('mousemove', e => {
                if (!dragSrc) return;
                e.preventDefault();
                ghost.style.top = e.clientY + 'px';
                const info = getTargetInfo(e.clientY);
                if (info) {
                    ghost.style.opacity = '.85';
                    info.above ? info.row.before(indicator) : info.row.after(indicator);
                } else {
                    ghost.style.opacity = '.35';
                    if (indicator.parentNode) indicator.parentNode.removeChild(indicator);
                }
            });

            document.addEventListener('mouseup', e => {
                if (!dragSrc) return;
                const info = getTargetInfo(e.clientY);
                if (info && indicator.parentNode) indicator.before(dragSrc);
                dragSrc.classList.remove('dragging');
                if (indicator.parentNode) indicator.parentNode.removeChild(indicator);
                if (ghost && ghost.parentNode) ghost.parentNode.removeChild(ghost);
                dragSrc = null;
                ghost = null;
                indicator = null;
                saveOrder();
            });
        }

        function saveOrder() {
            const rows = [...document.querySelectorAll('#corp_tab tr:not(.drop-indicator)')];
            const order = rows.map((r, i) => ({
                id: parseInt(r.dataset.id),
                position: i
            }));
            fetch('../cimes_api/index_api_sections.php?query=reorder_sections', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order
                    })
                })
                .then(r => r.json())
                .then(d => console.log('Ordre sauvegardé :', d))
                .catch(err => console.error(err));
        }

        // ── Fermeture modales au clic extérieur ───────────────────────────────────────

        window.onclick = function(e) {
            ['addModal', 'editModal', 'deleteModal'].forEach(id => {
                const m = document.getElementById(id);
                if (m && e.target === m) {
                    m.style.display = 'none';
                    if (id === 'deleteModal') itemToDelete = null;
                }
            });
        };

        document.querySelectorAll('.sec-close').forEach(btn => {
            btn.addEventListener('click', () => {
                const m = btn.closest('.sec-modal');
                if (m) {
                    m.style.display = 'none';
                    if (m.id === 'deleteModal') itemToDelete = null;
                }
            });
        });

        // ── Init ──────────────────────────────────────────────────────────────────────

        cree_tableau();
    </script>

</body>

</html>
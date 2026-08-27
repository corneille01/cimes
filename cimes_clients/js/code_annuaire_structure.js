/* ─────────────────────────────────────────────
   code_annuaire_structure.js  —  Annuaire des structures
   ───────────────────────────────────────────── */
'use strict';

const AVATAR_COLORS = [
  { bg: '#E1F5EE', text: '#085041' },
  { bg: '#E6F1FB', text: '#0C447C' },
  { bg: '#EEEDFE', text: '#3C3489' },
  { bg: '#FAECE7', text: '#712B13' },
  { bg: '#FBEAF0', text: '#72243E' },
  { bg: '#FAEEDA', text: '#633806' },
  { bg: '#EAF3DE', text: '#27500A' },
  { bg: '#FCEBEB', text: '#791F1F' },
];

let allData = [], filtered = [], currentView = 'grid';
let geoFilter = null;

function colorFor(seed) {
  let h = 0;
  for (let i = 0; i < seed.length; i++) h = (h * 31 + seed.charCodeAt(i)) % AVATAR_COLORS.length;
  return AVATAR_COLORS[Math.abs(h)];
}

function acronym(name) {
  return (name || '')
    .split(/[\s\-_]+/)
    .map(w => w[0] || '')
    .join('')
    .toUpperCase()
    .slice(0, 3);
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* ── Avatar : photo ou initiales du nom de la structure ── */
function buildAvatar(s) {
  const photo = s.photo ? s.photo.trim() : '';
  const name  = escHtml(s.etablissement || '');

  if (photo && photo !== 'default.jpg') {
    return `<div class="avatar"><img src="img/${escHtml(photo)}" alt="${name}" loading="lazy"></div>`;
  }
  const col = colorFor(s.etablissement || '');
  const ini = acronym(s.etablissement || '');
  return `<div class="avatar" style="background:${col.bg};color:${col.text}">${ini}</div>`;
}

/* ── Carte structure ── */
function buildCard(s) {
  const etab    = escHtml(s.etablissement     || '');
  const disc    = escHtml(s.discipline        || '');
  const addr    = escHtml(s.adresse           || '');
  
  const annee   = s.annee_creation ? String(s.annee_creation).slice(0, 4) : '';
  const web     = escHtml(s.site_web          || '');
  const resp    = escHtml(s.responsable       || '');
  const pres    = escHtml(s.presentation      || '');
  const domaine = escHtml(s.domaine_recherche || '');

  /* ── Section dépliable ── */
  const hasExtra = annee || domaine || pres;

  const extraHtml = hasExtra ? `
    <div class="card-extra">
      <div class="card-divider" style="margin-top:10px"></div>
      ${annee ? `<div class="meta-row">
        <i class="fa-solid fa-calendar-days meta-icon"></i>
        <span>Créée en ${annee}</span>
      </div>` : ''}
      ${domaine ? `<div class="meta-row">
        <i class="fa-solid fa-flask meta-icon"></i>
        <span>${domaine}</span>
      </div>` : ''}
      ${pres ? `<div class="meta-row" style="align-items:flex-start">
        <i class="fa-solid fa-align-left meta-icon" style="margin-top:3px"></i>
        <p class="struct-presentation">${pres}</p>
      </div>` : ''}
    </div>` : '';

 return `
    <article class="member-card" data-nom="${etab}" onclick="handleCardClick(event, this)">
      ${buildAvatar(s)}

      <div class="card-name">${etab}</div>

      ${resp ? `<div class="card-org">
        <i class="fa-solid fa-user-tie meta-icon"></i>
        <span>${resp}</span>
      </div>` : ''}

      <div class="card-divider"></div>

      <div class="card-meta">

        ${disc ? `<div class="meta-row">
          <i class="fa-solid fa-briefcase meta-icon"></i>
          <span>${disc}</span>
        </div>` : ''}

        ${addr ? `<div class="meta-row">
          <i class="fa-solid fa-location-dot meta-icon"></i>
          <span>${addr}</span>
        </div>` : ''}

        ${web ? `<div class="meta-row">
          <i class="fa-solid fa-link meta-icon"></i>
          <a class="mail-link" href="${web}" target="_blank" rel="noopener">${web}</a>
        </div>` : ''}

        ${tutelle ? `<div class="meta-row">
          <i class="fa-solid fa-landmark meta-icon"></i>
          <span>${tutelle}</span>
        </div>` : ''}

      </div>

      ${hasExtra ? `
      <button class="card-toggle-btn" aria-expanded="false" aria-label="Voir plus" onclick="toggleCard(this)">
        <i class="fa-solid fa-chevron-down toggle-icon"></i>
      </button>
      ${extraHtml}` : ''}

    </article>`;
}

// Détecte si c'est un vrai clic ou une sélection de texte
let mouseDownX = 0, mouseDownY = 0;

document.getElementById('annuaire_structures').addEventListener('mousedown', e => {
  mouseDownX = e.clientX;
  mouseDownY = e.clientY;
});

function handleCardClick(e, card) {
  // Ignorer liens, boutons, inputs
  if (e.target.closest('a, button, input')) return;

  // Ignorer si l'utilisateur a glissé la souris (sélection de texte)
  const dx = Math.abs(e.clientX - mouseDownX);
  const dy = Math.abs(e.clientY - mouseDownY);
  if (dx > 4 || dy > 4) return;

  // Ignorer si du texte est sélectionné
  const selection = window.getSelection();
  if (selection && selection.toString().length > 0) return;

  const btn = card.querySelector('.card-toggle-btn');
  if (btn) toggleCard(btn);
}

/* ── Dépliage ── */
function toggleCard(btn) {
  const card  = btn.closest('.member-card');
  const extra = card.querySelector('.card-extra');
  const open  = btn.getAttribute('aria-expanded') === 'true';
  btn.setAttribute('aria-expanded', !open);
  card.classList.toggle('card-open', !open);
  extra.style.maxHeight = open ? '0' : extra.scrollHeight + 'px';
}

/* ── Rendu ── */
function render(list) {
  const grid    = document.getElementById('annuaire_structures');
  const countEl = document.getElementById('count');
  const totalEl = document.getElementById('total-label');
  const plurEl  = document.getElementById('plural');
  const plur2El = document.getElementById('plural2');

  countEl.textContent = list.length;
  plurEl.textContent  = list.length > 1 ? 's' : '';
  plur2El.textContent = list.length > 1 ? 's' : '';
  totalEl.textContent = (allData.length && list.length < allData.length)
    ? ` sur ${allData.length}` : '';

  if (!list.length) {
    grid.innerHTML = `<div class="state-msg">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        <line x1="8" y1="11" x2="14" y2="11"/>
      </svg>
      <p>Aucune structure trouvée</p>
      <small>Essayez d'autres termes de recherche ou de filtres différents.</small>
    </div>`;
    return;
  }

  grid.innerHTML = list.map(buildCard).join('');

  const cards = grid.querySelectorAll('.member-card');
  cards.forEach((card, i) => { card.style.zIndex = cards.length - i; });
}

/* ── Filtre géographique ── */
function memberMatchesGeo(s) {
  if (!geoFilter) return true;
  const { citycode, postcode, city } = geoFilter;

  if (s.citycode   && s.citycode   === citycode)  return true;
  if (s.postcode   && s.postcode   === postcode)  return true;
  if (s.code_postal && s.code_postal === postcode) return true;

  const addr = (s.adresse || '').toLowerCase();
  if (!addr) return false;
  return addr.includes(postcode) || addr.includes(city.toLowerCase());
}

/* ── Debounce ── */
function debounce(fn, ms) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

/* ── BAN ── */
async function queryBAN(q) {
  if (q.length < 2) return [];
  const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(q)}&type=municipality&autocomplete=1&limit=6`;
  const res = await fetch(url);
  if (!res.ok) return [];
  const json = await res.json();
  return (json.features || []).map(f => ({
    label    : f.properties.label,
    city     : f.properties.city,
    postcode : f.properties.postcode,
    citycode : f.properties.citycode,
    context  : f.properties.context,
  }));
}

/* ── Autocomplete géo ── */
function initGeoAutocomplete() {
  const input    = document.getElementById('geo-input');
  const dropdown = document.getElementById('geo-dropdown');
  const clearBtn = document.getElementById('geo-clear');
  let highlighted = -1;

  function closeDropdown() {
    dropdown.classList.remove('open');
    dropdown.innerHTML = '';
    highlighted = -1;
  }

  function selectResult(result) {
    geoFilter = result;
    input.value = `${result.city} (${result.postcode})`;
    clearBtn.style.display = 'block';
    closeDropdown();
    applyFilters();
  }

  function clearGeo() {
    geoFilter = null;
    input.value = '';
    clearBtn.style.display = 'none';
    closeDropdown();
    applyFilters();
  }

  function renderResults(results) {
    if (!results.length) {
      dropdown.innerHTML = '<li class="geo-empty">Aucun résultat</li>';
      dropdown.classList.add('open');
      return;
    }
    dropdown.innerHTML = results.map((r, i) => `
      <li role="option" data-idx="${i}">
        <span class="geo-label">${escHtml(r.city)} <small style="font-weight:400">(${escHtml(r.postcode)})</small></span>
        <span class="geo-sub">${escHtml(r.context)}</span>
      </li>`).join('');
    dropdown.classList.add('open');
    dropdown.querySelectorAll('li').forEach((li, i) => {
      li.addEventListener('mousedown', e => { e.preventDefault(); selectResult(results[i]); });
    });
  }

  function highlightItem(idx) {
    const items = dropdown.querySelectorAll('li[data-idx]');
    items.forEach(li => li.removeAttribute('aria-selected'));
    if (idx >= 0 && idx < items.length) {
      items[idx].setAttribute('aria-selected', 'true');
      items[idx].scrollIntoView({ block: 'nearest' });
    }
  }

  let cachedResults = [];

  const search = debounce(async (q) => {
    if (!q.trim()) { closeDropdown(); return; }
    dropdown.innerHTML = '<li class="geo-loading">Recherche…</li>';
    dropdown.classList.add('open');
    try {
      cachedResults = await queryBAN(q);
      renderResults(cachedResults);
    } catch {
      dropdown.innerHTML = '<li class="geo-empty">Aucun résultat</li>';
    }
  }, 280);

  input.addEventListener('input', () => {
    highlighted = -1;
    if (geoFilter) { geoFilter = null; clearBtn.style.display = 'none'; applyFilters(); }
    search(input.value);
  });

  input.addEventListener('keydown', e => {
    const items = dropdown.querySelectorAll('li[data-idx]');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      highlighted = Math.min(highlighted + 1, items.length - 1);
      highlightItem(highlighted);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      highlighted = Math.max(highlighted - 1, 0);
      highlightItem(highlighted);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (highlighted >= 0 && cachedResults[highlighted]) selectResult(cachedResults[highlighted]);
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  input.addEventListener('blur', () => setTimeout(closeDropdown, 150));
  clearBtn.addEventListener('click', clearGeo);
}

/* ── Filtres principaux ── */
function applyFilters() {
  const q    = document.getElementById('search').value.toLowerCase().trim();
  const disc = document.getElementById('filter-discipline').value;
  const sort = document.getElementById('sort').value;

  filtered = allData.filter(s => {
    const hay = [
      s.etablissement, s.discipline, s.adresse,
      s.tutelles, s.responsable, s.site_web,
      s.presentation, s.domaine_recherche,
    ].join(' ').toLowerCase();

    const textOk = !q    || hay.includes(q);
    const discOk = !disc || s.discipline === disc;
    const geoOk  = memberMatchesGeo(s);
    return textOk && discOk && geoOk;
  });

  filtered.sort((a, b) => {
    switch (sort) {
      case 'name_asc':      return (a.etablissement||'').localeCompare(b.etablissement||'', 'fr');
      case 'name_desc':     return (b.etablissement||'').localeCompare(a.etablissement||'', 'fr');
      case 'discipline':    return (a.discipline||'').localeCompare(b.discipline||'', 'fr');
      case 'etablissement': return (a.etablissement||'').localeCompare(b.etablissement||'', 'fr');
      default: return 0;
    }
  });

  render(filtered);
}

/* ── Peuplement du filtre disciplines ── */
function populateDisciplines(data) {
  const sel = document.getElementById('filter-discipline');
  const disciplines = [...new Set(
    data.flatMap(s => (s.discipline || '').split(',').map(d => d.trim())).filter(Boolean)
  )].sort((a, b) => a.localeCompare(b, 'fr'));

  disciplines.forEach(d => {
    const o = document.createElement('option');
    o.value = o.textContent = d;
    sel.appendChild(o);
  });
}

/* ── Vue grille / liste ── */
function setView(view) {
  currentView = view;
  const grid = document.getElementById('annuaire_structures');
  document.getElementById('btn-grid').classList.toggle('active', view === 'grid');
  document.getElementById('btn-list').classList.toggle('active', view === 'list');
  grid.classList.toggle('view-list', view === 'list');
}

/* ── Init ── */
function init(data) {
  allData = data;
  allData.sort((a, b) => (a.etablissement||'').localeCompare(b.etablissement||'', 'fr'));
  populateDisciplines(allData);

  document.getElementById('search').addEventListener('input', applyFilters);
  document.getElementById('filter-discipline').addEventListener('change', applyFilters);
  document.getElementById('sort').addEventListener('change', applyFilters);
  document.getElementById('btn-grid').addEventListener('click', () => setView('grid'));
  document.getElementById('btn-list').addEventListener('click', () => setView('list'));

  initGeoAutocomplete();
  applyFilters();
}

/* ── Chargement ── */
fetch('../cimes_api/index_api.php?query=annuaire_structures')
  .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
  .then(data => init(data))
  .catch(err => {
    console.error('Erreur chargement annuaire structures :', err);
    document.getElementById('annuaire_structures').innerHTML = `<div class="state-msg">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <p>Impossible de charger l'annuaire</p>
      <small>Vérifiez votre connexion ou contactez l'administrateur.</small>
    </div>`;
  });
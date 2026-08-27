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

function initials(p, n) { return ((p[0] || '') + (n[0] || '')).toUpperCase(); }

function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function buildAvatar(m) {
  const photo = m.photo ? m.photo.trim() : '';
  const name  = escHtml(`${m.prenom||''} ${m.nom||''}`.trim());
  if (photo) {
    const src = photo.startsWith('http') ? photo : `img/${escHtml(photo)}`;
    return `<div class="avatar"><img src="${src}" alt="${name}" loading="lazy"></div>`;
  }
  const col = colorFor((m.nom||'') + (m.prenom||''));
  const ini = initials(m.prenom||'', m.nom||'');
  return `<div class="avatar" style="background:${col.bg};color:${col.text}">${ini}</div>`;
}

function buildCard(m) {
  const nom   = escHtml(`${m.prenom||''} ${m.nom||''}`.trim());
  const disc  = escHtml(m.discipline    || '');
  const etab  = escHtml(m.etablissement || '');
  const mail  = escHtml(m.mail          || '');
  const univ  = escHtml(m.universite    || '');
  const fonct = escHtml(m.fonction      || '');
  const web   = escHtml(m.page_web      || '');
  const terrain = escHtml(m.terrain_recherche || '');

  const motsArray = (m.mots_cles || '').split(',').map(s => s.trim()).filter(Boolean);
  const motsBadgesHtml = motsArray.length
    ? motsArray.map(kw => `<span style="display:inline-block;background:#eef5f2;border-radius:12px;padding:2px 10px;margin:2px 4px 2px 0;font-size:0.75rem;color:var(--green-dark);">${escHtml(kw)}</span>`).join('')
    : '';

  let publiHtml = `
    <div class="publi-container" data-person-id="${m.id}" data-idhal="${escHtml(m.id_hal || '')}">
      <div class="publi-skeleton">
        <div class="publi-header-skel"></div>
        <div class="publi-row-skel"></div>
        <div class="publi-row-skel"></div>
        <div class="publi-row-skel"></div>
      </div>
    </div>`;

  const hasExtra = etab || web || univ || terrain || motsBadgesHtml;

  const extraHtml = hasExtra ? `
    <div class="card-extra">
      <div class="card-divider" style="margin-top:10px"></div>
      ${etab ? `<div class="meta-row">
        <i class="fa-solid fa-building-columns meta-icon"></i>
        <span>${etab}</span>
      </div>` : ''}
      ${web ? `<div class="meta-row">
        <i class="fa-solid fa-link meta-icon"></i>
        <a class="mail-link" href="${web}" target="_blank" rel="noopener">${web}</a>
      </div>` : ''}
      ${univ ? `<div class="meta-row">
        <i class="fa-solid fa-graduation-cap meta-icon"></i>
        <span>${univ}</span>
      </div>` : ''}
      ${terrain ? `<div class="meta-row">
        <i class="fa-solid fa-mountain meta-icon"></i>
        <span>${terrain}</span>
      </div>` : ''}
      ${motsBadgesHtml ? `<div class="meta-row">
        <i class="fa-solid fa-tags meta-icon"></i>
        <span>${motsBadgesHtml}</span>
      </div>` : ''}
    </div>` : '';

  return `
    <article class="member-card" data-nom="${escHtml(m.nom||'')}">
      ${buildAvatar(m)}
      <div class="card-name">${nom}</div>
      ${fonct ? `<div class="card-org">
        <i class="fa-solid fa-id-badge meta-icon"></i>
        <span>${fonct}</span>
      </div>` : ''}
      <div class="card-divider"></div>
      <div class="card-meta">
        ${mail ? `<div class="meta-row">
          <i class="fa-solid fa-envelope meta-icon"></i>
          <a class="mail-link" href="mailto:${mail}">${mail}</a>
        </div>` : ''}
        ${disc ? `<div class="meta-row">
          <i class="fa-solid fa-briefcase meta-icon"></i>
          <span>${disc}</span>
        </div>` : ''}
        ${publiHtml}
      </div>
      ${hasExtra ? `
      <button class="card-toggle-btn" aria-expanded="false" aria-label="Voir plus" onclick="toggleCard(this)">
        <i class="fa-solid fa-chevron-down toggle-icon"></i>
      </button>
      ${extraHtml}` : ''}
    </article>`;
}

function toggleCard(btn) {
  const card  = btn.closest('.member-card');
  const extra = card.querySelector('.card-extra');
  const open  = btn.getAttribute('aria-expanded') === 'true';
  btn.setAttribute('aria-expanded', !open);
  card.classList.toggle('card-open', !open);
  if (extra) {
    extra.style.maxHeight = !open ? extra.scrollHeight + 'px' : '0';
  }
}

/* ── API HAL : récupère les 3 derniers articles d’un auteur ── */
async function fetchHALPublications(idHal, nom, prenom) {
  // Essai 1 : identifiant HAL (le plus fiable)
  if (idHal) {
    const url = `https://api.hal.science/search/?q=authIdHal_s:${encodeURIComponent(idHal.toLowerCase())}&rows=3&sort=producedDate_s+desc&fl=title_s,uri_s,docid,producedDate_s&fq=docType_s:ART&wt=json`;
    try {
      const resp = await fetch(url);
      if (resp.ok) {
        const json = await resp.json();
        const docs = json.response?.docs;
        if (docs && docs.length > 0) {
          return docs.map(doc => ({
            title: doc.title_s || 'Sans titre',
            link: doc.uri_s || `https://hal.science/${doc.docid}`,
            docid: doc.docid,
            date: doc.producedDate_s || ''
          }));
        }
      }
    } catch (e) {
      console.warn('Échec requête HAL par id_hal pour', idHal, e);
    }
  }

  // Essai 2 : nom exact + prénom préfixe (évite les homonymes)
  if (nom && prenom) {
    const escapedLastName = nom.replace(/ /g, '\\ '); // échappe les espaces dans le nom
    const url = `https://api.hal.science/search/?q=authLastName_t:"${encodeURIComponent(escapedLastName)}"+AND+authFirstName_t:${encodeURIComponent(prenom)}*&rows=3&sort=producedDate_s+desc&fl=title_s,uri_s,docid,producedDate_s&fq=docType_s:ART&wt=json`;
    try {
      const resp = await fetch(url);
      if (resp.ok) {
        const json = await resp.json();
        const docs = json.response?.docs;
        if (docs && docs.length > 0) {
          return docs.map(doc => ({
            title: doc.title_s || 'Sans titre',
            link: doc.uri_s || `https://hal.science/${doc.docid}`,
            docid: doc.docid,
            date: doc.producedDate_s || ''
          }));
        }
      }
    } catch (e) {
      console.warn('Échec requête HAL par nom/prénom pour', nom, prenom, e);
    }
  }

  return [];
}

async function injectHALPublications(personId, idHal, nom, prenom) {
  const container = document.querySelector(`.publi-container[data-person-id="${personId}"]`);
  if (!container) return;

  container.innerHTML = `<div class="publi-skeleton">
    <div class="publi-header-skel"></div>
    <div class="publi-row-skel"></div>
    <div class="publi-row-skel"></div>
    <div class="publi-row-skel"></div>
  </div>`;

  const pubs = await fetchHALPublications(idHal, nom, prenom);

  if (!pubs.length) {
    container.innerHTML = `<div class="publi-header">
      <i class="fa-solid fa-book-open meta-icon"></i>
      <span>Aucune publication HAL trouvée</span>
    </div>`;
    return;
  }

  const itemsHTML = pubs.map(p => `
    <div class="meta-row publi-item">
      <i class="fa-solid fa-file-lines meta-icon"></i>
      <a class="mail-link publi-link" href="publication.php?docid=${encodeURIComponent(p.docid)}&auteur=${encodeURIComponent(idHal || '')}&person_id=${encodeURIComponent(personId)}"
         title="${escHtml(p.title)}"  rel="noopener noreferrer">
        ${escHtml(p.title)}
      </a>
      ${p.date ? `<span class="publi-date">${escHtml(p.date.slice(0,4))}</span>` : ''}
    </div>`).join('');

  const allLink = `<div class="publi-all">
    <a href="publication.php?auteur=${encodeURIComponent(idHal || '')}&person_id=${encodeURIComponent(personId)}" title="Toutes les publications">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> Voir toutes les publications
    </a>
  </div>`;

  container.innerHTML = `
    <div class="publi-header">
      <i class="fa-solid fa-book-open meta-icon"></i>
      <span>3 derniers articles publiés</span>
    </div>
    ${itemsHTML}
    ${allLink}`;
}

function render(list) {
  const grid    = document.getElementById('annuaire');
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
      <p>Aucun membre trouvé</p>
      <small>Essayez d'autres termes ou filtres.</small>
    </div>`;
    return;
  }
  grid.innerHTML = list.map(buildCard).join('');

  const cards = grid.querySelectorAll('.member-card');
  const total = cards.length;
  cards.forEach((card, i) => { card.style.zIndex = total - i; });

  list.forEach(m => {
    if (m.id_hal || (m.nom && m.prenom)) {
      injectHALPublications(m.id, m.id_hal, m.nom, m.prenom);
    }
  });
}

/* ── Filtre géographique ── */
function memberMatchesGeo(m) {
  if (!geoFilter) return true;
  const addr = (m.adresse || m.address || '').toLowerCase();
  if (!addr) return true;

  const { citycode, postcode, city } = geoFilter;
  if (m.citycode  && m.citycode  === citycode)  return true;
  if (m.postcode  && m.postcode  === postcode)  return true;
  if (m.code_postal && m.code_postal === postcode) return true;

  const cityLow = city.toLowerCase();
  return addr.includes(postcode) || addr.includes(cityLow);
}

function debounce(fn, ms) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

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

function initGeoAutocomplete() {
  const input    = document.getElementById('geo-input');
  const dropdown = document.getElementById('geo-dropdown');
  const clearBtn = document.getElementById('geo-clear');

  let highlighted = -1;
  let cachedResults = [];

  function closeDropdown() {
    dropdown.classList.remove('open');
    dropdown.innerHTML = '';
    highlighted = -1;
  }

  function showLoading() {
    dropdown.innerHTML = '<li class="geo-loading">Recherche…</li>';
    dropdown.classList.add('open');
  }

  function showEmpty() {
    dropdown.innerHTML = '<li class="geo-empty">Aucun résultat</li>';
    dropdown.classList.add('open');
  }

  function selectResult(result) {
    geoFilter       = result;
    input.value     = `${result.city} (${result.postcode})`;
    clearBtn.style.display = 'block';
    closeDropdown();
    applyFilters();
  }

  function clearGeo() {
    geoFilter             = null;
    input.value           = '';
    clearBtn.style.display = 'none';
    closeDropdown();
    applyFilters();
  }

  function renderResults(results) {
    if (!results.length) { showEmpty(); return; }
    dropdown.innerHTML = results.map((r, i) => `
      <li role="option" data-idx="${i}">
        <span class="geo-label">${escHtml(r.city)} <small style="font-weight:400">(${escHtml(r.postcode)})</small></span>
        <span class="geo-sub">${escHtml(r.context)}</span>
      </li>`).join('');
    dropdown.classList.add('open');

    dropdown.querySelectorAll('li').forEach((li, i) => {
      li.addEventListener('mousedown', e => {
        e.preventDefault();
        selectResult(results[i]);
      });
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

  const search = debounce(async (q) => {
    if (!q.trim()) { closeDropdown(); return; }
    showLoading();
    try {
      cachedResults = await queryBAN(q);
      renderResults(cachedResults);
    } catch {
      showEmpty();
    }
  }, 280);

  input.addEventListener('input', () => {
    highlighted = -1;
    if (geoFilter) {
      geoFilter = null;
      clearBtn.style.display = 'none';
      applyFilters();
    }
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
      if (highlighted >= 0 && cachedResults[highlighted]) {
        selectResult(cachedResults[highlighted]);
      }
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  input.addEventListener('blur', () => {
    setTimeout(closeDropdown, 150);
  });

  clearBtn.addEventListener('click', clearGeo);
}

function applyFilters() {
  const q    = document.getElementById('search').value.toLowerCase().trim();
  const disc = document.getElementById('filter-discipline').value;
  const sort = document.getElementById('sort').value;

  filtered = allData.filter(m => {
    const hay = [
      m.nom, m.prenom, m.discipline, m.etablissement, m.mail,
      m.adresse, m.address, m.fonction, m.affiliation, m.page_web,
      m.universite, m.terrain_recherche, m.mots_cles,
      m.publications ? (typeof m.publications === 'string' ? m.publications : JSON.stringify(m.publications)) : ''
    ].join(' ').toLowerCase();
    const textOk = !q || hay.includes(q);

    let discOk = true;
    if (disc) {
      const persoDisciplines = (m.discipline || '').toLowerCase().split(',').map(s => s.trim());
      discOk = persoDisciplines.includes(disc.toLowerCase());
    }

    const geoOk  = memberMatchesGeo(m);
    return textOk && discOk && geoOk;
  });

  filtered.sort((a, b) => {
    switch (sort) {
      case 'name_asc':      return (a.nom+a.prenom).localeCompare(b.nom+b.prenom, 'fr');
      case 'name_desc':     return (b.nom+b.prenom).localeCompare(a.nom+a.prenom, 'fr');
      case 'discipline':    return (a.discipline||'').localeCompare(b.discipline||'', 'fr');
      case 'etablissement': return (a.etablissement||'').localeCompare(b.etablissement||'', 'fr');
      default: return 0;
    }
  });
  render(filtered);
}

function populateDisciplines(data) {
  const sel = document.getElementById('filter-discipline');
  const disciplineSet = new Set();

  data.forEach(m => {
    if (m.discipline) {
      m.discipline.split(',').forEach(d => {
        const trimmed = d.trim();
        if (trimmed) disciplineSet.add(trimmed);
      });
    }
  });

  [...disciplineSet]
    .sort((a, b) => a.localeCompare(b, 'fr'))
    .forEach(d => {
      const o = document.createElement('option');
      o.value = o.textContent = d;
      sel.appendChild(o);
    });
}

function setView(view) {
  currentView = view;
  const grid  = document.getElementById('annuaire');
  document.getElementById('btn-grid').classList.toggle('active', view === 'grid');
  document.getElementById('btn-list').classList.toggle('active', view === 'list');
  grid.classList.toggle('view-list', view === 'list');
}

function init(data) {
  allData = data;
  allData.sort((a, b) => (a.nom+a.prenom).localeCompare(b.nom+b.prenom, 'fr'));
  populateDisciplines(allData);

  document.getElementById('search').addEventListener('input', applyFilters);
  document.getElementById('filter-discipline').addEventListener('change', applyFilters);
  document.getElementById('sort').addEventListener('change', applyFilters);
  document.getElementById('btn-grid').addEventListener('click', () => setView('grid'));
  document.getElementById('btn-list').addEventListener('click', () => setView('list'));

  initGeoAutocomplete();
  applyFilters();
}

fetch('../cimes_api/index_api.php?query=annuaire')
  .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
  .then(data => init(data))
  .catch(err => {
    console.error('Erreur chargement annuaire :', err);
    document.getElementById('annuaire').innerHTML = `<div class="state-msg">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      <p>Impossible de charger l'annuaire</p>
      <small>Vérifiez votre connexion ou contactez l'administrateur.</small>
    </div>`;
  });
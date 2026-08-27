/* ═══════════════════════════════════════════════════════════════════════
   projets.js – CIMES Cartographie (popup desktop/mobile, zéro bounce)
   ═══════════════════════════════════════════════════════════════════════ */
'use strict';

// ========= GLOBAL =========
let map, markersCluster, heatLayer = null;
let allProjects = [];
let filteredProjects = [];
let activeFilters = {
  massifs: new Set(),
  disciplines: new Set(),
  partenaires: new Set(),
  statuts: new Set(),
  pays: new Set(),
  financeurs: new Set(),
  motsCles: new Set(),
  porteurs: new Set()       // nouveau filtre
};
let activeYear = new Date().getFullYear();
let heatmapActive = false;

const initialView = { center: [44.5, 3.5], zoom: 7 };
const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

// ========= ICÔNES =========
const normalPin = L.divIcon({
  className: 'custom-pin',
  html: `<svg width="24" height="36" viewBox="0 0 24 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 0C5.383 0 0 5.383 0 12c0 9 12 24 12 24s12-15 12-24C24 5.383 18.617 0 12 0z" fill="#0F766E" stroke="white" stroke-width="2"/>
    <circle cx="12" cy="12" r="5" fill="white"/>
  </svg>`,
  iconSize: [24, 36],
  iconAnchor: [12, 36],
  popupAnchor: [0, -36]
});

const highlightPin = L.divIcon({
  className: 'custom-pin',
  html: `<svg width="30" height="42" viewBox="0 0 24 36" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 0C5.383 0 0 5.383 0 12c0 9 12 24 12 24s12-15 12-24C24 5.383 18.617 0 12 0z" fill="#F59E0B" stroke="white" stroke-width="2"/>
    <circle cx="12" cy="12" r="5" fill="white"/>
  </svg>`,
  iconSize: [30, 42],
  iconAnchor: [15, 42],
  popupAnchor: [0, -42]
});

// ========= INIT =========
document.addEventListener('DOMContentLoaded', async () => {
  initMap();
  await fetchProjects();
  buildFiltersUI();
  applyFilters();
  setupEventListeners();
  setupBaseLayerSwitcher();
  setupLocateButton();
});

// ========= CARTE (attribution désactivée) =========
function initMap() {
  map = L.map('map', {
    attributionControl: false
  }).setView(initialView.center, initialView.zoom);

  window.topoLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    maxZoom: 17,
    attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> contributors'
  }).addTo(map);

  window.satLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    attribution: 'Esri & contributors'
  });
  window.osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  });

  fetch('./json/regions.geojson')
    .then(r => r.json())
    .then(data => L.geoJSON(data, { style: { color: '#222222', weight: 2, fillOpacity: 0, dashArray: '5 5' } }).addTo(map))
    .catch(err => console.warn('regions.geojson non chargé', err));

  markersCluster = L.markerClusterGroup({
    maxClusterRadius: 40,
    iconCreateFunction: function(cluster) {
      const count = cluster.getChildCount();
      const color = count < 5 ? '#0F766E' : (count < 15 ? '#C9D95B' : '#A32D2D');
      return L.divIcon({
        html: `<div style="background:${color}; border-radius:30px; width:34px; height:34px; display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">${count}</div>`,
        iconSize: [34, 34],
        className: ''
      });
    }
  }).addTo(map);

  document.getElementById('toggleClusters').addEventListener('change', e => {
    e.target.checked ? map.addLayer(markersCluster) : map.removeLayer(markersCluster);
  });

  document.getElementById('recenterBtn').addEventListener('click', () => {
    map.setView(initialView.center, initialView.zoom);
  });
}

// ========= FETCH PROJETS =========
async function fetchProjects() {
  try {
    const response = await fetch('../cimes_api/index_api.php?query=projet_carte');
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const rawData = await response.json();

    allProjects = rawData.map(item => ({
      id: item.id,
      titre: item.titre,
      acronyme: item.acronyme || '',
      financeurs: item.financeur ? item.financeur.split(',').map(s => s.trim()).filter(Boolean) : [],
      porteurPrincipal: item.porteur_principal || '',
      structureRattachement: item.structure_rattachement || '',
      siteWebPorteur: item.site_web_porteur || '',
      partenaires: item.partenaires ? item.partenaires.split(',').map(s => s.trim()).filter(Boolean) : [],
      disciplines: item.disciplines ? item.disciplines.split(',').map(s => s.trim()).filter(Boolean) : [],
      massifs: item.massif ? item.massif.split(',').map(s => s.trim()).filter(Boolean) : [],
      pays: item.pays ? item.pays.split(',').map(s => s.trim()).filter(Boolean) : [],
      objectifPrincipal: item.objectif_principal || '',
      localisations: parseLocalisations(item.localisations),
      dateDebut: item.date_debut ? new Date(item.date_debut) : null,
      dateFin: item.date_fin ? new Date(item.date_fin) : null,
      siteWeb: item.site_web || '',
      statut: computeStatus(item.date_debut, item.date_fin),
      motsCles: item.mots_cles ? item.mots_cles.split(',').map(s => s.trim()).filter(Boolean) : []
    }));

    updateYearDropdown();
  } catch (error) {
    console.error('Impossible de charger les projets :', error);
    allProjects = [];
  }
}

function parseLocalisations(raw) {
  if (!raw) return [];
  try { return JSON.parse(raw); } catch (e) { return []; }
}

function computeStatus(debut, fin) {
  if (!debut || !fin) return 'En cours';
  const now = new Date();
  if (new Date(fin) < now) return 'Terminé';
  if (new Date(debut) > now) return 'Planifié';
  return 'En cours';
}

// ========= FILTRES =========
function updateYearDropdown() {
  const currentYear = new Date().getFullYear();
  const yearsSet = new Set();
  allProjects.forEach(p => {
    if (p.dateDebut && p.dateFin) {
      for (let y = p.dateDebut.getFullYear(); y <= p.dateFin.getFullYear(); y++) yearsSet.add(y);
    } else if (p.dateDebut) yearsSet.add(p.dateDebut.getFullYear());
    else if (p.dateFin) yearsSet.add(p.dateFin.getFullYear());
  });
  let years = Array.from(yearsSet).sort((a, b) => a - b);
  if (years.length === 0) years = [currentYear];

  const select = document.getElementById('yearSelect');
  select.innerHTML = '';
  years.forEach(y => {
    const o = document.createElement('option');
    o.value = y; o.textContent = y;
    select.appendChild(o);
  });
  activeYear = years.includes(currentYear) ? currentYear : years[0];
  select.value = activeYear;
  applyFilters();
}

function buildFiltersUI() {
  buildTagContainer('massifsFilter', getUniqueValues('massifs'), 'massifs');
  buildTagContainer('disciplinesFilter', getUniqueValues('disciplines'), 'disciplines');
  buildTagContainer('partenairesFilter', getUniqueValues('partenaires'), 'partenaires');
  buildTagContainer('financeursFilter', getUniqueValues('financeurs'), 'financeurs');
  buildTagContainer('porteursFilter', getUniqueValues('porteurs'), 'porteurs');   // nouveau
  buildTagContainer('paysFilter', getUniqueValues('pays'), 'pays');
  buildTagContainer('motsClesFilter', getUniqueValues('motsCles'), 'motsCles');
  buildTagContainer('statutsFilter', ['En cours', 'Terminé', 'Planifié'], 'statuts');
}

function getUniqueValues(key) {
  const set = new Set();
  allProjects.forEach(p => {
    if (key === 'massifs') p.massifs?.forEach(m => set.add(m));
    else if (key === 'disciplines') p.disciplines?.forEach(d => set.add(d));
    else if (key === 'partenaires') p.partenaires?.forEach(part => set.add(part));
    else if (key === 'financeurs') p.financeurs?.forEach(f => set.add(f));
    else if (key === 'porteurs') { if (p.porteurPrincipal) set.add(p.porteurPrincipal); }   // nouveau
    else if (key === 'pays') p.pays?.forEach(c => set.add(c));
    else if (key === 'motsCles') p.motsCles?.forEach(mc => set.add(mc));
    else if (key === 'statuts') set.add(p.statut);
  });
  return Array.from(set).filter(Boolean).sort();
}

function buildTagContainer(containerId, values, filterKey) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = '<div class="cimes-tags-container"></div>';
  const tagsDiv = container.querySelector('.cimes-tags-container');
  values.forEach(val => {
    const tag = document.createElement('span');
    tag.className = 'cimes-tag';
    tag.textContent = val;
    tag.addEventListener('click', () => {
      if (activeFilters[filterKey].has(val)) activeFilters[filterKey].delete(val);
      else activeFilters[filterKey].add(val);
      updateFilterUI();
      applyFilters();
    });
    tagsDiv.appendChild(tag);
  });
}

function updateFilterUI() {
  ['massifs','disciplines','partenaires','financeurs','porteurs','pays','motsCles','statuts'].forEach(key => {
    document.querySelectorAll(`#${key}Filter .cimes-tag`).forEach(tag => {
      tag.classList.toggle('cimes-tag-active', activeFilters[key].has(tag.textContent));
    });
  });
}

function applyFilters() {
  const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';

  filteredProjects = allProjects.filter(p => {
    if (activeFilters.massifs.size > 0 && !p.massifs?.some(m => activeFilters.massifs.has(m))) return false;
    if (activeFilters.disciplines.size > 0 && !p.disciplines?.some(d => activeFilters.disciplines.has(d))) return false;
    if (activeFilters.partenaires.size > 0 && !p.partenaires?.some(part => activeFilters.partenaires.has(part))) return false;
    if (activeFilters.financeurs.size > 0 && !p.financeurs?.some(f => activeFilters.financeurs.has(f))) return false;
    if (activeFilters.porteurs.size > 0 && !activeFilters.porteurs.has(p.porteurPrincipal)) return false;   // nouveau
    if (activeFilters.pays.size > 0 && !p.pays?.some(c => activeFilters.pays.has(c))) return false;
    if (activeFilters.motsCles.size > 0 && !p.motsCles?.some(mc => activeFilters.motsCles.has(mc))) return false;
    if (activeFilters.statuts.size > 0 && !activeFilters.statuts.has(p.statut)) return false;
    if (activeYear && p.dateDebut && p.dateFin && (activeYear < p.dateDebut.getFullYear() || activeYear > p.dateFin.getFullYear())) return false;
    if (searchTerm) {
      const txt = `${p.titre} ${p.acronyme} ${p.disciplines.join(' ')} ${p.partenaires.join(' ')} ${p.pays.join(' ')} ${p.financeurs.join(' ')} ${p.motsCles.join(' ')} ${p.porteurPrincipal}`.toLowerCase();
      if (!txt.includes(searchTerm)) return false;
    }
    return true;
  });
  updateStats();
  renderMarkersAndHeat();
}

function updateStats() {
  document.getElementById('statProjects').textContent = filteredProjects.length;
  const massifsSet = new Set();
  filteredProjects.forEach(p => p.massifs?.forEach(m => massifsSet.add(m)));
  document.getElementById('statMassifs').textContent = massifsSet.size;
}

let allMarkers = [];

// ========= RENDU CARTE (popup au survol/click) =========
function renderMarkersAndHeat() {
    markersCluster.clearLayers();
    allMarkers = [];
    const heatPoints = [];

    filteredProjects.forEach(proj => {
        proj.localisations?.forEach(loc => {
            const marker = L.marker([loc.lat, loc.lng], { icon: normalPin });
            marker._proj = proj;
            allMarkers.push(marker);

            const popupContent = getPopupContent(proj);

            if (!isTouchDevice) {
                marker.bindPopup(popupContent, {
                    closeButton: false,
                    autoClose: false,
                    closeOnClick: false
                });

                marker.on('mouseover', function () {
                    allMarkers.forEach(m => {
                        if (m._proj.id === this._proj.id) {
                            m.setIcon(highlightPin);
                            m.setZIndexOffset(1000);
                        }
                    });
                    clearTimeout(this._closeTimeout);
                    this.openPopup();
                    setTimeout(() => {
                        const popupEl = this.getPopup()?.getElement();
                        if (popupEl) {
                            popupEl.onmouseenter = () => clearTimeout(this._closeTimeout);
                            popupEl.onmouseleave = () => {
                                this.closePopup();
                                allMarkers.forEach(m => {
                                    if (m._proj.id === this._proj.id) m.setIcon(normalPin);
                                });
                            };
                        }
                    }, 10);
                });

                marker.on('mouseout', function () {
                    this._closeTimeout = setTimeout(() => {
                        this.closePopup();
                        allMarkers.forEach(m => {
                            if (m._proj.id === this._proj.id) m.setIcon(normalPin);
                        });
                    }, 50);
                });
            } else {
                marker.bindPopup(popupContent, { closeButton: true, autoClose: true });
            }

            markersCluster.addLayer(marker);
            heatPoints.push([loc.lat, loc.lng, 1.0]);
        });
    });
    updateHeatmap(heatPoints);
}

// ========= CONTENU DE LA POPUP (modifié) =========
function getPopupContent(proj) {
    const massifs = proj.massifs?.join(', ') || '—';
    const disciplines = proj.disciplines?.join(', ') || '—';
    const partenaires = proj.partenaires?.join(', ') || '—';
    const financeurs = proj.financeurs?.join(', ') || '—';
    const pays = proj.pays?.join(', ') || '—';
    const coordonnateur = proj.porteurPrincipal || '—';
    const structure = proj.structureRattachement || '—';
    const sitePorteur = proj.siteWebPorteur;

    const locs = proj.localisations?.length 
        ? `<div style="margin-top: 10px;"><strong>Localisation(s) :</strong><ul style="margin:5px 0; padding-left:0; list-style:none;">${proj.localisations.map(l => 
            `<li style="margin-bottom: 4px;">
                <i class="fa-solid fa-location-dot" style="color:var(--vert); margin-right:6px;"></i>
                ${l.nom} <span style="color:#666; font-size:0.8rem;">(${l.lat.toFixed(2)}N, ${l.lng.toFixed(2)}E)</span>
            </li>`).join('')}</ul></div>` 
        : '';

    const siteWebHtml = proj.siteWeb 
        ? `<p style="margin: 8px 0;"><strong>Site web / page du projet :</strong> <a href="${proj.siteWeb}" target="_blank" style="color:var(--vert); text-decoration:underline;">${proj.siteWeb}</a></p>` 
        : '';

    const sitePorteurHtml = sitePorteur
        ? `<p style="margin: 4px 0;"><strong>Site du porteur :</strong> <a href="${sitePorteur}" target="_blank" style="color:var(--vert); text-decoration:underline;">${sitePorteur}</a></p>`
        : '';

    return `
        <div style="min-width:260px; max-width:320px; font-size: 0.9rem; line-height: 1.4;">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px; border-bottom:1px solid #eee; padding-bottom:10px;">
                <i class="fa-solid fa-map-pin" style="font-size:1.8rem; color:var(--vert);"></i>
                <div>
                    <h3 style="margin:0; font-size:1rem; color:var(--vert-dark);">${proj.titre}</h3>
                    <span style="font-size:0.8rem; color:var(--muted); font-weight:bold;">${proj.acronyme || ''}</span>
                </div>
            </div>
            
            <p style="margin: 4px 0;"><strong>Massif(s) :</strong> ${massifs}</p>
            <p style="margin: 4px 0;"><strong>Coordonnateur :</strong> ${coordonnateur}</p>
            <p style="margin: 4px 0;"><strong>Structure de rattachement :</strong> ${structure}</p>
            ${sitePorteurHtml}
            <p style="margin: 4px 0;"><strong>Discipline(s) :</strong> ${disciplines}</p>
            <p style="margin: 4px 0;"><strong>Partenaire(s) :</strong> ${partenaires}</p>
            <p style="margin: 4px 0;"><strong>Financeur(s) :</strong> ${financeurs}</p>
            <p style="margin: 4px 0;"><strong>Pays des partenaires :</strong> ${pays}</p>
            <p style="margin: 4px 0;"><strong>Période :</strong> ${proj.dateDebut?.getFullYear()||'?'} – ${proj.dateFin?.getFullYear()||'?'}</p>
            
            <div style="margin: 10px 0; font-size: 0.85rem;">
                <strong>Objectif principal :</strong><br>${proj.objectifPrincipal || '—'}
            </div>
            
            ${siteWebHtml}
            ${locs}
        </div>
    `;
}

// ========= HEATMAP (inchangé) =========
function updateHeatmap(points) {
  if (heatLayer) { map.removeLayer(heatLayer); heatLayer = null; }
  if (heatmapActive && points.length > 0) {
    heatLayer = L.heatLayer(points, {
      radius: 35, blur: 20, maxZoom: 10,
      gradient: { 0.2: '#FFEDA0', 0.4: '#FEB24C', 0.6: '#F03B20', 0.8: '#BD0026' }
    });
    heatLayer.addTo(map);
  }
}

// ========= ÉVÉNEMENTS GÉNÉRAUX =========
function setupEventListeners() {
  document.getElementById('yearSelect').addEventListener('change', e => {
    activeYear = parseInt(e.target.value);
    applyFilters();
  });

  document.getElementById('resetFiltersBtn').addEventListener('click', () => {
    activeFilters = {
      massifs: new Set(), disciplines: new Set(), partenaires: new Set(),
      statuts: new Set(), pays: new Set(), financeurs: new Set(), motsCles: new Set(),
      porteurs: new Set()
    };
    const currentYear = new Date().getFullYear();
    const select = document.getElementById('yearSelect');
    const options = Array.from(select.options).map(o => parseInt(o.value));
    activeYear = options.includes(currentYear) ? currentYear : options[0] || currentYear;
    select.value = activeYear;
    document.getElementById('searchInput').value = '';
    updateFilterUI();
    applyFilters();
    map.setView(initialView.center, initialView.zoom);
  });

  const heatmapBtn = document.getElementById('toggleHeatmapBtn');
  if (heatmapBtn) {
    heatmapBtn.addEventListener('click', () => {
      heatmapActive = !heatmapActive;
      heatmapBtn.classList.toggle('cimes-btn-active-heatmap', heatmapActive);
      heatmapBtn.innerHTML = heatmapActive
        ? '<i class="fa-solid fa-fire"></i> Carte de chaleur (ON)'
        : '<i class="fa-solid fa-fire"></i> Carte de chaleur';
      renderMarkersAndHeat();
    });
  }

  document.getElementById('closeSidebarBtn')?.addEventListener('click', () => document.getElementById('sidebar').classList.remove('cimes-sidebar-open'));
  document.getElementById('openSidebarBtn')?.addEventListener('click', () => document.getElementById('sidebar').classList.add('cimes-sidebar-open'));

  document.querySelectorAll('.cimes-filter-header').forEach(header => {
    header.addEventListener('click', () => header.closest('.cimes-filter-group').classList.toggle('cimes-filter-group-open'));
  });

  document.getElementById('searchInput').addEventListener('input', applyFilters);
}

// ========= SÉLECTEUR DE FOND DE CARTE =========
function setupBaseLayerSwitcher() {
  const select = document.getElementById('baseLayerSelect');
  if (!select) return;
  const layers = {
    topo: window.topoLayer,
    satellite: window.satLayer,
    osm: window.osmLayer
  };
  select.addEventListener('change', e => {
    const chosen = e.target.value;
    for (let key in layers) map.removeLayer(layers[key]);
    map.addLayer(layers[chosen]);
  });
}

// ========= LOCALISATION =========
function setupLocateButton() {
  document.getElementById('locateBtn').addEventListener('click', () => {
    map.locate({ setView: true, maxZoom: 12 });
  });
}
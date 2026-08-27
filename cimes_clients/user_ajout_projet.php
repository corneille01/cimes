<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../cimes_clients/espace_personnel.php");
    exit();
}
?>
<?php include('include/head.html') ?>
<?php include('include/header.html') ?>

<title>Ajouter votre projet</title>
<meta name="description" content="Formulaire d'ajout d'un projet - espace utilisateur">



</head>

<body>

    <div class="form-wrapper">
        <h1 class="form-page-title">Ajouter votre projet</h1>
        <p class="form-page-sub">Saisissez les informations de votre projet. Les localisations peuvent être pointées sur une carte.</p>

        <!-- Informations générales -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-file-lines"></i> Informations générales</p>
            <div class="form-row">
                <div class="form-group">
                    <label for="titre">Nom du projet <span class="required">*</span></label>
                    <input type="text" class="form-control" id="titre" placeholder="Nom complet du projet">
                </div>
                <div class="form-group">
                    <label for="acronyme">Acronyme</label>
                    <input type="text" class="form-control" id="acronyme" placeholder="ex. BIODIVALP">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="financeur">Financeurs</label>
                    <input type="text" class="form-control" id="financeur" placeholder="ex. ANR, Europe...">
                </div>
                <div> <!-- colonne de droite : Coordonnateur + Structure empilés -->
                    <div class="form-group">
                        <label for="porteur_principal">Coordonnateur</label>
                        <input type="text" class="form-control" id="porteur_principal" placeholder="ex. CNRS – Dr. Dupont">
                    </div>
                    <div class="form-group">
                        <label for="structure_rattachement">Structure de rattachement</label>
                        <input type="text" class="form-control" id="structure_rattachement" placeholder="ex. CNRS – Dr. Dupont">
                    </div>
                    <div class="form-group">
                        <label for="site_web_porteur">Site web du porteur</label>
                        <input type="url" class="form-control" id="site_web_porteur" placeholder="https://...">
                    </div>

                </div>
            </div>
            <div class="form-row full">
                <div class="form-group">
                    <label for="mots_cles">Mots-clés (Maximum 3, séparés par des virgules)</label>
                    <input type="text" class="form-control" id="mots_cles" placeholder="ex. BIODIVALP, écologie, Alpes">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="partenaires">Partenaires (séparés par des virgules)</label>
                    <input type="text" class="form-control" id="partenaires" placeholder="ex. INRAE, Univ. Grenoble">
                </div>
                <div class="form-group">
                    <label for="disciplines">Disciplines (séparées par des virgules)</label>
                    <input type="text" class="form-control" id="disciplines" placeholder="ex. Écologie, Climatologie">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="pays">Pays des partenaires (séparés par des virgules si plusieurs)</label>
                    <input type="text" class="form-control" id="pays" placeholder="ex. France">
                </div>
                <div class="form-group">
                    <label for="site_web">Site web ou page du projet</label>
                    <input type="url" class="form-control" id="site_web" placeholder="https://...">
                </div>
            </div>
            <div class="form-row full">
                <div class="form-group">
                    <label for="objectif_principal">Objectif principal du projet / Description</label>
                    <textarea class="form-control" id="objectif_principal" rows="4" placeholder="Résumé du projet..."></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input type="date" class="form-control" id="date_debut">
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" class="form-control" id="date_fin">
                </div>
            </div>
        </div>

        <!-- Localisations -->
        <div class="form-card">
            <p class="form-card__title"><i class="fa-solid fa-map-location-dot"></i> Localisations et massif</p>
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="massif">Massif(s) (séparés par des virgules)</label>
                <input type="text" class="form-control" id="massif" placeholder="ex. Alpes, Pyrénées">
            </div>

            <!-- 📌 TEXTE EXPLICATIF AJOUTÉ -->
            <p class="field-hint" style="margin-bottom: 12px;">
                <i class="fa-solid fa-circle-info"></i>
                Ajoutez un ou plusieurs sites concernés par le projet.
                Utilisez le bouton <i class="fa-solid fa-map"></i> pour pointer chaque lieu sur une carte.
            </p>

            <div id="localisationsContainer"></div>
            <button type="button" class="btn-add-loc" id="addLocalisationBtn">
                <i class="fa-solid fa-plus"></i> Ajouter une localisation
            </button>
        </div>

        <div class="form-actions" style="display:flex; justify-content:flex-end; gap:12px; margin-top:8px;">
            <a href="javascript:history.back();" class="btn-cancel">Annuler</a>
            <a href="#" class="btn-submit" id="envoyer">
                <i class="fa-solid fa-floppy-disk"></i> Ajouter le projet
            </a>
        </div>
        <div id="erreur" class="alerte"></div>
    </div>

    <!-- Modale carte -->
    <div class="map-modal" id="mapModal">
        <div class="map-modal-content">
            <button class="btn-close-map" id="closeMapModal">&times;</button>
            <h3 style="margin-top:0;">Rechercher un lieu ou cliquer sur la carte</h3>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Chercher une adresse..." autocomplete="off">
                <button type="button" id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></button>
                <div id="searchSuggestions"></div>
            </div>
            <div id="localisationMap"></div>
            <p style="margin:8px 0;"><strong>Lat :</strong> <span id="selectedLat">--</span> <strong>Lon :</strong> <span id="selectedLon">--</span></p>
            <button class="btn-submit" id="validateCoords">Valider ces coordonnées</button>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // ==================== BASE DE DONNÉES DES MASSIFS (GeoJSON) ====================
        const MASSIFS_GEOJSON = {
            "type": "FeatureCollection",
            "features": [{
                    "type": "Feature",
                    "id": "Pyrénées",
                    "properties": {
                        "nom": "Pyrénées"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [-1.9, 43.2],
                                [0.0, 43.4],
                                [2.0, 42.8],
                                [3.3, 42.4],
                                [2.6, 42.1],
                                [0.5, 42.5],
                                [-1.5, 42.7],
                                [-1.9, 43.2]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Alpes",
                    "properties": {
                        "nom": "Alpes"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [4.8, 46.0],
                                [8.0, 46.0],
                                [8.0, 43.8],
                                [4.8, 43.8],
                                [4.8, 46.0]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Massif Central",
                    "properties": {
                        "nom": "Massif Central"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [1.8, 46.2],
                                [4.2, 46.2],
                                [4.2, 44.0],
                                [1.8, 44.0],
                                [1.8, 46.2]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Cévennes",
                    "properties": {
                        "nom": "Cévennes"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [2.8, 44.7],
                                [4.3, 44.7],
                                [4.3, 43.8],
                                [2.8, 43.8],
                                [2.8, 44.7]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Mercantour",
                    "properties": {
                        "nom": "Mercantour"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [6.7, 44.4],
                                [7.6, 44.4],
                                [7.6, 43.85],
                                [6.7, 43.85],
                                [6.7, 44.4]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Écrins",
                    "properties": {
                        "nom": "Écrins"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [6.0, 45.2],
                                [6.8, 45.2],
                                [6.8, 44.7],
                                [6.0, 44.7],
                                [6.0, 45.2]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Ventoux",
                    "properties": {
                        "nom": "Mont Ventoux"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [5.1, 44.3],
                                [5.5, 44.3],
                                [5.5, 44.1],
                                [5.1, 44.1],
                                [5.1, 44.3]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Luberon",
                    "properties": {
                        "nom": "Luberon"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [5.0, 43.9],
                                [5.6, 43.9],
                                [5.6, 43.65],
                                [5.0, 43.65],
                                [5.0, 43.9]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Sainte-Victoire",
                    "properties": {
                        "nom": "Sainte-Victoire"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [5.5, 43.6],
                                [5.8, 43.6],
                                [5.8, 43.5],
                                [5.5, 43.5],
                                [5.5, 43.6]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Alpilles",
                    "properties": {
                        "nom": "Alpilles"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [4.7, 43.8],
                                [5.0, 43.8],
                                [5.0, 43.6],
                                [4.7, 43.6],
                                [4.7, 43.8]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Maures",
                    "properties": {
                        "nom": "Massif des Maures"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [6.3, 43.5],
                                [6.8, 43.5],
                                [6.8, 43.2],
                                [6.3, 43.2],
                                [6.3, 43.5]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Estérel",
                    "properties": {
                        "nom": "Estérel"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [6.7, 43.5],
                                [7.0, 43.5],
                                [7.0, 43.3],
                                [6.7, 43.3],
                                [6.7, 43.5]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Corbières",
                    "properties": {
                        "nom": "Corbières"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [2.2, 43.1],
                                [2.8, 43.1],
                                [2.8, 42.8],
                                [2.2, 42.8],
                                [2.2, 43.1]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Canigou",
                    "properties": {
                        "nom": "Massif du Canigou"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [2.2, 42.7],
                                [2.6, 42.7],
                                [2.6, 42.4],
                                [2.2, 42.4],
                                [2.2, 42.7]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Montagne Noire",
                    "properties": {
                        "nom": "Montagne Noire"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [2.0, 43.5],
                                [2.5, 43.5],
                                [2.5, 43.3],
                                [2.0, 43.3],
                                [2.0, 43.5]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Aubrac",
                    "properties": {
                        "nom": "Aubrac"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [2.7, 44.7],
                                [3.3, 44.7],
                                [3.3, 44.4],
                                [2.7, 44.4],
                                [2.7, 44.7]
                            ]
                        ]
                    }
                },
                {
                    "type": "Feature",
                    "id": "Margeride",
                    "properties": {
                        "nom": "Margeride"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [
                            [
                                [3.0, 45.1],
                                [3.5, 45.1],
                                [3.5, 44.8],
                                [3.0, 44.8],
                                [3.0, 45.1]
                            ]
                        ]
                    }
                }
            ]
        };

        const massifCache = {};

        function pointInPolygon(lat, lng, polygon) {
            let inside = false;
            for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                const xi = polygon[i][0],
                    yi = polygon[i][1];
                const xj = polygon[j][0],
                    yj = polygon[j][1];
                const intersect = ((yi > lat) != (yj > lat)) && (lng < (xj - xi) * (lat - yi) / (yj - yi) + xi);
                if (intersect) inside = !inside;
            }
            return inside;
        }

        function trouverMassif(lat, lng) {
            const key = `${lat.toFixed(4)},${lng.toFixed(4)}`;
            if (massifCache[key] !== undefined) return massifCache[key];
            let found = null;
            for (const feature of MASSIFS_GEOJSON.features) {
                const coords = feature.geometry.coordinates[0];
                if (pointInPolygon(lat, lng, coords)) {
                    found = feature.properties.nom;
                    break;
                }
            }
            massifCache[key] = found;
            return found;
        }

        function synchroniserMassifs() {
            const rows = document.querySelectorAll('.localisation-row');
            const massifsSet = new Set();
            rows.forEach(row => {
                const latInput = row.querySelector('.loc-lat');
                const lngInput = row.querySelector('.loc-lng');
                if (latInput && lngInput) {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const massif = trouverMassif(lat, lng);
                        if (massif) massifsSet.add(massif);
                    }
                }
            });
            const massifsList = Array.from(massifsSet).sort();
            const massifField = document.getElementById('massif');
            if (massifField) massifField.value = massifsList.join(', ');
        }

        // ==================== GESTION DES LOCALISATIONS ====================
        let localisationCounter = 0;
        const container = document.getElementById('localisationsContainer');
        const addBtn = document.getElementById('addLocalisationBtn');

        function createLocalisationRow(nom = '', lat = '', lng = '') {
            const row = document.createElement('div');
            row.className = 'localisation-row';
            row.innerHTML = `
                <input type="text" class="form-control loc-nom" placeholder="Nom du site" value="${nom.replace(/"/g, '&quot;')}">
                <input type="text" class="form-control loc-lat" placeholder="Latitude" value="${lat}" readonly>
                <input type="text" class="form-control loc-lng" placeholder="Longitude" value="${lng}" readonly>
                <button type="button" class="btn-map"><i class="fa-solid fa-map"></i> choisir le point sur la carte </button>
                <button type="button" class="btn-remove-loc"><i class="fa-solid fa-trash"></i></button>
            `;
            row.querySelector('.btn-remove-loc').addEventListener('click', () => {
                if (container.children.length > 1 || localisationCounter > 0) {
                    row.remove();
                    localisationCounter--;
                    synchroniserMassifs();
                }
            });
            row.querySelector('.btn-map').addEventListener('click', () => {
                const nomInput = row.querySelector('.loc-nom');
                const latInput = row.querySelector('.loc-lat');
                const lngInput = row.querySelector('.loc-lng');
                openMapModal(latInput, lngInput, nomInput);
            });
            container.appendChild(row);
            localisationCounter++;
            synchroniserMassifs();
        }

        if (container.children.length === 0) createLocalisationRow();
        addBtn.addEventListener('click', () => createLocalisationRow());

        // ==================== CARTE MODALE ====================
        let mapModalInstance = null;
        let currentLatInput, currentLngInput, currentNomInput;
        let selectedMarker = null;
        let debounceTimer = null;

        function openMapModal(latInput, lngInput, nomInput) {
            currentLatInput = latInput;
            currentLngInput = lngInput;
            currentNomInput = nomInput;
            document.getElementById('mapModal').classList.add('active');
            document.getElementById('searchInput').value = '';
            document.getElementById('searchSuggestions').style.display = 'none';
            document.getElementById('selectedLat').textContent = '--';
            document.getElementById('selectedLon').textContent = '--';

            if (!mapModalInstance) {
                mapModalInstance = L.map('localisationMap').setView([44.5, 3.5], 6);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(mapModalInstance);

                mapModalInstance.on('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    reverseGeocode(lat, lng, function(displayName) {
                        if (selectedMarker) mapModalInstance.removeLayer(selectedMarker);
                        selectedMarker = L.marker([lat, lng]).addTo(mapModalInstance);
                        selectedMarker.bindPopup(displayName || 'Position sélectionnée').openPopup();
                        document.getElementById('selectedLat').textContent = lat.toFixed(5);
                        document.getElementById('selectedLon').textContent = lng.toFixed(5);
                        if (currentNomInput) currentNomInput.value = displayName || '';
                    });
                    if (currentLatInput) currentLatInput.value = lat;
                    if (currentLngInput) currentLngInput.value = lng;
                    synchroniserMassifs();
                });
            } else {
                if (currentLatInput.value && currentLngInput.value) {
                    const lat = parseFloat(currentLatInput.value);
                    const lng = parseFloat(currentLngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) mapModalInstance.setView([lat, lng], 10);
                    else mapModalInstance.setView([44.5, 3.5], 6);
                } else {
                    mapModalInstance.setView([44.5, 3.5], 6);
                }
                if (selectedMarker) mapModalInstance.removeLayer(selectedMarker);
                selectedMarker = null;
            }
            setTimeout(() => mapModalInstance.invalidateSize(), 100);
        }

        document.getElementById('closeMapModal').addEventListener('click', () => {
            document.getElementById('mapModal').classList.remove('active');
        });
        document.getElementById('validateCoords').addEventListener('click', () => {
            const latSpan = document.getElementById('selectedLat').textContent;
            const lngSpan = document.getElementById('selectedLon').textContent;
            if (latSpan !== '--' && lngSpan !== '--') {
                currentLatInput.value = latSpan;
                currentLngInput.value = lngSpan;
                synchroniserMassifs();
            }
            document.getElementById('mapModal').classList.remove('active');
        });

        // ==================== RECHERCHE D'ADRESSES ====================
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const searchSuggestions = document.getElementById('searchSuggestions');

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            if (query.length < 2) {
                searchSuggestions.style.display = 'none';
                return;
            }
            debounceTimer = setTimeout(() => {
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
                    .then(r => r.json())
                    .then(data => {
                        searchSuggestions.innerHTML = '';
                        if (data.length === 0) searchSuggestions.innerHTML = '<div style="padding:8px;">Aucun résultat</div>';
                        else data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.textContent = item.display_name;
                            div.addEventListener('click', () => selectLocation(parseFloat(item.lat), parseFloat(item.lon), item.display_name));
                            searchSuggestions.appendChild(div);
                        });
                        searchSuggestions.style.display = 'block';
                    })
                    .catch(() => {
                        searchSuggestions.innerHTML = '<div style="padding:8px;color:red;">Erreur</div>';
                        searchSuggestions.style.display = 'block';
                    });
            }, 300);
        });

        searchBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length < 2) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(r => r.json())
                .then(data => {
                    if (data.length > 0) selectLocation(parseFloat(data[0].lat), parseFloat(data[0].lon), data[0].display_name);
                    else alert('Aucun lieu trouvé.');
                });
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) searchSuggestions.style.display = 'none';
        });

        function selectLocation(lat, lng, displayName) {
            if (currentNomInput) currentNomInput.value = displayName || '';
            placeMarker(lat, lng, displayName);
            document.getElementById('selectedLat').textContent = lat.toFixed(5);
            document.getElementById('selectedLon').textContent = lng.toFixed(5);
            mapModalInstance.setView([lat, lng], 14);
            searchSuggestions.style.display = 'none';
            searchInput.value = '';
            if (currentLatInput) currentLatInput.value = lat;
            if (currentLngInput) currentLngInput.value = lng;
            synchroniserMassifs();
        }

        function placeMarker(lat, lng, popupText) {
            if (selectedMarker) mapModalInstance.removeLayer(selectedMarker);
            selectedMarker = L.marker([lat, lng]).addTo(mapModalInstance);
            if (popupText) selectedMarker.bindPopup(popupText).openPopup();
        }

        function reverseGeocode(lat, lng, callback) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(r => r.json())
                .then(data => callback(data && data.display_name ? data.display_name : 'Position sélectionnée'))
                .catch(() => callback('Position sélectionnée'));
        }

        // ==================== EXPOSITION POUR creer_modif_projet.js ====================
        window.getFormData = function() {
            const localisations = [];
            document.querySelectorAll('.localisation-row').forEach(row => {
                const nom = row.querySelector('.loc-nom').value.trim();
                const lat = parseFloat(row.querySelector('.loc-lat').value);
                const lng = parseFloat(row.querySelector('.loc-lng').value);
                if (nom && !isNaN(lat) && !isNaN(lng)) localisations.push({
                    nom,
                    lat,
                    lng
                });
            });
            return {
                titre: document.getElementById('titre').value.trim(),
                acronyme: document.getElementById('acronyme').value.trim(),
                financeur: document.getElementById('financeur').value.trim(),
                porteur_principal: document.getElementById('porteur_principal').value.trim(),
                structure_rattachement: document.getElementById('structure_rattachement').value.trim(),
                mots_cles: document.getElementById('mots_cles').value.trim(),
                partenaires: document.getElementById('partenaires').value.trim(),
                disciplines: document.getElementById('disciplines').value.trim(),
                massif: document.getElementById('massif').value.trim(),
                pays: document.getElementById('pays').value.trim(),
                objectif_principal: document.getElementById('objectif_principal').value.trim(),
                date_debut: document.getElementById('date_debut').value,
                date_fin: document.getElementById('date_fin').value,
                site_web: document.getElementById('site_web').value.trim(),
                site_web_porteur: document.getElementById('site_web_porteur').value.trim(),
                localisations
            };
        };

        window.resetForm = function() {
            document.getElementById('titre').value = '';
            document.getElementById('acronyme').value = '';
            document.getElementById('financeur').value = '';
            document.getElementById('porteur_principal').value = '';
            document.getElementById('structure_rattachement').value = '';
            document.getElementById('mots_cles').value = '';
            document.getElementById('partenaires').value = '';
            document.getElementById('disciplines').value = '';
            document.getElementById('massif').value = '';
            document.getElementById('pays').value = '';
            document.getElementById('objectif_principal').value = '';
            document.getElementById('date_debut').value = '';
            document.getElementById('date_fin').value = '';
            document.getElementById('site_web').value = '';
            document.getElementById('site_web_porteur').value = '';
            container.innerHTML = '';
            localisationCounter = 0;
            createLocalisationRow();
        };

        let lien = 'cree_projet';
    </script>
    <script src="../cimes_admin/js/creer_modif_projet.js" async defer></script>
</body>

</html>
<?php
session_start();
// Pas de restriction d'accès ici (carte publique)
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>CIMES · Cartographie des projets de recherche</title>
    <link rel="stylesheet" href="css/style.css" />
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        /* ============================
            VARIABLES GLOBALES
        ============================ */
        :root {
            --vert: #0F766E;
            --vert-dark: #134e4a;
            --vert-light: #E1F5EE;
            --vert-mid: #9FE1CB;
            --bg: #f0f4f3;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text: #1e293b;
            --muted: #64748b;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow: hidden;
            background: var(--bg);
            color: var(--text);
        }

        .cimes-app {
            display: flex;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        .cimes-sidebar {
            position: relative;
            width: 340px;
            height: 100%;
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            transform: none !important;
            display: flex;
            flex-direction: column;
        }

        .cimes-sidebar.cimes-sidebar-open {
            transform: translateX(0);
        }

        .cimes-sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px 8px;
            border-bottom: 1px solid var(--border);
        }

        .cimes-back-btn {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .cimes-back-btn:hover {
            color: var(--vert);
        }

        .cimes-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
            font-size: 0.2rem;
        }

        .cimes-logo-icon {
            font-size: 1.9rem;
            color: var(--vert);
        }

        .cimes-logo h1 {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text);
        }

        .cimes-logo h1 span {
            font-weight: normal;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .cimes-toggle-sidebar:hover {
            color: var(--vert);
        }

        .cimes-sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 12px 20px 16px;
        }

        .cimes-search-box {
            position: relative;
            margin-bottom: 16px;
        }

        .cimes-search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 0.9rem;
        }

        .cimes-search-box input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.9rem;
            background: var(--surface);
            outline: none;
            transition: border 0.2s;
        }

        .cimes-search-box input:focus {
            border-color: var(--vert);
        }

        .cimes-stats-row {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .cimes-stat-card {
            flex: 1;
            background: var(--vert-light);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--vert-dark);
        }

        .cimes-stat-number {
            display: block;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .cimes-filters-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 16px;
        }

        .cimes-filter-group {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            background: var(--surface);
        }

        .cimes-filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: #f8fafc;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
            gap: 8px;
        }

        .cimes-filter-header i {
            color: var(--vert);
            font-size: 0.85rem;
        }

        .cimes-filter-header .cimes-arrow {
            transition: transform 0.2s;
            margin-left: auto;
        }

        .cimes-filter-group-open .cimes-filter-header .cimes-arrow {
            transform: rotate(180deg);
        }

        .cimes-filter-body {
            display: none;
            padding: 8px 12px 10px;
        }

        .cimes-filter-group-open .cimes-filter-body {
            display: block;
        }

        .cimes-tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .cimes-tag {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            user-select: none;
        }

        .cimes-tag:hover {
            background: var(--vert);
            border-color: var(--vert);
            color: white;
        }

        .cimes-tag-active {
            background: var(--vert);
            color: white;
            border-color: var(--vert);
        }

        .cimes-slider-group {
            margin-bottom: 16px;
        }

        .cimes-slider-group label {
            display: block;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .cimes-slider-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
        }

        .cimes-btn {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .cimes-btn-primary {
            background: var(--vert);
            color: white;
        }

        .cimes-btn-primary:hover {
            background: var(--vert-dark);
        }

        .cimes-sidebar-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--muted);
        }

        .cimes-map-container {
            flex: 1;
            position: relative;
            height: 100%;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .cimes-recenter-btn,
        .cimes-locate-btn {
            position: absolute;
            top: 12px;
            z-index: 1000;
            background: white;
            border: 2px solid var(--vert);
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 1rem;
            color: var(--vert);
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .cimes-recenter-btn {
            right: 12px;
        }

        .cimes-locate-btn {
            right: 56px;
        }

        .cimes-recenter-btn:hover,
        .cimes-locate-btn:hover {
            background: var(--vert-light);
        }

        .cimes-base-select {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 0.8rem;
        }

        .cimes-layer-control {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            padding: 6px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            font-size: 0.85rem;
        }

        .cimes-layer-control label {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .custom-pin {
            background: none;
            border: none;
        }

        .marker-cluster {
            background: transparent !important;
        }

        .marker-cluster div {
            background: rgba(15, 118, 110, 0.85) !important;
            border-radius: 30px;
            color: white;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 2px !important;
        }

        .leaflet-control-attribution,
        .cimes-legend {
            display: none !important;
        }

        @media (max-width: 900px) {
            .cimes-app {
                flex-direction: column;
            }

            .cimes-sidebar {
                width: 100%;
                height: 300px;
            }
        }

        @media (max-width: 600px) {
            .cimes-sidebar {
                width: 280px;
            }
        }
    </style>
</head>

<body>
    <div class="cimes-app">
        <!-- SIDEBAR GAUCHE -->
        <aside class="cimes-sidebar" id="sidebar">
            <div class="cimes-sidebar-header">
                <a href="javascript:history.back()" class="cimes-back-btn" title="Retour"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="cimes-logo">
                    <i class="fa-solid fa-mountain cimes-logo-icon"></i>
                    <h1>Cartographie des projets des membres du CIMES</h1>
                </div>
                <button class="cimes-toggle-sidebar" id="closeSidebarBtn">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
            </div>
            <div class="cimes-sidebar-content">
                <!-- Recherche -->
                <div class="cimes-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Rechercher un projet, discipline, partenaire...">
                </div>
                <!-- Stats -->
                <div class="cimes-stats-row">
                    <div class="cimes-stat-card">
                        <span class="cimes-stat-number" id="statProjects">0</span><span>Projets</span>
                    </div>
                    <div class="cimes-stat-card">
                        <span class="cimes-stat-number" id="statMassifs">0</span><span>Massifs</span>
                    </div>
                </div>
                <!-- Filtres -->
                <div class="cimes-filters-section">
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-tags"></i> Mots-clés <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="motsClesFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-mountain"></i> Massifs <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="massifsFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-flask"></i> Disciplines <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="disciplinesFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-handshake"></i> Partenaires <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="partenairesFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-building-columns"></i> Financeurs <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="financeursFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-user"></i> Porteurs de projet <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="porteursFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-earth-europe"></i> Pays des partenaires <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="paysFilter"></div>
                    </div>
                    <div class="cimes-filter-group">
                        <div class="cimes-filter-header"><i class="fa-solid fa-circle-info"></i> Statut <i class="fa-solid fa-chevron-down cimes-arrow"></i></div>
                        <div class="cimes-filter-body" id="statutsFilter"></div>
                    </div>
                </div>
                <div class="cimes-slider-group">
                    <label for="yearSelect">Année</label>
                    <select id="yearSelect"></select>
                </div>
                <button class="cimes-btn cimes-btn-primary" id="resetFiltersBtn"><i class="fa-solid fa-arrows-spin"></i> Réinitialiser</button>
            </div>
            <div class="cimes-sidebar-footer">
                <span>© CIMES 2026</span>
                <i class="fa-solid fa-leaf"></i>
            </div>
        </aside>

        <button class="cimes-floating-open" id="openSidebarBtn"><i class="fa-solid fa-bars"></i></button>

        <!-- CARTE -->
        <main class="cimes-map-container">
            <div id="map"></div>
            <button id="recenterBtn" class="cimes-recenter-btn" title="Recentrer la carte"><i class="fa-solid fa-location-arrow"></i></button>
            <button id="locateBtn" class="cimes-locate-btn" title="Ma position"><i class="fa-solid fa-crosshairs"></i></button>

            <!-- Sélecteur de fond de carte -->
            <select id="baseLayerSelect" class="cimes-base-select">
                <option value="topo">Topographique</option>
                <option value="satellite">Satellite</option>
                <option value="osm">OpenStreetMap</option>
            </select>

            <div class="cimes-layer-control">
                <label><input type="checkbox" id="toggleClusters" checked> Projets</label>
            </div>
        </main>
    </div>

    <!-- Scripts externes -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script>
        const BASE_URL = "<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/'; ?>";
    </script>
    <script src="js/projets.js"></script>
</body>

</html>
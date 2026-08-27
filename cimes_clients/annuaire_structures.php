<?php include('include/head.html') ?>

<title>Annuaire des structures</title>
<meta name="description" content="Annuaire des structures du réseau">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>

<body>
    <?php include('include/header.html') ?>

    <section class="annuaire-structure_hero">
        <div class="annuaire-hero__inner">
            <h1><strong>Annuaire des structures académiques</strong></h1>
            <p class="annuaire-hero__sub">Retrouvez les structures du réseau.</p>
        </div>
    </section>

    <div class="annuaire-controls">
        <div class="annuaire-controls__bar">
            <div class="search-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input class="search-input" type="search" id="search" placeholder="Rechercher un nom, établissement…" autocomplete="off">
            </div>
            <div class="ctrl-divider"></div>
            <select class="ctrl-select" id="filter-discipline">
                <option value="">Toutes les disciplines</option>
            </select>
            <select class="ctrl-select" id="sort">
                <option value="name_asc">Nom A → Z</option>
                <option value="name_desc">Nom Z → A</option>

                <option value="etablissement">Établissement</option>
            </select>
            <div class="ctrl-divider"></div>
            <div class="geo-wrap" id="geo-wrap">
                <span class="geo-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </span>
                <input class="search-input" type="text" id="geo-input" placeholder="Ville, code postal…" autocomplete="off">
                <button class="geo-clear" id="geo-clear" title="Effacer" aria-label="Effacer le filtre géographique">×</button>
                <ul class="geo-dropdown" id="geo-dropdown" role="listbox"></ul>
            </div>
        </div>
    </div>

    <div class="annuaire-meta">
        <p class="annuaire-meta__count">
            <strong id="count">0</strong> membre<span id="plural"></span> affiché<span id="plural2"></span>
            <span id="total-label"></span>
        </p>
        <div class="view-toggle">
            <button class="view-btn active" id="btn-grid" title="Vue grille" aria-label="Vue grille">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                </svg>
            </button>
            <button class="view-btn" id="btn-list" title="Vue liste" aria-label="Vue liste">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6" />
                    <line x1="8" y1="12" x2="21" y2="12" />
                    <line x1="8" y1="18" x2="21" y2="18" />
                    <circle cx="3" cy="6" r="1.5" fill="currentColor" stroke="none" />
                    <circle cx="3" cy="12" r="1.5" fill="currentColor" stroke="none" />
                    <circle cx="3" cy="18" r="1.5" fill="currentColor" stroke="none" />
                </svg>
            </button>
        </div>
    </div>

    <section class="annuaire-grid" id="annuaire_structures" aria-label="Liste des structures">
        <div class="loader">
            <div class="loader__dot"></div>
            <div class="loader__dot"></div>
            <div class="loader__dot"></div>
        </div>
    </section>

    <script src="js/code_annuaire_structure.js"></script>
    <?php include('include/footer.html') ?>
</body>

</html>
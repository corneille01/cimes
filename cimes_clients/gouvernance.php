<?php include('include/head.html'); ?>
<title>Gouvernance du CIMeS</title>
<meta name="description" content="">
<style>
    /* ── TOUS LES STYLES (inchangés) ── */
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,100..900&display=swap');

    :root {
        --vert: #0F766E;
        --vert-dark: #134e4a;
        --vert-light: #E1F5EE;
        --vert-mid: #9FE1CB;
        --vert-text: #C9D95B;
        --bg: #f0f4f3;
        --surface: #ffffff;
        --border: #e2e8f0;
        --border-hover: #cbd5e1;
        --text: #1e293b;
        --muted: #64748b;
        --hint: #94a3b8;
        --danger: #8b6161;
        --radius: 2px;
        --radius-sm: 2px;
        --nav-height: 80px;
        --btn-h: 30px;
        --btn-min-w: 100px;
        --btn-font: 0.74rem;
        --btn-px: 12px;
        --cimes-bg: var(--vert-dark);
        --cimes-hover: var(--vert);
        --cimes-accent: var(--vert-mid);
        --cimes-light: var(--vert-light);
        --cimes-gray: var(--bg);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: var(--surface);
        color: var(--text);
        line-height: 1.6;
        overflow-x: hidden;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    #gov-page {
        display: block !important;
        width: 100%;
    }

    /* ── HERO ── */
    #gov-page .gov-hero {
        margin-top: 72px;
        position: relative !important;
        min-height: 420px;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-end !important;
        overflow: hidden !important;
        isolation: isolate;
        background: #072a28;
    }

    #gov-page .gov-hero__img {
        position: absolute !important;
        inset: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center center !important;
        z-index: 0 !important;
        display: block !important;
        opacity: 1 !important;
    }

    #gov-page .gov-hero::before {
        content: '';
        position: absolute !important;
        inset: 0 !important;
        z-index: 1 !important;
        background: linear-gradient(to top,
                color-mix(in srgb, var(--vert-dark) 97%, transparent) 0%,
                color-mix(in srgb, var(--vert-dark) 80%, transparent) 30%,
                color-mix(in srgb, var(--vert-dark) 40%, transparent) 62%,
                color-mix(in srgb, var(--vert-dark) 8%, transparent) 100%);
    }

    #gov-page .gov-hero__grid {
        position: absolute !important;
        inset: 0 !important;
        z-index: 2 !important;
        opacity: .035;
        background-image: linear-gradient(rgba(255, 255, 255, .8) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .8) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }

    #gov-page .gov-hero__content {
        position: relative !important;
        z-index: 3 !important;
        padding: 0 60px 60px;
    }

    #gov-page .gov-hero__h1 {
        font-size: clamp(46px, 7vw, 86px);
        color: #fff;
        line-height: 0.95;
        letter-spacing: -.02em;
        margin-bottom: 20px;
        margin-top: 0;
        font-weight: 700;
    }

    #gov-page .gov-hero__h1 em {
        font-style: italic;
        color: #9FE1CB;
        display: block;
        font-weight: 300;
    }

    /* ── NAV ── */
    #gov-page .gov-sitenav {
        position: static !important;
        background: #0F766E !important;
        display: flex !important;
        flex-wrap: wrap;
        padding: 0 60px !important;
        margin: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    #gov-page .gov-sitenav__a {
        font-size: 10.5px !important;
        font-weight: 700 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: rgba(255, 255, 255, 0.6) !important;
        text-decoration: none !important;
        padding: 15px 17px !important;
        border-bottom: 2.5px solid transparent !important;
        transition: all .2s !important;
        white-space: nowrap !important;
        display: inline-block !important;
    }

    #gov-page .gov-sitenav__a:hover,
    #gov-page .gov-sitenav__a--active {
        color: #fff !important;
        border-bottom-color: #C9D95B !important;
    }

    /* ── WRAPPER ── */
    .gov-wrapper {
        max-width: 100%;
        margin: 0 auto;
        padding: 72px 48px 110px;
    }

    /* ── SCHEMA ── */
    .schema-wrap {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: 2px;
        padding: 48px 52px;
        margin-bottom: 68px;
        position: relative;
        overflow: hidden;
    }

    .schema-title {
        font-size: 15px;
        font-weight: bold;
        letter-spacing: .2em;
        text-transform: uppercase;
        margin-bottom: 32px;
    }

    .schema-flow {
        display: flex;
        align-items: stretch;
        flex-wrap: wrap;
    }

    .schema-node {
        flex: 1;
        min-width: 180px;
        padding: 24px 22px;
        border-radius: 2px;
    }

    .schema-node.accent {
        background: linear-gradient(135deg, rgba(201, 217, 91, .09) 0%, var(--vert-light) 100%);
        border: 1.5px solid rgba(15, 118, 110, .18);
    }

    .schema-node-arrow {
        display: flex;
        align-items: center;
        padding: 0 16px;
        color: var(--vert-mid);
        font-size: 22px;
        flex-shrink: 0;
    }

    .schema-node-name {
        font-size: 20px;
        line-height: 1.2;
        margin-bottom: 9px;
        color: var(--text);
    }

    .schema-node-desc {
        font-size: 12px;
        line-height: 1.55;
        color: var(--muted);
    }

    .schema-freq {
        display: inline-block;
        margin-top: 10px;
        font-size: 10px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        background: rgba(15, 118, 110, .1);
        color: var(--vert);
    }

    /* ── SECTION HEADERS ── */
    .sec-head {
        margin-bottom: 8px;
        padding-bottom: 18px;
        border-bottom: 2px solid var(--border);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    .sec-title {
        font-size: clamp(28px, 3.2vw, 42px);
        color: var(--text);
        line-height: 1.05;
        font-weight: bolder;
    }

    .sec-ghost {
        font-size: 78px;
        color: rgba(15, 118, 110, .06);
        line-height: 1;
        user-select: none;
    }

    .sec-desc {
        font-size: 14.5px;
        font-weight: 300;
        color: var(--text);
        max-width: 700px;
        line-height: 1.7;
        margin: 18px 0 36px;
    }

    /* ── GRIDS ── */
    .gov-block {
        margin-bottom: 80px;
    }

    .direction-grid,
    .presidence-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .people-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    /* ════════════════ CARTE DIRECTION / PRÉSIDENCE ════════════════ */
    .dcard {
        background: var(--surface);
        border-radius: 2px;
        border: 1.5px solid var(--border);
        overflow: hidden;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        transition: transform .3s cubic-bezier(.25, .46, .45, .94), box-shadow .3s, border-color .3s;
    }

    .dcard:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(15, 118, 110, .14);
        border-color: var(--vert-mid);
    }

    .dcard.open {
        border-color: var(--vert);
        box-shadow: 0 12px 36px rgba(15, 118, 110, .15);
    }

    .dcard-banner {
        height: 88px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .dcard-banner.clr-gold {
        background: linear-gradient(135deg, #3d4e0a 0%, #6a7d12 50%, #9aad1a 100%);
    }

    .dcard-banner.clr-green {
        background: linear-gradient(135deg, var(--vert-dark) 0%, var(--vert) 60%, #1ca098 100%);
    }

    .dcard-banner.clr-sage {
        background: linear-gradient(135deg, #1a4d35 0%, #2d7a58 60%, #3ea07a 100%);
    }

    .dcard-banner.clr-presidence {
        background: linear-gradient(135deg, #0d5e4a, #0F766E, #1ca098);
    }

    .dcard-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(45deg, rgba(255, 255, 255, .04) 0px, rgba(255, 255, 255, .04) 1px, transparent 1px, transparent 18px);
    }

    .dcard-photo-wrap {
        display: flex;
        justify-content: center;
        margin-top: -46px;
        position: relative;
        z-index: 2;
        flex-shrink: 0;
    }

    .dcard-photo {
        width: 92px;
        height: 92px;
        border-radius: 50%;
        border: 4px solid var(--surface);
        object-fit: cover;
        object-position: center top;
        display: block;
        box-shadow: 0 4px 20px rgba(15, 118, 110, .22);
        background: var(--bg);
    }

    .dcard-badge {
        position: absolute;
        bottom: -2px;
        right: calc(50% - 60px);
        background: var(--vert);
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 20px;
        border: 2px solid var(--surface);
        white-space: nowrap;
    }

    .dcard-badge.gold {
        background: #7a8f10;
    }

    .dcard-badge.sage {
        background: #2d7a58;
    }

    .dcard-badge.presidence {
        background: #C9D95B;
        color: var(--vert-dark);
    }

    .dcard-body {
        padding: 14px 24px 20px;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .dcard-name {
        font-size: 20px;
        color: var(--text);
        line-height: 1.15;
        margin-bottom: 4px;
    }

    .dcard-role {
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: var(--vert);
        margin-bottom: 8px;
    }

    .dcard-domain {
        font-size: 12.5px;
        color: var(--muted);
        line-height: 1.5;
    }

    .dcard-affil {
        font-size: 11px;
        color: var(--hint);
        margin-top: 5px;
        font-style: italic;
    }

    .dcard-toggle {
        margin-top: auto;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--vert);
        border-top: 1px solid var(--border);
        padding: 14px 0 0;
        transition: color .2s;
    }

    .dcard-toggle svg {
        transition: transform .3s;
    }

    .dcard.open .dcard-toggle svg {
        transform: rotate(45deg);
    }

    .dcard-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height .5s cubic-bezier(.25, .46, .45, .94);
    }

    .dcard-panel.open {
        max-height: 700px;
    }

    .dcard-acc {
        border-top: 1.5px solid var(--border);
        background: linear-gradient(to bottom, var(--vert-light), rgba(225, 245, 238, .25));
        padding: 20px 24px 24px;
    }

    /* ════════════════ CARTE PERSONNES (CG, Bureau, CS, CO) ════════════════ */
    .pcard {
        background: var(--surface);
        border-radius: 2px;
        border: 1.5px solid var(--border);
        overflow: hidden;
        cursor: pointer;
        transition: transform .28s cubic-bezier(.25, .46, .45, .94), box-shadow .28s, border-color .28s;
    }

    .pcard:hover {
        border-color: var(--vert-mid);
        box-shadow: 0 8px 28px rgba(15, 118, 110, .11);
        transform: translateY(-3px);
    }

    .pcard.open {
        border-color: var(--vert);
        box-shadow: 0 8px 28px rgba(15, 118, 110, .12);
    }

    .pcard-top {
        padding: 20px 20px 16px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .av-wrap {
        flex-shrink: 0;
        position: relative;
    }

    .av-img {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center top;
        border: 3px solid var(--border);
        display: block;
        background: var(--bg);
        transition: border-color .25s;
    }

    .pcard:hover .av-img,
    .pcard.open .av-img {
        border-color: var(--vert-mid);
    }

    .av-dot {
        position: absolute;
        bottom: -1px;
        right: -1px;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        border: 2.5px solid var(--surface);
        background: var(--vert);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .av-dot.gold {
        background: #7a8f10;
    }

    .av-dot.sage {
        background: #2d7a58;
    }

    .av-dot.terra {
        background: #8a4a1a;
    }

    .av-dot.plum {
        background: #5a3a7a;
    }

    .av-dot.teal {
        background: #1a6a6a;
    }

    .av-dot.deep {
        background: var(--vert-dark);
    }

    .av-dot.mist {
        background: var(--hint);
    }

    .pcard-info {
        flex: 1;
        min-width: 0;
    }

    .p-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 2px;
        line-height: 1.25;
    }

    .p-role {
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
        color: var(--vert);
        margin-bottom: 4px;
    }

    .p-domain {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.45;
    }

    .p-affil {
        font-size: 11px;
        color: var(--hint);
        margin-top: 3px;
        font-style: italic;
    }

    .toggle-btn {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--cimes-light);
        border: 1.5px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .28s;
        margin-top: 2px;
    }

    .pcard:hover .toggle-btn,
    .pcard.open .toggle-btn {
        background: var(--vert);
        border-color: var(--vert);
    }

    .pcard.open .toggle-btn {
        transform: rotate(45deg);
        background: var(--vert-dark);
        border-color: var(--vert-dark);
    }

    .toggle-btn svg {
        transition: stroke .2s;
        stroke: var(--vert);
    }

    .pcard:hover .toggle-btn svg,
    .pcard.open .toggle-btn svg {
        stroke: #fff;
    }

    .acc-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height .5s cubic-bezier(.25, .46, .45, .94);
    }

    .acc-panel.open {
        max-height: 800px;
    }

    .acc-body {
        border-top: 1.5px solid var(--border);
        padding: 18px 20px 22px;
        background: linear-gradient(to bottom, var(--vert-light) 0%, rgba(225, 245, 238, .2) 100%);
    }

    .acc-row {
        display: flex;
        gap: 12px;
        margin-bottom: 9px;
        align-items: flex-start;
    }

    .acc-lbl {
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--hint);
        min-width: 110px;
        padding-top: 3px;
        flex-shrink: 0;
    }

    .acc-val {
        font-size: 13px;
        color: var(--text);
        line-height: 1.55;
    }

    .acc-val a {
        color: var(--vert);
        text-decoration: none;
        border-bottom: 1px solid rgba(15, 118, 110, .3);
    }

    .acc-val a:hover {
        border-bottom-color: var(--vert);
    }

    .acc-bio {
        margin-top: 14px;
        font-size: 13px;
        font-style: italic;
        font-weight: 300;
        color: var(--muted);
        line-height: 1.7;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .acc-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 3px;
    }

    .tag {
        font-size: 10.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        background: var(--cimes-light);
        color: var(--vert-dark);
        border: 1px solid rgba(15, 118, 110, .16);
    }

    /* ── FEATURED (un seul président) ── */
    .featured-wrap {
        display: block;
        margin-bottom: 30px;
        width: 100%;
    }

    .featured-wrap .dcard {
        width: 100%;
        max-width: 100%;
        border: 2.5px solid #C9D95B;
        box-shadow: 0 8px 32px rgba(201, 217, 91, .25);
    }

    .featured-wrap .dcard:hover {
        box-shadow: 0 16px 48px rgba(201, 217, 91, .35);
        border-color: #b5c74e;
    }

    /* ── PARTENAIRES ── */
    .partners-btn {
        display: block;
        width: fit-content;
        margin: 40px auto 0;
        padding: 14px 32px;
        background: var(--vert);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        border-radius: 999px;
        text-decoration: none;
        box-shadow: 0 8px 22px rgba(15, 118, 110, .25);
        transition: all 0.25s ease;
    }

    .partners-btn:hover {
        background: var(--vert-dark) !important;
        color: #fff !important;
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(15, 118, 110, .35);
    }

    .fade-in {
        opacity: 0;
        transform: translateY(18px);
        transition: opacity .6s ease, transform .6s ease;
    }

    .fade-in.visible {
        opacity: 1;
        transform: none;
    }

    @media (max-width: 960px) {

        .direction-grid,
        .presidence-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 680px) {
        #gov-page .gov-hero__content {
            padding: 0 20px 48px;
        }

        #gov-page .gov-sitenav {
            padding: 0 20px !important;
            overflow-x: auto;
            flex-wrap: nowrap;
        }

        .gov-wrapper {
            padding: 36px 16px 72px;
        }

        .schema-flow {
            flex-direction: column;
        }

        .direction-grid,
        .presidence-grid {
            grid-template-columns: 1fr;
        }

        .people-grid {
            grid-template-columns: 1fr;
        }

        .featured-wrap .dcard {
            max-width: 100%;
        }
    }
</style>
</head>

<body>
    <?php include('include/header.html'); ?>

    <div id="gov-page">
        <div class="gov-hero">
            <div class="gov-hero__grid"></div>
            <img class="gov-hero__img" src="img/gouvernance.png" alt="Gouvernance du CIMES" onerror="this.style.display='none'">
            <div class="gov-hero__content">
                <h1 class="gov-hero__h1">Gouvernance<em>du CIMES</em></h1>
            </div>
        </div>
        <nav class="gov-sitenav">
            <a href="#presidence-groupement" class="gov-sitenav__a gov-sitenav__a--active">Présidence &amp; CG</a>
            <a href="#direction" class="gov-sitenav__a">Direction</a>
            <a href="#bureau" class="gov-sitenav__a">Bureau</a>
            <a href="#conseil-scientifique" class="gov-sitenav__a">Conseil Scientifique</a>
            <a href="#comite-orientation" class="gov-sitenav__a">Comité d'Orientation</a>
            <a href="#partenaires" class="gov-sitenav__a">Partenaires</a>
        </nav>
    </div>

    <div class="gov-wrapper">
        <div class="schema-wrap fade-in">
            <div class="schema-title">Schéma de gouvernance</div>
            <div class="schema-flow" id="schema-flow"></div>
        </div>

        <!-- 1. PRÉSIDENCE ET CONSEIL DE GROUPEMENT -->
        <section id="presidence-groupement" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Présidence et Conseil de groupement</div>
                </div>
                <div class="sec-ghost">CG</div>
            </div>
            <!-- Conteneur pour la Présidence (un ou plusieurs) -->
            <div id="presidence-container"></div>
            <!-- Conteneur pour le Conseil de groupement -->
            <div class="people-grid" id="cg-grid"></div>
        </section>

        <!-- 2. DIRECTION -->
        <section id="direction" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Direction</div>
                </div>
                <div class="sec-ghost">Dir.</div>
            </div>
            <div class="direction-grid" id="direction-grid"></div>
        </section>

        <!-- 3. BUREAU -->
        <section id="bureau" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Bureau</div>
                </div>
                <div class="sec-ghost">Bur.</div>
            </div>
            <div class="people-grid" id="bureau-grid"></div>
        </section>

        <!-- 4. CONSEIL SCIENTIFIQUE -->
        <section id="conseil-scientifique" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Conseil Scientifique</div>
                </div>
                <div class="sec-ghost">CS</div>
            </div>
            <div class="people-grid" id="cs-grid"></div>
        </section>

        <!-- 5. COMITÉ D'ORIENTATION -->
        <section id="comite-orientation" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Comité d'Orientation</div>
                </div>
                <div class="sec-ghost">CO</div>
            </div>
            <div class="people-grid" id="co-grid"></div>
        </section>

        <!-- PARTENAIRES -->
        <section id="partenaires" class="gov-block fade-in">
            <div class="sec-head">
                <div>
                    <div class="sec-title">Partenaires signataires</div>
                </div>
                <div class="sec-ghost">9</div>
            </div>
            <p class="sec-desc">La convention constitutive du CIMES rassemble universités, CNRS et acteurs territoriaux autour de la montagne du Sud.</p>
            <a href="partenaires.php" class="partners-btn">Voir les partenaires signataires</a>
        </section>
    </div>

    <?php include('include/footer.html'); ?>

    <script>
        const API_URL = '../cimes_api/index_api.php';
        const DIR_BADGES = ['Directrice', 'Dir. Adjoint'];
        const DIR_BADGE_C = ['', 'sage'];
        const DIR_BANNER = ['clr-green', 'clr-sage'];
        const CG_DOTS = ['deep', 'plum', 'terra', 'teal', 'gold', 'sage', 'deep'];

        // ---- UTILITAIRES ----
        function fullName(m) {
            const p = m.prenom ? m.prenom.trim() : '';
            const n = m.nom ? m.nom.trim() : '';
            return (p + ' ' + n).trim();
        }

        function normalizeName(str) {
            return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        function avatarUrl(m, bg = '0F766E', color = 'fff', size = 120, fontsize = 0.5) {
            if (m.photo) return `img/${m.photo}`;
            const name = encodeURIComponent(fullName(m) || '?');
            return `https://ui-avatars.com/api/?name=${name}&background=${bg}&color=${color}&size=${size}&bold=true&font-size=${fontsize}`;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
        }

        // ---- FONCTION DE RENDU POUR DIRECTION ET PRÉSIDENCE ----
        function renderDCard(m, i, type, customBadge = null) {
            const name = fullName(m);
            const p = avatarUrl(m, '0F766E', 'fff', 120, 0.45);
            const affil = m.laboratoire || m.tutelle || '';
            const badge = customBadge || (type === 'presidence' ? 'Président(e)' : (DIR_BADGES[i] || ''));
            const bannerClass = (type === 'presidence') ? 'clr-presidence' : (DIR_BANNER[i] || 'clr-green');
            const badgeClass = (type === 'presidence') ? 'presidence' : (DIR_BADGE_C[i] || '');
            return `
        <div class="dcard" onclick="toggleDCard(this)">
            <div class="dcard-banner ${bannerClass}"></div>
            <div class="dcard-photo-wrap">
                <img class="dcard-photo" src="${p}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name||'?')}&background=0F766E&color=fff&size=120&bold=true&font-size=0.45'" alt="${escapeHtml(name)}">
                <span class="dcard-badge ${badgeClass}">${badge}</span>
            </div>
            <div class="dcard-body">
                <div class="dcard-name">${escapeHtml(name)}</div>
                <div class="dcard-role">${escapeHtml(m.role||'')}</div>
                <div class="dcard-domain">${escapeHtml(m.discipline||'')}</div>
                <div class="dcard-affil">${escapeHtml(affil)}</div>
                <div class="dcard-toggle"><span>Voir le profil</span><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/></svg></div>
            </div>
            <div class="dcard-panel"><div class="dcard-acc">
                ${m.fonction?`<div class="acc-row"><div class="acc-lbl">Fonction</div><div class="acc-val">${escapeHtml(m.fonction)}</div></div>`:''}
                ${m.email?`<div class="acc-row"><div class="acc-lbl">Email</div><div class="acc-val"><a href="mailto:${escapeHtml(m.email)}">${escapeHtml(m.email)}</a></div></div>`:''}
                ${m.page_web?`<div class="acc-row"><div class="acc-lbl">Page web</div><div class="acc-val"><a href="${escapeHtml(m.page_web)}" target="_blank">${escapeHtml(m.page_web)}</a></div></div>`:''}
                ${m.laboratoire?`<div class="acc-row"><div class="acc-lbl">Laboratoire</div><div class="acc-val">${escapeHtml(m.laboratoire)}</div></div>`:''}
                ${m.page_web_labo?`<div class="acc-row"><div class="acc-lbl">Page web labo</div><div class="acc-val"><a href="${escapeHtml(m.page_web_labo)}" target="_blank">${escapeHtml(m.page_web_labo)}</a></div></div>`:''}
                ${m.etablissement?`<div class="acc-row"><div class="acc-lbl">Établissement</div><div class="acc-val">${escapeHtml(m.etablissement)}</div></div>`:''}
                ${m.discipline?`<div class="acc-row"><div class="acc-lbl">Discipline(s)</div><div class="acc-val"><div class="acc-tags">${m.discipline.split(',').map(t=>`<span class="tag">${escapeHtml(t.trim())}</span>`).join('')}</div></div></div>`:''}
                ${m.terrain_recherche?`<div class="acc-row"><div class="acc-lbl">Terrain(s) de recherche</div><div class="acc-val">${escapeHtml(m.terrain_recherche)}</div></div>`:''}
                ${m.bio?`<div class="acc-bio">${escapeHtml(m.bio)}</div>`:''}
            </div></div>
        </div>`;
        }

        // ---- FONCTION POUR LES CARTES PERSONNES (CG, Bureau, CS, CO) ----
        function renderPerson(m, dotClass = '') {
            const name = fullName(m);
            const p = avatarUrl(m, '134e4a', '9FE1CB', 120, 0.45);
            const affil = m.etablissement || m.laboratoire || '';
            const dot = dotClass ? `av-dot ${dotClass}` : 'av-dot';
            return `
        <div class="pcard" onclick="toggleCard(this)">
            <div class="pcard-top">
                <div class="av-wrap">
                    <img class="av-img" src="${p}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(name||'?')}&background=134e4a&color=9FE1CB&size=120&bold=true&font-size=0.45'" alt="${escapeHtml(name)}">
                    <div class="${dot}"></div>
                </div>
                <div class="pcard-info">
                    <div class="p-name">${escapeHtml(name)}</div>
                    <div class="p-role">${escapeHtml(m.role||'')}</div>
                    <div class="p-domain">${escapeHtml(m.discipline||'')}</div>
                    <div class="p-affil">${escapeHtml(affil)}</div>
                </div>
                <div class="toggle-btn"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke-width="2.2" stroke-linecap="round"><line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/></svg></div>
            </div>
            <div class="acc-panel"><div class="acc-body">
                ${m.email?`<div class="acc-row"><div class="acc-lbl">Email</div><div class="acc-val"><a href="mailto:${escapeHtml(m.email)}">${escapeHtml(m.email)}</a></div></div>`:''}
            </div></div>
        </div>`;
        }

        // ---- TOGGLES ----
        function toggleDCard(c) {
            const p = c.querySelector('.dcard-panel'),
                o = c.classList.contains('open');
            document.querySelectorAll('.dcard.open').forEach(x => {
                x.classList.remove('open');
                const y = x.querySelector('.dcard-panel');
                if (y) y.classList.remove('open');
            });
            if (!o) {
                c.classList.add('open');
                if (p) p.classList.add('open');
                setTimeout(() => {
                    const r = c.getBoundingClientRect();
                    if (r.bottom > innerHeight) c.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 120);
            }
        }

        function toggleCard(c) {
            const p = c.querySelector('.acc-panel'),
                o = c.classList.contains('open');
            document.querySelectorAll('.pcard.open').forEach(x => {
                x.classList.remove('open');
                const y = x.querySelector('.acc-panel');
                if (y) y.classList.remove('open');
            });
            if (!o) {
                c.classList.add('open');
                if (p) p.classList.add('open');
                setTimeout(() => {
                    const r = c.getBoundingClientRect();
                    if (r.bottom > innerHeight) c.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 120);
            }
        }

        // ---- CHARGEMENT ----
        Promise.all([
            fetch(`${API_URL}?query=gouvernance_schema`).then(r => r.json()),
            fetch(`${API_URL}?query=gouvernance`).then(r => r.json())
        ]).then(([schema, membres]) => {
            // Schéma
            document.getElementById('schema-flow').innerHTML = schema.map((n, i) =>
                `${i>0?'<div class="schema-node-arrow">→</div>':''}
             <div class="schema-node accent">
                 <div class="schema-node-name">${escapeHtml(n.titre)}</div>
                 <div class="schema-node-desc">${escapeHtml(n.description||'')}</div>
                 ${n.reunion?`<span class="schema-freq">${escapeHtml(n.reunion)}</span>`:''}
             </div>`
            ).join('');

            // ---- 1. PRÉSIDENCE : garder l'ordre de la base (pas de tri) ----
            const presidence = membres.filter(m => m.type === 'presidence');
            const containerPres = document.getElementById('presidence-container');
            if (presidence.length === 0) {
                containerPres.innerHTML = '';
            } else if (presidence.length === 1) {
                containerPres.innerHTML = `<div class="featured-wrap">${renderDCard(presidence[0], 0, 'presidence')}</div>`;
            } else {
                containerPres.innerHTML = `<div class="presidence-grid">${presidence.map((m, i) => renderDCard(m, i, 'presidence')).join('')}</div>`;
            }

            // ---- 2. CONSEIL DE GROUPEMENT : tri alphabétique ----
            const cgAll = membres.filter(m => m.type === 'conseil_groupement').sort((a, b) => fullName(a).localeCompare(fullName(b), 'fr', {
                sensitivity: 'base'
            }));
            document.getElementById('cg-grid').innerHTML = cgAll.map((m, i) => renderPerson(m, CG_DOTS[i % CG_DOTS.length])).join('');

            // ---- 3. DIRECTION : garder l'ordre de la base (pas de tri) ----
            const directionAll = membres.filter(m => m.type === 'direction');
            document.getElementById('direction-grid').innerHTML = directionAll.map((m, i) => renderDCard(m, i, 'direction')).join('');

            // ---- 4. BUREAU : tri alphabétique ----
            const bureau = membres.filter(m => m.type === 'bureau').sort((a, b) => fullName(a).localeCompare(fullName(b), 'fr', {
                sensitivity: 'base'
            }));
            document.getElementById('bureau-grid').innerHTML = bureau.map((m, i) => renderPerson(m, CG_DOTS[i % CG_DOTS.length])).join('');

            // ---- 5. CONSEIL SCIENTIFIQUE : tri alphabétique ----
            const cs = membres.filter(m => m.type === 'conseil_scientifique').sort((a, b) => fullName(a).localeCompare(fullName(b), 'fr', {
                sensitivity: 'base'
            }));
            document.getElementById('cs-grid').innerHTML = cs.map(m => renderPerson(m, '')).join('');

            // ---- 6. COMITÉ D'ORIENTATION : tri alphabétique ----
            const co = membres.filter(m => m.type === 'comite_orientation').sort((a, b) => fullName(a).localeCompare(fullName(b), 'fr', {
                sensitivity: 'base'
            }));
            document.getElementById('co-grid').innerHTML = co.map(m => renderPerson(m, 'mist')).join('');

            initPage();
        }).catch(err => console.error('Erreur:', err));

        // ---- INIT ----
        function initPage() {
            const obs = new IntersectionObserver(e => {
                e.forEach(x => {
                    if (x.isIntersecting) {
                        x.target.classList.add('visible');
                        obs.unobserve(x.target);
                    }
                });
            }, {
                threshold: 0.06,
                rootMargin: '0px 0px -36px 0px'
            });
            document.querySelectorAll('.fade-in').forEach(el => obs.observe(el));

            const sec = document.querySelectorAll('section[id]'),
                nav = document.querySelectorAll('.gov-sitenav__a');
            window.addEventListener('scroll', () => {
                let c = '';
                sec.forEach(s => {
                    if (scrollY >= s.offsetTop - 130) c = s.id;
                });
                nav.forEach(a => a.classList.toggle('gov-sitenav__a--active', a.getAttribute('href') === '#' + c));
            }, {
                passive: true
            });
        }
    </script>
</body>

</html>
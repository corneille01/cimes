<?php
include('include/head.html');
?>
<title>Partenaires – CIMES | Centre International des Montagnes du Sud</title>
<meta name="description" content="Découvrez l'ensemble des partenaires académiques, institutionnels et scientifiques du GIS CIMES.">

<style>
    /* ═══════════════════════════════════════════════════════════════
       PARTENAIRES PAGE — isolation via #partenaires-page
       Toutes les variables depuis :root global
    ═══════════════════════════════════════════════════════════════ */

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
        --radius: 14px;
        --radius-sm: 8px;
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
        --shadow-sm: 0 4px 20px rgba(15, 118, 110, .07), 0 1px 4px rgba(0, 0, 0, .04);
        --shadow-hover: 0 18px 48px -8px rgba(15, 118, 110, .18), 0 4px 12px rgba(0, 0, 0, .06);
    }

    /* ── BASE ────────────────────────────────────────────────── */
    #partenaires-page {
        display: block;
        width: 100%;
        background: var(--bg);
        color: var(--text);
        overflow-x: hidden;
    }

    /* ── HERO ───────────────────────────────────────────────── */
    #partenaires-page .p-hero {
        margin-top: var(--nav-height);
        position: relative;
        min-height: 440px;
        background-image: url("img/aubrac-min.jpg");
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 0 60px 60px;
        overflow: hidden;
        isolation: isolate;
    }

    #partenaires-page .p-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(to top,
                rgba(7, 42, 40, 0.97) 0%,
                rgba(7, 42, 40, 0.78) 35%,
                rgba(7, 42, 40, 0.35) 65%,
                rgba(7, 42, 40, 0.06) 100%);
    }

    #partenaires-page .p-hero__grid {
        position: absolute;
        inset: 0;
        z-index: 2;
        opacity: .035;
        background-image:
            linear-gradient(rgba(255, 255, 255, .8) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .8) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }

    #partenaires-page .p-hero__content {
        position: relative;
        z-index: 3;
    }

    #partenaires-page .p-hero__eyebrow {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .3em;
        text-transform: uppercase;
        color: var(--vert-text);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #partenaires-page .p-hero__eyebrow::before {
        content: '';
        width: 30px;
        height: 2px;
        background: var(--vert-text);
        display: block;
        flex-shrink: 0;
    }

    #partenaires-page .p-hero__h1 {
        font-size: clamp(46px, 7vw, 86px);
        font-weight: 700;
        color: #fff;
        line-height: .95;
        letter-spacing: -.02em;
        margin-bottom: 18px;
    }

    #partenaires-page .p-hero__h1 em {
        font-style: italic;
        color: var(--vert-mid);
        display: block;
        font-weight: 300;
    }

    #partenaires-page .p-hero__desc {
        font-size: 13.5px;
        font-weight: 300;
        color: rgba(255, 255, 255, .68);
        max-width: 500px;
        line-height: 1.75;
    }

    /* ── SITENAV ─────────────────────────────────────────────── */
    #partenaires-page .p-sitenav {
        background: var(--cimes-hover);
        padding: 0 60px;
        display: flex;
        gap: 2px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    #partenaires-page .p-sitenav a {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .13em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .5);
        text-decoration: none;
        padding: 15px 17px;
        border-bottom: 2.5px solid transparent;
        transition: color .2s, border-color .2s;
        white-space: nowrap;
    }

    #partenaires-page .p-sitenav a:hover,
    #partenaires-page .p-sitenav a.active {
        color: #fff;
        border-bottom-color: var(--vert-text);
    }

    /* ── WRAPPER ─────────────────────────────────────────────── */
    #partenaires-page .p-wrap {
        max-width: 1400px;
        margin: 0 auto;
        padding: 80px 60px 120px;
    }

    /* ── STATS BANNER (optionnel) ────────────────────────────── */
    #partenaires-page .p-banner {
        background: var(--cimes-bg);
        border-radius: var(--radius);
        padding: 44px 52px;
        margin-bottom: 88px;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        position: relative;
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity .65s ease, transform .65s ease;
    }

    #partenaires-page .p-banner.visible {
        opacity: 1;
        transform: none;
    }

    #partenaires-page .p-banner::after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 280px;
        background: radial-gradient(ellipse at right center, rgba(159, 225, 203, .13) 0%, transparent 70%);
        pointer-events: none;
    }

    #partenaires-page .p-stat {
        position: relative;
        z-index: 1;
        padding-right: 44px;
    }

    #partenaires-page .p-stat+.p-stat {
        padding-left: 44px;
        border-left: 1px solid rgba(255, 255, 255, .09);
    }

    #partenaires-page .p-stat__num {
        font-size: 62px;
        font-weight: 700;
        color: var(--vert-text);
        line-height: 1;
        margin-bottom: 6px;
    }

    #partenaires-page .p-stat__label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, .4);
        margin-bottom: 5px;
    }

    #partenaires-page .p-stat__desc {
        font-size: 12.5px;
        color: rgba(255, 255, 255, .65);
        line-height: 1.55;
    }

    /* ── SECTION ─────────────────────────────────────────────── */
    #partenaires-page .p-section {
        margin-bottom: 96px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity .65s ease, transform .65s ease;
    }

    #partenaires-page .p-section.visible {
        opacity: 1;
        transform: none;
    }

    #partenaires-page .p-sec-head {
        margin-bottom: 8px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    #partenaires-page .p-sec-tag {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .22em;
        text-transform: uppercase;
        color: var(--vert);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #partenaires-page .p-sec-tag::before {
        content: '';
        width: 16px;
        height: 1.5px;
        background: var(--vert-mid);
        display: block;
        flex-shrink: 0;
    }

    #partenaires-page .p-sec-title {
        font-size: clamp(28px, 3.2vw, 44px);
        font-weight: 700;
        color: var(--text);
        line-height: 1.05;
        letter-spacing: -.01em;
        text-transform: uppercase;
    }

    #partenaires-page .p-sec-ghost {
        font-size: 82px;
        font-weight: 700;
        color: rgba(15, 118, 110, .05);
        line-height: 1;
        user-select: none;
        letter-spacing: -.04em;
    }

    #partenaires-page .p-sec-desc {
        font-size: 14px;
        font-weight: 300;
        font-style: italic;
        color: var(--muted);
        max-width: 700px;
        line-height: 1.8;
        margin: 18px 0 40px;
        text-align: justify;
    }

    /* ── GRILLE CARTES ───────────────────────────────────────── */
    #partenaires-page .p-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 28px;
        margin-top: 20px;
    }

    /* ── CARTE ───────────────────────────────────────────────── */
    #partenaires-page .p-card {
        background: var(--surface);
        border-radius: 2px;
        border: 1.5px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        box-shadow: var(--shadow-sm);
        position: relative;
        transition:
            transform .32s cubic-bezier(.2, 0, 0, 1),
            border-color .22s ease,
            box-shadow .32s cubic-bezier(.2, 0, 0, 1);
    }

    #partenaires-page .p-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: var(--radius);
        background: linear-gradient(160deg, rgba(15, 118, 110, 0) 60%, rgba(15, 118, 110, .04) 100%);
        opacity: 0;
        transition: opacity .32s ease;
        pointer-events: none;
    }

    #partenaires-page .p-card:hover {
        transform: translateY(-8px) scale(1.01);
        border-color: rgba(159, 225, 203, .7);
        box-shadow: var(--shadow-hover);
    }

    #partenaires-page .p-card:hover::after {
        opacity: 1;
    }



    #partenaires-page .p-card:hover::before {
        transform: scaleX(1);
    }

    /* ── LOGO AREA ───────────────────────────────────────────── */
    #partenaires-page .p-card__logo {
        background: #f8fafa;
        border-bottom: 1.5px solid var(--border);
        height: 160px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    #partenaires-page .p-card__logo::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 70% 70% at 50% 50%, rgba(159, 225, 203, .09) 0%, transparent 70%);
        opacity: 0;
        transition: opacity .3s;
        pointer-events: none;
    }

    #partenaires-page .p-card:hover .p-card__logo::after {
        opacity: 1;
    }

    #partenaires-page .p-card__img {
        max-width: 100%;
        max-height: 120px;
        object-fit: contain;
        display: block;
        margin: auto;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, .07));
        transition: transform .3s ease, filter .2s ease;
        position: relative;
        z-index: 1;
    }

    #partenaires-page .p-card:hover .p-card__img {
        transform: scale(1.06);
        filter: drop-shadow(0 6px 16px rgba(15, 118, 110, .18));
    }

    #partenaires-page .p-card__fallback {
        width: 88px;
        height: 88px;
        background: linear-gradient(135deg, var(--vert-dark) 0%, var(--vert) 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -.02em;
        transition: transform .32s cubic-bezier(.2, 0, 0, 1);
        position: relative;
        z-index: 1;
    }

    /* ── CONTENU TEXTE ───────────────────────────────────────── */
    #partenaires-page .p-card__body {
        padding: 24px 24px 28px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    #partenaires-page .p-card__name {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -.01em;
        margin-bottom: 5px;
        line-height: 1.3;
    }

    #partenaires-page .p-card__type {
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--vert);
        margin-bottom: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    #partenaires-page .p-card__type::before {
        content: '';
        width: 5px;
        height: 5px;
        background: var(--vert-mid);
        border-radius: 50%;
        flex-shrink: 0;
    }

    #partenaires-page .p-card__desc {
        font-size: 12.5px;
        font-weight: 300;
        color: var(--muted);
        line-height: 1.65;
        margin-bottom: 20px;
        flex: 1;
    }

    /* CTA */
    #partenaires-page .p-card__cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--vert);
        padding: 8px 14px;
        background: rgba(15, 118, 110, .07);
        border-radius: 2px;
        margin-top: auto;
        align-self: flex-start;
        transition: background .2s, gap .2s, color .2s;
    }

    #partenaires-page .p-card:hover .p-card__cta {
        background: rgba(15, 118, 110, .13);
        gap: 12px;
        color: var(--vert-dark);
    }

    #partenaires-page .p-card__cta svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
        transition: transform .2s;
    }

    #partenaires-page .p-card:hover .p-card__cta svg {
        transform: translateX(3px);
    }

    /* ── LIEN RETOUR ─────────────────────────────────────────── */
    #partenaires-page .p-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--surface);
        padding: 12px 26px;
        border-radius: 5px;
        border: 1.5px solid var(--border);
        color: var(--vert);
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }

    #partenaires-page .p-back:hover {
        border-color: var(--vert-mid);
        box-shadow: 0 6px 20px rgba(15, 118, 110, .12);
        transform: translateX(3px);
        color: var(--vert-dark);
    }

    /* ── RESPONSIVE ──────────────────────────────────────────── */
    @media (max-width: 900px) {
        #partenaires-page .p-hero {
            padding: 0 24px 48px;
            background-attachment: scroll;
        }

        #partenaires-page .p-sitenav {
            padding: 0 16px;
            overflow-x: auto;
        }

        #partenaires-page .p-wrap {
            padding: 40px 20px 80px;
        }

        #partenaires-page .p-banner {
            grid-template-columns: 1fr;
            padding: 28px 24px;
            gap: 24px;
            margin-bottom: 52px;
        }

        #partenaires-page .p-stat+.p-stat {
            padding-left: 0;
            border-left: none;
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding-top: 24px;
        }

        #partenaires-page .p-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>

<body>
    <?php include('include/header.html'); ?>

    <div id="partenaires-page">
        <!-- ── HERO ── -->
        <div class="p-hero">
            <div class="p-hero__grid"></div>
            <div class="p-hero__content">
                <h1 class="p-hero__h1">Partenaires<em>du CIMES</em></h1>
            </div>
        </div>

        <!-- ── SITENAV ── -->
        <nav class="p-sitenav">
            <a href="#academiques" class="active">Partenaires académiques</a>
            <a href="#territoriaux">Partenaires territoriaux</a>
            <a href="#reseaux">Réseaux associés</a>
        </nav>

        <div class="p-wrap">
            <!-- ═══ PARTENAIRES ACADÉMIQUES ══════════════════════════ -->
            <section id="academiques" class="p-section">
                <div class="p-sec-head">
                    <div>
                        <div class="p-sec-title">Partenaires académiques</div>
                    </div>
                </div>

                <div class="p-grid" id="grid-academiques"></div>
            </section>

            <!-- ═══ PARTENAIRES TERRITORIAUX ══════════════════════════ -->
            <section id="territoriaux" class="p-section">
                <div class="p-sec-head">
                    <div>
                        <div class="p-sec-title">Partenaires territoriaux</div>
                    </div>
                </div>

                <div class="p-grid" id="grid-territoriaux"></div>
            </section>

            <!-- ═══ RÉSEAUX ASSOCIÉS ═══════════════════════════════════ -->
            <section id="reseaux" class="p-section">
                <div class="p-sec-head">
                    <div>
                        <div class="p-sec-title">Réseaux associés</div>
                    </div>
                </div>
                <p class="p-sec-desc">Les membres des réseaux associés au CIMES sont actuellement en cours d’identification et de référencement. Cette rubrique sera prochainement mise à jour afin de présenter les structures partenaires et les collaborations engagées.</p>

                <div class="p-grid" id="grid-reseaux"></div>
            </section>

            <div style="text-align:center; margin-top:64px;">
                <a href="missions.php" class="p-back">
                    Découvrir nos missions
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M2.5 7h9M9 4l3.5 3L9 10" />
                    </svg>
                </a>
            </div>
        </div><!-- /p-wrap -->
    </div>

    <?php include('include/footer.html'); ?>

    <script>
        // Utilisation de la colonne "categorie" de la base
        fetch('../cimes_api/index_api.php?query=partenaires')
            .then(r => r.json())
            .then(data => {
                const grids = {
                    academique: document.getElementById('grid-academiques'),
                    territorial: document.getElementById('grid-territoriaux'),
                    reseau: document.getElementById('grid-reseaux')
                };
                if (!grids.academique || !grids.territorial || !grids.reseau) return;
                grids.academique.innerHTML = '';
                grids.territorial.innerHTML = '';
                grids.reseau.innerHTML = '';

                data.forEach(p => {
                    const cat = p.categorie || 'academique';
                    const fallbackText = p.titre.substring(0, 2).toUpperCase();
                    const card = `
                        <a href="${escapeHtml(p.lien_site)}" target="_blank" rel="noopener" class="p-card">
                            <div class="p-card__logo">
                                ${p.image ? `<img class="p-card__img" src="img/${escapeHtml(p.image)}" alt="${escapeHtml(p.titre)}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">` : ''}
                                <div class="p-card__fallback" style="${p.image ? 'display:none' : ''}">${escapeHtml(fallbackText)}</div>
                            </div>
                            <div class="p-card__body">
                                <div class="p-card__name">${escapeHtml(p.titre)}</div>
                                <div class="p-card__type">${escapeHtml(p.role)}</div>
                                <div class="p-card__desc">${escapeHtml(p.description)}</div>
                                <div class="p-card__cta">
                                    Visiter le site
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 8h10M9 4l4 4-4 4" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    `;
                    if (cat === 'academique') grids.academique.innerHTML += card;
                    else if (cat === 'territorial') grids.territorial.innerHTML += card;
                    else grids.reseau.innerHTML += card;
                });

                // Effet fade-in au scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(e => {
                        if (e.isIntersecting) {
                            e.target.classList.add('visible');
                            observer.unobserve(e.target);
                        }
                    });
                }, {
                    threshold: 0.06,
                    rootMargin: '0px 0px -36px 0px'
                });
                document.querySelectorAll('#partenaires-page .p-section, #partenaires-page .p-banner')
                    .forEach(el => observer.observe(el));

                // Active link sitenav au scroll
                const sections = document.querySelectorAll('#partenaires-page section[id]');
                const navLinks = document.querySelectorAll('#partenaires-page .p-sitenav a');
                window.addEventListener('scroll', () => {
                    let current = '';
                    sections.forEach(s => {
                        if (window.scrollY >= s.offsetTop - 180) current = s.id;
                    });
                    navLinks.forEach(link => {
                        link.classList.toggle('active', link.getAttribute('href') === '#' + current);
                    });
                }, {
                    passive: true
                });
            })
            .catch(err => console.error(err));

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
        }
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Transdisciplinarité — GIS CIMES</title>
    <meta name="description" content="La transdisciplinarité au cœur du GIS CIMES : croiser les savoirs, les territoires et les acteurs pour une recherche ancrée en montagne.">
    <!-- Font Awesome (icônes génériques) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ----- RESET & VARIABLES (inspirées de la charte, classes génériques) ----- */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-dark: #0F766E;
            --primary-deep: #134e4a;
            --primary-soft: #E1F5EE;
            --primary-mid: #9FE1CB;
            --accent-lime: #C9D95B;
            --bg-light: #f0f4f3;
            --surface-white: #ffffff;
            --border-light: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --text-hint: #94a3b8;
            --radius-card: 14px;
            --radius-sm: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
            --shadow-hover: 0 4px 16px rgba(0, 0, 0, .10), 0 2px 6px rgba(0, 0, 0, .06);
            --transition: .2s cubic-bezier(.4, 0, .2, 1);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: system-ui, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            background: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ----- HEADER GÉNÉRIQUE (remplacement include/header.html) ----- */
        .site-header {
            background: var(--primary-deep);
            padding: 1rem 2rem;
            position: relative;
            z-index: 20;
        }

        .site-header__inner {
            max-width: 1440px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .site-logo {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.05);
            padding: 0.3rem 1rem;
            border-radius: 40px;
            transition: background 0.2s;
        }

        .site-logo:hover {
            background: rgba(255,255,255,0.12);
        }

        /* ----- HERO (classes génériques) ----- */
        .page-hero {
            margin-top: 0;
            position: relative;
            min-height: 380px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            overflow: hidden;
            isolation: isolate;
            background: var(--primary-deep);
        }

        .hero__image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            z-index: 0;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(to top,
                color-mix(in srgb, var(--primary-deep) 95%, transparent) 0%,
                color-mix(in srgb, var(--primary-deep) 70%, transparent) 35%,
                color-mix(in srgb, var(--primary-deep) 30%, transparent) 65%,
                transparent 100%);
        }

        .hero__content {
            position: relative;
            z-index: 3;
            padding: 0 60px 56px;
        }

        .hero__title {
            font-size: clamp(40px, 6vw, 76px);
            color: #fff;
            line-height: 0.95;
            letter-spacing: -0.025em;
            font-weight: 800;
            margin: 0;
        }

        /* ----- NAVIGATION SECONDAIRE (ancres) ----- */
        .section-nav {
            background: var(--primary-dark);
            display: flex;
            flex-wrap: wrap;
            padding: 0 60px;
            width: 100%;
            gap: 0.25rem;
        }

        .section-nav__link {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.13em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
            padding: 15px 16px;
            border-bottom: 2px solid transparent;
            transition: color var(--transition), border-color var(--transition);
            white-space: nowrap;
        }

        .section-nav__link:hover,
        .section-nav__link--active {
            color: #fff;
            border-bottom-color: var(--accent-lime);
        }

        /* ----- LAYOUT GÉNÉRIQUE ----- */
        .main-wrapper {
            max-width: 1440px;
            margin: 0 auto;
            padding: 80px 60px 120px;
        }

        .content-block {
            margin-bottom: 90px;
        }

        .section-header {
            margin-bottom: 1.5rem;
            border-bottom: 1.5px solid var(--border-light);
            padding-bottom: 0.75rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .section-title {
            font-size: clamp(26px, 3vw, 38px);
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }

        .section-description {
            font-size: 1rem;
            color: var(--text-muted);
            max-width: 780px;
            margin: 1rem 0 2rem 0;
            line-height: 1.65;
        }

        /* ----- CITATION / BLOC MIS EN AVANT ----- */
        .feature-quote {
            background: var(--primary-dark);
            border-radius: 5px;
            padding: 52px 56px;
            margin-bottom: 80px;
            position: relative;
            overflow: hidden;
        }

        .feature-quote::before {
            content: '\201C';
            position: absolute;
            top: -10px;
            left: 38px;
            font-size: 200px;
            color: rgba(201, 217, 91, 0.06);
            line-height: 1;
            pointer-events: none;
        }

        .quote__text {
            font-size: clamp(16px, 1.8vw, 21px);
            font-style: italic;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            max-width: 760px;
            position: relative;
            z-index: 1;
            margin-bottom: 22px;
        }

        .quote__text strong {
            color: var(--accent-lime);
            font-style: normal;
            font-weight: 600;
        }

        .quote__source {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
            position: relative;
            z-index: 1;
        }

        /* ----- GRILLE ACTEURS (basée sur le texte exact) ----- */
        .actors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .actor-card {
            background: var(--surface-white);
            border-radius: var(--radius-card);
            border: 1px solid var(--border-light);
            padding: 1.75rem 1.5rem;
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .actor-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-mid);
        }

        .actor-card__icon {
            width: 48px;
            height: 48px;
            background: var(--primary-soft);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
            color: var(--primary-dark);
        }

        .actor-card__icon i {
            font-size: 1.3rem;
        }

        .actor-card__name {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .actor-card__desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ----- VALEURS (issues du texte) ----- */
        .values-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .value-item {
            background: var(--surface-white);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-sm);
            padding: 1.25rem;
            transition: all 0.2s;
        }

        .value-item:hover {
            border-color: var(--primary-mid);
            background: #fafefc;
        }

        .value-item__title {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--primary-dark);
            margin-bottom: 0.4rem;
        }

        .value-item__text {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.55;
        }

        /* ----- MISE EN FORME DES EXTRAITS TEXTUELS (blocs citation ou verbatim) ----- */
        .verbatim-block {
            background: var(--surface-white);
            border-left: 4px solid var(--primary-dark);
            padding: 1.8rem 2rem;
            border-radius: var(--radius-sm);
            margin: 2rem 0 1.8rem 0;
            box-shadow: var(--shadow-sm);
        }

        .text-highlight {
            font-weight: 500;
            color: var(--primary-deep);
        }

        /* transition fade-in */
        .fade-on-scroll {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }

        .fade-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* responsive */
        @media (max-width: 860px) {
            .main-wrapper {
                padding: 40px 24px 80px;
            }
            .hero__content {
                padding: 0 24px 44px;
            }
            .section-nav {
                padding: 0 20px;
                overflow-x: auto;
                flex-wrap: nowrap;
            }
            .feature-quote {
                padding: 34px 24px;
            }
            .verbatim-block {
                padding: 1.2rem 1.4rem;
            }
        }

        @media (max-width: 580px) {
            .section-nav__link {
                font-size: 9px;
                padding: 12px 10px;
            }
        }
    </style>
</head>
<body>

<!-- HEADER (standalone, remplace include/header.html) -->
<header class="site-header">
    <div class="site-header__inner">
        <a href="#" class="site-logo">GIS CIMES</a>
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.7);">recherche · territoires · transdisciplinarité</div>
    </div>
</header>

<!-- PAGE PRINCIPALE -->
<div id="app-transdisciplinarite">

    <!-- HERO -->
    <div class="page-hero">
        <img class="hero__image" src="img/transdisciplinarite.png" alt="Paysage montagne transdisciplinarité" onerror="this.style.display='none'">
        <div class="hero__content">
            <h1 class="hero__title">Favoriser la transdisciplinarité<br>entre recherche, territoires et acteurs</h1>
        </div>
    </div>

    <!-- NAVIGATION INTERNE -->
    <nav class="section-nav">
        <a href="#fondement" class="section-nav__link section-nav__link--active">Fondements</a>
        <a href="#inter-trans" class="section-nav__link">Inter & Trans</a>
        <a href="#interface" class="section-nav__link">Interface recherche‑territoires</a>
        <a href="#recherche-partenariale" class="section-nav__link">Recherche collaborative</a>
        <a href="#engagement" class="section-nav__link">Engagement</a>
        <a href="#acteurs-cles" class="section-nav__link">Acteurs</a>
    </nav>

    <div class="main-wrapper">

        <!-- SECTION 1 : AMBITION / FONDEMENT (texte intégral) -->
        <section id="fondement" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Une ambition : lier recherche, territoires et acteurs</div>
            </div>
            <div class="verbatim-block">
                <p style="margin-bottom: 1rem; font-size: 1.05rem; line-height: 1.6;">
                    <strong>Le GIS CIMES a pour ambition de renforcer les liens entre la recherche académique, les territoires et les acteurs qui les font vivre.</strong> 
                    Dans un contexte marqué par des transformations environnementales, sociales, économiques, culturelles et politiques, 
                    les territoires de montagne constituent des espaces où les enjeux sont souvent <strong>complexes, interdépendants et situés</strong>. 
                    Leur compréhension nécessite de croiser les regards, les savoirs et les expériences.
                </p>
            </div>
            <p class="section-description" style="margin-top: 0;">
                Cette approche place la transdisciplinarité au cœur du projet scientifique. Dépasser les clivages entre disciplines 
                et entre mondes académiques et non‑académiques, pour construire ensemble des connaissances capables d’éclairer les réalités territoriales.
            </p>
        </section>

        <!-- SECTION 2 : INTERDISCIPLINARITÉ / TRANSDISCIPLINARITÉ (extrait mot pour mot) -->
        <section id="inter-trans" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Interdisciplinarité et transdisciplinarité</div>
            </div>
            <div class="verbatim-block" style="background: #fafdfc;">
                <p style="margin-bottom: 1rem;"><span class="text-highlight">L’interdisciplinarité</span> permet déjà de dépasser les cadres disciplinaires classiques, en construisant des questions de recherche communes à plusieurs champs scientifiques. Elle favorise le dialogue entre géographes, sociologues, écologues, économistes, historiens, politistes ou spécialistes de l’aménagement, afin de mieux saisir la complexité des phénomènes étudiés.</p>
                <p style="margin-bottom: 0.75rem;"><span class="text-highlight">La transdisciplinarité va plus loin.</span> Elle ouvre la construction même des questions de recherche aux acteurs des territoires et aux milieux non académiques. <strong>Les collectivités, institutions, associations, professionnels, habitantes et habitants, gestionnaires, usagers ou acteurs socio‑économiques</strong> ne sont alors pas uniquement considérés comme des objets d’étude ou des sources d’information. Ils participent à la formulation des problèmes, à l’identification des enjeux, à la mise en discussion des savoirs et, lorsque cela est possible, à l’élaboration de pistes d’action partagées.</p>
            </div>
        </section>

        <!-- SECTION 3 : RÔLE D'INTERFACE (texte original) -->
        <section id="interface" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Interface entre recherche et action territoriale</div>
            </div>
            <div class="verbatim-block">
                <p>Le GIS CIMES souhaite ainsi jouer un <strong>rôle d’interface entre les mondes de la recherche et ceux de l’action territoriale</strong>. Il vise à créer des espaces de rencontre, d’échange et de coopération où les préoccupations des territoires peuvent nourrir les questionnements scientifiques, et où les connaissances produites peuvent en retour contribuer aux réflexions collectives. Cette démarche permet de mieux formuler les problèmes, de rendre visibles la diversité des points de vue, de reconnaître les savoirs situés et d’ouvrir des perspectives adaptées aux contextes locaux.</p>
            </div>
        </section>

        <!-- SECTION 4 : RECHERCHE PARTENARIALE, COLLABORATIVE ET RÉFLEXIVE (intégral) -->
        <section id="recherche-partenariale" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Recherche partenariale, collaborative et réflexive</div>
            </div>
            <div class="verbatim-block">
                <p>À travers ses activités, le GIS CIMES entend soutenir des formes de <strong>recherche partenariale, collaborative et réflexive</strong>. Il s’agit de favoriser des démarches dans lesquelles les connaissances sont produites <em>avec les territoires</em>, en tenant compte des temporalités locales, des attentes des acteurs, des controverses existantes et des conditions concrètes du dialogue. Cette orientation suppose une attention particulière aux modalités de coopération, à la qualité des échanges, à la restitution des résultats et à la valorisation des savoirs produits collectivement.</p>
            </div>
        </section>

        <!-- CITATION FORTE (extrait clé déjà présent dans le texte) -->
        <div class="feature-quote fade-on-scroll">
            <p class="quote__text">
                « Les territoires de montagne constituent des espaces où les enjeux sont souvent <strong>complexes, interdépendants et situés</strong>. Leur compréhension nécessite de croiser les regards, les savoirs et les expériences. »
            </p>
            <div class="quote__source">GIS CIMES — texte fondateur</div>
        </div>

        <!-- SECTION 5 : ENGAGEMENT DURABLE / VISION OUVERTE -->
        <section id="engagement" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Une recherche ouverte, ancrée et attentive aux réalités</div>
            </div>
            <div class="verbatim-block" style="border-left-color: var(--accent-lime);">
                <p>En plaçant la transdisciplinarité au centre de son projet, le GIS CIMES affirme sa volonté de contribuer à une <strong>recherche ouverte, ancrée et attentive aux réalités territoriales</strong>. Il entend accompagner la production de connaissances partagées, capables d’éclairer les débats, de nourrir les coopérations et de soutenir les capacités d’action des acteurs des territoires de montagne.</p>
            </div>
            <!-- valeurs extraites textuellement sans invention -->
            <div class="values-list">
                <div class="value-item">
                    <div class="value-item__title">Attention aux temporalités locales</div>
                    <div class="value-item__text">Intégrer les rythmes et les attentes des acteurs, les controverses existantes.</div>
                </div>
                <div class="value-item">
                    <div class="value-item__title">Qualité des échanges & coopération</div>
                    <div class="value-item__text">Modalités de coopération, restitution des résultats, valorisation collective des savoirs.</div>
                </div>
                <div class="value-item">
                    <div class="value-item__title">Reconnaissance des savoirs situés</div>
                    <div class="value-item__text">Faire dialoguer savoirs académiques et savoirs d’expérience, habitants et acteurs.</div>
                </div>
                <div class="value-item">
                    <div class="value-item__title">Capacités d’action partagées</div>
                    <div class="value-item__text">Produire des connaissances qui éclairent les débats et soutiennent l’action territoriale.</div>
                </div>
            </div>
        </section>

        <!-- SECTION ACTEURS (reprise exhaustive de la liste du document) -->
        <section id="acteurs-cles" class="content-block fade-on-scroll">
            <div class="section-header">
                <div class="section-title">Les acteurs impliqués dans la démarche</div>
            </div>
            <p class="section-description">Conformément au texte fondateur du GIS CIMES, la transdisciplinarité associe pleinement les parties prenantes des territoires à la production des connaissances. Voici les principaux acteurs qui participent à la formulation des problèmes, à la mise en discussion des savoirs et à l’élaboration de réponses adaptées.</p>
            
            <div class="actors-grid">
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-landmark"></i></div>
                    <div class="actor-card__name">Collectivités territoriales</div>
                    <div class="actor-card__desc">Régions, départements, communes, intercommunalités des massifs montagnards.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-building"></i></div>
                    <div class="actor-card__name">Institutions & Parcs</div>
                    <div class="actor-card__desc">Parcs naturels régionaux/nationaux, réserves, établissements publics.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-hand-peace"></i></div>
                    <div class="actor-card__name">Associations</div>
                    <div class="actor-card__desc">Acteurs associatifs locaux, réseaux de protection et de développement.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-person-walking"></i></div>
                    <div class="actor-card__name">Habitantes et habitants</div>
                    <div class="actor-card__desc">Résidents permanents, saisonniers, usagers quotidiens des territoires.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="actor-card__name">Gestionnaires & usagers</div>
                    <div class="actor-card__desc">Gestionnaires d’espaces, usagers des ressources, acteurs de terrain.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-briefcase"></i></div>
                    <div class="actor-card__name">Acteurs socio‑économiques</div>
                    <div class="actor-card__desc">Entreprises, agriculteurs, filières touristiques, développement local.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <div class="actor-card__name">Chercheurs & scientifiques</div>
                    <div class="actor-card__desc">Géographes, sociologues, écologues, économistes, historiens, politistes, spécialistes de l’aménagement.</div>
                </div>
                <div class="actor-card">
                    <div class="actor-card__icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                    <div class="actor-card__name">Professionnels & experts techniques</div>
                    <div class="actor-card__desc">Bureaux d’études, ingénieurs territoriaux, acteurs opérationnels.</div>
                </div>
            </div>
            <p style="margin-top: 1.8rem; font-size: 0.85rem; color: var(--text-muted); border-left: 2px solid var(--primary-mid); padding-left: 1rem;">
                <i class="fa-solid fa-quote-left" style="margin-right: 8px; opacity: 0.6;"></i> Ces acteurs ne sont pas considérés uniquement comme des objets d’étude ou des sources d’information. Ils participent à la formulation des problèmes, à l’identification des enjeux, à la mise en discussion des savoirs et, lorsque cela est possible, à l’élaboration de pistes d’action partagées.
            </p>
        </section>

        <!-- RAPPEL FINAL (extrait conclusion) -->
        <div class="fade-on-scroll" style="margin-top: 40px; text-align: center; font-style: normal; border-top: 1px solid var(--border-light); padding-top: 40px;">
            <p style="font-size: 0.9rem; color: var(--text-muted); max-width: 720px; margin: 0 auto;">
                <strong>GIS CIMES</strong> — Groupement d’Intérêt Scientifique : ancrer la transdisciplinarité au service des territoires de montagne.
            </p>
        </div>
    </div>
</div>

<!-- FOOTER (standalone remplacement include/footer.html) -->
<footer style="background: var(--primary-deep); color: rgba(255,255,255,0.7); text-align: center; padding: 2rem; font-size: 0.75rem; border-top: 1px solid rgba(255,255,255,0.1);">
    <p>GIS CIMES — Favoriser la transdisciplinarité entre recherche, territoires et acteurs</p>
    <p style="margin-top: 0.5rem;">Document de référence — texte fondateur reproduit intégralement</p>
</footer>

<script>
    // active highlight sur ancres (navigation générique)
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.section-nav__link');
    function updateActiveNav() {
        let current = '';
        const scrollPos = window.scrollY + 140;
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionBottom = sectionTop + section.offsetHeight;
            if (scrollPos >= sectionTop && scrollPos < sectionBottom) {
                current = section.getAttribute('id');
            }
        });
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.substring(1) === current) {
                link.classList.add('section-nav__link--active');
            } else {
                link.classList.remove('section-nav__link--active');
            }
        });
    }
    window.addEventListener('scroll', updateActiveNav, { passive: true });
    window.addEventListener('resize', updateActiveNav);
    updateActiveNav();

    // Intersection Observer pour fade-in
    const fadeElements = document.querySelectorAll('.fade-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
    fadeElements.forEach(el => observer.observe(el));
</script>
</body>
</html>
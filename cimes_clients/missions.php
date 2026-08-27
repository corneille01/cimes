<?php include('include/head.html') ?>
<title>Les missions du CIMES – Centre International des Montagnes du Sud</title>
<meta name="description" content="Découvrez les missions scientifiques et territoriales du GIS CIMES.">

<style>
  /* ═══════════════════════════════════════════════════════════════
   MISSIONS PAGE — isolation totale via #missions-page
   Police : Oswald héritée du projet
   ═══════════════════════════════════════════════════════════════ */

  :root {
    /* Couleurs principales */
    --vert: #0F766E;
    --vert-dark: #134e4a;
    --vert-light: #E1F5EE;
    --vert-mid: #9FE1CB;
    --vert-text: #C9D95B;

    /* Couleurs UI */
    --bg: #f0f4f3;
    --surface: #ffffff;
    --border: #e2e8f0;
    --border-hover: #cbd5e1;

    /* Texte */
    --text: #1e293b;
    --muted: #64748b;
    --hint: #94a3b8;
    --danger: #8b6161;

    /* Layout */
    --radius: 14px;
    --radius-sm: 8px;
    --nav-height: 80px;

    /* Boutons */
    --btn-h: 30px;
    --btn-min-w: 100px;
    --btn-font: 0.74rem;
    --btn-px: 12px;

    /* Alias branding (CIMES) */
    --cimes-bg: var(--vert-dark);
    --cimes-hover: var(--vert);
    --cimes-accent: var(--vert-mid);
    --cimes-light: var(--vert-light);
    --cimes-gray: var(--bg);

    /* Card height */
    --card-h: 320px;
  }

  /* ── HERO ─────────────────────────────────────────────────── */
  #missions-page {
    display: block;
    width: 100%;
  }

  #missions-page .m-hero {
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

  #missions-page .m-hero::before {
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

  #missions-page .m-hero__grid {
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

  #missions-page .m-hero__content {
    position: relative;
    z-index: 3;
  }

  #missions-page .m-hero__eyebrow {
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

  #missions-page .m-hero__eyebrow::before {
    content: '';
    width: 30px;
    height: 2px;
    background: var(--vert-text);
    display: block;
    flex-shrink: 0;
  }

  #missions-page .m-hero__h1 {
    font-size: clamp(46px, 7vw, 86px);
    font-weight: 700;
    color: #fff;
    line-height: .95;
    letter-spacing: -.02em;
    margin-bottom: 18px;
  }

  #missions-page .m-hero__h1 em {
    font-style: italic;
    color: var(--vert-mid);
    display: block;
    font-weight: 300;
  }

  #missions-page .m-hero__desc {
    font-size: 15px;
    font-weight: 300;
    color: rgba(255, 255, 255, .68);
    max-width: 480px;
    line-height: 1.75;
  }

  /* ── WRAPPER ─────────────────────────────────────────────── */
  #missions-page .m-wrap {
    background: var(--bg);
    padding: 80px 60px 110px;
  }

  /* ── INTRO ───────────────────────────────────────────────── */
  #missions-page .m-intro {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: end;
    margin-bottom: 72px;
    padding-bottom: 56px;
    border-bottom: 1.5px solid var(--border);
  }

  #missions-page .m-intro__label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .28em;
    text-transform: uppercase;
    color: var(--vert);
    display: block;
    margin-bottom: 14px;
  }

  #missions-page .m-intro__title {
    font-size: clamp(28px, 3.5vw, 46px);
    font-weight: 700;
    color: var(--text);
    line-height: 1.0;
    letter-spacing: -.01em;
    text-transform: uppercase;
  }

  #missions-page .m-intro__title em {
    font-style: italic;
    font-weight: 300;
    color: var(--vert);
    text-transform: none;
  }

  #missions-page .m-intro__text {
    font-size: 15px;
    font-weight: 300;
    color: var(--text);
    line-height: 1.8;
    text-align: justify;
  }

  /* ── FEED ────────────────────────────────────────────────── */
  #missions-page .m-feed {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  /* ── ITEM ────────────────────────────────────────────────── */
  #missions-page .m-item {
    display: grid;
    grid-template-columns: 2fr 3fr;
    height: var(--card-h);
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--surface);
    box-shadow: 0 4px 20px rgba(15, 118, 110, .07), 0 1px 4px rgba(0, 0, 0, .04);
    border: 1.5px solid var(--border);
    opacity: 0;
    transform: translateY(22px);
    transition: opacity .55s ease, transform .55s ease, box-shadow .3s ease;
  }

  #missions-page .m-item.visible {
    opacity: 1;
    transform: none;
  }

  #missions-page .m-item:hover {
    box-shadow: 0 18px 48px -8px rgba(15, 118, 110, .18), 0 4px 12px rgba(0, 0, 0, .06);
  }

  /* Impairs : image à droite */
  #missions-page .m-item.reverse {
    grid-template-columns: 3fr 2fr;
  }

  /* ── IMAGE COL ───────────────────────────────────────────── */
  #missions-page .m-item__img-col {
    position: relative;
    overflow: hidden;
    height: 100%;
  }

  #missions-page .m-item.reverse .m-item__img-col {
    order: 2;
  }

  #missions-page .m-item__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .65s cubic-bezier(.2, 0, 0, 1);
  }

  #missions-page .m-item:hover .m-item__img {
    transform: scale(1.05);
  }

  #missions-page .m-item__img-col::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(15, 118, 110, .13) 0%, transparent 55%);
    pointer-events: none;
    z-index: 1;
  }

  #missions-page .m-item.reverse .m-item__img-col::after {
    background: linear-gradient(to left, rgba(15, 118, 110, .13) 0%, transparent 55%);
  }

  #missions-page .m-item__num {
    position: absolute;
    top: 14px;
    left: 14px;
    z-index: 2;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #fff;
    background: rgba(7, 42, 40, .6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 5px 12px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, .16);
  }

  #missions-page .m-item.reverse .m-item__num {
    left: auto;
    right: 14px;
  }

  /* ── TEXTE COL ───────────────────────────────────────────── */
  #missions-page .m-item__text-col {
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    overflow: hidden;
    height: 100%;
    box-sizing: border-box;
  }

  #missions-page .m-item__text-col::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;

    transform: scaleX(0);
    transform-origin: left;
    transition: transform .4s cubic-bezier(.2, 0, 0, 1);
  }



  #missions-page .m-item__bg-num {
    position: absolute;
    bottom: -16px;
    right: -6px;
    font-size: 130px;
    font-weight: 700;
    color: rgba(15, 118, 110, .04);
    line-height: 1;
    pointer-events: none;
    user-select: none;
    z-index: 0;
  }

  #missions-page .m-item__tag {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: var(--vert);
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
  }

  #missions-page .m-item__tag::before {
    content: '';
    width: 16px;
    height: 1.5px;
    background: var(--vert-mid);
    display: block;
    flex-shrink: 0;
  }

  #missions-page .m-item__title {
    font-size: clamp(16px, 1.8vw, 22px);
    font-weight: 700;
    color: var(--text);
    line-height: 1.15;
    letter-spacing: -.01em;
    margin-bottom: 12px;
    text-transform: uppercase;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
  }

  /* Description — scrollable, hauteur limitée, ne fait pas grossir la carte */
  #missions-page .m-item__desc {
    font-size: 15px;
    font-weight: 300;
    color: var(--text);
    line-height: 1.75;
    text-align: justify;
    position: relative;
    z-index: 1;
    max-height: 160px;
    overflow-y: auto;
    margin-right: 10px;
    flex: 1;
    min-height: 0;
    padding-right: 10px;
  }

  #missions-page .m-item__desc::-webkit-scrollbar {
    width: 4px;
  }

  #missions-page .m-item__desc::-webkit-scrollbar-thumb {
    background: var(--vert-mid);
    border-radius: 4px;
  }

  #missions-page .m-item__desc::-webkit-scrollbar-track {
    background: var(--vert-light);
    border-radius: 4px;
  }

  /* ── SKELETON ────────────────────────────────────────────── */
  #missions-page .m-skeleton {
    display: grid;
    grid-template-columns: 2fr 3fr;
    border-radius: var(--radius);
    overflow: hidden;
    background: var(--surface);
    border: 1.5px solid var(--border);
    height: var(--card-h);
  }

  #missions-page .m-skel {
    background: linear-gradient(90deg, #e8edeb 25%, #d8e5e1 50%, #e8edeb 75%);
    background-size: 200% 100%;
    animation: m-shimmer 1.5s infinite;
    height: 100%;
  }

  #missions-page .m-skel-text {
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    gap: 11px;
  }

  #missions-page .m-skel-line {
    border-radius: var(--radius-sm);
    background: linear-gradient(90deg, #e8edeb 25%, #d8e5e1 50%, #e8edeb 75%);
    background-size: 200% 100%;
    animation: m-shimmer 1.5s infinite;
  }

  @keyframes m-shimmer {
    to {
      background-position: -200% 0;
    }
  }

  /* ── BACK ────────────────────────────────────────────────── */
  #missions-page .m-back {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 60px;
    padding: 12px 26px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 5px;
    color: var(--vert);
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    transition: border-color .2s, box-shadow .2s, transform .2s;
  }

  #missions-page .m-back:hover {
    border-color: var(--vert-mid);
    box-shadow: 0 6px 20px rgba(15, 118, 110, .12);
    transform: translateX(-3px);
    color: var(--vert-dark) !important;
  }

  #missions-page .m-empty {
    text-align: center;
    padding: 60px 20px;
    color: var(--hint);
    font-style: italic;
    font-size: 14px;
  }

  /* ── RESPONSIVE ──────────────────────────────────────────── */
  @media (max-width: 900px) {
    #missions-page .m-hero {
      padding: 0 24px 48px;
      background-attachment: scroll;
    }

    #missions-page .m-wrap {
      padding: 52px 20px 80px;
    }

    #missions-page .m-intro {
      grid-template-columns: 1fr;
      gap: 20px;
      margin-bottom: 48px;
    }

    #missions-page .m-item,
    #missions-page .m-item.reverse,
    #missions-page .m-skeleton {
      grid-template-columns: 1fr !important;
      height: auto !important;
    }

    #missions-page .m-item__img-col {
      height: 200px;
      order: 0 !important;
    }

    #missions-page .m-item.reverse .m-item__img-col {
      order: 0 !important;
    }

    #missions-page .m-item__text-col {
      padding: 26px 22px 30px;
      height: auto !important;
    }

    #missions-page .m-item__desc {
      max-height: 120px;
    }

    #missions-page .m-skel {
      height: 180px;
    }
  }
</style>
</head>

<body>
  <?php include('include/header.html') ?>

  <div id="missions-page">

    <div class="m-hero">
      <div class="m-hero__grid"></div>
      <div class="m-hero__content">

        <h1 class="m-hero__h1">Les missions<em>du CIMES</em></h1>

      </div>
    </div>

    <div class="m-wrap">

      <div class="m-intro">
        <div>

          <h2 class="m-intro__title">Ce que porte<br><em>le CIMES</em></h2>
        </div>
        <p class="m-intro__text">Chaque mission traduit un engagement collectif des membres du GIS&nbsp;: produire de la connaissance robuste, former la prochaine génération de chercheurs et ancrer la science dans les territoires de montagne.</p>
      </div>

      <!-- Skeletons -->
      <div id="m-skeletons" class="m-feed" aria-hidden="true">
        <div class="m-skeleton">
          <div class="m-skel"></div>
          <div class="m-skel-text">
            <div class="m-skel-line" style="width:26%;height:9px;"></div>
            <div class="m-skel-line" style="width:62%;height:22px;margin-top:6px;"></div>
            <div class="m-skel-line" style="width:48%;height:22px;"></div>
            <div class="m-skel-line" style="width:100%;height:12px;margin-top:12px;"></div>
            <div class="m-skel-line" style="width:92%;height:12px;"></div>
            <div class="m-skel-line" style="width:80%;height:12px;"></div>
            <div class="m-skel-line" style="width:68%;height:12px;"></div>
          </div>
        </div>
        <div class="m-skeleton" style="grid-template-columns:3fr 2fr;">
          <div class="m-skel-text">
            <div class="m-skel-line" style="width:26%;height:9px;"></div>
            <div class="m-skel-line" style="width:58%;height:22px;margin-top:6px;"></div>
            <div class="m-skel-line" style="width:44%;height:22px;"></div>
            <div class="m-skel-line" style="width:100%;height:12px;margin-top:12px;"></div>
            <div class="m-skel-line" style="width:86%;height:12px;"></div>
            <div class="m-skel-line" style="width:74%;height:12px;"></div>
          </div>
          <div class="m-skel"></div>
        </div>
      </div>

      <!-- Feed injecté -->
      <div id="m-feed" class="m-feed" style="display:none;"></div>



    </div>
  </div>

  <?php $id = htmlspecialchars($_GET['id'] ?? ''); ?>
  <input type="hidden" id="main-id" value="<?php echo $id; ?>">

  <script>
    (function() {
      const feed = document.getElementById('m-feed');
      const skeletons = document.getElementById('m-skeletons');

      const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
          if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
          }
        });
      }, {
        threshold: 0.06,
        rootMargin: '0px 0px -30px 0px'
      });

      fetch('../cimes_api/index_api.php?query=mission')
        .then(r => r.json())
        .then(data => {
          skeletons.style.display = 'none';
          feed.style.display = 'flex';

          if (!data || data.length === 0) {
            feed.innerHTML = '<div class="m-empty">Aucune mission disponible pour le moment.</div>';
            return;
          }

          data.forEach((ligne, index) => {
            const isReverse = index % 2 !== 0;
            const numLabel = String(index + 1).padStart(2, '0');

            const item = document.createElement('div');
            item.className = 'm-item' + (isReverse ? ' reverse' : '');

            const imgCol = document.createElement('div');
            imgCol.className = 'm-item__img-col';
            imgCol.innerHTML = `
                        <span class="m-item__num">Mission ${numLabel}</span>
                        <img
                            class="m-item__img"
                            src="img/${ligne.image}"
                            alt="${ligne.nom}"
                            loading="lazy"
                            onerror="this.closest('.m-item__img-col').style.background='#cdddd8';this.style.display='none';"
                        >`;

            const textCol = document.createElement('div');
            textCol.className = 'm-item__text-col';
            textCol.innerHTML = `
                        <div class="m-item__bg-num">${numLabel}</div>
                        <div class="m-item__tag">Axe ${numLabel}</div>
                        <h2 class="m-item__title">${ligne.nom}</h2>
                        <p class="m-item__desc">${ligne.texte}</p>`;

            if (!isReverse) {
              item.appendChild(imgCol);
              item.appendChild(textCol);
            } else {
              item.appendChild(textCol);
              item.appendChild(imgCol);
            }

            feed.appendChild(item);
            setTimeout(() => observer.observe(item), index * 55);
          });
        })
        .catch(() => {
          skeletons.style.display = 'none';
          feed.style.display = 'flex';
          feed.innerHTML = '<div class="m-empty">Impossible de charger les missions. Veuillez réessayer plus tard.</div>';
        });
    })();
  </script>

  <?php include('include/footer.html') ?>
</body>

</html>
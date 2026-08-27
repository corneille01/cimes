<?php include('include/head.html') ?>
<title>CIMeS — Centre International des Montagnes du Sud</title>
<meta name="description" content="Groupement d'Intérêt Scientifique dédié à la recherche et à l'innovation dans les environnements montagnards des Pyrénées.">

<style>
    /* Styles communs pour les cartes actualités et activités */
    .card_actu {
        background: var(--surface, #fff);
        width: 100%;

        margin: 0 auto;
        border-radius: 5px;
        display: flex;
        overflow: hidden;
        border: 1px solid var(--border, #e2e8f0);
        transition: transform 0.3s ease;
        margin: 0 auto;
    }

    .card_actu:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .card-side-date {
        background: var(--vert, #0F766E);
        color: white;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card-side-date span {

        white-space: nowrap;
        font-weight: bold;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .card-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .card-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .card-content h3 {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: var(--vert-dark, #134e4a);
        max-height: 3rem;
        overflow-y: auto;
    }

    .lieu {
        color: var(--vert, #0F766E);
        font-size: 0.85rem;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .card-description-scroll {
        flex-grow: 1;
        overflow-y: auto;
        font-size: 0.9rem;
        line-height: 1.5;
        color: var(--muted, #64748b);
        padding-right: 5px;
        margin-bottom: 10px;
        text-align: justify;
    }

    .actu-link,
    .actu-link-activite {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--cimes-bg, #134e4a);
        color: white !important;
        text-decoration: none;
        padding: 8px;
        border-radius: var(--radius-sm, 8px);
        font-size: 0.8rem;
        transition: 0.2s;
    }

    .actu-link:hover,
    .actu-link-activite:hover {
        background: var(--cimes-hover, #0F766E);
    }

    .card_actu .card-description-scroll {
        max-height: 80px;
        /* pour que la carte ne soit pas trop grande */
    }

    /* Avant : 1 colonne en dessous de 768px → on supprime ce comportement */
    @media (max-width: 768px) {

        .actu-grid,
        .events-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            /* 2 colonnes */
            gap: 12px;
        }
    }

    /* On garde 1 colonne uniquement pour les très petits écrans si nécessaire (optionnel) */
    @media (max-width: 480px) {

        .actu-grid,
        .events-grid {
            grid-template-columns: 1fr;
            /* une seule colonne si l'écran est vraiment étroit */
        }
    }
</style>

</head>

<body>
    <?php include('include/header.html'); ?>
    <main id="sections-wrapper">
        <!-- ===== HERO CARROUSEL ===== -->
        <div class="hero-slider" data-section="hero">
            <div class="carousel-track-container">
                <div class="carousel-track" id="carouselTrack"></div>
            </div>
            <div class="carousel-btn btn-prev" id="prevBtn"><i class="fas fa-chevron-left"></i></div>
            <div class="carousel-btn btn-next" id="nextBtn"><i class="fas fa-chevron-right"></i></div>
            <div class="carousel-indicators" id="indicatorsContainer"></div>
            <a href="#presentation" class="hero-scroll"><i class="fas fa-chevron-down"></i></a>
        </div>

        <!-- ===== PRÉSENTATION ===== -->
        <section id="presentation" class="presentation-section" data-section="presentation">
            <div class="presentation-container">
                <div id="contenu_presentation" class="presentation-grid"></div>
            </div>
        </section>

        <!-- ===== ACTUALITÉS ===== -->
        <section id="actualites" class="evenements-section" data-section="actualites">
            <div class="section-header">
                <h2>Actualités du CIMeS</h2>
                <div class="divider-custom"></div>
                <p>Les dernières nouvelles de nos laboratoires et terrains de recherche.</p>
            </div>
            <div class="actu-grid" id="actu"></div>
            <div class="voir-plus">
                <a href="actualites.php" class="btn-outline">Voir toutes les actualités <i class="fas fa-arrow-right"></i></a>
            </div>
        </section>

        <!-- MODAL actualités -->
        <div id="actuModal" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close" id="closeModal"><i class="fas fa-times"></i></span>
                <img id="modalImg" src="" alt="">
                <p id="modalDate"></p>
                <h2 id="modalTitle"></h2>
                <p id="modalText"></p>
                <p id="modalLieu"><i class="fas fa-map-marker-alt"></i></p>
            </div>
        </div>

        <!-- ===== ACTIVITÉS ===== -->
        <section id="activites" class="programme-section" data-section="activites">
            <div class="section-header">
                <h2>Les Activités du CIMES</h2>
                <div class="divider-custom" style="background-color: var(--surface);"></div>
                <p>Les actions concrètes menées par le CIMES sur les territoires.</p>
            </div>
            <div class="actu-grid" id="activite"></div>
            <div class="voir-plus">
                <a href="activites.php" class="btn-outline">Voir toutes les activités <i class="fas fa-arrow-right"></i></a>
            </div>
        </section>

        <!-- MODAL activités -->
        <div id="actuModalActivite" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close" id="closeModalActivite"><i class="fas fa-times"></i></span>
                <img id="modalImgActivite" src="" alt="">
                <p id="modalDateActivite"></p>
                <h2 id="modalTitleActivite"></h2>
                <p id="modalTextActivite"></p>
                <p id="modalLieuActivite"><i class="fas fa-map-marker-alt"></i></p>
            </div>
        </div>

        <!-- ... le reste du contenu (axes, programmes, partenaires) est conservé à l'identique ... -->
        <!-- ===== AXES DE RECHERCHE ===== -->
        <section class="activites-section" data-section="axes">
            <div class="section-header">
                <h2>Nos axes de recherche innovants</h2>
                <div class="divider-custom"></div>
                <p>Thématiques majeures qui structurent notre activité scientifique et répondent aux enjeux des territoires de montagne.</p>
            </div>
            <div class="axes-container">
                <div id="axes" class="axes-grid-main"></div>
            </div>
            <div class="voir-plus">
                <a href="axes.php" class="btn-outline">Découvrir tous nos axes <i class="fas fa-arrow-right"></i></a>
            </div>
        </section>

        <!-- ===== PROGRAMMES CARROUSEL ===== -->
        <section id="programmes" class="programme-section" data-section="programmes">
            <div class="section-header">
                <h2>Les programmes du CIMES</h2>
                <div class="divider-custom" style="background-color: var(--surface);"></div>
                <p>Des initiatives concrètes pour un territoire durable et résilient.</p>
            </div>
            <div id="contenu_programme" class="prog-cards-grid"></div>
            <div class="voir-plus">
                <a href="programme.php" class="btn-outline">Voir tous les programmes <i class="fas fa-arrow-right"></i></a>
            </div>
        </section>

        <!-- MODAL programmes -->
        <div id="actuModalProgramme" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close" id="closeModalProgramme"><i class="fas fa-times"></i></span>
                <img id="modalImgProgramme" src="" alt="">
                <h2 id="modalTitleProgramme"></h2>
                <div id="modalTextProgramme" style="overflow-y: auto; max-height: 300px; padding-right: 8px;"></div>
            </div>
        </div>

        <!-- ===== BANDE STATISTIQUE ===== -->
        <div class="partners-section" data-section="partenaires">
            <h2>NOS PARTENAIRES</h2>
            <div class="divider-custom"></div>
            <div class="partners-marquee">
                <div class="partners-track" id="partnersTrack"></div>
            </div>
        </div>
    </main>

    <script>
        /* ============================================================
         *  HERO — carrousel infini avec clones
         * ============================================================ */
        function initCarrousel() {
            const track = document.getElementById('carouselTrack');
            if (!track) return;
            const slides = Array.from(track.children);
            const slideCount = slides.length;
            if (slideCount === 0) return;
            const DELAY = 5000;
            let cur = 1;
            let transitioning = false;
            let timer = null;
            const firstClone = slides[0].cloneNode(true);
            const lastClone = slides[slideCount - 1].cloneNode(true);
            track.appendChild(firstClone);
            track.insertBefore(lastClone, slides[0]);
            track.style.transition = 'none';
            track.style.transform = 'translateX(-100%)';
            void track.offsetWidth;
            const indBox = document.getElementById('indicatorsContainer');
            indBox.innerHTML = '';
            for (let i = 0; i < slideCount; i++) {
                const d = document.createElement('button');
                d.className = 'indicator-dot' + (i === 0 ? ' active' : '');
                d.addEventListener('click', () => {
                    if (!transitioning) {
                        moveTo(i + 1);
                        resetTimer();
                    }
                });
                indBox.appendChild(d);
            }

            function updateDots() {
                const dots = indBox.querySelectorAll('.indicator-dot');
                let idx = ((cur - 1) % slideCount + slideCount) % slideCount;
                dots.forEach((d, i) => d.classList.toggle('active', i === idx));
            }

            function setPosition(index) {
                track.style.transition = 'none';
                track.style.transform = `translateX(-${index * 100}%)`;
                void track.offsetWidth;
            }

            function moveTo(index, animated = true) {
                if (transitioning && animated) return;
                cur = index;
                transitioning = animated;
                if (animated) {
                    track.style.transition = 'transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                } else {
                    track.style.transition = 'none';
                    void track.offsetWidth;
                }
                track.style.transform = `translateX(-${cur * 100}%)`;
                updateDots();
                if (!animated) transitioning = false;
            }
            track.addEventListener('transitionend', () => {
                if (cur === slideCount + 1) {
                    setPosition(1);
                    cur = 1;
                } else if (cur === 0) {
                    setPosition(slideCount);
                    cur = slideCount;
                }
                transitioning = false;
            });
            document.getElementById('nextBtn').addEventListener('click', () => {
                if (!transitioning) {
                    moveTo(cur + 1);
                    resetTimer();
                }
            });
            document.getElementById('prevBtn').addEventListener('click', () => {
                if (!transitioning) {
                    moveTo(cur - 1);
                    resetTimer();
                }
            });

            function startTimer() {
                timer = setInterval(() => {
                    if (!transitioning) moveTo(cur + 1);
                }, DELAY);
            }

            function resetTimer() {
                clearInterval(timer);
                startTimer();
            }
            startTimer();
        }

        let carrousel = [];
        fetch('../cimes_api/index_api.php?query=carrousel')
            .then(r => r.json())
            .then(res => {
                carrousel = res.slice(0, 4);
                renderCarrousel();
            })
            .catch(err => console.error('Erreur CARROUSEL:', err));

        const renderCarrousel = () => {
            const container = document.getElementById('carouselTrack');
            if (!container || !carrousel.length) return;
            const liens = [{
                    href: '#presentation',
                    label: 'Découvrir'
                },
                {
                    href: '#activites',
                    label: 'Les activités'
                },
                {
                    href: '#programmes',
                    label: 'Les programmes'
                },
                {
                    href: '#actualites',
                    label: 'Actualités'
                },
            ];
            let html = '';
            carrousel.forEach((item, index) => {
                html += `
                <li class="carousel-slide">
                    <img src="img/${item.image}" alt="${item.titre}">
                    <div class="slide-overlay"></div>
                    <div class="slide-content">
                        <h2>${item.titre}</h2>
                        <h1>${item.sous_titre}</h1>
                        <a href="${liens[index].href}" class="btn-hero">${liens[index].label}</a>
                    </div>
                </li>`;
            });
            container.innerHTML = html;
            initCarrousel();
        };

        /* ============================================================
         *  présentation
         * ============================================================ */
        fetch('../cimes_api/index_api.php?query=presentation')
            .then(r => r.json())
            .then(res => {
                renderPresentation(res[0]);
            })
            .catch(err => console.error('Erreur PRESENTATION:', err));

        function renderPresentation(data) {
            const container = document.querySelector('#contenu_presentation');
            container.innerHTML = `
                <div class="presentation-text">
                    <h2>Présentation du GIS-CIMES</h2>
                    <div class="divider-custom"></div>
                    <p>${data.texte}</p>
                    <a href="missions.php?id=9" class="btn-primary">
                        Les missions du CIMES <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="presentation-image">
                    <img src="img/${data.image}" alt="Massif pyrénéen">
                </div>`;
        }

        /* ============================================================
         *  ACTUALITÉS
         * ============================================================ */
        let dataActu = [];
        fetch('../cimes_api/index_api.php?query=actu')
            .then(r => r.json())
            .then(res => {
                dataActu = res.sort((a, b) => new Date(b.date) - new Date(a.date)).reverse();
                renderActu();
            })
            .catch(err => console.error('Erreur ACTU:', err));

        function renderActu() {
            const container = document.querySelector('#actu');
            if (!container || !dataActu.length) return;
            container.innerHTML = '';
            dataActu.slice(0, 4).forEach(item => {
                const dateFr = new Date(item.date + 'T00:00:00').toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const card = document.createElement('div');
                card.className = 'card_actu';
                card.innerHTML = `
                    <div class="card-side-date"><span>${dateFr}</span></div>
                    <div class="card-body">
                        <img src="img/${item.image}" class="card-thumb" alt="">
                        <div class="card-content">
                            <h3>${item.titre}</h3>
                            <p class="lieu"><i class="fas fa-map-marker-alt"></i> ${item.lieu ?? ''}</p>
                            <p>${item.description_courte}</p>
                            <a href="#" class="actu-link" data-id="${item.id}">En savoir plus <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>`;
                container.appendChild(card);
            });
            bindModalActu();
        }

        const modal = document.getElementById('actuModal');
        const closeModal = document.getElementById('closeModal');

        function bindModalActu() {
            document.querySelectorAll('.actu-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const item = dataActu.find(a => a.id == link.dataset.id);
                    if (!item) return;
                    const dateFr = new Date(item.date + 'T00:00:00').toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('modalTitle').textContent = item.titre;
                    document.getElementById('modalDate').textContent = dateFr;
                    document.getElementById('modalImg').src = 'img/' + item.image;
                    document.getElementById('modalText').textContent = item.description_longue;
                    document.getElementById('modalLieu').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${item.lieu ?? ''}`;
                    modal.style.display = 'flex';
                });
            });
        }
        closeModal.onclick = () => {
            modal.style.display = 'none';
        };
        window.addEventListener('click', e => {
            if (e.target === modal) modal.style.display = 'none';
        });

        /* ============================================================
         *  ACTIVITÉS — AVEC BANDEAU DATE ET DATE DANS LE MODAL
         * ============================================================ */
        let dataActivite = [];
        fetch('../cimes_api/index_api.php?query=activites')
            .then(r => r.json())
            .then(res => {
                dataActivite = res.slice(0, 4);
                renderActivite();
            })
            .catch(err => console.error('Erreur ACTIVITES:', err));

        function renderActivite() {
            const container = document.querySelector('#activite');
            if (!container || !dataActivite.length) return;
            container.innerHTML = '';
            dataActivite.forEach(item => {
                const dateFr = new Date(item.date + 'T00:00:00').toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const card = document.createElement('div');
                card.className = 'card_actu'; // même classe que les actualités pour partager le style
                card.innerHTML = `
                    <div class="card-side-date"><span>${dateFr}</span></div>
                    <div class="card-body">
                        <img src="img/${item.image}" class="card-thumb" alt="">
                        <div class="card-content">
                            <h3>${item.titre}</h3>
                            <p class="lieu"><i class="fas fa-map-marker-alt"></i> ${item.lieu ?? ''}</p>
                            <p>${item.description_courte}</p>
                            <a href="#" class="actu-link-activite" data-id="${item.id}">En savoir plus <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>`;
                container.appendChild(card);
            });
            bindModalActivite();
        }

        const modalA = document.getElementById('actuModalActivite');
        const closeModalA = document.getElementById('closeModalActivite');

        function bindModalActivite() {
            document.querySelectorAll('.actu-link-activite').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const item = dataActivite.find(a => a.id == link.dataset.id);
                    if (!item) return;
                    const dateFr = new Date(item.date + 'T00:00:00').toLocaleDateString('fr-FR', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('modalTitleActivite').textContent = item.titre;
                    document.getElementById('modalDateActivite').textContent = dateFr; // activé
                    document.getElementById('modalImgActivite').src = 'img/' + item.image;
                    document.getElementById('modalTextActivite').textContent = item.description_longue;
                    document.getElementById('modalLieuActivite').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${item.lieu ?? ''}`;
                    modalA.style.display = 'flex';
                });
            });
        }
        closeModalA.onclick = () => {
            modalA.style.display = 'none';
        };
        window.addEventListener('click', e => {
            if (e.target === modalA) modalA.style.display = 'none';
        });


        let dataAxes = [];
        fetch('../cimes_api/index_api.php?query=axes')
            .then(r => r.json())
            .then(res => {
                dataAxes = res.slice(0, 8);
                renderAxes();
            })
            .catch(err => console.error('Erreur AXES:', err));
        const renderAxes = () => {
            const container = document.getElementById('axes');
            if (!container || !dataAxes.length) return;
            let carte = '';
            dataAxes.forEach(item => {
                carte += `
                <div class="axe-card">
                    <div class="axe-img-wrapper">
                        <img src="img/${item.image}" alt="${item.titre}">
                    </div>
                    <div class="axe-content">
                        <h3>${item.titre}</h3>
                        <p>${item.description}</p>
                    </div>
                </div>`;
            });
            container.innerHTML = carte;
        };

        let dataProgrammes = [];
        fetch('../cimes_api/index_api.php?query=programme')
            .then(r => r.json())
            .then(res => {
                // On garde uniquement les 4 derniers programmes (les plus récents)
                const derniers = res.slice(-4);
                dataProgrammes = derniers;
                renderProgrammes(derniers);
            })
            .catch(err => console.error('Erreur PROGRAMMES:', err));

        function renderProgrammes(data) {
            const container = document.querySelector('#contenu_programme');
            if (!container) return;
            let html = '';
            data.forEach(item => {
                html += `
                <div class="prog-card">
                    <img src="img/${item.image}" alt="${item.titre}" class="prog-card-img">
                    <div class="prog-card-body">
                        <h3>${item.titre}</h3>
                        <p>${item.texte_court ?? item.texte}</p>
                        <a href="#" class="prog-link" data-id="${item.id}">
                            En savoir plus <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
            bindModalProgramme();
        }
        const modalP = document.getElementById('actuModalProgramme');
        const closeModalP = document.getElementById('closeModalProgramme');

        function bindModalProgramme() {
            document.querySelectorAll('.prog-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const item = dataProgrammes.find(p => p.id == link.dataset.id);
                    if (!item) return;
                    document.getElementById('modalTitleProgramme').textContent = item.titre;
                    document.getElementById('modalImgProgramme').src = 'img/' + item.image;
                    document.getElementById('modalTextProgramme').textContent = item.texte_long ?? item.texte;
                    modalP.style.display = 'flex';
                });
            });
        }
        closeModalP.onclick = () => {
            modalP.style.display = 'none';
        };
        window.addEventListener('click', e => {
            if (e.target === modalP) modalP.style.display = 'none';
        });

        let logo = "";
        fetch('../cimes_api/index_api.php?query=logo_partenaire')
            .then(r => r.json())
            .then(res => {
                renderLogo(res);
            })
            .catch(err => console.error('Erreur LOGO:', err));

        function renderLogo(data) {
            const container = document.querySelector('#partnersTrack');
            const doubledData = [...data, ...data];
            container.innerHTML = doubledData.map(item => `
                <div class="partner-logo">
                    <img src="img/${item.logo}" alt="${item.alt}">
                </div>
            `).join('');
        }

        (function() {
            fetch('../cimes_api/index_api_sections.php?query=get_sections')
                .then(r => r.json())
                .then(sections => {
                    sections.forEach(s => {
                        const el = document.querySelector(`[data-section="${s.section_key}"]`);
                        if (!el) return;
                        el.style.display = s.visible == 0 ? 'none' : '';
                    });
                    const parent = document.getElementById('sections-wrapper');
                    sections.forEach(s => {
                        const el = parent.querySelector(`[data-section="${s.section_key}"]`);
                        if (el) parent.appendChild(el);
                    });
                })
                .catch(err => console.error('Erreur sections visibilité :', err));
        })();
    </script>

    <?php include('include/footer.html'); ?>
</body>

</html>
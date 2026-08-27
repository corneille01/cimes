<?php include('include/head.html') ?>
<title>Axes de Recherche - CIMES</title>

<style>
    :root {
        --vert: #0F766E;
        --vert-dark: #134e4a;
        --vert-light: #E1F5EE;
        --vert-mid: #9FE1CB;
        --vert-text: #C9D95B;
        --bg: #f0f4f3;
        --surface: #ffffff;
        --border: #e2e8f0;
        --text: #1e293b;
        --muted: #64748b;
        --radius: 20px;
        /* Un peu plus arrondi pour le côté "innovation" */
        --radius-sm: 8px;
        --cimes-bg: var(--vert-dark);
        --cimes-hover: var(--vert);
    }



    /* --- HERO SECTION --- */
    .parallax-research {
        background-image: linear-gradient(rgba(19, 78, 74, 0.8), rgba(19, 78, 74, 0.8)), url("img/mountain.jpg");
        height: 400px;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 20px;
        padding-top: 100px;
    }

    .titre_page_research {
        color: white;
        font-size: 2.5rem;
        text-transform: uppercase;
        margin-bottom: 10px;
        letter-spacing: 1px;
    }

    .sous_titre_research {
        color: var(--vert-mid);
        font-size: 1.1rem;
        text-align: center;
        font-weight: 300;
    }

    /* --- GRILLE DES AXES --- */
    .container_research_axes {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 40px;
        padding: 50px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* --- CARTE AXE DE RECHERCHE --- */
    .card_research {
        background: var(--surface);
        border-radius: var(--radius);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid var(--border);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        height: 100%;
    }

    .card_research:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 30px rgba(15, 118, 110, 0.1);
        border-color: var(--vert-mid);
    }

    /* Header de la carte avec l'image et le badge d'axe */
    .research-img-container {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .research-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card_research:hover .research-thumb {
        transform: scale(1.1);
    }

    .axe-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--vert-text);
        color: var(--vert-dark);
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.75rem;
        text-transform: uppercase;
        z-index: 2;
    }

    /* Contenu de la recherche */
    .research-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .research-content h3 {
        margin: 0 0 15px 0;
        font-size: 1.4rem;
        color: var(--vert-dark);
        line-height: 1.2;
        text-align: center;
    }


    .research-content p {
        max-height: 150px;
        overflow-y: auto;
        margin-right: 10px;
        text-align: justify;
    }

    .research-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .tag {
        background: var(--vert-light);
        color: var(--vert);
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 5px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .research-description {
        font-size: 0.95rem;
        line-height: 1.6;
        color: var(--muted);
        text-align: justify;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .research-footer {
        border-top: 1px solid var(--border);
        padding-top: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-details-research {
        color: var(--vert);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: 0.3s;
    }

    .btn-details-research:hover {
        color: var(--vert-dark);
        padding-left: 5px;
    }

    /* Animation d'entrée */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card_research {
        animation: fadeInUp 0.6s ease forwards;
    }
</style>

</head>

<body>
    <?php include('include/header.html') ?>

    <div class="parallax-research">
        <h1 class="titre_page_research">Axes de Recherche Innovants</h1>
        <p class="sous_titre_research">
            Le CIMES structure ses travaux autour de thématiques de pointe pour répondre aux enjeux socio-environnementaux des territoires de montagne.
        </p>
    </div>

    <!-- Section des axes -->
    <main class="container_research_axes" id="research-container">



    </main>

    <?php include('include/footer.html') ?>
    <script>
        // axes
        fetch('../cimes_api/index_api.php?query=axes')
            .then(r => r.json())
            .then(res => {
                // On limite à 8 résultats comme dans ton code original
                const dataAxes = res.slice(0, 8)
                renderAxes(dataAxes);
            })
            .catch(err => console.error('Erreur AXES:', err));

        const renderAxes = (dataAxes) => {
            const container = document.getElementById('research-container');
            if (!container || !dataAxes.length) return;

            let carte = '';
            container.innerHTML = '';

            // Utilisation de (item, index) pour dynamiser les chiffres et délais
            dataAxes.forEach((item, index) => {
                const numeroAxe = index + 1; // Commence à 1 au lieu de 0
                // const delaiAnimation = index * 0.2; // Ajoute 0.2s de délai par carte

                carte += `
                <article class="card_research" s">
                    <div class="axe-badge">Axe ${numeroAxe}</div>
                    <div class="research-img-container">
                        <img src="img/${item.image}" class="research-thumb" alt="${item.titre}" onerror="this.src='img/default.jpg'">
                    </div>
                    <div class="research-content">
                        <h3>${item.titre}</h3>
                        <p class="research-description">
                            ${item.description || 'Description à venir.'}
                        </p>
                    </div>
                </article>`;
            });

            container.innerHTML = carte;
        };
    </script>
</body>

</html>
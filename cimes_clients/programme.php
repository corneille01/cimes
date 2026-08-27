<?php include('include/head.html') ?>
<title>Programmes - CIMES</title>

<style>
    :root {
        --vert: #0F766E;
        --vert-dark: #134e4a;
        --vert-mid: #9FE1CB;
        --bg: #f0f4f3;
        --surface: #ffffff;
        --border: #e2e8f0;
        --text: #1e293b;
        --muted: #64748b;
        --radius: 2px;
        --radius-sm: 8px;
        --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.05);
        --transition: 0.3s ease;
    }

    body {
        background-color: var(--bg);
        color: var(--text);
        margin: 0;
    }

    /* Hero */
    .prog-parallax {
        background-image: linear-gradient(rgba(19, 78, 74, 0.85), rgba(19, 78, 74, 0.85)), url("img/mountain.jpg");
        height: 300px;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 0 20px;
    }

    .prog-titre-page {
        color: white;
        font-size: 2.8rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0 0 10px 0;
    }

    .prog-sous-titre {
        color: var(--vert-mid);
        font-size: 1.1rem;
        font-weight: 300;
    }

    .prog-section-title {
        text-align: center;
        font-size: 1.8rem;
        color: var(--vert-dark);
        margin: 40px 0 20px;
        font-weight: 600;
    }

    /* Conteneur large */
    .prog-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0 20px 60px;
        gap: 30px;
        max-width: 1900px;
        margin: 0 auto;
    }

    /* Carte : empilement vertical */
    .prog-card {
        background: var(--surface);
        width: calc(33.333% - 20px);
        min-width: 280px;
        max-width: 600px;
        height: 500px;
        /* hauteur fixe */
        border-radius: var(--radius);
        display: flex;
        flex-direction: column;
        /* empilement vertical */
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        transition: transform var(--transition), box-shadow var(--transition);
        opacity: 0;
        transform: translateY(25px);
    }

    .prog-card.prog-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .prog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    /* Image agrandie */
    .prog-card-thumb {
        width: 100%;
        height: 320px;
        /* plus grande */
        object-fit: cover;
        flex-shrink: 0;
    }

    /* Contenu */
    .prog-card-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
        /* pour que la scrollbar soit dans la desc */
    }

    .prog-card-content h3 {
        margin: 0 0 10px 0;
        font-size: 1.15rem;
        color: var(--vert-dark);
        flex-shrink: 0;
        /* pas de scrollbar sur le titre, on laisse déborder si trop long */
        overflow: hidden;
        text-align: center;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .prog-description-scroll {
        flex-grow: 1;
        overflow-y: auto;
        max-height: 100%;
        /* occupe l'espace restant */
        font-size: 0.9rem;
        line-height: 1.5;
        color: var(--muted);
        padding-right: 5px;
        text-align: justify;
        /* masquer la scrollbar si non nécessaire */
    }

    /* Personnalisation de la scrollbar */
    .prog-description-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .prog-description-scroll::-webkit-scrollbar-thumb {
        background: var(--vert-mid);
        border-radius: 10px;
    }

    .prog-empty,
    .prog-error {
        color: var(--muted);
        text-align: center;
        width: 100%;
        padding: 40px 0;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .prog-card {
            width: calc(50% - 15px);
            max-width: 600px;
        }
    }

    @media (max-width: 700px) {
        .prog-card {
            width: 100%;
        }

        .prog-card-thumb {
            height: 180px;
            /* légèrement réduit sur mobile */
        }
    }
</style>

</head>

<body>
    <?php include('include/header.html') ?>

    <header class="prog-parallax">
        <h1 class="prog-titre-page">Nos Programmes</h1>
        <p class="prog-sous-titre">
            Des initiatives concrètes pour un territoire durable et résilient.
        </p>
    </header>

    <h2 class="prog-section-title">Découvrez nos programmes de recherche</h2>

    <main id="prog-container" class="prog-container">
        <p class="prog-empty">Chargement des programmes…</p>
    </main>

    <?php include('include/footer.html') ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('prog-container');

            fetch('../cimes_api/index_api.php?query=programme')
                .then(r => r.json())
                .then(res => {
                    res.sort((a, b) => a.titre.localeCompare(b.titre));
                    renderProgramme(res);
                })
                .catch(err => {
                    console.error('Erreur API Programmes:', err);
                    container.innerHTML = '<p class="prog-error">Erreur lors du chargement des programmes.</p>';
                });

            function renderProgramme(data) {
                if (!container || !data.length) {
                    container.innerHTML = '<p class="prog-empty">Aucun programme disponible pour le moment.</p>';
                    return;
                }

                container.innerHTML = '';

                data.forEach((item, index) => {
                    const card = document.createElement('div');
                    card.className = 'prog-card';

                    const titre = item.titre || 'Programme sans titre';
                    const description = item.texte || 'Pas de description.';
                    const image = item.image || 'default.jpg';

                    // Carte simplifiée : image, titre, description (sans barre latérale ni bouton)
                    card.innerHTML = `
                        <img src="img/${image}" class="prog-card-thumb" alt="${titre}" 
                             onerror="this.src='img/default.jpg'" loading="lazy">
                        <div class="prog-card-content">
                            <h3 title="${titre}">${titre}</h3>
                            <div class="prog-description-scroll">${description}</div>
                        </div>`;

                    container.appendChild(card);

                    setTimeout(() => {
                        card.classList.add('prog-visible');
                    }, index * 100);
                });
            }
        });
    </script>
</body>

</html>
<?php include('include/head.html') ?>
<title>Activités - CIMES</title>

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
        --radius: 14px;
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
    .actv-parallax {
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

    .actv-titre-page {
        color: white;
        font-size: 2.8rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0 0 10px 0;
    }

    .actv-sous-titre {
        color: var(--vert-mid);
        font-size: 1.1rem;
        font-weight: 300;
    }

    .actv-section-title {
        text-align: center;
        font-size: 1.8rem;
        color: var(--vert-dark);
        margin: 40px 0 20px;
        font-weight: 600;
    }

    /* Conteneur large (1900px) pour 3 cartes */
    .actv-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0 20px 60px;
        gap: 30px;
        max-width: 1900px;
        /* ← 1900px */
        margin: 0 auto;
    }

    /* Carte : largeur calculée pour 3 par ligne dans 1900px */
    .actv-card {
        background: var(--surface);
        width: calc(33.333% - 20px);
        /* 3 cartes avec gap de 30px */
        min-width: 280px;
        max-width: 600px;
        /* s'adapte mieux à 1900px */
        height: 500px;
        border-radius: var(--radius);
        display: flex;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        transition: transform var(--transition), box-shadow var(--transition);
        opacity: 0;
        transform: translateY(25px);
    }

    .actv-card.actv-visible {
        opacity: 1;
        transform: translateY(0);
    }

    .actv-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    /* Bandeau date vertical */
    .actv-card-side-date {
        background: var(--vert);
        color: white;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .actv-card-side-date span {
        display: inline-block;
        transform: rotate(-90deg);
        white-space: nowrap;
        font-weight: bold;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1;
    }

    /* Corps */
    .actv-card-body-flex {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .actv-card-thumb {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .actv-card-content {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .actv-card-content h3 {
        margin: 0 0 5px 0;
        font-size: 1.15rem;
        color: var(--vert-dark);
        max-height: 3rem;
        overflow-y: auto;
    }

    .actv-lieu {
        color: var(--vert);
        font-size: 0.85rem;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .actv-description-scroll {
        flex-grow: 1;
        overflow-y: auto;
        max-height: 120px;
        font-size: 0.9rem;
        line-height: 1.5;
        color: var(--muted);
        padding-right: 5px;
        margin-bottom: 10px;
        text-align: justify;
    }

    .actv-description-scroll::-webkit-scrollbar,
    .actv-card-content h3::-webkit-scrollbar {
        width: 4px;
    }

    .actv-description-scroll::-webkit-scrollbar-thumb {
        background: var(--vert-mid);
        border-radius: 10px;
    }

    .actv-empty,
    .actv-error {
        color: var(--muted);
        text-align: center;
        width: 100%;
        padding: 40px 0;
    }

    /* Responsive : 2 puis 1 colonne */
    @media (max-width: 1400px) {
        .actv-card {
            width: calc(50% - 15px);
            max-width: 600px;
        }
    }

    @media (max-width: 700px) {
        .actv-card {
            width: 100%;
        }
    }
</style>

</head>

<body>
    <?php include('include/header.html') ?>

    <header class="actv-parallax">
        <h1 class="actv-titre-page">Nos Activités</h1>
        <p class="actv-sous-titre">
            Explorez les territoires de montagne à travers nos ateliers, conférences et sorties de terrain.
        </p>
    </header>

    <h2 class="actv-section-title">Découvrez nos activités</h2>

    <main id="actv-container" class="actv-container">
        <p class="actv-empty">Chargement des activités…</p>
    </main>

    <?php include('include/footer.html') ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('actv-container');

            fetch('../cimes_api/index_api.php?query=activites')
                .then(r => r.json())
                .then(res => {
                    res.sort((a, b) => new Date(b.date) - new Date(a.date));
                    renderActivite(res);
                })
                .catch(err => {
                    console.error('Erreur API Activités:', err);
                    container.innerHTML = '<p class="actv-error">Erreur lors du chargement.</p>';
                });

            function formatDate(dateStr) {
                if (!dateStr || dateStr === '0000-00-00') return 'Date indéfinie';
                const d = new Date(dateStr + 'T00:00:00');
                if (isNaN(d.getTime())) return 'Date indéfinie';
                return d.toLocaleDateString('fr-FR', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function renderActivite(data) {
                if (!container || !data.length) {
                    container.innerHTML = '<p class="actv-empty">Aucune activité pour le moment.</p>';
                    return;
                }

                container.innerHTML = '';

                data.forEach((item, index) => {
                    const card = document.createElement('div');
                    card.className = 'actv-card';

                    const dateFr = formatDate(item.date);
                    const titre = item.titre || 'Activité sans titre';
                    const lieu = item.lieu || 'Lieu non défini';
                    const image = item.image || 'default.jpg';
                    const description = item.description_longue || 'Pas de description disponible.';

                    card.innerHTML = `
                        <div class="actv-card-side-date"><span>${dateFr}</span></div>
                        <div class="actv-card-body-flex">
                            <img src="img/${image}" class="actv-card-thumb" alt="${titre}" 
                                 onerror="this.src='img/default.jpg'" loading="lazy">
                            <div class="actv-card-content">
                                <h3>${titre}</h3>
                                <p class="actv-lieu"><i class="fas fa-map-marker-alt"></i> ${lieu}</p>
                                <div class="actv-description-scroll">${description}</div>
                            </div>
                        </div>`;

                    container.appendChild(card);

                    setTimeout(() => {
                        card.classList.add('actv-visible');
                    }, index * 100);
                });
            }
        });
    </script>
</body>

</html>
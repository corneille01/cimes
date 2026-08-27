<?php include('include/head.html') ?>
<title>Actualités - CIMES</title>

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
        --radius: 14px;
        --radius-sm: 8px;
        --cimes-bg: var(--vert-dark);
        --cimes-hover: var(--vert);
    }

    body {
        background-color: var(--bg);
        color: var(--text);
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
    }

    .parallax {
        background-image: linear-gradient(rgba(19, 78, 74, 0.85), rgba(19, 78, 74, 0.85)), url("img/mountain.jpg");
        height: 300px;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        padding: 0 20px;
    }

    .titre_page {
        color: white;
        font-size: 2.8rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0 0 10px 0;
        width: 100%;
        text-align: center !important;
    }

    .sous_titre_page {
        color: var(--vert-mid);
        font-size: 1.1rem;
        margin-top: 10px;
        font-weight: 300;
        max-width: 800px;
        width: 100%;
        text-align: center !important;
    }

    #breadcrumb-container {
        padding: 20px 50px;
    }

    .actu-section-title {
        text-align: center;
        font-size: 1.8rem;
        color: var(--vert-dark);
        margin: 40px 0 20px;
        font-weight: 600;
    }

    .container_actu_event {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0 20px 60px;
        gap: 30px;
        margin-bottom: 50px;
        max-width: 1900px;
        margin-left: auto;
        margin-right: auto;
    }

    .card_actu {
        background: var(--surface);
        width: calc(33.333% - 20px);
        min-width: 280px;
        height: 500px;
        border-radius: var(--radius);
        display: flex;
        overflow: hidden;
        border: 1px solid var(--border);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card_actu:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .card-side-date {
        background: var(--vert);
        color: white;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card-body-flex {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        overflow: hidden;
    }

    .card-thumb {
        width: 100%;
        height: 290px;
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
        font-size: 1.15rem;
        color: var(--vert-dark);
        text-align: center;
    }

    .lieu {
        color: var(--vert);
        font-size: 0.85rem;
        margin-bottom: 10px;
        font-weight: 500;
    }

    .card-description-scroll {
        flex-grow: 1;
        overflow-y: auto;
        font-size: 0.9rem;
        line-height: 1.5;
        color: var(--muted);
        padding-right: 5px;
        margin-bottom: 10px;
        max-height: 280px;
        text-align: justify;
    }

    .card-description-scroll::-webkit-scrollbar,
    .card-content h3::-webkit-scrollbar {
        width: 4px;
    }

    .card-description-scroll::-webkit-scrollbar-thumb {
        background: var(--vert-mid);
        border-radius: 10px;
    }

    .actu-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--cimes-bg);
        color: white !important;
        text-decoration: none;
        padding: 8px;
        border-radius: var(--radius-sm);
        font-size: 0.8rem;
        transition: 0.2s;
    }

    .actu-link:hover {
        background: var(--cimes-hover);
    }

    @media (max-width: 1400px) {
        .card_actu {
            width: calc(50% - 15px);
        }
    }

    @media (max-width: 700px) {
        .card_actu {
            width: 100%;
        }
    }
</style>

</head>

<body>
    <?php include('include/header.html') ?>

    <div class="parallax">
        <h1 class="titre_page">Actualités</h1>
        <p class="sous_titre_page">Retrouvez toute la vie de notre centre de recherche et les temps forts du réseau.</p>
    </div>

    <div id="breadcrumb-container"></div>

    <h2 class="actu-section-title">Découvrez nos actualités</h2>

    <section class="container_actu_event" id="actu">
        <div style="color:var(--muted)">Chargement des actualités...</div>
    </section>

    <?php $id_cat = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>
    <input type="hidden" id="main-id" value="<?php echo $id_cat; ?>">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const idCat = document.getElementById('main-id').value;
            const container = document.querySelector('#actu');

            const apiUrl = `../cimes_api/index_api.php?query=actu`

            fetch(apiUrl)
                .then(r => r.json())
                .then(res => {
                    let dataActu = res.sort((a, b) => new Date(b.date) - new Date(a.date)).reverse();

                    if (!dataActu.length) {
                        container.innerHTML = "<p>Aucune actualité pour le moment.</p>";
                        return;
                    }

                    container.innerHTML = '';
                    dataActu.forEach(item => {
                        const dateFr = new Date(item.date + 'T00:00:00').toLocaleDateString('fr-FR', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        });

                        const card = document.createElement('div');
                        card.className = 'card_actu';
                        card.innerHTML = `
                        <div class="card-side-date"><span>${dateFr}</span></div>
                        <div class="card-body-flex">
                            <img src="img/${item.image}" class="card-thumb" alt="${item.titre}" onerror="this.src='img/default.jpg'">
                            <div class="card-content">
                                <h3>${item.titre}</h3>
                                <p class="lieu"><i class="fas fa-map-marker-alt"></i> ${item.lieu ?? 'Lieu non défini'}</p>
                                <div class="card-description-scroll">
                                    ${item.description_longue || 'Pas de description disponible.'}
                                </div>
                               
                            </div>
                        </div>`;
                        container.appendChild(card);
                    });
                })
                .catch(err => {
                    console.error('Erreur ACTU:', err);
                    container.innerHTML = "<p>Erreur lors du chargement.</p>";
                });
        });
    </script>

    <?php include('include/footer.html') ?>
</body>

</html>
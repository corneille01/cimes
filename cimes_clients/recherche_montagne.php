<?php include('include/head.html') ?>
<title>Recherche Montagne</title>
<meta name="description" content="">
</head>

<body>
<?php include('include/header.html') ?>

<style>
    .parallax {
        background-image: url("img/mountain.jpg");
        
    }

    

    .container_recherche_montagne {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 20px;
        gap: 20px;
    margin-bottom: 20px;

    }

    /* Style pour les cartes */
.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px; /* Largeur fixe pour les cartes */
}

/* Conteneur pour l'image */
.card-image {
    width: 100%;
    height: 200px; /* Hauteur fixe pour les images */
    overflow: hidden;
}

/* Style pour l'image */
.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ajuste l'image pour couvrir le conteneur sans déformation */
}

/* Contenu de la carte */
.card-content {
    padding: 15px;
    text-align: center;
}


    .card h2 {
        font-size: 2em;
        margin-bottom: 10px;
        color: #333;
    }

    .card p {
        color: #666;
        font-size: 1em;
    }

    .card:hover {
       
    }
    .btn-card{
        background-color: white;
    padding: 0;
    }
</style>

<!-- Container element -->
<div class="parallax">
    <h1 class="titre_page">Recherche Montagne</h1>
</div>
<div id="breadcrumb-container"></div>

<section class="container_recherche_montagne" id="recherche_montagne">
    <!-- Contenu dynamique inséré ici -->
</section>
<?php $id = htmlspecialchars($_GET['id']); ?>
<input type="hidden" id="main-id" value="<?php echo $id; ?>">
<script src="js/recherche_montagne.js"></script>
<?php include('include/footer.html') ?>
</body>
</html>

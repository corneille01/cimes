<?php include('include/head.html') ?>
<title>Établissements membres</title>
<meta name="description" content="">
</head>

<body>
<?php include('include/header.html') ?>

<style>
    .parallax {
        background-image: url("img/sheeps.jpg");
        
    }

   

    .etablissement-container {
        padding: 20px;
    }

    .etablissement-grid {
        display: flex;
        flex-direction: column;
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .etablissement-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
    flex-direction: column-reverse;
}

.etablissement-item img {
    height: 100px; /* Ajustez selon vos besoins */
    object-fit: cover; /* Maintient les proportions */
    border-radius: 8px;
}


    .etablissement-info {
        flex: 1;
    }

    .etablissement-info h3 {
        margin: 0 0 10px;
        font-size: 1.5em;
        color: #333;
    }

    .etablissement-info p {
        margin: 5px 0;
        font-size: 1em;
        color: #555;
    }

    .etablissement-info a {
        color: #007BFF;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s;
    }

    .etablissement-info a:hover {
        color: #0056b3;
    }

    @media (max-width: 768px) {
        .parallax {
            min-height: 300px;
        }

        .titre_page {
            font-size: 2em;
        }

        

        .etablissement-item img {
            
            height: 80px;
        }
    }
</style>

<!-- Container element -->
<div class="parallax">
    <h1 class="titre_page">Établissements Scientifiques</h1>
</div>
<div id="breadcrumb-container"></div>

<div class="etablissement-container">
    <div class="etablissement-grid" id="etablissement">
        <!-- Contenu dynamique inséré ici -->
    </div>
</div>
<?php $id = htmlspecialchars($_GET['id']); ?>
<input type="hidden" id="main-id" value="<?php echo $id; ?>">
<script src="js/code_etablissement.js"></script>

<?php include('include/footer.html') ?>
</body>
</html>

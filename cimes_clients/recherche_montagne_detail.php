<?php
include('include/head.html');
?>
<title>Détails du Programme</title>
<meta name="description" content="">
</head>

<body>
    <?php include('include/header.html'); ?>

    <style>
        .titre_page {
            padding: 100px 0 50px;
            color: black;
            text-shadow: 0 0 BLACK;
            text-align: center;
            font-size: 3rem;
        }

        .auteur {
            color: grey;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            flex: 1;
            /* Permet à la section de se développer pour prendre l'espace disponible */
        }

        .programme-image img {
            width: 100%;
            height: 80vh;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .programme-title {
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }

        .programme-description {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 20px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 20px auto;
            /* Centrage horizontal */
            display: block;
            /* Assure que le bouton est un bloc pour que margin:auto fonctionne */
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>

    <!-- Container element -->
    <div class="container" id="recherche_montagne_detail">
        <!-- Le contenu dynamique sera injecté ici -->
    </div>


    <script src="js/recherche_montagne_detail.js"></script>
    <?php include('include/footer.html'); ?>
</body>

</html>
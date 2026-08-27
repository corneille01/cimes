<?php
include('include/head.html');
?>
<title>Programme</title>
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
     


.container {
    padding: 20px;
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    border-radius: 5px;
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
@media (max-width: 500px) {
    .programme-image img {
    height: auto;
    
}
}


</style>

<!-- Container element -->
<div class="container" id="programme-detail">
    <!-- Le contenu dynamique sera injecté ici -->
</div>



<script src="js/programme_detail.js"></script>
<?php include('include/footer.html'); ?>
</body>
</html>

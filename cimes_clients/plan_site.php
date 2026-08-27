<?php include('include/head.html')?>
<?php include('include/header.html')?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Plan du site</title>
  <meta name="description" content="">
  <style>
    .parallax {
      background-image: url("img/ilets.jpg");
     
    }
    
  </style>
</head>
<body>

<!-- Effet Parallaxe -->
<div class="parallax">
  <h1 class="titre_page">Plan du site</h1>
</div>

<!-- Arbre de navigation -->
<div class="tree" id="tree"></div>

<?php include('include/footer.html')?>
<!-- <script src="js/planMap.js"></script> -->
<script>
  fetch('../cimes_api/index_api_head.php?query=navbar')
    .then(response => response.json())
    .then(data => {
      console.log(data);
      let planDetail = '';
      data.forEach(ligne => {
        // Vérifier si ligne.sub_items existe et est un tableau
        if (Array.isArray(ligne.sub_items)) {
          planDetail += `<ul>
                          <li>
                          <span class="caret">${ligne.name}</span>
                          <ul class="nested">`;

          ligne.sub_items.forEach(subItem => {
            planDetail += `<li><a href="${subItem.url}?id=${subItem.id}">${subItem.name}</a></li>`;
          });

          planDetail += `  </ul>
                          </li>
                      </ul>`;
        }
      });
      document.querySelector('#tree').innerHTML = planDetail;

      // Ajouter les gestionnaires d'événements pour les boutons caret
      var toggler = document.getElementsByClassName("caret");
      for (var i = 0; i < toggler.length; i++) {
        toggler[i].addEventListener("click", function() {
          this.parentElement.querySelector(".nested").classList.toggle("active");
          this.classList.toggle("caret-down");
        });
      }
    })
    .catch(error => console.error('Erreur lors du chargement des données:', error));
</script>
</body>
</html>

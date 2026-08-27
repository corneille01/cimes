const id = document.getElementById('main-id').value;
let contenu_tableau = '';
let breadcrumb = '';
    fetch(`../cimes_api/api_breadcrumb.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            
            // Construct breadcrumb
            breadcrumb = `
                 <nav style="--bs-breadcrumb-divider: '>';position: relative;margin: 50px;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./index.php">Page principale</a></li>
                        <li class="breadcrumb-item active" aria-current="page">${data.prevName || 'Nom inconnu'}</li>
                        <li class="breadcrumb-item active" aria-current="page">${data.name || 'Nom inconnu'}</li>

                    </ol>
                </nav>`;
            document.querySelector("#breadcrumb-container").innerHTML = breadcrumb;})

    fetch("../cimes_api/index_api.php?query=gouvernance")
        .then(response => response.json())
        .then(data => {
            console.log(data);
            data.forEach(membre => {
                contenu_tableau += `
                <div class="membre-card">
                    <div class="membre-image">
                        <img src="../cimes_admin/img/img_gouvernance/${membre.image}" alt="${membre.nom}">
                    </div>
                    <div class="membre-info">
                        <h2 class="membre-name">${membre.nom}</h2>
                        <p class="membre-description">${membre.texte}</p>
                    </div>
                    
                </div>
                `;
            });
            document.querySelector("#gouvernance").innerHTML = contenu_tableau;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.querySelector("#gouvernance").innerHTML = '<p>Erreur lors de la récupération des données de gouvernance.</p>';
        });

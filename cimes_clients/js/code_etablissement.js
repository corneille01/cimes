    let contenu_bloc = '';
    const id = document.getElementById('main-id').value;
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
    fetch("../cimes_api/index_api.php?query=etablissement")
        .then(response => response.json())
        .then(data => {
            console.log(data);
            afficherBlocs(data);
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.querySelector("#etablissement").innerHTML = '<p>Erreur lors de la récupération des données des établissements.</p>';
        });

    function afficherBlocs(data) {
        contenu_bloc = '';
        data.forEach(etablissement => {
            contenu_bloc += `
                <div class="etablissement-item">
                <div class="etablissement-info">
                        <h3>${etablissement.nom}</h3>
                        <p><a href="mailto:${etablissement.mail}">${etablissement.mail}</a></p>
                    </div>
                    <img src="../cimes_admin/img/img_etablissement/${etablissement.image}" alt="${etablissement.nom}">
                    
                </div>
            `;
        });
        document.querySelector("#etablissement").innerHTML = contenu_bloc;
    }

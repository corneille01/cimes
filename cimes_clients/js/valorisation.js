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
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const valorisation = params.get('id');

    if (valorisation) {
        fetch(`../cimes_api/index_api.php?query=valorisation&id=${valorisation}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let rechercheDetail = '';
                    data.forEach(ligne => {
                        rechercheDetail += `
                        <div class="card">
                            <div class="card-image">
                                <img src="../cimes_admin/img/img_valorisation/${ligne.image}" alt="${ligne.titre}">
                            </div>
                            <div class="card-content">
                                <h2>${ligne.titre}</h2>
                                <p>${ligne.texte}<p>
                            </div>
                        </div>
                        `;
                    });
                    document.querySelector('#valorisation').innerHTML = rechercheDetail;
                } else {
                    document.querySelector('#valorisation').innerHTML = '<p>Aucun contenu pour cette page.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#valorisation').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
            });
    } else {
        document.querySelector('#valorisation').innerHTML = '<p>ID manquant.</p>';
    }
});

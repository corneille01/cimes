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
    const recherche_montagne = params.get('id');

    if (recherche_montagne) {
        fetch(`../cimes_api/index_api.php?query=recherche_montagne&id=${recherche_montagne}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let rechercheDetail = '';
                    data.forEach(ligne => {
                        rechercheDetail += `<a href="recherche_montagne_detail.php?id=${ligne.id}" class="btn-card">
                        <div class="card">
                            <div class="card-image">
                                <img src="../cimes_admin/img/img_recherche_montagne/${ligne.image}" alt="${ligne.titre}">
                            </div>
                            <div class="card-content">
                                <h2>${ligne.titre}</h2>
                            </div>
                        </div></a>
                        `;
                    });
                    document.querySelector('#recherche_montagne').innerHTML = rechercheDetail;
                } else {
                    document.querySelector('#recherche_montagne').innerHTML = '<p>Aucun contenu pour cette page.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#recherche_montagne').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
            });
    } else {
        document.querySelector('#recherche_montagne').innerHTML = '<p>ID manquant.</p>';
    }
});

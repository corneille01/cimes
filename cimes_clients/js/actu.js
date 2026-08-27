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
                const actu = params.get('id');
            
                if (actu) {
                    fetch(`../cimes_api/index_api.php?query=actu&id=${actu}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            console.log(data);
                            let rechercheDetail = '';

                            // Trier les données par date croissante
                            data.sort((a, b) => {
                                let dateA = new Date(...a.date.split('-').map((item, index) => index === 1 ? item - 1 : item));
                                let dateB = new Date(...b.date.split('-').map((item, index) => index === 1 ? item - 1 : item));
                                return dateA - dateB;
                            });

                            // Générer le HTML des cartes d'actualités triées
                            data.forEach(ligne => {
                                let [annee, mois, jour] = ligne.date.split('-');
                                let dateFormatee = `${jour}/${mois}/${annee}`;
                                rechercheDetail += `<div class="card_actu">
                                    <div class="card-date">${dateFormatee}</div>
                                    <div class="card-content">
                                        <div class="card-header"><img src="../cimes_admin/img/img_actu/${ligne.image}" alt="${ligne.titre}"></div>
                                        <div class="card-body">
                                            <h2 style="padding-left: 20px;">${ligne.titre}</h2>
                                        </div>
                                        <div class="savoir-btn"><a href="actu_event_detail.php?id=${ligne.id}">En savoir plus</a></div>
                                    </div>
                                </div>`;
                            });

                            document.querySelector('#actu').innerHTML = rechercheDetail;
                        } else {
                            document.querySelector('#actu').innerHTML = '<p>Aucun contenu pour cette page.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        document.querySelector('#actu').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
                    });

                } else {
                    document.querySelector('#actu').innerHTML = '<p>ID manquant.</p>';
                }
            });
            
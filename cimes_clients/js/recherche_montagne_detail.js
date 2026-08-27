document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const recherche_montagne_detail = params.get('id');

    if (recherche_montagne_detail) {
        fetch(`../cimes_api/index_api.php?query=recherche_montagne_detail&id=${recherche_montagne_detail}`)
            .then(response => response.json())
            .then(data => {
                 document.title = data[0].titre;
                console.log(data);

                if (data) {
                    // Formatage de la date
                    let [annee, mois, jour] = data[0].date.split('-');
                    let dateFormatee = `${jour}/${mois}/${annee}`;

                    // Construction du contenu de la page
                    const recherche_detail = `
                        <h2 class="titre_page">${data[0].titre}</h2>
                        <p class="auteur">Auteur : ${data[0].auteur}</p>
                        <p>${dateFormatee}</p>

                        <div class="programme-image">
                            <img src="../cimes_admin/img/img_recherche_montagne/${data[0].image}" alt="${data[0].titre}">
                        </div>
                        
                        <p class="programme-description">${data[0].texte}</p>
                    `;
                    
                    document.querySelector('#recherche_montagne_detail').innerHTML = recherche_detail;
                } else {
                    document.querySelector('#recherche_montagne_detail').innerHTML = '<p>Programme non trouvé.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#recherche_montagne_detail').innerHTML = '<p>Erreur lors de la récupération des détails du programme.</p>';
            });
    } else {
        document.querySelector('#recherche_montagne_detail').innerHTML = '<p>ID de programme manquant.</p>';
    }
});

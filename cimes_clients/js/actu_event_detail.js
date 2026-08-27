
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const act_event_Id = params.get('id');

    if (act_event_Id) {
        fetch(`../cimes_api/index_api.php?query=actu_event_detail&id=${act_event_Id}`)
            .then(response => response.json())
            .then(data => {
                  document.title = data[0].titre;
                console.log(data);
                if (data) {
                    let [annee, mois, jour] = data[0].date.split('-');
                    let dateFormatee = `${jour}/${mois}/${annee}`;
                    const actu_event_contenu = `
                    <h2 class="titre_page">${data[0].titre}</h2>
                    <p class="auteur">Par : ${data[0].etablissement}</p>
                        <p>${dateFormatee}</p>

                        <div class="programme-image">
                            <img src="../cimes_admin/img/img_actu_event/${data[0].image}" alt="${data[0].titre}">
                        </div>
                        
                        <p class="programme-description">${data[0].texte}</p>
                    `;
                    document.querySelector('#actu_event_detail').innerHTML = actu_event_contenu;
                } else {
                    document.querySelector('#actu_event_detail').innerHTML = '<p>Programme non trouvé.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#actu_event_detail').innerHTML = '<p>Erreur lors de la récupération des détails</p>';
            });
    } else {
        document.querySelector('#actu_event_detail').innerHTML = '<p>ID manquant.</p>';
    }
});

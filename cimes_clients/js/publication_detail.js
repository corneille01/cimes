
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const publicationId = params.get('id');

    if (publicationId) {
        fetch(`../cimes_api/index_api.php?query=publication_detail&id=${publicationId}`)
            .then(response => response.json())
            .then(data => {
                document.title = data[0].titre;
                console.log(data);
                if (data) {
                    let [annee, mois, jour] = data[0].date.split('-');
                    let dateFormatee = `${jour}/${mois}/${annee}`;
                    const publicationDetail = `
                    <h2 class="titre_page">${data[0].titre}</h2>
                    <p class="auteur">Auteur : ${data[0].auteur}</p>
                    <p>${dateFormatee}</p>
                        <div class="programme-image">
                            <img src="../cimes_admin/img/img_publication/${data[0].image}" alt="${data[0].titre}">
                        </div>
                        
                        <p class="programme-description">${data[0].texte}</p>
                    `;
                    document.querySelector('#publication-detail').innerHTML =publicationDetail ;
                    
                } else {
                    document.querySelector('#publication-detail').innerHTML = '<p>Programme non trouvé.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#publication-detail').innerHTML = '<p>Erreur lors de la récupération des détails du programme.</p>';
            });
    } else {
        document.querySelector('#publication-detail').innerHTML = '<p>ID de programme manquant.</p>';
    }
});

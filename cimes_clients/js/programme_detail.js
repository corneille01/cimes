
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const programmeId = params.get('id');

    if (programmeId) {
        fetch(`../cimes_api/index_api.php?query=programme_detail&id=${programmeId}`)
            .then(response => response.json())
            .then(data => {
                 document.title = data[0].titre;
                console.log(data);
                if (data) {
                    const programmeDetail = `
                    <h2 class="titre_page">${data[0].titre}</h2>
                        <div class="programme-image">
                            <img src="../cimes_admin/img/img_programme/${data[0].image}" alt="${data[0].titre}">
                        </div>
                        
                        <p class="programme-description">${data[0].texte}</p>
                       
                    `;
                    document.querySelector('#programme-detail').innerHTML = programmeDetail;
                } else {
                    document.querySelector('#programme-detail').innerHTML = '<p>Programme non trouvé.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.querySelector('#programme-detail').innerHTML = '<p>Erreur lors de la récupération des détails du programme.</p>';
            });
    } else {
        document.querySelector('#programme-detail').innerHTML = '<p>ID de programme manquant.</p>';
    }
});

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
fetch("../cimes_api/index_api.php?query=programme")
.then(response => response.json())
.then(data => {
    console.log(data);
    data.forEach(ligne => {
        contenu_tableau += `
        <div class="programme-card">
            <div class="icon">
                <img src="../cimes_admin/img/img_programme/${ligne.image}" alt="${ligne.nom}">
            </div>
            <div class="programme-content">
                <h2 class="programme-title">${ligne.titre}</h2>
                
                <a href="programme_detail.php?id=${ligne.id}" class="btn-more">En savoir plus</a>
            </div>
        </div>
        `;
    });
    document.querySelector("#programme").innerHTML = contenu_tableau;
});

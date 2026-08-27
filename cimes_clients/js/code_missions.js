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
fetch("../cimes_api/index_api.php?query=mission")
.then(response => response.json())
.then(data => {
    console.log(data);
    data.forEach(ligne => {
        contenu_tableau += `
        <div class="mission-card">
            <div class="icon">
                <img src="../cimes_admin/img/img_mission/${ligne.image}" alt="${ligne.nom}">
            </div>
            <div class="mission-content">
                <h2 class="mission-title">${ligne.nom}</h2>
                <p class="mission-description">${ligne.texte}</p>
            </div>
        </div>
        `;
    });
    document.querySelector("#apropos").innerHTML = contenu_tableau;
});

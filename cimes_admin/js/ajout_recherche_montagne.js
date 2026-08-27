document.title = `Ajouter dans la recherche sur la montagne`;
document.querySelector("#envoyer").addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    let thematique = document.querySelector("#thematique").value;
    let titre = document.querySelector("#titre").value;
    let date = document.querySelector("#date").value;
    let texte = document.querySelector("#texte").value;
    let inputfile = document.querySelector("#image");
    let id = document.querySelector("#main-id").value; // Récupère id
    
    if (inputfile.files.length > 0) {
        if (inputfile.files[0].type == "image/jpeg" || inputfile.files[0].type == "image/jpg" || inputfile.files[0].type == "image/png") {
            let image = inputfile.files[0].name;
            envoi_image(thematique, titre, date, texte, image, id);
        } else {
            document.querySelector("#erreur").innerHTML = "Vous n'avez pas sélectionné une image valide";
        }
    }
});

function envoi_donnees(thematique, titre, date, texte, image, id) {
    let data = {
        lien: lien,
        thematique: thematique,
        titre: titre,
        date: date,
        texte: texte,
        image: image,
        id:id
    };
    

    fetch('../cimes_api/api_dynam.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
        if (data === "ok") {
            document.querySelector("#erreur").innerHTML = `
                <p>La publication a bien été enregistrée</p>
                <a href="dynam.php?id=${id}" class="bouton_ajouter">Revenir à la page de gestion des recherche sur la montagne</a>
            `;
        } else {
            document.querySelector("#erreur").textContent = "Il y a un problème";
        }
    });
}

function envoi_image(thematique, titre, date, texte, image, id) {
    const formData = new FormData();
    formData.append('image_envoyee', document.querySelector("#image").files[0]);
    formData.append('lien', lien); // Ajout du lien pour déterminer le chemin côté serveur
    fetch('../cimes_api/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "ok") {
            envoi_donnees(thematique, titre, date, texte, image, id);
        } else if (data === "trop_lourd") {
            document.querySelector("#erreur").textContent = "L'image est trop volumineuse";
        } else if (data === "fichier_existant") {
            document.querySelector("#erreur").textContent = "Le fichier existe déjà, merci de modifier le nom du fichier";
        } else if (data === "fichier_pas_image") {
            document.querySelector("#erreur").textContent = "Le fichier n'est pas une image";
        }
    });
}

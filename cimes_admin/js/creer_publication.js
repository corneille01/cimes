document.title ='Ajouter une publication';

document.querySelector("#envoyer").addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default form submission

    let thematique = document.querySelector("#thematique").value;
    let titre = document.querySelector("#titre").value;
    let auteur = document.querySelector("#auteur").value;
    let date = document.querySelector("#date").value;
    let texte = document.querySelector("#texte").value;
    let inputfile = document.querySelector("#image");
    let massif = document.querySelector("#massif").value; // Récupère id_massif
    
    if (inputfile.files.length > 0) {
        if (inputfile.files[0].type == "image/jpeg" || inputfile.files[0].type == "image/jpg" || inputfile.files[0].type == "image/png") {
            let image = inputfile.files[0].name;
            envoi_image(thematique, titre, auteur, date, texte, image, massif);
        } else {
            document.querySelector("#erreur").innerHTML = "Vous n'avez pas sélectionné une image valide";
        }
    }
});

function envoi_donnees(thematique, titre, auteur, date, texte, image, massif) {
    let data = {
        lien: lien,
        thematique: thematique,
        titre: titre,
        auteur: auteur,
        date: date,
        texte: texte,
        image: image
    };

    if (lien === 'cree_publication') {
        data.massif = massif;
    }

    if (lien === 'modif_publication') {
        data.id = id; // Assurez-vous que id est défini quelque part dans votre code pour les modifications
    }

    fetch('../cimes_api/index_api.php', {
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
                <a href="data_publication.php?id=${massif}" class="bouton_ajouter">Revenir à la page de gestion des publications</a>
            `;
        } else {
            document.querySelector("#erreur").textContent = "Il y a un problème";
        }
    });
}

function envoi_image(thematique, titre, auteur, date, texte, image, massif) {
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
            envoi_donnees(thematique, titre, auteur, date, texte, image, massif);
        } else if (data === "trop_lourd") {
            document.querySelector("#erreur").textContent = "L'image est trop volumineuse";
        } else if (data === "fichier_existant") {
            document.querySelector("#erreur").textContent = "Le fichier existe déjà, merci de modifier le nom du fichier";
        } else if (data === "fichier_pas_image") {
            document.querySelector("#erreur").textContent = "Le fichier n'est pas une image";
        }
    });
}

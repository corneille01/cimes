document.title= 'Modifier une publication';

document.addEventListener('DOMContentLoaded', function() {
    let massifsData = [];

    // Charger les massifs dans le menu déroulant
    function loadMassifs(selectedMassifId = null) {
        fetch('./api_utilisateur.php?action=get_massifs')
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.massifs) {
                    massifsData = data.massifs; // Stocker les massifs pour une utilisation ultérieure
                    const massifSelect = document.getElementById('massif');
                    massifSelect.innerHTML = ''; // Clear existing options

                    data.massifs.forEach(massif => {
                        const option = document.createElement('option');
                        option.value = massif.id;
                        option.textContent = massif.nom;
                        massifSelect.appendChild(option);
                    });

                    if (selectedMassifId) {
                        massifSelect.value = selectedMassifId;
                    }
                }
            })
            .catch(error => console.error('Erreur lors du chargement des massifs:', error));
    }

    // Fonction pour afficher le formulaire de modification
    function showUpdateForm(publicationId) {
        fetch(`./api_utilisateur.php?action=get_publicationId&id=${publicationId}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);

                if (data.publication) {
                    const publication = data.publication;
                    document.getElementById('thematique').value = publication.thematique;
                    document.getElementById('titre').value = publication.titre;
                    document.getElementById('date').value = publication.date;
                    document.getElementById('texte').value = publication.texte;
                    document.getElementById('current_image').value = publication.image;
                    document.getElementById('publication_id').value = publicationId;

                    // Afficher l'image actuelle
                    const currentImagePreview = document.getElementById('currentImagePreview');
                    currentImagePreview.src = `../cimes_admin/img/img_publication/${publication.image}`;
                    currentImagePreview.style.display = 'block';

                    // Charger les massifs et sélectionner le bon
                    loadMassifs(publication.massif);

                    // Afficher le formulaire
                    document.getElementById('updatePublicationForm').style.display = 'block';
                } else {
                    alert('Publication non trouvée.');
                }
            })
            .catch(error => console.error('Erreur lors du chargement de la publication:', error));
    }

    // Extraire l'ID de la publication de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const publicationId = urlParams.get('id');
    if (publicationId) {
        showUpdateForm(publicationId);
    } else {
        console.error('ID de publication manquant dans l\'URL');
    }

    // Gestion de l'envoi du formulaire de modification
    document.getElementById('updatePublicationForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('./api_utilisateur.php?action=update_publication', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Publication mise à jour avec succès!');
                // Rediriger vers la page mes_publications.php
                window.location.href = 'mes_publications.php';
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => alert('Erreur: ' + error.message));
    });
});

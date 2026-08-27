document.title= 'Modifier une recherche';

document.addEventListener('DOMContentLoaded', function() {
    let thematiquesData = [];

    // Charger les thematiques dans le menu déroulant
    function loadThematiques(selectedThematiqueName = null) {
        fetch('./api_utilisateur.php?action=get_thematiques')
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.thematiques) {
                    thematiquesData = data.thematiques; // Stocker les thematiques pour une utilisation ultérieure
                    const thematiqueSelect = document.getElementById('thematique');
                    thematiqueSelect.innerHTML = ''; // Clear existing options

                    data.thematiques.forEach(thematique => {
                        const option = document.createElement('option');
                        option.value = thematique.nom; // Utilise le nom comme valeur
                        option.textContent = thematique.nom;
                        thematiqueSelect.appendChild(option);
                    });

                    if (selectedThematiqueName) {
                        thematiqueSelect.value = selectedThematiqueName;
                    }
                }
            })
            .catch(error => console.error('Erreur lors du chargement des thematiques:', error));
    }

    // Fonction pour afficher le formulaire de modification
    function showUpdateForm(rechercheId) {
        fetch(`./api_utilisateur.php?action=get_rechercheId&id=${rechercheId}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);

                if (data.recherche) {
                    const recherche = data.recherche;
                    document.getElementById('titre').value = recherche.titre;
                    document.getElementById('date').value = recherche.date;
                    document.getElementById('texte').value = recherche.texte;
                    document.getElementById('current_image').value = recherche.image;
                    document.getElementById('publication_id').value = rechercheId;

                    // Afficher l'image actuelle
                    const currentImagePreview = document.getElementById('currentImagePreview');
                    currentImagePreview.src = `../cimes_admin/img/img_recherche_montagne/${recherche.image}`;
                    currentImagePreview.style.display = 'block';

                    // Charger les thematiques et sélectionner le bon
                    loadThematiques(recherche.thematique);

                    // Afficher le formulaire
                    document.getElementById('updateRechercheForm').style.display = 'block';
                } else {
                    alert('Recherche non trouvée.');
                }
            })
            .catch(error => console.error('Erreur lors du chargement de la publication:', error));
    }

    // Extraire l'ID de la publication de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const rechercheId = urlParams.get('id');
    if (rechercheId) {
        showUpdateForm(rechercheId);
    } else {
        console.error('ID de publication manquant dans l\'URL');
    }

    // Gestion de l'envoi du formulaire de modification
    document.getElementById('updateRechercheForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('./api_utilisateur.php?action=update_recherche', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recherche mise à jour avec succès!');
                // Rediriger vers la page mes_recherches.php
                window.location.href = 'mes_recherches.php';
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => alert('Erreur: ' + error.message));
    });
});

document.title= 'Ajouter une nouvelle recherche';

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('recherche-form');
    const dir = '../cimes_admin/img/img_recherche_montagne/';  // Exemple de trajectoire

    // Charger les thématiques dans le menu déroulant
    fetch('./api_utilisateur.php?action=get_thematiques')
        .then(response => response.json())
        .then(data => {
            console.log(data);
            const thematiqueSelect = document.getElementById('thematique');
            if (data.thematiques && data.thematiques.length > 0) {
                data.thematiques.forEach(thematique => {
                    const option = document.createElement('option');
                    option.value = thematique.parent_id;
                    option.textContent = thematique.nom;
                    thematiqueSelect.appendChild(option);
                });
            } else {
                console.error('Aucune thématique trouvée.');
            }
        })
        .catch(error => console.error('Erreur lors du chargement des thématiques:', error));

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        const imageFile = formData.get('image');
        
        // Vérifier si l'image existe déjà
        if (imageFile) {
            fetch(`./api_utilisateur.php?action=check_image&image_name=${encodeURIComponent(imageFile.name)}&dir=${encodeURIComponent(dir)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        alert('L\'image existe déjà. Veuillez en choisir une autre.');
                    } else {
                        submitForm(formData);
                    }
                })
                .catch(error => console.error('Erreur lors de la vérification de l\'image:', error));
        } else {
            submitForm(formData);
        }
    });

    function submitForm(formData) {
        fetch('./api_utilisateur.php?action=add_recherche', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recherche ajoutée avec succès!');
                window.location.href = 'mes_recherches.php'; // Exemple de redirection
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
        });
    }
});

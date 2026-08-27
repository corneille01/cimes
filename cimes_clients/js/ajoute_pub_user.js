document.title= 'Ajouter une nouvelle publication';
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('publication-form');
    const dir = '../cimes_admin/img/img_programme/';  // Exemple de trajectoire

    // Charger les massifs dans le menu déroulant
    fetch('./api_utilisateur.php?action=get_massifs')
        .then(response => response.json())
        .then(data => {
            const massifSelect = document.getElementById('massif');
            data.massifs.forEach(massif => {
                const option = document.createElement('option');
                option.value = massif.id;
                option.textContent = massif.nom;
                massifSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur lors du chargement des massifs:', error));

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
        fetch('./api_utilisateur.php?action=add_publication', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Publication ajoutée avec succès!');
                window.location.href = 'mes_publications.php'; // Exemple de redirection
            } else {
                alert('Erreur: ' + data.error);
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    fetch('./api_utilisateur.php?action=get_user_info')
        .then(response => response.json())
        .then(data => {
            document.title = data.user.nom;
            console.log(data);
            if (data.error) {
                document.getElementById('user-info').innerText = data.error;
            } else {
                const user = data.user;
                const userInfoDiv = document.getElementById('user-info');
                userInfoDiv.innerHTML = `
                    <p>Bienvenue, ${user.nom} ${user.prénom}!</p>
                    <p>Email : ${user.email}</p>
                    <button class="btn-user" onclick="editUserInfo()">Modifier mes informations</button>
                    <div id="edit-user-info">
                        <form id="user-info-form">
                            <label class="user-label" for="nom">Nom:</label>
                            <input class="user-input" type="text" id="nom" name="nom" value="${user.nom}">
                            <br>
                            <label class="user-label" for="prenom">Prénom:</label>
                            <input class="user-input" type="text" id="prenom" name="prenom" value="${user.prénom}">
                            <br>
                            <label class="user-label" for="email">Email:</label>
                            <input class="user-input" type="email" id="email" name="email" value="${user.email}">
                            <br>
                            
                            <button type="button" class="btn-user" onclick="saveUserInfo()">Enregistrer</button>
                            <button type="button" class="btn-user" onclick="annuler()">Annuler</button>

                        </form>
                    </div>
                `;
            }
        })
        .catch(error => {
            document.getElementById('user-info').innerText = 'Erreur: ' + error.message;
        });
});

function editUserInfo() {
    document.getElementById('edit-user-info').style.display = 'block';
}

function annuler() {
    document.getElementById('edit-user-info').style.display = 'none';
}
function saveUserInfo() {
    const formData = new FormData(document.getElementById('user-info-form'));
    fetch('./api_utilisateur.php?action=update_user_info', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Informations mises à jour avec succès');
            window.location.reload();
        } else {
            alert('Erreur lors de la mise à jour des informations');
        }
    })
    .catch(error => {
        alert('Erreur: ' + error.message);
    });
}

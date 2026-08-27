if (lien === 'ajout_massif') {
    document.title ='Ajouter un massif';
        
    } else {
    document.title ='Modifier un massif';
        
    }

// Initialiser la carte Leaflet
var map = L.map('map').setView([46.603354, 1.888334], 5); // Coordonnées du centre de la France

// Ajouter une couche de carte OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Variable pour stocker le marqueur actuel
var currentMarker = null;

// Ajouter un marqueur sur la carte et mettre à jour les champs de latitude et longitude
map.on('click', function(e) {
    var lat = e.latlng.lat.toFixed(6);
    var lng = e.latlng.lng.toFixed(6);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    // Retirer le marqueur précédent s'il existe
    if (currentMarker) {
        map.removeLayer(currentMarker);
    }

    // Ajouter un nouveau marqueur
    currentMarker = L.marker([lat, lng]).addTo(map);
});


let nom = "";
let chaine = "";
let region = "";
let pays = "";
let continent = "";
let latitude = "";
let longitude = "";

document.querySelector("#envoyer").addEventListener('click', function() {
    nom = document.querySelector("#nom_massif").value;
    chaine = document.querySelector("#chaine").value;
    region = document.querySelector("#région").value;
    pays = document.querySelector("#pays").value;
    continent = document.querySelector("#continent").value;
    latitude = document.querySelector("#latitude").value;
    longitude = document.querySelector("#longitude").value;

    if (nom && chaine && region && pays && continent && latitude && longitude) {
        envoi_donnees();
    } else {
        document.querySelector("#erreur").textContent = "Vous avez oublié de remplir un champ du formulaire";
    }
});

function envoi_donnees() {
    let data = {
        lien: lien, // Assurez-vous que ce champ correspond à ce que le serveur attend
        id: typeof id !== 'undefined' ? id : null,
        nom: nom,
        chaine: chaine,
        region: region,
        pays: pays,
        continent: continent,
        latitude: latitude,
        longitude: longitude
    };
    fetch('../cimes_api/index_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(data => {
        data = data.trim(); // Supprime les espaces en début et fin de la réponse
        console.log('Response from server:', data); // Log la réponse pour le débogage

        if (data === "ok") {
            document.querySelector("#erreur").innerHTML = `
                <p>Le massif a bien été enregistré</p>
                <a href="data_massif.php" class="bouton_ajouter">Revenir à la page de la gestion des massifs</a>
            `;
            // Vider les champs du formulaire
            document.querySelector("#nom_massif").value = "";
            document.querySelector("#chaine").value = "";
            document.querySelector("#région").value = "";
            document.querySelector("#pays").value = "";
            document.querySelector("#continent").value = "";
            document.querySelector("#latitude").value = "";
            document.querySelector("#longitude").value = "";
            // Retirer le marqueur de la carte
            if (currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
            }
        } else if (data === "exists") {
            document.querySelector("#erreur").textContent = "Ce massif existe déjà.";
        } else {
            document.querySelector("#erreur").textContent = "Il y a un problème";
        }
    })
    
}


let breadcrumb = '';
  
            breadcrumb = `
                 <nav style="--bs-breadcrumb-divider: '>';position: relative;margin: 50px;" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="./index.php">Page principale</a></li>
                       
                        <li class="breadcrumb-item active" aria-current="page">Les publications</li>

                    </ol>
                </nav>`;
            document.querySelector("#breadcrumb-container").innerHTML = breadcrumb;
// Initialiser la carte
var map = L.map('Map').setView([46.603354, 1.888334], 5); // Coordonnées centrées sur la France

// Ajouter la couche de base OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Initialiser le cluster de marqueurs avec des marqueurs groupés chiffrés
var markers = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: true,
    zoomToBoundsOnClick: true
});

// Variable pour stocker les marqueurs individuels
var individualMarkers = {};

// Fonction pour mettre à jour la légende avec toutes les publications correspondantes
function updateLegendWithFilters(data) {
    var legend = document.querySelector('.legend');
    legend.innerHTML = '<h4>Massifs et Publications</h4>';
    if (data.length > 0) {
        data.forEach(massif => {
            if (massif.publications.length > 0) {
                legend.innerHTML += '<p class="massif-title">' + massif.nom + '</p>'; // Ajout du nom du massif
                massif.publications.forEach(pub => {
                    let [annee, mois, jour] = pub.date.split('-');
                    let dateFormatee = `${jour}/${mois}/${annee}`;
                    legend.innerHTML += '<a href="publication_detail.php?id='+pub.id+'" class="btn-card"><p class="publication-title">' + pub.titre + '<br>Auteur : ' + pub.auteur + '<br>' + dateFormatee + '</p></a>';
                });
            } else {
                legend.innerHTML += '<p class="massif-title">' + massif.nom + '</p>'; // Ajout du nom du massif même s'il n'y a pas de publications
                legend.innerHTML += '<p class="no-publications">Aucune publication</p>';
            }
        });
    } else {
        legend.innerHTML += '<p>Aucun massif sélectionné</p>';
    }
}

// Fonction pour mettre à jour la légende avec les massifs et leurs publications
function updateLegend(massifs) {
    var legend = document.querySelector('.legend');
    legend.innerHTML = '<h4>Massifs et Publications</h4>';
    if (massifs.length > 0) {
        massifs.forEach(massif => {
            legend.innerHTML += '<p class="massif-title">' + massif.nom + '</p>';
            if (massif.publications.length > 0) {
                massif.publications.forEach(pub => {
                    let [annee, mois, jour] = pub.date.split('-');
                    let dateFormatee = `${jour}/${mois}/${annee}`;
                    legend.innerHTML += '<a href="publication_detail.php?id='+pub.id+'" class="btn-card"><p class="publication-title">' + pub.titre + '<br>Auteur : ' + pub.auteur + '<br>' + dateFormatee + '</p></a>';
                });
            } else {
                legend.innerHTML += '<p class="no-publications">Aucune publication</p>';
            }
        });
    } else {
        legend.innerHTML += '<p>Aucun massif sélectionné</p>';
    }
}
// Récupérer les données des massifs depuis votre API PHP au chargement de la page
fetch('../cimes_api/api_carte.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        // Ajouter les marqueurs pour chaque massif
        data.forEach(massif => {
            var marker = L.marker([massif.latitude, massif.longitude])
                .bindPopup(massif.nom); // Utilisez massif.nom ou un autre champ pour le popup
            markers.addLayer(marker);

            // Enregistrer chaque marqueur individuel par son nom pour pouvoir le retrouver plus tard
            individualMarkers[massif.nom] = marker;

            // Associer les publications aux marqueurs
            marker.massifInfo = massif;
        });

        // Ajouter le cluster de marqueurs à la carte
        map.addLayer(markers);

        // Écouter les clics sur les marqueurs et les clusters
        markers.on('clusterclick', function(event) {
            var cluster = event.layer;
            var markersInCluster = cluster.getAllChildMarkers();
            var massifs = [];
            markersInCluster.forEach(marker => {
                massifs.push(marker.massifInfo);
            });
            updateLegend(massifs);
        });

        markers.on('click', function(event) {
            var marker = event.layer;
            updateLegend([marker.massifInfo]);
        });
    })
    .catch(error => console.error('Erreur lors de la récupération des données :', error));

// Fonction pour récupérer les filtres depuis l'API
function fetchFilters() {
    fetch('../cimes_api/api_carte.php?filters=true')
        .then(response => response.json())
        .then(data => {
            populateFilterOptions('thematiques', data.thematiques);
            populateFilterOptions('auteurs', data.auteurs);
            populateFilterOptions('massifs', data.massifs);
            populateFilterOptions('chaines', data.chaines);
            populateFilterOptions('regions', data.regions);
            populateFilterOptions('pays', data.pays);
            populateFilterOptions('continents', data.continents);

            // Appliquer les filtres et le moteur de recherche une fois que les options sont chargées
            applyFilters();
        })
        .catch(error => console.error('Erreur lors de la récupération des filtres :', error));
}

// Fonction pour remplir les options des filtres
function populateFilterOptions(selectId, options) {
    var select = document.getElementById(selectId);
    select.innerHTML = '<option value="">Tous</option>'; // Réinitialiser les options
    options.forEach(option => {
        var opt = document.createElement('option');
        opt.value = option;
        opt.textContent = option;
        select.appendChild(opt);
    });
}

// Fonction pour appliquer les filtres et récupérer les données des massifs
function applyFilters(event) {
    if (event) event.preventDefault();

    var thematique = document.getElementById('thematiques').value;
    var auteur = document.getElementById('auteurs').value;
    var massif = document.getElementById('massifs').value;
    var chaine = document.getElementById('chaines').value;
    var region = document.getElementById('regions').value;
    var pays = document.getElementById('pays').value;
    var continent = document.getElementById('continents').value;

    var urlParams = new URLSearchParams();
    
    if (thematique) urlParams.append('thematique', thematique);
    if (auteur) urlParams.append('auteur', auteur);
    if (massif) urlParams.append('massif', massif);
    if (chaine) urlParams.append('chaine', chaine);
    if (region) urlParams.append('region', region);
    if (pays) urlParams.append('pays', pays);
    if (continent) urlParams.append('continent', continent);

    fetch('../cimes_api/api_carte.php?' + urlParams.toString())
        .then(response => response.json())
        .then(data => {
            markers.clearLayers(); // Supprimer tous les marqueurs existants

            data.forEach(massif => {
                var marker = L.marker([massif.latitude, massif.longitude])
                    .bindPopup(massif.nom);
                markers.addLayer(marker);

                individualMarkers[massif.nom] = marker;
                marker.massifInfo = massif;
            });

            map.addLayer(markers); // Ajouter les nouveaux marqueurs à la carte

            if (data.length > 0) {
                var bounds = L.latLngBounds(data.map(massif => [massif.latitude, massif.longitude]));
                map.fitBounds(bounds, { padding: [5, 5], maxZoom: 15 });
            }

            updateLegendWithFilters(data); // Mettre à jour la légende avec les résultats filtrés
            
        })
        .catch(error => console.error('Erreur lors de la récupération des données des massifs :', error));
}

// Initialiser les filtres et la carte
fetchFilters();

// Gérer les événements de changement pour tous les filtres
document.getElementById('thematiques').addEventListener('change', applyFilters);
document.getElementById('auteurs').addEventListener('change', applyFilters);
document.getElementById('massifs').addEventListener('change', applyFilters);
document.getElementById('chaines').addEventListener('change', applyFilters);
document.getElementById('regions').addEventListener('change', applyFilters);
document.getElementById('pays').addEventListener('change', applyFilters);
document.getElementById('continents').addEventListener('change', applyFilters);





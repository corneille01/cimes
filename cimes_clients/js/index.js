// Fonction pour récupérer et afficher les actualités
function afficherActualites() {
    fetch('../cimes_api/index_api.php?query=actu')
    .then(response => response.json())
    .then(dataActu => {
        let rechercheDetailActu = `<h2 style="font-size: 4rem;padding-bottom: 3%;">ACTUALITÉS DU CIMeS</h2>`;
        let cardsContentActu = '';  

        // Obtenir la date actuelle
        let currentDate = new Date();

        // Trier les actualités par date décroissante (les plus récentes en premier)
        dataActu.sort((a, b) => new Date(b.date) - new Date(a.date));

        // Filtrer les actualités pour n'inclure que celles dont la date est passée ou aujourd'hui
        let actualitesPassees = dataActu.filter(ligne => {
            let [annee, mois, jour] = ligne.date.split('-');
            let actuDate = new Date(annee, mois - 1, jour);
            return actuDate <= currentDate;
        });

        // Limiter l'affichage aux 6 dernières actualités
        actualitesPassees.slice(0, 6).forEach(ligne => {
            let [annee, mois, jour] = ligne.date.split('-');
            let dateFormatee = `${jour}/${mois}/${annee}`;
            cardsContentActu += `<div class="card_actu">
                <div class="card-date">${dateFormatee}</div>
                <div class="card-content">
                    <div class="card-header"><img src="../cimes_admin/img/img_actu_event/${ligne.image}" alt="${ligne.titre}"></div>
                    <div class="card-body">
                        <h2 style="padding-left: 20px;">${ligne.titre}</h2>
                    </div>
                    <div class="savoir-btn"><a href="actu_event_detail.php?id=${ligne.id}">En savoir plus</a></div>
                </div>
            </div>`;
        });

        rechercheDetailActu += cardsContentActu;
        rechercheDetailActu += `
        <div class="button-wrapper">
            <div class="voir-button-container">
                <a href="actu_event.php?id=${dataActu[0].parent_id}" class="voir-toutes-btn">Voir toutes les actualités</a>
            </div>
        </div>`;

        document.querySelector('#actu').innerHTML = rechercheDetailActu;
        document.querySelector('#actualite_section').style.display = 'block';
        document.querySelector('#evenement_section').style.display = 'none';
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.querySelector('#actu').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
    });
    
}






// Fonction pour récupérer et afficher les événements
function afficherEvenements() {
    fetch('../cimes_api/index_api.php?query=event')
        .then(response => response.json())
        .then(dataEvent => {
            let rechercheDetailEvent = `<h2 style="font-size: 4rem;padding-bottom: 3%;">ÉVÈNEMENTS DU CIMeS</h2>`;
            let cardsContentEvent = '';  

            // Obtenir la date actuelle
            let currentDate = new Date();

            // Filtrer les événements pour n'inclure que ceux dont la date est future
            let evenementsFuturs = dataEvent.filter(ligne => {
                let [annee, mois, jour] = ligne.date.split('-');
                let eventDate = new Date(annee, mois - 1, jour);
                return eventDate > currentDate;
            });

            // Trier les événements par date (la plus proche en premier)
            evenementsFuturs.sort((a, b) => new Date(a.date) - new Date(b.date));

            // Limiter l'affichage à 6 événements
            evenementsFuturs.slice(0, 6).forEach(ligne => {
                let [annee, mois, jour] = ligne.date.split('-');
                let dateFormatee = `${jour}/${mois}/${annee}`;
                cardsContentEvent += `<div class="card_actu">
                    <div class="card-date">${dateFormatee}</div>
                    <div class="card-content">
                        <div class="card-header"><img src="../cimes_admin/img/img_actu_event/${ligne.image}" alt="${ligne.titre}"></div>
                        <div class="card-body">
                            <h2 style="padding-left: 20px;">${ligne.titre}</h2>
                        </div>
                        <div class="savoir-btn"><a href="actu_event_detail.php?id=${ligne.id}">En savoir plus</a></div>
                    </div>
                </div>`;
            });

            rechercheDetailEvent += cardsContentEvent;
            rechercheDetailEvent += `
            <div class="button-wrapper">
                <div class="voir-button-container">
                    <a href="actu_event.php?id=${evenementsFuturs[0]?.parent_id || ''}" class="voir-toutes-btn">Voir tous les événements</a>
                </div>
            </div>`;

            document.querySelector('#event').innerHTML = rechercheDetailEvent;
            document.querySelector('#evenement_section').style.display = 'block';
            document.querySelector('#actualite_section').style.display = 'none';
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.querySelector('#event').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
        });
}

// Charger les actualités par défaut
afficherActualites();

// Gestionnaire d'événements pour le bouton "Afficher Actualités"
document.getElementById('toggle-actualite').addEventListener('click', function() {
    if (document.querySelector('#actualite_section').style.display === 'none') {
        afficherActualites();
    }
});

// Gestionnaire d'événements pour le bouton "Afficher Événements"
document.getElementById('toggle-evenement').addEventListener('click', function() {
    if (document.querySelector('#evenement_section').style.display === 'none') {
        afficherEvenements();
    }
});

function afficherPub() {
    fetch('../cimes_api/index_api.php?query=publication')
    .then(response => response.json())
    .then(dataEvent => {
        let cardsContentEvent = '';  

        // Obtenir la date actuelle
        let currentDate = new Date();

        // Filtrer les événements pour n'inclure que ceux dont la date est future ou actuelle
        let evenementsRecents = dataEvent.filter(ligne => {
            let [annee, mois, jour] = ligne.date.split('-');
            let eventDate = new Date(annee, mois - 1, jour);
            return eventDate >= currentDate;
        });

        // Trier les événements par date décroissante (la plus récente en premier)
        evenementsRecents.sort((a, b) => new Date(b.date) - new Date(a.date));

        // Limiter l'affichage à 6 événements
        evenementsRecents.slice(0, 6).forEach(ligne => {
            let [annee, mois, jour] = ligne.date.split('-');
            let dateFormatee = `${jour}/${mois}/${annee}`;
            cardsContentEvent += `<div class="card-enj">
                <img class="card-enj-header2" src="../cimes_admin/img/img_publication/${ligne.image}" alt="${ligne.titre}">
                <div class="card-enj-body" style="font-family: 'Arial', sans-serif;padding:0;padding-bottom:10px;color:white">
                    <h2 style="font-size:1rem;font-family: 'Arial', sans-serif;margin: 20px auto;padding:5px;">${ligne.titre}</h2>
                    <p style="font-size:0.9rem;">${ligne.auteur}</p>
                    <a style ="color:white;" href="publication_detail.php?id=${ligne.id}">En savoir plus</a>
                </div>
            </div>`;
        });

        document.querySelector('#publication').innerHTML = cardsContentEvent;
    })
        .catch(error => {
            console.error('Erreur:', error);
            document.querySelector('#publication').innerHTML = '<p>Erreur lors de la récupération des données.</p>';
        });
}
afficherPub();
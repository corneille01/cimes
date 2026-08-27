function loadPublications() {
    fetch(`./api_utilisateur.php?action=get_publications`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            document.title = 'Mes publications';
            if (data.error) {
                alert(data.error);
            } else {
                const publications = data.publications;
                const corpTab = document.getElementById('corp_tab');
                corpTab.innerHTML = ''; // Clear any existing content

                if (publications.length === 0) {
                    corpTab.innerHTML = '<tr><td colspan="4">Aucune publication trouvée.</td></tr>';
                } else {
                    publications.forEach(publication => {
                        // Transformer la date au format jj/mm/aaaa
                        const dateObj = new Date(publication.date);
                        const day = String(dateObj.getDate()).padStart(2, '0');
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const year = dateObj.getFullYear();
                        const formattedDate = `${day}/${month}/${year}`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${publication.id}</td>
                            <td>${publication.thematique}</td>
                            <td>${publication.titre}</td>
                            <td>${formattedDate}</td>
                            <td>${publication.nom_massif}</td>
                            <td><a href="modif_pub_user.php?id=${publication.id}"><i class="fas fa-pen" style="color:#0d6efd;"></i></a></td>
                            <td><a href="#" onclick="confirmDelete(${publication.id})"><i class="fas fa-window-close" style="color:#0d6efd;"></i></a></td>
                        `;
                        corpTab.appendChild(tr);
                    });
                }
            }
        })
        .catch(error => {
            alert('Erreur: ' + error.message);
        });
}



let quelid = "";

function confirmDelete(id) {
    quelid = id;
    document.querySelector("#panneausup").style.display = "flex";
}

document.querySelector("#oui").addEventListener('click', function() {
    deletePublication(quelid);
    document.querySelector("#panneausup").style.display = "none";
});

document.querySelector("#non").addEventListener('click', function() {
    document.querySelector("#panneausup").style.display = "none";
});

function deletePublication(id) {
    fetch(`./api_utilisateur.php?action=delete_publication&id=${id}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
       
            
            document.querySelector("#corp_tab").innerHTML = "";
            loadPublications();
        
    })
    .catch(error => {
        alert('Erreur: ' + error.message);
    });
}

    loadPublications();
    



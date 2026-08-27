function loadRecherches() {
    fetch(`./api_utilisateur.php?action=get_recherches`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            document.title = 'Mes recherches sur la montagne';
            if (data.error) {
                alert(data.error);
            } else {
                const recherches = data.recherches;
                const corpTab = document.getElementById('corp_tab');
                corpTab.innerHTML = ''; // Clear any existing content

                if (recherches.length === 0) {
                    corpTab.innerHTML = '<tr><td colspan="4">Aucune recherche trouvée.</td></tr>';
                } else {
                    recherches.forEach(recherche => {
                        // Transformer la date au format jj/mm/aaaa
                        const dateObj = new Date(recherche.date);
                        const day = String(dateObj.getDate()).padStart(2, '0');
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const year = dateObj.getFullYear();
                        const formattedDate = `${day}/${month}/${year}`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${recherche.id}</td>
                            <td>${recherche.thematique}</td>
                            <td>${recherche.titre}</td>
                            <td>${formattedDate}</td>
                            <td><a href="modif_recherche_user.php?id=${recherche.id}"><i class="fas fa-pen" style="color:#0d6efd;"></i></a></td>
                            <td><a href="#" onclick="confirmDelete(${recherche.id})"><i class="fas fa-window-close" style="color:#0d6efd;"></i></a></td>
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
    fetch(`./api_utilisateur.php?action=delete_recherche&id=${id}`, {
        method: 'DELETE',
    })
    .then(response => response.json())
    .then(data => {
       
            
            document.querySelector("#corp_tab").innerHTML = "";
            loadRecherches();
        
    })
    .catch(error => {
        alert('Erreur: ' + error.message);
    });
}

    loadRecherches();
    



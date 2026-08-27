
    // Récupérer parent_id depuis le champ caché
    let parent_id = document.querySelector("#parent_id").value;

    fetch(`../cimes_api/api_dynam.php?id=${parent_id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            document.querySelector("#thematique").value = data[0].thematique;
            document.querySelector("#titre").value = data[0].titre;
            document.querySelector("#date").value = data[0].date;
            document.querySelector("#texte").value = data[0].texte;
            document.querySelector("#image").innerHTML = `<strong>${data[0].image}</strong>`;
        })
        


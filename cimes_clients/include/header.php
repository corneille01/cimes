<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des évènements</title>
    <meta name="description" content="">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<body>
<nav class="first-container">
    <div class="wrapper nav-container">
        <div class="logo">
            <a href="index.php"><img src="img/logo_cimes1.png" style="width: 160px;"></a>
        </div>
        <input type="radio" name="slider" id="menu-btn">
        <input type="radio" name="slider" id="close-btn">
        <ul class="nav-links" id="nav-links">
            <label for="close-btn" class="btn close-btn"><i class="fas fa-times"></i></label>
            <?php
            session_start();
            if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) {
                echo '<li><a href="dashboard.php" class="desktop-item"><i class="fa-solid fa-user"></i> ' . htmlspecialchars($_SESSION['firstname']) . ' ' . htmlspecialchars($_SESSION['lastname']) . '</a></li>';
                echo '<li><a href="logout.php" class="desktop-item">Se déconnecter</a></li>';
            } else {
                echo '<li><a href="login.php" class="desktop-item"><i class="fa-solid fa-user"></i> Connexion</a></li>';
            }
            ?>
        </ul>
        <label for="menu-btn" class="btn menu-btn"><i class="fas fa-bars"></i></label>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('../cimes_api/index_api_head.php?query=navbar')
        .then(response => response.json())
        .then(data => {
            
            // Ajouter l'élément espace_personnel à la fin des éléments de navigation
            data.push({
                name: 'Espace personnel',
                url: 'espace_personnel.php',
                icon: 'fa-solid fa-user'
            });
            generateNav(data);
        })
        .catch(error => console.error('Error fetching data:', error));
});

function generateNav(items) {
    const navLinks = document.querySelector('.nav-links');

    // Création et ajout du bouton de fermeture
    const closeBtn = document.createElement('label');
    closeBtn.setAttribute('for', 'close-btn');
    closeBtn.classList.add('btn', 'close-btn');
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    navLinks.appendChild(closeBtn);

    items.forEach(item => {
        const li = document.createElement('li');

        // Création du lien pour la version desktop
        const desktopLink = document.createElement('a');
        desktopLink.href = item.url;
        desktopLink.classList.add('desktop-item');
        if (item.icon) {
            const icon = document.createElement('i');
            icon.className = item.icon;
            desktopLink.appendChild(icon);
        }
        if (item.name && item.name !== 'Espace personnel') {
            const text = document.createTextNode(item.name);
            desktopLink.appendChild(text);
        }
        li.appendChild(desktopLink);

        // Ajout du lien pour la version mobile
        const mobileLink = document.createElement('a');
        mobileLink.href = item.url;
        mobileLink.setAttribute('for', 'showMega' + items.indexOf(item));
        mobileLink.classList.add('mobile-item');
        if (item.icon) {
            const icon = document.createElement('i');
            icon.className = item.icon;
            mobileLink.appendChild(icon);
        }
        if (item.name) {
            const text = document.createTextNode(item.name);
            mobileLink.appendChild(text);
        }
        li.appendChild(mobileLink);

        if (item.sub_items && item.sub_items.length > 0) {
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = 'showMega' + items.indexOf(item);
            li.appendChild(checkbox);

            const megaBox = document.createElement('div');
            megaBox.classList.add('mega-box');

            const content = document.createElement('div');
            content.classList.add('content');

            if (item.image) {
                const rowImage = document.createElement('div');
                rowImage.classList.add('row_image');
                const img = document.createElement('img');
                img.src = item.image;
                img.alt = item.name;
                rowImage.appendChild(img);
                content.appendChild(rowImage);
            }

            const chunks = arrayChunk(item.sub_items, Math.ceil(item.sub_items.length / 2));

            chunks.forEach(chunk => {
                const row = document.createElement('div');
                row.classList.add('row');

                const ul = document.createElement('ul');
                ul.classList.add('mega-links');

                chunk.forEach(subItem => {
                    const subLi = document.createElement('li');
                    const subLink = document.createElement('a');
                    subLink.href = `${subItem.url}?id=${subItem.id}`; // Inclure l'ID dans l'URL
                    subLink.textContent = subItem.name;
                    subLi.appendChild(subLink);
                    ul.appendChild(subLi);
                });

                row.appendChild(ul);
                content.appendChild(row);
            });

            megaBox.appendChild(content);
            li.appendChild(megaBox);
        }

        navLinks.appendChild(li);
    });
}

function arrayChunk(arr, chunkSize) {
    const chunks = [];
    for (let i = 0; i < arr.length; i += chunkSize) {
        chunks.push(arr.slice(i, i + chunkSize));
    }
    return chunks;
}

</script>

</body>
</html>

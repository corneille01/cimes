<!DOCTYPE html>
<html lang="fr">

<head>

    <?php include('include/head.html'); ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Authentification</title>

    <link rel="stylesheet" href="styles.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f5f7fa;
            font-family: Arial, sans-serif;
        }

        .container1 {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 100px;
            padding-bottom: 30px;
        }

        .container2 {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 420px;
            width: 100%;
            padding: 25px;

            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 6px;
            color: #555;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 18px;
            outline: none;
            transition: 0.2s;
            font-size: 15px;
        }

        input:focus {
            border-color: #007bff;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #007bff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        p {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .toggle-link {
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }

        .toggle-link:hover {
            color: #0056b3;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        #message,
        #erreur,
        #message1,
        #erreur1 {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        #message,
        #message1 {
            color: #0a7a2f;
            background: rgba(0, 255, 0, 0.08);
        }

        #erreur,
        #erreur1 {
            color: #b00020;
            background: rgba(255, 0, 0, 0.08);
        }

        .password-hint {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }
    </style>

</head>

<body>

    <?php include('include/header.html'); ?>

    <div class="container1">

        <div class="container2">

            <!-- =========================
                 CONNEXION
            ========================== -->

            <div id="login-form" class="form-container active">

                <h3>Connexion</h3>

                <form id="login-form-element">

                    <label for="login-email">
                        Email :
                    </label>

                    <input
                        type="text"
                        id="login-email"
                        name="email"
                        autocomplete="email"
                        required>

                    <label for="login-password">
                        Mot de passe :
                    </label>

                    <input
                        type="password"
                        id="login-password"
                        name="password"
                        autocomplete="current-password"
                        required>

                    <button type="submit">
                        Se connecter
                    </button>

                </form>

                <p>
                    Pas encore de compte ?

                    <span
                        class="toggle-link"
                        onclick="toggleForms()">
                        Inscrivez-vous ici
                    </span>
                </p>

                <div id="message"></div>
                <div id="erreur"></div>

            </div>

            <!-- =========================
                 INSCRIPTION
            ========================== -->

            <div id="register-form" class="form-container">

                <h3>Inscription</h3>

                <form id="register-form-element">

                    <label for="firstname">
                        Prénom :
                    </label>

                    <input
                        type="text"
                        id="firstname"
                        name="firstname"
                        autocomplete="given-name"
                        required>

                    <label for="lastname">
                        Nom :
                    </label>

                    <input
                        type="text"
                        id="lastname"
                        name="lastname"
                        autocomplete="family-name"
                        required>

                    <label for="register-email">
                        Email :
                    </label>

                    <input
                        type="text"
                        id="register-email"
                        name="email"
                        autocomplete="email"
                        required>

                    <label for="register-password">
                        Mot de passe :
                    </label>

                    <small class="password-hint">
                        8 caractères minimum avec :
                        une majuscule, une minuscule,
                        un chiffre et un caractère spécial.
                        <br>
                        Exemple : MonPass@123
                    </small>

                    <input
                        type="password"
                        id="register-password"
                        name="password"
                        placeholder="Ex : MonPass@123"
                        autocomplete="new-password"
                        required>

                    <label for="confirm-password">
                        Confirmer le mot de passe :
                    </label>

                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm_password"
                        autocomplete="new-password"
                        required>

                    <button
                        type="submit"
                        id="register-btn">
                        S'inscrire
                    </button>

                </form>

                <p>
                    Déjà inscrit ?

                    <span
                        class="toggle-link"
                        onclick="toggleForms()">
                        Connectez-vous ici
                    </span>
                </p>

                <div id="message1"></div>
                <div id="erreur1"></div>

            </div>

        </div>

    </div>

    <script>
        // =========================
        // INSCRIPTION
        // =========================

        document
            .getElementById('register-form-element')
            .addEventListener('submit', async function(e) {

                e.preventDefault();

                const form = this;

                const formData = new FormData(form);

                const msgRegister = document.getElementById('message1');
                const errRegister = document.getElementById('erreur1');

                const msgLogin = document.getElementById('message');

                const registerBtn = document.getElementById('register-btn');

                msgRegister.textContent = "";
                errRegister.textContent = "";
                msgLogin.textContent = "";

                registerBtn.disabled = true;

                try {

                    const res = await fetch('register.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();

                    if (data.success) {

                        msgRegister.textContent = data.message;

                        form.reset();

                        setTimeout(() => {

                            toggleForms();

                            msgLogin.textContent =
                                "Inscription réussie, connecte-toi.";

                            msgRegister.textContent = "";

                        }, 200);

                    } else {

                        errRegister.textContent = data.message;
                    }

                } catch (error) {

                    errRegister.textContent =
                        "Erreur réseau ou serveur.";

                } finally {

                    registerBtn.disabled = false;
                }
            });

        // =========================
        // CONNEXION
        // =========================

        document
            .getElementById('login-form-element')
            .addEventListener('submit', async function(e) {

                e.preventDefault();

                const formData = new FormData(this);

                const msg = document.getElementById('message');
                const err = document.getElementById('erreur');

                msg.textContent = "";
                err.textContent = "";

                try {

                    const res = await fetch('login.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();
                    if (data.success) {
                        msg.textContent = "Connexion réussie.";
                        setTimeout(() => {
                            if (data.role === 'admin') {
                                window.location.href = "dashboard.php";
                            } else if (data.role === 'user') {
                                window.location.href = "user_dynam_projets.php";
                            } else {
                                // fallback de sécurité
                                window.location.href = "dashboard.php";
                            }
                        }, 200);
                    } else {

                        err.textContent = data.message;
                    }

                } catch (error) {

                    err.textContent =
                        "Erreur réseau ou serveur.";
                }
            });

        // =========================
        // SWITCH FORMS
        // =========================

        function toggleForms() {

            document
                .getElementById('register-form')
                .classList.toggle('active');

            document
                .getElementById('login-form')
                .classList.toggle('active');
        }
    </script>

</body>

</html>
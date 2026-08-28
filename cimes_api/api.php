<?php
session_start();



function ajouter_modif_contact($input)
{
    $pdo = getConnexion();
    $nom = trim($input['nom'] ?? '');

    try {
        if ($input['lien'] === 'cree_contact') {
            $stmt = $pdo->prepare('INSERT INTO cimes_contacts (nom, email, telephone) VALUES (:nom, :email, :telephone)');
        } else {
            $stmt = $pdo->prepare('UPDATE cimes_contacts SET nom = :nom, email = :email, telephone = :telephone WHERE id = :id');
        }

        $data = [
            ':nom'       => $nom,
            ':email'     => trim($input['email'] ?? ''),
            ':telephone' => trim($input['telephone'] ?? ''),
        ];
        if ($input['lien'] === 'modif_contact') {
            $data[':id'] = (int)$input['id'];
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_contact : ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) $stmt->closeCursor();
}

function supprimer_contact($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    $stmt = $pdo->prepare('DELETE FROM cimes_contacts WHERE id = :id');
    if ($stmt->execute([':id' => $id])) echo 'ok';
    else echo 'ko';
}













function getdonnees($quoi)
{
    $pdo = getConnexion();

    if ($quoi === 'mission') {
        $req = 'SELECT * FROM cimes_mission';
    }
    if ($quoi === 'programme') {
        $req = 'SELECT * FROM cimes_programme';
    }
    if ($quoi === 'logo_partenaire') {
        $req = 'SELECT * FROM cimes_logo_partenaires';
    }
    if ($quoi === 'carrousel') {
        $req = 'SELECT * FROM cimes_carrousel';
    }

    if ($quoi === 'projet_carte') {
        $req = 'SELECT * FROM cimes_projets';
    }
    if ($quoi === 'contact') {
        $req = 'SELECT * FROM cimes_contacts ORDER BY nom';
    }

    if ($quoi === 'gouvernance') {
        $sql = 'SELECT * FROM cimes_gouvernance ORDER BY ordre ASC';
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $type = $_GET['type'];
            $stmt = $pdo->prepare('SELECT * FROM cimes_gouvernance WHERE type = :type ORDER BY ordre ASC');
            $stmt->execute([':type' => $type]);
            sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
            return;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
        return;
    }

    // Dans getdonnees($quoi), ajoutez :
    if ($quoi === 'gouvernance_types') {
        $req = ("SELECT DISTINCT type FROM cimes_gouvernance ORDER BY type");
    }
    if ($quoi === 'gouvernance_schema') {
        $req = 'SELECT * FROM cimes_gouvernance_schema ORDER BY ordre ASC';
    }

    if ($quoi === 'annuaire_structure') {
        $req = 'SELECT * FROM cimes_annuaire_structures';
    }
    if ($quoi === 'etablissement') {
        $req = 'SELECT * FROM cimes_etablissement_membre';
    }
    if ($quoi === 'massif') {
        $req = 'SELECT * FROM cimes_massifs';
    }
    if ($quoi === 'publication') {
        $req = 'SELECT * FROM cimes_publication';
    }
    if ($quoi === 'actu') {
        $req = 'SELECT * FROM cimes_actu';
    }
    if ($quoi === 'activites') {
        $req = 'SELECT * FROM cimes_activites';
    }
    if ($quoi === 'axes') {
        $req = 'SELECT * FROM cimes_axes';
    }
    if ($quoi === 'presentation') {
        $req = 'SELECT * FROM cimes_presentation';
    }

    /* ── Annuaire des personnes (avec publications jointes) ── */
    if ($quoi === 'annuaire') {

        $stmt = $pdo->prepare('
        SELECT *
        FROM cimes_annuaire_personnes
        ORDER BY nom, prenom
    ');

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJSON($rows);

        return;
    }

    /* ── Annuaire des structures ── */
    if ($quoi === 'annuaire_structures') {
        $stmt = $pdo->prepare('
            SELECT
                id,
                photo,
                etablissement,
                discipline,
                adresse,
                tutelles,
                annee_creation,
                site_web,
                responsable,
                presentation,
                domaine_recherche
            FROM cimes_annuaire_structures
            ORDER BY etablissement ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        foreach ($rows as &$row) {
            /* Tronquer annee_creation à YYYY */
            if (!empty($row['annee_creation'])) {
                $row['annee_creation'] = substr($row['annee_creation'], 0, 4);
            }
            /* Photo vide ou default.jpg → null (le JS affiche les initiales) */
            if (empty($row['photo']) || $row['photo'] === 'default.jpg') {
                $row['photo'] = null;
            }
            /* Trim sur tous les champs texte */
            foreach ($row as $k => $v) {
                if (is_string($v)) $row[$k] = trim($v);
            }
        }
        unset($row);

        sendJSON($rows);
        return;
    }

    if ($quoi === 'partenaires') {
        $sql = 'SELECT * FROM cimes_partenaires ORDER BY id ASC';
        if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
            $categorie = $_GET['categorie'];
            $stmt = $pdo->prepare('SELECT * FROM cimes_partenaires WHERE categorie = :categorie ORDER BY id ASC');
            $stmt->execute([':categorie' => $categorie]);
            sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
            return;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
        return;
    }
    if ($quoi === 'partenaires_categories') {
        $stmt = $pdo->prepare("SELECT DISTINCT categorie FROM cimes_partenaires ORDER BY categorie");
        $stmt->execute();
        sendJSON($stmt->fetchAll(PDO::FETCH_COLUMN));
        return;
    }

    if ($quoi === 'projet') {
        // Admin voit tout, un utilisateur ne voit que ses propres projets
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            $stmt = $pdo->prepare('SELECT * FROM cimes_projets ORDER BY id DESC');
            $stmt->execute();
        } else {
            // Utilisateur connecté (via session)
            $userId = $_SESSION['id'] ?? 0;
            $stmt = $pdo->prepare('SELECT * FROM cimes_projets WHERE user_id = :user_id ORDER BY id DESC');
            $stmt->execute([':user_id' => $userId]);
        }
        sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
        return;
    }


    /* ── Requêtes génériques (fallback) ── */
    $stmt = $pdo->prepare($req);
    $stmt->execute();
    $resultat_req = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    sendJSON($resultat_req);
}




function  getdonnees_par_id($quoi, $id)
{
    $pdo = getConnexion();

    if ($quoi === 'carrousel') {
        $req = 'SELECT * FROM cimes_carrousel WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_carrousel') {
        $req = 'DELETE FROM cimes_carrousel WHERE id=:id';
    }
    if ($quoi === 'contact') {
        $req = 'SELECT * FROM cimes_contacts WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_contact') {
        $req = 'DELETE FROM cimes_contacts WHERE id=:id';
    }

    if ($quoi === 'actu') {
        $req = 'SELECT * FROM cimes_actu WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_actu') {
        $req = 'DELETE FROM cimes_actu WHERE id=:id';
    }

    if ($quoi === 'activites') {
        $req = 'SELECT * FROM cimes_activites WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_activites') {
        $req = 'DELETE FROM cimes_activites WHERE id=:id';
    }

    if ($quoi === 'partenaire') {
        $req = 'SELECT * FROM cimes_partenaires WHERE id=:id';
    }


    if ($quoi === 'axes') {
        $req = 'SELECT * FROM cimes_axes WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_axes') {
        $req = 'DELETE FROM cimes_axes WHERE id=:id';
    }

    if ($quoi === 'mission') {
        $req = 'SELECT * FROM cimes_mission WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_mission') {
        $req = 'DELETE FROM cimes_mission WHERE id=:id';
    }

    if ($quoi === 'programme') {
        $req = 'SELECT * FROM cimes_programme WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_programme') {
        $req = 'DELETE FROM cimes_programme WHERE id=:id';
    }

    // Ajoutez ce cas dans getdonnees_par_id
    if ($quoi === 'gouvernance') {
        $req = 'SELECT * FROM cimes_gouvernance WHERE id = :id';
    }

    if ($quoi === 'annuaire') {
        $req = 'SELECT * FROM cimes_annuaire_personnes WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_annuaire_personnes') {
        $req = 'DELETE FROM cimes_annuaire_personnes WHERE id=:id';
    }

    if ($quoi === 'annuaire_structure') {
        $req = 'SELECT * FROM cimes_annuaire_structures WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_annuaire_structures') {
        $req = 'DELETE FROM cimes_annuaire_structures WHERE id=:id';
    }

    if ($quoi === 'projet') {
        // Si un user_id est passé en GET, on filtre par cet ID (utilisé par l'Apps Script)
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $userId = (int) $_GET['user_id'];
            $stmt = $pdo->prepare('SELECT * FROM cimes_projets WHERE user_id = :user_id ORDER BY id DESC');
            $stmt->execute([':user_id' => $userId]);
            sendJSON($stmt->fetchAll(PDO::FETCH_ASSOC));
            return;
        }
        // Sinon, comportement normal (admin voit tout, user voit ses propres projets)
        if ($_SESSION['role'] === 'admin') {
            $stmt = $pdo->prepare('SELECT * FROM cimes_projets ORDER BY id DESC');
            $stmt->execute();
        } else {
            $userId = $_SESSION['id'];
            $stmt = $pdo->prepare('SELECT * FROM cimes_projets WHERE user_id = :user_id ORDER BY id DESC');
            $stmt->execute([':user_id' => $userId]);
        }
        $resultat_req = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendJSON($resultat_req);
        return;
    }
    if ($quoi === 'supp_cimes_projets') {
        if ($_SESSION['role'] !== 'admin') {
            $check = $pdo->prepare('SELECT user_id FROM cimes_projets WHERE id = :id');
            $check->execute([':id' => $id]);
            $owner = $check->fetchColumn();
            if ($owner != $_SESSION['id']) {
                sendJSON(['error' => 'unauthorized']);
                return;
            }
        }
        $req = 'DELETE FROM cimes_projets WHERE id = :id';
    }

    if ($quoi === 'massif') {
        $req = 'SELECT * FROM cimes_massifs WHERE id=:id';
    }
    if ($quoi === 'supp_cimes_massifs') {
        $req = 'DELETE FROM cimes_massifs WHERE id=:id';
    }
    if ($quoi === 'publication') {
        $req = 'SELECT *FROM cimes_publication  WHERE massif = :id';
    }
    if ($quoi === 'supp_cimes_publication') {
        $req = 'DELETE FROM cimes_publication WHERE id=:id';
    }

    if ($quoi === 'publication_value') {
        $req = 'SELECT *FROM cimes_publication  WHERE id = :id';
    }

    if ($quoi === 'supp_cimes_recherche_montagne') {
        $req = 'DELETE FROM cimes_recherche_montagne WHERE id = :id';
    }
    if ($quoi === 'supp_cimes_ecosysteme') {
        $req = 'DELETE FROM cimes_ecosysteme WHERE id = :id';
    }

    if ($quoi === 'supp_cimes_valorisation') {
        $req = 'DELETE FROM cimes_valorisation WHERE id = :id';
    }
    if ($quoi === 'supp_cimes_ecosysteme') {
        $req = 'DELETE FROM cimes_ecosysteme WHERE id = :id';
    }
    if ($quoi === 'supp_cimes_actu_event') {
        $req = 'DELETE FROM cimes_actu_event WHERE id = :id';
    }

    if ($quoi === 'programme_detail') {
        $req = 'SELECT * FROM cimes_programme WHERE id=:id';
    }
    if ($quoi === 'recherche_montagne') {
        $req = 'SELECT * FROM cimes_recherche_montagne WHERE parent_id=:id';
    }
    if ($quoi === 'recherche_montagne_detail') {
        $req = 'SELECT * FROM cimes_recherche_montagne WHERE id=:id';
    }
    if ($quoi === 'valorisation') {
        $req = 'SELECT *FROM cimes_valorisation WHERE parent_id=:id';
    }
    if ($quoi === 'ecosysteme') {
        $req = 'SELECT *FROM cimes_ecosysteme WHERE parent_id=:id';
    }

    if ($quoi === 'publication_detail') {
        $req = 'SELECT *FROM cimes_publication WHERE id=:id';
    }

    $stmt = $pdo->prepare($req);
    $stmt->bindValue(":id", $id, PDO::PARAM_STR);
    $stmt->execute();
    $resultat_req = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
    sendJSON($resultat_req);
}
function supprimer_programme($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    $stmt = $pdo->prepare('DELETE FROM cimes_programme WHERE id = :id');
    if ($stmt->execute([':id' => $id])) echo 'ok';
    else echo 'ko';
}


// ==================== LOGO PARTENAIRES ====================
function ajouter_modif_logo_partenaire($input)
{
    $pdo = getConnexion();

    // Gestion de l'image
    $logoFilename = null;
    if (!empty($input['logo_base64']) && !empty($input['logo_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['logo_mime'];
        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }
        $decoded = base64_decode($input['logo_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }
        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $ext = $extMap[$mime];
        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $logoFilename = uniqid('logo_', true) . '.' . $ext;
        if (file_put_contents($uploadDir . $logoFilename, $decoded) === false) {
            echo 'ko_write';
            return;
        }
        // Supprimer l'ancienne image en modification
        if ($input['lien'] === 'modif_logo_partenaire' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT logo FROM cimes_logo_partenaires WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldLogo = $old->fetchColumn();
            if ($oldLogo && file_exists($uploadDir . $oldLogo)) unlink($uploadDir . $oldLogo);
        }
    }

    try {
        if ($input['lien'] === 'cree_logo_partenaire') {
            $stmt = $pdo->prepare('INSERT INTO cimes_logo_partenaires (logo, alt) VALUES (:logo, :alt)');
            $data = [
                ':logo' => $logoFilename,
                ':alt'  => trim($input['alt'] ?? '')
            ];
        } else { // modif_logo_partenaire
            $sql = "UPDATE cimes_logo_partenaires SET alt = :alt";
            if ($logoFilename) $sql .= ", logo = :logo";
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $data = [
                ':alt' => trim($input['alt'] ?? ''),
                ':id'  => (int)$input['id']
            ];
            if ($logoFilename) $data[':logo'] = $logoFilename;
        }

        if ($stmt->execute($data)) echo 'ok';
        else echo 'ko_sql';
    } catch (Exception $e) {
        error_log('Erreur logo partenaire : ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }
}

function supprimer_logo_partenaire($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    // Récupérer le nom du fichier pour suppression
    $old = $pdo->prepare('SELECT logo FROM cimes_logo_partenaires WHERE id = :id');
    $old->execute([':id' => $id]);
    $logo = $old->fetchColumn();
    $stmt = $pdo->prepare('DELETE FROM cimes_logo_partenaires WHERE id = :id');
    if ($stmt->execute([':id' => $id])) {
        // Supprimer le fichier physique
        if ($logo) {
            $path = __DIR__ . '/../cimes_clients/img/' . $logo;
            if (file_exists($path)) unlink($path);
        }
        echo 'ok';
    } else {
        echo 'ko';
    }
}

function supprimer_projet($input)
{
    $pdo = getConnexion();

    // ── Déterminer l'utilisateur effectif ──
    $isTokenValid = (($input['token'] ?? '') === 'SECRET123');
    $userId = null;

    if ($isTokenValid && isset($input['user_id'])) {
        // Appel externe (ex: Apps Script) avec token valide
        $userId = (int) $input['user_id'];
    } elseif (isset($_SESSION['id'])) {
        // Utilisateur connecté via session (admin ou user)
        $userId = (int) $_SESSION['id'];
    } else {
        echo 'ko_unauthorized';
        return;
    }

    $id = (int) $input['id'];

    // Récupérer le propriétaire du projet
    $check = $pdo->prepare('SELECT user_id FROM cimes_projets WHERE id = :id');
    $check->execute([':id' => $id]);
    $owner = $check->fetchColumn();

    if ($owner === false) {
        echo 'ko_not_found';
        return;
    }

    // Admin (via session) peut tout supprimer
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // admin autorisé
    }
    // Appel externe : le projet doit appartenir au user_id fourni
    elseif ($isTokenValid) {
        if ($owner != $userId) {
            echo 'ko_unauthorized';
            return;
        }
    }
    // Utilisateur connecté (non admin) : doit être propriétaire
    else {
        if ($owner != $userId) {
            echo 'ko_unauthorized';
            return;
        }
    }

    $stmt = $pdo->prepare('DELETE FROM cimes_projets WHERE id = :id');
    if ($stmt->execute([':id' => $id])) {
        echo 'ok';
    } else {
        echo 'ko';
    }
}

// ==================== PARTENAIRES ====================


function ajouter_modif_partenaire($input)
{
    $pdo = getConnexion();

    // Vérifier que l'id est présent en modification
    if ($input['lien'] === 'modif_partenaire' && empty($input['id'])) {
        echo 'ko_id_manquant';
        return;
    }

    // Gestion de l'image (logo)
    $imageFilename = null;
    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['image_mime'];
        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }
        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }
        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $ext = $extMap[$mime];
        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $imageFilename = uniqid('part_', true) . '.' . $ext;
        if (file_put_contents($uploadDir . $imageFilename, $decoded) === false) {
            echo 'ko_write';
            return;
        }
        // Supprimer l'ancienne image en modification
        if ($input['lien'] === 'modif_partenaire' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT image FROM cimes_partenaires WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    try {
        if ($input['lien'] === 'cree_partenaire') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_partenaires
                (titre, role, categorie, description, lien_site, image)
                VALUES (:titre, :role, :categorie, :description, :lien_site, :image)
            ');
            $data = [
                'titre'       => trim($input['titre'] ?? ''),
                'role'        => trim($input['role'] ?? ''),
                'categorie'   => trim($input['categorie'] ?? ''),
                'description' => trim($input['description'] ?? ''),
                'lien_site'   => trim($input['lien_site'] ?? ''),
                'image'       => $imageFilename
            ];
        } else { // modif_partenaire
            $sql = "UPDATE cimes_partenaires SET
                        titre = :titre,
                        role = :role,
                        categorie = :categorie,
                        description = :description,
                        lien_site = :lien_site";
            if ($imageFilename) $sql .= ", image = :image";
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $data = [
                'titre'       => trim($input['titre'] ?? ''),
                'role'        => trim($input['role'] ?? ''),
                'categorie'   => trim($input['categorie'] ?? ''),
                'description' => trim($input['description'] ?? ''),
                'lien_site'   => trim($input['lien_site'] ?? ''),
                'id'          => (int)$input['id']
            ];
            if ($imageFilename) $data['image'] = $imageFilename;
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("PDO error (partenaire): " . print_r($errorInfo, true));
            echo 'ko_sql';
        }
    } catch (Exception $e) {
        error_log("Exception partenaire : " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
        echo 'ko_exception:' . $e->getMessage(); // renvoie le message d'erreur
        return;
    }
}

// Suppression d'un partenaire
function supprimer_partenaire($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    $stmt = $pdo->prepare('DELETE FROM cimes_partenaires WHERE id = :id');
    if ($stmt->execute([':id' => $id])) {
        echo 'ok';
    } else {
        echo 'ko';
    }
}

// =====// ==================== GOUVERNANCE UNIFIÉE ====================
function ajouter_modif_gouvernance_entite($input)
{
    $pdo = getConnexion();
    $type = $input['type'] ?? '';
    $allowedTypes = ['presidence', 'direction', 'conseil_groupement', 'bureau', 'conseil_scientifique', 'comite_orientation'];
    if (!in_array($type, $allowedTypes)) {
        echo 'ko_type';
        return;
    }

    // Gestion de la photo
    $photoFilename = null;
    if (!empty($input['photo_base64']) && !empty($input['photo_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = $input['photo_mime'];
        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }
        $decoded = base64_decode($input['photo_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }
        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $ext = $extMap[$mime];
        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $photoFilename = uniqid('gov_', true) . '.' . $ext;
        if (file_put_contents($uploadDir . $photoFilename, $decoded) === false) {
            echo 'ko_write';
            return;
        }
        if ($input['lien'] === 'modif_gouvernance_entite' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT photo FROM cimes_gouvernance WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldPhoto = $old->fetchColumn();
            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) unlink($uploadDir . $oldPhoto);
        }
    }

    // Champs communs – adaptés aux colonnes de la table
    $champs = [
        ':type'              => $type,
        ':prenom'            => trim($input['prenom'] ?? ''),
        ':nom'               => trim($input['nom'] ?? ''),
        ':email'             => trim($input['email'] ?? ''),
        ':role'              => trim($input['role'] ?? ''),
        ':fonction'          => trim($input['fonction'] ?? ''),
        ':laboratoire'       => trim($input['laboratoire'] ?? ''),
        ':tutelle'           => trim($input['tutelle'] ?? ''),
        ':etablissement'     => trim($input['etablissement'] ?? ''),
        ':discipline'        => trim($input['discipline'] ?? ''),
        ':unites'            => trim($input['unites'] ?? ''),
        ':bio'               => trim($input['bio'] ?? ''),
        ':ordre'             => (int)($input['ordre'] ?? 0),
        ':page_web'          => trim($input['page_web'] ?? ''),
        ':page_web_labo'     => trim($input['page_web_labo'] ?? ''),
        ':terrain_recherche' => trim($input['terrain_recherche'] ?? '')
    ];

    try {
        if ($input['lien'] === 'cree_gouvernance_entite') {
            $stmt = $pdo->prepare("
                INSERT INTO cimes_gouvernance
                (type, prenom, nom, email, role, fonction, laboratoire, tutelle, etablissement,
                 discipline, unites, bio, photo, ordre, page_web, page_web_labo, terrain_recherche)
                VALUES
                (:type, :prenom, :nom, :email, :role, :fonction, :laboratoire, :tutelle, :etablissement,
                 :discipline, :unites, :bio, :photo, :ordre, :page_web, :page_web_labo, :terrain_recherche)
            ");
            $champs[':photo'] = $photoFilename;
            $stmt->execute($champs);
            echo 'ok';
        } else {
            $sql = "UPDATE cimes_gouvernance SET
                    type = :type,
                    prenom = :prenom,
                    nom = :nom,
                    email = :email,
                    role = :role,
                    fonction = :fonction,
                    laboratoire = :laboratoire,
                    tutelle = :tutelle,
                    etablissement = :etablissement,
                    discipline = :discipline,
                    unites = :unites,
                    bio = :bio,
                    ordre = :ordre,
                    page_web = :page_web,
                    page_web_labo = :page_web_labo,
                    terrain_recherche = :terrain_recherche";

            if ($photoFilename !== null) {
                $sql .= ", photo = :photo";
                $champs[':photo'] = $photoFilename;
            }

            $sql .= " WHERE id = :id";
            $champs[':id'] = (int)$input['id'];

            $stmt = $pdo->prepare($sql);
            $stmt->execute($champs);
            echo 'ok';
        }
    } catch (Exception $e) {
        error_log("Erreur gouvernance entite: " . $e->getMessage());
        echo 'ko_exception: ' . $e->getMessage();
    }
}

function supprimer_gouvernance_entite($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    $stmt = $pdo->prepare('DELETE FROM cimes_gouvernance WHERE id = :id');
    if ($stmt->execute([':id' => $id])) echo 'ok';
    else echo 'ko';
}






// ==================== GOUVERNANCE SCHEMA ====================
function ajouter_modif_gouvernance_schema($input)
{
    $pdo = getConnexion();
    if ($input['lien'] === 'cree_gouvernance_schema') {
        $stmt = $pdo->prepare('INSERT INTO cimes_gouvernance_schema (titre, description, reunion, ordre) VALUES (:titre, :description, :reunion, :ordre)');
        $data = [
            ':titre' => trim($input['titre'] ?? ''),
            ':description' => trim($input['description'] ?? ''),
            ':reunion' => trim($input['reunion'] ?? ''),
            ':ordre' => (int)($input['ordre'] ?? 0)
        ];
    } else { // modif_gouvernance_schema
        $stmt = $pdo->prepare('UPDATE cimes_gouvernance_schema SET titre = :titre, description = :description, reunion = :reunion, ordre = :ordre WHERE id = :id');
        $data = [
            ':titre' => trim($input['titre'] ?? ''),
            ':description' => trim($input['description'] ?? ''),
            ':reunion' => trim($input['reunion'] ?? ''),
            ':ordre' => (int)($input['ordre'] ?? 0),
            ':id' => (int)$input['id']
        ];
    }
    if ($stmt->execute($data)) echo 'ok';
    else echo 'ko';
}
function supprimer_gouvernance_schema($input)
{
    $pdo = getConnexion();
    $id = (int)$input['id'];
    $stmt = $pdo->prepare('DELETE FROM cimes_gouvernance_schema WHERE id = :id');
    if ($stmt->execute([':id' => $id])) echo 'ok';
    else echo 'ko';
}




function getName($id)
{
    $pdo = getConnexion();
    $req = "SELECT name FROM nav_items WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}
function getParentId($id)
{
    $pdo = getConnexion();
    $req = "SELECT parent_id FROM nav_items WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}

function ajouter_modif_projet($input)
{
    $pdo = getConnexion();

    // ── Déterminer l'utilisateur effectif ──
    $isTokenValid = (($input['token'] ?? '') === 'SECRET123');
    $userId = null;

    if ($isTokenValid && isset($input['user_id'])) {
        $userId = (int) $input['user_id'];
    } elseif (isset($_SESSION['id'])) {
        $userId = (int) $_SESSION['id'];
    } else {
        echo 'ko_unauthorized';
        return;
    }

    // Traitement des localisations
    $localisations = $input['localisations'] ?? '[]';
    if (is_array($localisations)) {
        $localisations = json_encode($localisations, JSON_UNESCAPED_UNICODE);
    }

    try {
        if ($input['lien'] === 'cree_projet') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_projets
                (titre, acronyme, financeur, porteur_principal, structure_rattachement, site_web_porteur,
                 mots_cles, partenaires, disciplines, massif, pays, objectif_principal,
                 localisations, date_debut, date_fin, site_web, user_id)
                VALUES (:titre, :acronyme, :financeur, :porteur_principal, :structure_rattachement, :site_web_porteur,
                 :mots_cles, :partenaires, :disciplines, :massif, :pays, :objectif_principal,
                 :localisations, :date_debut, :date_fin, :site_web, :user_id)
            ');
        } else { // modif_projet
            $check = $pdo->prepare('SELECT user_id FROM cimes_projets WHERE id = :id');
            $check->execute([':id' => (int)$input['id']]);
            $owner = $check->fetchColumn();

            if ($owner === false) {
                echo 'ko_not_found';
                return;
            }

            // Vérification des droits
            if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
                if ($owner != $userId) {
                    echo 'ko_unauthorized';
                    return;
                }
            }

            $stmt = $pdo->prepare("
                UPDATE cimes_projets SET
                    titre = :titre,
                    acronyme = :acronyme,
                    financeur = :financeur,
                    porteur_principal = :porteur_principal,
                    structure_rattachement = :structure_rattachement,
                    site_web_porteur = :site_web_porteur,
                    mots_cles = :mots_cles,
                    partenaires = :partenaires,
                    disciplines = :disciplines,
                    massif = :massif,
                    pays = :pays,
                    objectif_principal = :objectif_principal,
                    localisations = :localisations,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    site_web = :site_web
                WHERE id = :id
            ");
        }

        // Données communes
        $data = [
            ':titre'                 => trim($input['titre'] ?? ''),
            ':acronyme'              => trim($input['acronyme'] ?? ''),
            ':financeur'             => trim($input['financeur'] ?? ''),
            ':porteur_principal'     => trim($input['porteur_principal'] ?? ''),
            ':structure_rattachement' => trim($input['structure_rattachement'] ?? ''),
            ':site_web_porteur'      => trim($input['site_web_porteur'] ?? ''),
            ':mots_cles'             => trim($input['mots_cles'] ?? ''),
            ':partenaires'           => trim($input['partenaires'] ?? ''),
            ':disciplines'           => trim($input['disciplines'] ?? ''),
            ':massif'                => trim($input['massif'] ?? ''),
            ':pays'                  => trim($input['pays'] ?? ''),
            ':objectif_principal'    => trim($input['objectif_principal'] ?? ''),
            ':localisations'         => $localisations,
            ':date_debut'            => trim($input['date_debut'] ?? ''),
            ':date_fin'              => trim($input['date_fin'] ?? ''),
            ':site_web'              => trim($input['site_web'] ?? ''),
        ];

        if ($input['lien'] === 'cree_projet') {
            $data[':user_id'] = $userId;
        } else {
            $data[':id'] = (int)$input['id'];
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            error_log('ajouter_modif_projet failed: ' . json_encode($stmt->errorInfo()));
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_projet exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) $stmt->closeCursor();
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ajouter_modif_publication($input)
{
    $pdo = getConnexion();
    if ($input['lien'] === 'cree_publication') {
        $publication = $pdo->prepare('INSERT INTO cimes_publication (thematique, titre, auteur, date, image, texte, massif) VALUES (:var1, :var2, :var3, :var4, :var5, :var6, :var7)');
    } elseif ($input['lien'] === 'modif_publication') {
        $publication = $pdo->prepare('UPDATE cimes_publication SET thematique = :var1, titre = :var2, auteur = :var3, date =:var4, image=:var5, texte=:var6 WHERE id = :id');
    }

    try {
        $data = [
            'var1' => $input['thematique'],
            'var2' => $input['titre'],
            'var3' => $input['auteur'],
            'var4' => $input['date'],
            'var5' => $input['image'],
            'var6' => $input['texte']
        ];

        if ($input['lien'] === 'cree_publication') {
            $data['var7'] = $input['massif'];
        }

        if ($input['lien'] === 'modif_publication') {
            $data['id'] = $input['id'];
        }

        if ($publication->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $publication->closeCursor();
}



/////////////////////////////////////////////////////////GOUVERNANCE (NOUVELLE VERSION)///////////////////////////////////////////
function get_gouvernance_page()
{
    $pdo = getConnexion();

    $stmt = $pdo->prepare('SELECT * FROM cimes_gouvernance ORDER BY section_key, ordre ASC');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Organiser les données par section
    $data = [
        'hero'               => [],
        'nav'                => [],
        'stats'              => [],
        'schema'             => [],
        'direction_header'   => null,
        'direction_cards'    => [],
        'cg_header'          => null,
        'cg_people'          => [],
        'cs_header'          => null,
        'cs_people'          => [],
        'co_header'          => null,
        'co_people'          => [],
        'partenaires_header' => null,
    ];

    foreach ($rows as $row) {
        switch ($row['section_key']) {
            case 'hero':
                $data['hero'] = $row;
                break;
            case 'nav':
                $data['nav'][] = $row;
                break;
            case 'stats':
                $data['stats'][] = $row;
                break;
            case 'schema':
                $data['schema'][] = $row;
                break;
            case 'direction':
                if ($row['section_type'] === 'section_header') {
                    $data['direction_header'] = $row;
                } else {
                    $data['direction_cards'][] = $row;
                }
                break;
            case 'conseil-groupement':
                if ($row['section_type'] === 'section_header') {
                    $data['cg_header'] = $row;
                } else {
                    $data['cg_people'][] = $row;
                }
                break;
            case 'conseil-scientifique':
                if ($row['section_type'] === 'section_header') {
                    $data['cs_header'] = $row;
                } else {
                    $data['cs_people'][] = $row;
                }
                break;
            case 'comite-orientation':
                if ($row['section_type'] === 'section_header') {
                    $data['co_header'] = $row;
                } else {
                    $data['co_people'][] = $row;
                }
                break;
            case 'partenaires':
                $data['partenaires_header'] = $row;
                break;
        }
    }

    sendJSON($data);
}


////////////////////////////////////PROGRAMMES/////////////////////////////////////////////
function ajouter_modif_programme($input)
{
    $pdo = getConnexion();

    /* ── Gestion de l'image ── */
    $imageFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['image_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $extensions[$mime];
        $imageFilename = uniqid('prog_', true) . '.' . $ext;
        $dest = $uploadDir . $imageFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne image en mode modification
        if ($input['lien'] === 'modif_programme' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT image FROM cimes_programme WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    /* ── Vérification doublon (création uniquement) ── */
    if ($input['lien'] === 'cree_programme') {
        $check = $pdo->prepare('SELECT COUNT(*) FROM cimes_programme WHERE LOWER(titre) = LOWER(:titre)');
        $check->execute([':titre' => trim($input['titre'] ?? '')]);
        if ((int)$check->fetchColumn() > 0) {
            echo 'ko_doublon';
            return;
        }
    }

    /* ── Requêtes SQL ── */
    try {
        if ($input['lien'] === 'cree_programme') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_programme
                    (titre, texte, image)
                VALUES
                    (:titre, :texte, :image)
            ');
        } else {
            $imageClause = $imageFilename ? ', image = :image' : '';
            $stmt = $pdo->prepare("
                UPDATE cimes_programme SET
                    titre = :titre,
                    texte = :texte
                    {$imageClause}
                WHERE id = :id
            ");
        }

        $str = fn($k) => isset($input[$k]) && $input[$k] !== '' ? trim($input[$k]) : null;

        $data = [
            'titre' => $str('titre'),
            'texte' => $str('texte'),
        ];

        if ($input['lien'] === 'cree_programme') {
            $data['image'] = $imageFilename;
        }

        if ($input['lien'] === 'modif_programme') {
            $data['id'] = (int)$input['id'];
            if ($imageFilename) {
                $data['image'] = $imageFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            error_log('ajouter_modif_programme failed: ' . json_encode($stmt->errorInfo()));
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_programme exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}

////////////////////////////massif//////////////////////////////////////////////////////////
function ajouter_modif_massif($input)
{
    $pdo = getConnexion();

    // Vérifier si le massif existe déjà
    $verifMassif = $pdo->prepare('SELECT COUNT(*) FROM cimes_massifs WHERE nom = :nom');
    $verifMassif->execute(['nom' => $input['nom']]);
    if ($verifMassif->fetchColumn() > 0) {
        echo 'exists';
        return;
    }

    if ($input['lien'] === 'ajout_massif') {
        $massif = $pdo->prepare('INSERT INTO cimes_massifs(nom, latitude, longitude, chaine, région, pays, continent) VALUES (:var1, :var2, :var3, :var4, :var5, :var6, :var7)');
    } elseif ($input['lien'] === 'modif_massif') {
        $massif = $pdo->prepare('UPDATE cimes_massifs SET nom = :var1, latitude = :var2, longitude = :var3, chaine = :var4, région = :var5, pays = :var6, continent = :var7 WHERE id = :id');
    }

    try {
        $data = [
            'var1' => $input['nom'],
            'var2' => $input['latitude'],
            'var3' => $input['longitude'],
            'var4' => $input['chaine'],
            'var5' => $input['region'],
            'var6' => $input['pays'],
            'var7' => $input['continent']
        ];

        if ($input['lien'] === 'modif_massif') {
            $data['id'] = $input['id'];
        }

        if ($massif->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $massif->closeCursor();
}


function ajouter_modif_actu($input)
{
    $pdo = getConnexion();

    // Gestion de la photo (identique à l’annuaire)
    $photoFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($input['image_mime'], $allowed)) {
            echo 'ko_mime';
            return;
        }

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];
        $ext = $extMap[$input['image_mime']];

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $photoFilename = uniqid('actu_', true) . '.' . $ext;
        $dest = $uploadDir . $photoFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne image en cas de modification
        if ($input['lien'] === 'modif_actu' && !empty($input['id'])) {
            $oldStmt = $pdo->prepare('SELECT image FROM cimes_actu WHERE id = :id');
            $oldStmt->execute([':id' => (int)$input['id']]);
            $oldImage = $oldStmt->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    try {
        if ($input['lien'] === 'cree_actu') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_actu
                (titre, description_courte, description_longue, date, lieu, image)
                VALUES (:titre, :description_courte, :description_longue, :date, :lieu, :image)
            ');
        } else { // modif_actu
            $sql = "UPDATE cimes_actu SET
                        titre = :titre,
                        description_courte = :description_courte,
                        description_longue = :description_longue,
                        date = :date,
                        lieu = :lieu";
            if ($photoFilename) {
                $sql .= ", image = :image";
            }
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
        }

        $data = [
            'titre'               => $input['titre'] ?? '',
            'description_courte'  => $input['description_courte'] ?? '',
            'description_longue'  => $input['description_longue'] ?? '',
            'date'                => $input['date'] ?? '',
            'lieu'                => $input['lieu'] ?? ''
        ];

        if ($input['lien'] === 'cree_actu') {
            $data['image'] = $photoFilename;
        }

        if ($input['lien'] === 'modif_actu') {
            $data['id'] = (int)$input['id'];
            if ($photoFilename) {
                $data['image'] = $photoFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_actu : ' . $e->getMessage());
        echo 'ko';
    }

    if (isset($stmt)) $stmt->closeCursor();
}

function ajouter_modif_activites($input)
{
    $pdo = getConnexion();

    $photoFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($input['image_mime'], $allowed)) {
            echo 'ko_mime';
            return;
        }

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];
        $ext = $extMap[$input['image_mime']];

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $photoFilename = uniqid('activ_', true) . '.' . $ext;
        $dest = $uploadDir . $photoFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        if ($input['lien'] === 'modif_activites' && !empty($input['id'])) {
            $oldStmt = $pdo->prepare('SELECT image FROM cimes_activites WHERE id = :id');
            $oldStmt->execute([':id' => (int)$input['id']]);
            $oldImage = $oldStmt->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    try {
        if ($input['lien'] === 'cree_activites') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_activites
                (titre, date, lieu, description_courte, description_longue, image)
                VALUES (:titre, :date, :lieu, :description_courte, :description_longue, :image)
            ');
        } else {
            $sql = "UPDATE cimes_activites SET
                        titre = :titre,
                        date = :date,
                        lieu = :lieu,
                        description_courte = :description_courte,
                        description_longue = :description_longue";
            if ($photoFilename) {
                $sql .= ", image = :image";
            }
            $sql .= " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
        }

        $data = [
            'titre'               => $input['titre'] ?? '',
            'date'                => $input['date'] ?? '',
            'lieu'                => $input['lieu'] ?? '',
            'description_courte'  => $input['description_courte'] ?? '',
            'description_longue'  => $input['description_longue'] ?? ''
        ];

        if ($input['lien'] === 'cree_activites') {
            $data['image'] = $photoFilename;
        }

        if ($input['lien'] === 'modif_activites') {
            $data['id'] = (int)$input['id'];
            if ($photoFilename) {
                $data['image'] = $photoFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_activites : ' . $e->getMessage());
        echo 'ko';
    }

    if (isset($stmt)) $stmt->closeCursor();
}


/////////////////////////////////////////////////////////AXES DE RECHERCHE///////////////////////////////////////////
function ajouter_modif_axes($input)
{
    $pdo = getConnexion();

    /* ── Gestion de l'image ── */
    $imageFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['image_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $extensions[$mime];
        $imageFilename = uniqid('axe_', true) . '.' . $ext;
        $dest = $uploadDir . $imageFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne image en mode modification
        if ($input['lien'] === 'modif_axes' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT image FROM cimes_axes WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    /* ── Vérification doublon (création uniquement) ── */
    if ($input['lien'] === 'cree_axes') {
        $check = $pdo->prepare('SELECT COUNT(*) FROM cimes_axes WHERE LOWER(titre) = LOWER(:titre)');
        $check->execute([':titre' => trim($input['titre'] ?? '')]);
        if ((int)$check->fetchColumn() > 0) {
            echo 'ko_doublon';
            return;
        }
    }

    /* ── Requêtes SQL ── */
    try {
        if ($input['lien'] === 'cree_axes') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_axes
                    (titre, description, mots_cles, image)
                VALUES
                    (:titre, :description, :mots_cles, :image)
            ');
        } else {
            $imageClause = $imageFilename ? ', image = :image' : '';
            $stmt = $pdo->prepare("
                UPDATE cimes_axes SET
                    titre       = :titre,
                    description = :description,
                    mots_cles   = :mots_cles
                    {$imageClause}
                WHERE id = :id
            ");
        }

        $str = fn($k) => isset($input[$k]) && $input[$k] !== '' ? trim($input[$k]) : null;

        $data = [
            'titre'       => $str('titre'),
            'description' => $str('description'),
            'mots_cles'   => $str('mots_cles'),
        ];

        if ($input['lien'] === 'cree_axes') {
            $data['image'] = $imageFilename;
        }

        if ($input['lien'] === 'modif_axes') {
            $data['id'] = (int)$input['id'];
            if ($imageFilename) {
                $data['image'] = $imageFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            error_log('ajouter_modif_axes failed: ' . json_encode($stmt->errorInfo()));
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_axes exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}

////////////////////////////etablissement//////////////////////////////////////////////////////////

function ajouter_modif_etablissement($input)
{

    $pdo = getConnexion();
    if ($input['lien'] === 'cree_etablissement') {
        $etablissement = $pdo->prepare('INSERT INTO cimes_etablissement_membre(nom, mail) VALUES (:var1, :var2)');
    } elseif ($input['lien'] === 'modif_etablissement') {
        $etablissement = $pdo->prepare('UPDATE cimes_etablissement_membre SET nom = :var1, mail = :var2 WHERE id = :id');
    }

    try {
        $data = [
            'var1' => $input['nom'],
            'var2' => $input['mail']
        ];

        if ($input['lien'] === 'modif_etablissement') {
            $data['id'] = $input['id'];
        }

        if ($etablissement->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $etablissement->closeCursor();
}


function modifier_profil($input)
{
    $pdo = getConnexion();

    try {

        // =========================
        // VALIDATION DU MOT DE PASSE (désactivée)
        // =========================
        if (!empty($input['password'])) {

            $password = trim($input['password']);

            // // 8 caractères minimum
            // if (strlen($password) < 8) {
            //     echo json_encode([
            //         "status" => "error",
            //         "message" => "Mot de passe trop court (8 caractères minimum)."
            //     ]);
            //     exit;
            // }

            // // majuscule
            // if (!preg_match('/[A-Z]/', $password)) {
            //     echo json_encode([
            //         "status" => "error",
            //         "message" => "Le mot de passe doit contenir une majuscule."
            //     ]);
            //     exit;
            // }

            // // minuscule
            // if (!preg_match('/[a-z]/', $password)) {
            //     echo json_encode([
            //         "status" => "error",
            //         "message" => "Le mot de passe doit contenir une minuscule."
            //     ]);
            //     exit;
            // }

            // // chiffre
            // if (!preg_match('/[0-9]/', $password)) {
            //     echo json_encode([
            //         "status" => "error",
            //         "message" => "Le mot de passe doit contenir un chiffre."
            //     ]);
            //     exit;
            // }

            // // caractère spécial
            // if (!preg_match('/[\W_]/', $password)) {
            //     echo json_encode([
            //         "status" => "error",
            //         "message" => "Ajoute au moins un caractère spécial."
            //     ]);
            //     exit;
            // }
        }

        // =========================
        // MODIFICATION ADMIN
        // =========================
        if ($input['lien'] === 'modif_profil_admin') {

            $sql = "UPDATE cimes_admin SET 
                        lastname = :lastname,
                        firstname = :firstname,
                        email = :email";

            if (!empty($input['password'])) {
                $sql .= ", password = :password";
            }

            $sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $data = [
                'lastname'  => trim($input['lastname']),
                'firstname' => trim($input['firstname']),
                'email'     => trim($input['email']),
                'id'        => (int)$input['id']
            ];

            if (!empty($input['password'])) {
                $data['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            if ($stmt->execute($data)) {
                // Mise à jour des variables de session
                $_SESSION['lastname']  = $data['lastname'];
                $_SESSION['firstname'] = $data['firstname'];
                $_SESSION['email']     = $data['email'];

                echo json_encode([
                    "status" => "success",
                    "message" => "Profil admin modifié"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Erreur modification admin"
                ]);
            }
        }

        // =========================
        // MODIFICATION UTILISATEUR (table cimes_utilisateurs)
        // =========================
        if ($input['lien'] === 'modif_profil_user') {

            // Séparation du nom complet en prénom et nom
            $nameParts = explode(' ', trim($input['name'] ?? ''), 2);
            $firstname = $nameParts[0] ?? '';
            $lastname  = $nameParts[1] ?? '';

            $sql = "UPDATE cimes_utilisateurs SET 
                        lastname = :lastname,
                        firstname = :firstname,
                        email = :email";

            if (!empty($input['password'])) {
                $sql .= ", password = :password";
            }

            $sql .= " WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $data = [
                'lastname'  => $lastname,
                'firstname' => $firstname,
                'email'     => trim($input['email']),
                'id'        => (int)$input['id']
            ];

            if (!empty($input['password'])) {
                $data['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            if ($stmt->execute($data)) {
                // Mise à jour des variables de session
                $_SESSION['firstname'] = $firstname;
                $_SESSION['lastname']  = $lastname;
                $_SESSION['email']     = $data['email'];

                echo json_encode([
                    "status" => "success",
                    "message" => "Profil utilisateur modifié"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Erreur modification utilisateur"
                ]);
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

/////////////////////////////////////////////////ANNUAIRE DES PERSONNES////////////////////////////
function ajouter_modif_annuaire($input)
{
    $pdo = getConnexion();

    // ── 1. VÉRIFICATION DE LA LÉGITIMITÉ DE LA REQUÊTE ──
    $isTokenValid = (($input['token'] ?? '') === 'SECRET123');
    $isSessionValid = isset($_SESSION['id']);   // utilisateur connecté côté admin

    if (!$isTokenValid && !$isSessionValid) {
        echo json_encode(['status' => 'error', 'message' => 'Accès non autorisé']);
        return;
    }

    // ── 2. MODES ACCEPTÉS ──
    if (!in_array($input['lien'] ?? '', ['cree_annuaire', 'modif_annuaire'])) {
        echo 'ko_lien';
        return;
    }

    // ── 3. CHAMPS OBLIGATOIRES ──
    $nom    = trim($input['nom']    ?? '');
    $prenom = trim($input['prenom'] ?? '');
    if ($nom === '' || $prenom === '') {
        echo 'ko_champs';
        return;
    }

    // ── 4. GESTION PHOTO (base64) ──
    $photoFilename = null;
    if (!empty($input['photo_base64']) && !empty($input['photo_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($input['photo_mime'], $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $decoded = base64_decode($input['photo_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        $ext = $extMap[$input['photo_mime']];

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $photoFilename = uniqid('pers_', true) . '.' . $ext;
        $dest = $uploadDir . $photoFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne photo en modification
        if ($input['lien'] === 'modif_annuaire' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT photo FROM cimes_annuaire_personnes WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldPhoto = $old->fetchColumn();
            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                unlink($uploadDir . $oldPhoto);
            }
        }
    }

    // ── 5. VÉRIFICATION DOUBLON EN CRÉATION ──
    if ($input['lien'] === 'cree_annuaire') {
        $check = $pdo->prepare(
            'SELECT COUNT(*) FROM cimes_annuaire_personnes WHERE nom = :nom AND prenom = :prenom'
        );
        $check->execute([':nom' => $nom, ':prenom' => $prenom]);
        if ((int)$check->fetchColumn() > 0) {
            echo 'ko_doublon';
            return;
        }
    }

    // ── 6. RÉCUPÉRATION DES AUTRES CHAMPS ──
    $mail            = trim($input['mail']            ?? '');
    $fonction        = trim($input['fonction']        ?? '');
    $discipline      = trim($input['discipline']      ?? '');
    $etablissement   = trim($input['etablissement']   ?? '');
    $universite      = trim($input['universite']      ?? '');
    $page_web        = trim($input['page_web']        ?? '');
    $id_hal          = trim($input['id_hal']          ?? '');
    $terrain_recherche = trim($input['terrain_recherche'] ?? '');
    $mots_cles       = trim($input['mots_cles']       ?? '');
    $publications    = $input['publications']         ?? '';  // JSON string

    // ── 7. REQUÊTE SQL ──
    try {
        if ($input['lien'] === 'cree_annuaire') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_annuaire_personnes
                (nom, prenom, id_hal, mail, discipline, etablissement, universite,
                 fonction, page_web, terrain_recherche, mots_cles, publications, photo)
                VALUES
                (:nom, :prenom, :id_hal, :mail, :discipline, :etablissement, :universite,
                 :fonction, :page_web, :terrain_recherche, :mots_cles, :publications, :photo)
            ');
            $params = [
                ':nom'               => $nom,
                ':prenom'            => $prenom,
                ':id_hal'            => $id_hal,
                ':mail'              => $mail,
                ':discipline'        => $discipline,
                ':etablissement'     => $etablissement,
                ':universite'        => $universite,
                ':fonction'          => $fonction,
                ':page_web'          => $page_web,
                ':terrain_recherche' => $terrain_recherche,
                ':mots_cles'         => $mots_cles,
                ':publications'      => $publications,
                ':photo'             => $photoFilename,
            ];
        } else { // modif_annuaire
            $sql = '
                UPDATE cimes_annuaire_personnes SET
                    nom               = :nom,
                    prenom            = :prenom,
                    id_hal            = :id_hal,
                    mail              = :mail,
                    discipline        = :discipline,
                    etablissement     = :etablissement,
                    universite        = :universite,
                    fonction          = :fonction,
                    page_web          = :page_web,
                    terrain_recherche = :terrain_recherche,
                    mots_cles         = :mots_cles,
                    publications      = :publications
            ';
            if ($photoFilename) {
                $sql .= ', photo = :photo';
            }
            $sql .= ' WHERE id = :id';

            $stmt = $pdo->prepare($sql);

            $params = [
                ':nom'               => $nom,
                ':prenom'            => $prenom,
                ':id_hal'            => $id_hal,
                ':mail'              => $mail,
                ':discipline'        => $discipline,
                ':etablissement'     => $etablissement,
                ':universite'        => $universite,
                ':fonction'          => $fonction,
                ':page_web'          => $page_web,
                ':terrain_recherche' => $terrain_recherche,
                ':mots_cles'         => $mots_cles,
                ':publications'      => $publications,
                ':id'                => (int)$input['id'],
            ];
            if ($photoFilename) {
                $params[':photo'] = $photoFilename;
            }
        }

        $stmt->execute($params);
        echo 'ok';   // Format texte simple pour la compatibilité JS

    } catch (Exception $e) {
        error_log('ajouter_modif_annuaire error: ' . $e->getMessage());
        echo 'ko_execute';
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}



function ajouter_modif_annuaire_structure($input)
{
    $pdo = getConnexion();

    if (!in_array($input['lien'] ?? '', ['cree_structure', 'modif_structure'])) {
        echo 'ko_lien';
        return;
    }

    /* ── Gestion de la photo/logo ── */
    $photoFilename = null;

    if (!empty($input['photo_base64']) && !empty($input['photo_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['photo_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $decoded = base64_decode($input['photo_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $extensions[$mime];
        $photoFilename = uniqid('struct_', true) . '.' . $ext;
        $dest = $uploadDir . $photoFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne photo en mode modification
        if ($input['lien'] === 'modif_structure' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT photo FROM cimes_annuaire_structures WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldPhoto = $old->fetchColumn();
            if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                unlink($uploadDir . $oldPhoto);
            }
        }
    }

    /* ── Vérification doublon (création uniquement) ── */
    if ($input['lien'] === 'cree_structure') {
        $check = $pdo->prepare('SELECT COUNT(*) FROM cimes_annuaire_structures WHERE LOWER(etablissement) = LOWER(:nom)');
        $check->execute([':nom' => trim($input['etablissement'] ?? '')]);
        if ((int)$check->fetchColumn() > 0) {
            echo 'ko_doublon';
            return;
        }
    }

    /* ── Requêtes SQL ── */
    try {
        // Transformation de l'année de création (AAAA → AAAA-01-01)
        $annee_raw = trim($input['annee_creation'] ?? '');
        if ($annee_raw !== '' && preg_match('/^\d{4}$/', $annee_raw)) {
            $annee_creation = $annee_raw . '-01-01';
        } else {
            $annee_creation = null; // valeur non valide → NULL
        }

        if ($input['lien'] === 'cree_structure') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_annuaire_structures
                    (etablissement, responsable, discipline, domaine_recherche,
                     tutelles, annee_creation, adresse,
                     site_web, presentation, photo)
                VALUES
                    (:etablissement, :responsable, :discipline, :domaine_recherche,
                     :tutelles, :annee_creation, :adresse,
                     :site_web, :presentation, :photo)
            ');
        } else {
            $photoClause = $photoFilename ? ', photo = :photo' : '';
            $stmt = $pdo->prepare("
                UPDATE cimes_annuaire_structures SET
                    etablissement     = :etablissement,
                    responsable       = :responsable,
                    discipline        = :discipline,
                    domaine_recherche = :domaine_recherche,
                    tutelles          = :tutelles,
                    annee_creation    = :annee_creation,
                    adresse           = :adresse,
                    site_web          = :site_web,
                    presentation      = :presentation
                    {$photoClause}
                WHERE id = :id
            ");
        }

        $str = fn($k) => isset($input[$k]) && $input[$k] !== '' ? trim($input[$k]) : null;

        $data = [
            'etablissement'     => $str('etablissement'),
            'responsable'       => $str('responsable'),
            'discipline'        => $str('discipline'),
            'domaine_recherche' => $str('domaine_recherche'),
            'tutelles'          => $str('tutelles'),
            'annee_creation'    => $annee_creation,
            'adresse'           => $str('adresse'),
            'site_web'          => $str('site_web'),
            'presentation'      => $str('presentation'),
        ];

        if ($input['lien'] === 'cree_structure') {
            $data['photo'] = $photoFilename;
        }

        if ($input['lien'] === 'modif_structure') {
            $data['id'] = (int)$input['id'];
            if ($photoFilename) {
                $data['photo'] = $photoFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            error_log('ajouter_modif_structure failed: ' . json_encode($stmt->errorInfo()));
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_structure exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}


/////////////////////////////////////////////////////////Présentation///////////////////////////////////////////
function modif_presentation($input)
{
    $pdo = getConnexion();

    /* ── Gestion de l'image ── */
    $imageFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = $input['image_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        $decoded = base64_decode($input['image_base64'], true);

        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 10 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        $uploadDir = __DIR__ . '/../cimes_clients/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        /* Supprime l'ancienne image */
        $old = $pdo->query('SELECT image FROM cimes_presentation LIMIT 1');
        $oldImage = $old->fetchColumn();
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }

        $ext           = $extensions[$mime];
        $imageFilename = uniqid('pres_', true) . '.' . $ext;
        $dest          = $uploadDir . $imageFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }
    }

    /* ── UPDATE (ligne unique, pas de WHERE id) ── */
    try {
        $imageClause = $imageFilename ? ', image = :image' : '';

        $stmt = $pdo->prepare("
            UPDATE cimes_presentation SET
                texte = :texte
                {$imageClause}
        ");

        $str = fn($k) => isset($input[$k]) && $input[$k] !== '' ? trim($input[$k]) : null;

        $data = [
            'texte' => $str('texte'),
        ];

        if ($imageFilename) {
            $data['image'] = $imageFilename;
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        error_log('modif_presentation : ' . $e->getMessage());
        echo 'ko';
    }

    if (isset($stmt)) $stmt->closeCursor();
}

/////////////////////////////////////////////////////////CARROUSEL///////////////////////////////////////////
/////////////////////////////////////////////////////////CARROUSEL///////////////////////////////////////////
function modif_carrousel($input)
{
    $pdo = getConnexion();

    $id         = (int)($input['id'] ?? 0);
    $titre      = trim($input['titre'] ?? '');
    $sous_titre = trim($input['sous_titre'] ?? '');

    if ($id <= 0) {
        echo 'ko_id';
        return;
    }

    $uploadDir = __DIR__ . '/../cimes_clients/img/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $imageFilename = null;

    // Gestion de l'image si fournie
    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $mime = $input['image_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        // Supprimer l'ancienne image
        $oldStmt = $pdo->prepare('SELECT image FROM cimes_carrousel WHERE id = :id');
        $oldStmt->execute([':id' => $id]);
        $oldImage = $oldStmt->fetchColumn();
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }

        $ext = $extensions[$mime];
        $imageFilename = uniqid('carrousel_', true) . '.' . $ext;
        $dest = $uploadDir . $imageFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }
    }

    try {
        $imageClause = $imageFilename ? ', image = :image' : '';

        $stmt = $pdo->prepare("
            UPDATE cimes_carrousel SET
                titre      = :titre,
                sous_titre = :sous_titre
                {$imageClause}
            WHERE id = :id
        ");

        $params = [
            ':titre'      => $titre,
            ':sous_titre' => $sous_titre,
            ':id'         => $id,
        ];

        if ($imageFilename) {
            $params[':image'] = $imageFilename;
        }

        $stmt->execute($params);
        echo 'ok';
    } catch (Exception $e) {
        error_log('modif_carrousel exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}




/////////////////////////////////////////////////EVENEMENT////////////////////////////

function ajouter_modif_evenement($input)
{
    $pdo = getConnexion();

    if ($input['lien'] === 'cree_evenement') {
        $query = 'INSERT INTO cims_event(event_titre, event_date, event_lieu, event_img, event_descriptif, event_texte) VALUES (:var1, :var2, :var3, :var4, :var5, :var6)';
    } else if ($input['lien'] === 'modif_evenement') {
        $query = 'UPDATE cims_event SET event_titre = :var1, event_date = :var2, event_lieu = :var3, event_img = :var4, event_descriptif = :var5, event_texte = :var6 WHERE event_id = :id';
    }

    $annuaire = $pdo->prepare($query);

    try {
        $params = [
            'var1' => $input['titre'],
            'var2' => $input['date'],
            'var3' => $input['lieu'],
            'var4' => $input['image'],
            'var5' => $input['desc'],
            'var6' => $input['texte']
        ];

        if ($input['lien'] === 'modif_evenement') {
            $params['id'] = $input['id'];
        }

        if ($annuaire->execute($params)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $annuaire->closeCursor();
}
/////////////////////////////////////////////////////////FORMATIONS///////////////////////////////////////////
function ajouterModifRechercheMontagne($input)
{
    $pdo = getConnexion();
    if ($input['lien'] === 'ajout_recherche_montagne') {
        $recherche_montagne = $pdo->prepare('INSERT INTO cimes_recherche_montagne (parent_id, thematique, titre, date, image, texte) VALUES (:var1, :var2, :var3, :var4, :var5, :var6)');
    } elseif ($input['lien'] === 'modif_recherche_montagne') {
        $recherche_montagne = $pdo->prepare('UPDATE cimes_recherche_montagne SET thematique = :var2, titre = :var3, date = :var4, image = :var5, texte = :var6 WHERE id = :id');
    }

    try {
        $data = [
            'var2' => $input['thematique'],
            'var3' => $input['titre'],
            'var4' => $input['date'],
            'var5' => $input['image'],
            'var6' => $input['texte']
        ];

        if ($input['lien'] === 'ajout_recherche_montagne') {
            $data['var1'] = $input['id'];
        }

        if ($input['lien'] === 'modif_recherche_montagne') {
            $data['id'] = $input['id'];
        }

        if ($recherche_montagne->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $recherche_montagne->closeCursor();
}

////////////////////////////////////////////////////////mission////////////////////////////////////////////

function ajouter_modif_mission($input)
{
    $pdo = getConnexion();

    /* ── Gestion de l'image ── */
    $imageFilename = null;

    if (!empty($input['image_base64']) && !empty($input['image_mime'])) {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = $input['image_mime'];

        if (!in_array($mime, $allowedMimes)) {
            echo 'ko_mime';
            return;
        }

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        $decoded = base64_decode($input['image_base64'], true);
        if ($decoded === false) {
            echo 'ko_base64';
            return;
        }

        if (strlen($decoded) > 13 * 1024 * 1024) {
            echo 'ko_size';
            return;
        }

        // Dossier d'upload des missions (identique à mission.php : ../cimes_admin/img/img_mission/)
        $uploadDir = __DIR__ . '/../cimes_admin/img/img_mission/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $extensions[$mime];
        $imageFilename = uniqid('mission_', true) . '.' . $ext;
        $dest = $uploadDir . $imageFilename;

        if (file_put_contents($dest, $decoded) === false) {
            echo 'ko_write';
            return;
        }

        // Supprimer l'ancienne image en mode modification
        if ($input['lien'] === 'modif_mission' && !empty($input['id'])) {
            $old = $pdo->prepare('SELECT image FROM cimes_mission WHERE id = :id');
            $old->execute([':id' => (int)$input['id']]);
            $oldImage = $old->fetchColumn();
            if ($oldImage && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }
        }
    }

    /* ── Vérification doublon (création uniquement) ── */
    if ($input['lien'] === 'cree_mission') {
        $check = $pdo->prepare('SELECT COUNT(*) FROM cimes_mission WHERE LOWER(nom) = LOWER(:nom)');
        $check->execute([':nom' => trim($input['nom'] ?? '')]);
        if ((int)$check->fetchColumn() > 0) {
            echo 'ko_doublon';
            return;
        }
    }

    /* ── Requêtes SQL ── */
    try {
        if ($input['lien'] === 'cree_mission') {
            $stmt = $pdo->prepare('
                INSERT INTO cimes_mission
                    (nom, texte, image)
                VALUES
                    (:nom, :texte, :image)
            ');
        } else {
            $imageClause = $imageFilename ? ', image = :image' : '';
            $stmt = $pdo->prepare("
                UPDATE cimes_mission SET
                    nom   = :nom,
                    texte = :texte
                    {$imageClause}
                WHERE id = :id
            ");
        }

        $str = fn($k) => isset($input[$k]) && $input[$k] !== '' ? trim($input[$k]) : null;

        $data = [
            'nom'   => $str('nom'),
            'texte' => $str('texte'),
        ];

        if ($input['lien'] === 'cree_mission') {
            $data['image'] = $imageFilename;
        }

        if ($input['lien'] === 'modif_mission') {
            $data['id'] = (int)$input['id'];
            if ($imageFilename) {
                $data['image'] = $imageFilename;
            }
        }

        if ($stmt->execute($data)) {
            echo 'ok';
        } else {
            error_log('ajouter_modif_mission failed: ' . json_encode($stmt->errorInfo()));
            echo 'ko_execute';
        }
    } catch (Exception $e) {
        error_log('ajouter_modif_mission exception: ' . $e->getMessage());
        echo 'ko_exception:' . $e->getMessage();
    }

    if (isset($stmt)) {
        $stmt->closeCursor();
    }
}



/////////////////////////////////////////////////////////Index///////////////////////////////////////////
function ajouter_modif_index($input)
{

    $pdo = getConnexion();

    if ($input['lien'] === 'modif_index') {
        $index = $pdo->prepare('UPDATE cims_page_index SET photo1=:var1,  photo2=:var2,  photo3=:var3,  photo4=:var4, titre=:var5, texte=:var6, image=:var7, titre2=:var8 WHERE id=:id');
    }
    try {

        if ($input['lien'] === 'modif_index') {
            if ($index->execute(array(
                'var1' => $input['photo1'],
                'var2' => $input['photo2'],
                'var3' => $input['photo3'],
                'var4' => $input['photo4'],
                'var5' => $input['titre'],
                'var6' => $input['texte'],
                'var7' => $input['image'],
                'var8' => $input['titre2'],
                'id' => $input['id']

            )) === true) {
                echo 'ok';
            } else {
                echo 'ko';
            }
        }
    } catch (Exception $e) {
        $message = "Il ya eu un problème" . $e->getMessage();
        echo 'ko';
    }
    $index->closeCursor();
}

/////////////////////////////////////////////////////////Gouvernance///////////////////////////////////////////
function ajouter_modif_gouvernance($input)
{
    $pdo = getConnexion();
    if ($input['lien'] === 'cree_gouvernance') {
        $gouvernance = $pdo->prepare('INSERT INTO cimes_gouvernance(nom, texte, image) VALUES (:var1, :var2, :var3)');
    } elseif ($input['lien'] === 'modif_gouvernance') {
        $gouvernance = $pdo->prepare('UPDATE cimes_gouvernance SET nom = :var1, texte = :var2, image = :var3 WHERE id = :id');
    }

    try {
        $data = [
            'var1' => $input['nom'],
            'var2' => $input['texte'],
            'var3' => $input['image']
        ];

        if ($input['lien'] === 'modif_gouvernance') {
            $data['id'] = $input['id'];
        }

        if ($gouvernance->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $gouvernance->closeCursor();
}
/////////////////////////////////////////////////////////////////
function getConnexion()
{
    return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

function sendJSON($lesdonneesrecuperees)
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    echo json_encode($lesdonneesrecuperees, JSON_UNESCAPED_UNICODE);
}

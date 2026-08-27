<?php
session_start();
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

// Récupérer l'action depuis le paramètre GET
$action = $_GET['action'] ?? '';

try {
    $conn = getConnexion();
    $userId = $_SESSION['id'];

    switch ($action) {
        case 'get_user_info':
            getUserInfo($conn, $userId);
            break;
        case 'update_user_info':
            updateUserInfo($conn, $userId);
            break;
        case 'get_publications':
            getPublications($conn, $userId);
            break;
        case 'get_recherches':
            getRecherches($conn, $userId);
            break;
        case 'get_rechercheId':
            $rechercheId = $_GET['id'] ?? '';
            if ($rechercheId) {
                getRechercheId($conn, $rechercheId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de recherche manquant']);
            }
            break;

        case 'get_publicationId':
            $publicationId = $_GET['id'] ?? '';
            if ($publicationId) {
                getPublicationId($conn, $publicationId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de publication manquant']);
            }
            break;

        case 'update_recherche':
            $rechercheId = $_POST['id'] ?? '';
            if ($rechercheId) {
                updateRecherche($conn, $rechercheId, $userId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de recherche manquant']);
            }
            break;

        case 'delete_publication':
            $publicationId = $_GET['id'] ?? '';
            if ($publicationId) {
                deletePublication($conn, $publicationId, $userId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de publication manquant']);
            }
            break;
        case 'delete_recherche':
            $rechercheId = $_GET['id'] ?? '';
            if ($rechercheId) {
                deleteRecherche($conn, $rechercheId, $userId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de recherche manquant']);
            }
            break;
        case 'get_massifs':
            getMassifs($conn);
            break;
        case 'get_thematiques':
            getThematique($conn);
            break;
        case 'check_image':
            $imageName = $_GET['image_name'] ?? '';
            if ($imageName) {
                checkImage($conn, $imageName);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'lastname d\'image manquant']);
            }
            break;
        case 'add_publication':
            addPublication($conn, $userId);
            break;
        case 'add_recherche':
            addRecherche($conn, $userId);
            break;
        case 'update_publication':
            $publicationId = $_POST['id'] ?? '';
            if ($publicationId) {
                updatePublication($conn, $publicationId, $userId);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'ID de publication manquant']);
            }
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Action non valide']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}

function getUserInfo($conn, $userId)
{
    $userSql = "SELECT firstname, lastname, email FROM cimes_utilisateurs WHERE id = :id";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bindParam(':id', $userId);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    } else {
        echo json_encode(['user' => $user]);
    }
}

function getPublications($conn, $userId)
{
    $pubSql = "
        SELECT p.thematique, p.id, p.titre, p.date, p.massif, m.lastname AS lastname_massif
        FROM cimes_publication p
        JOIN cimes_massifs m ON p.massif = m.id
        WHERE p.id_auteur = :id_auteur
        ORDER BY p.thematique ASC;
";
    $pubStmt = $conn->prepare($pubSql);
    $pubStmt->bindParam(':id_auteur', $userId);
    $pubStmt->execute();
    $publications = $pubStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['publications' => $publications]);
}
function getRecherches($conn, $userId)
{
    $pubSql = "
        SELECT r.thematique, r.id, r.titre, r.date, r.parent_id
        FROM cimes_recherche_montagne r
        WHERE r.id_auteur = :id_auteur
        ORDER BY r.thematique ASC;
";
    $pubStmt = $conn->prepare($pubSql);
    $pubStmt->bindParam(':id_auteur', $userId);
    $pubStmt->execute();
    $recherches = $pubStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['recherches' => $recherches]);
}
function getPublicationId($conn, $publicationId)
{
    $pubSql = "
        SELECT p.id, p.thematique, p.titre, p.date, p.image, p.texte, p.massif
        FROM cimes_publication p
        WHERE p.id = :id";
    $pubStmt = $conn->prepare($pubSql);
    $pubStmt->bindParam(':id', $publicationId);
    $pubStmt->execute();
    $publication = $pubStmt->fetch(PDO::FETCH_ASSOC);

    if ($publication) {
        echo json_encode(['publication' => $publication]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Publication non trouvée']);
    }
}
function getRechercheId($conn, $rechercheId)
{
    $sql = "SELECT * FROM cimes_recherche_montagne WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $rechercheId);
    $stmt->execute();
    $recherche = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recherche) {
        echo json_encode(['recherche' => $recherche]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Recherche non trouvée']);
    }
}

function updateUserInfo($conn, $userId)
{
    $lastname = $_POST['lastname'] ?? '';
    $prelastname = $_POST['prelastname'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($lastname && $prelastname && $email) {
        $updateSql = "UPDATE cimes_utilisateurs SET lastname = :lastname, prélastname = :prelastname, email = :email WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':lastname', $lastname);
        $updateStmt->bindParam(':prelastname', $prelastname);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':id', $userId);
        $updateStmt->execute();

        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes ou invalides']);
    }
}

function deletePublication($conn, $publicationId, $userId)
{
    $delSql = "DELETE FROM cimes_publication WHERE id = :id AND id_auteur = :id_auteur";
    $delStmt = $conn->prepare($delSql);
    $delStmt->bindParam(':id', $publicationId);
    $delStmt->bindParam(':id_auteur', $userId);
    $delStmt->execute();

    if ($delStmt->rowCount() > 0) {
        echo json_encode(['success' => 'Publication supprimée avec succès']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Publication non trouvée ou non autorisée']);
    }
}
function deleteRecherche($conn, $rechercheId, $userId)
{
    $delSql = "DELETE FROM cimes_recherche_montagne WHERE id = :id AND id_auteur = :id_auteur";
    $delStmt = $conn->prepare($delSql);
    $delStmt->bindParam(':id', $rechercheId);
    $delStmt->bindParam(':id_auteur', $userId);
    $delStmt->execute();

    if ($delStmt->rowCount() > 0) {
        echo json_encode(['success' => 'recherche supprimée avec succès']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'recherche non trouvée ou non autorisée']);
    }
}

function getMassifs($conn)
{
    $massifSql = "SELECT id, lastname FROM cimes_massifs";
    $massifStmt = $conn->prepare($massifSql);
    $massifStmt->execute();
    $massifs = $massifStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['massifs' => $massifs]);
}
function getThematique($conn)
{
    $thematiqueSql = "SELECT parent_id, thematique AS lastname FROM cimes_recherche_montagne GROUP BY thematique";
    $thematiqueStmt = $conn->prepare($thematiqueSql);
    $thematiqueStmt->execute();
    $thematiques = $thematiqueStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['thematiques' => $thematiques]);
}



function checkImage($conn, $imageName)
{
    $dir = isset($_GET['dir']) ? $_GET['dir'] : '';  // Récupérer la trajectoire depuis l'URL
    $imagePath = $dir . $imageName;

    if (file_exists($imagePath)) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
}


function addPublication($conn, $userId)
{
    // Obtenir les informations utilisateur pour l'auteur
    $userSql = "SELECT lastname, firstname FROM cimes_utilisateurs WHERE id = :id";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bindParam(':id', $userId);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $thematique = $_POST['thematique'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $texte = $_POST['texte'] ?? '';
    $massif = $_POST['massif'] ?? '';
    $imageName = null;

    if (!$thematique || !$titre || !$date || !$texte || !$massif) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $imagePath = '../cimes_admin/img/img_publication/' . $imageName;

        // Vérifier si l'image existe déjà
        if (file_exists($imagePath)) {
            http_response_code(409);
            echo json_encode(['error' => 'Image déjà existante']);
            exit();
        }

        // Déplacer le fichier téléchargé
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath) === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors du téléchargement de l\'image']);
            exit();
        }
    }

    // Combiner lastname et prélastname pour le champ auteur
    $auteur = ($user['lastname'] ?? '') . ' ' . ($user['firstname'] ?? '');

    $insertSql = "
        INSERT INTO cimes_publication (thematique, titre, date, image, texte, massif, id_auteur, auteur)
        VALUES (:thematique, :titre, :date, :image, :texte, :massif, :id_auteur, :auteur)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bindParam(':thematique', $thematique);
    $insertStmt->bindParam(':titre', $titre);
    $insertStmt->bindParam(':date', $date);
    $insertStmt->bindParam(':image', $imageName); // Utiliser uniquement le lastname du fichier
    $insertStmt->bindParam(':texte', $texte);
    $insertStmt->bindParam(':massif', $massif);
    $insertStmt->bindParam(':id_auteur', $userId);
    $insertStmt->bindParam(':auteur', $auteur);
    $insertStmt->execute();

    echo json_encode(['success' => true]);
}
function addRecherche($conn, $userId)
{
    // Obtenir les informations utilisateur pour l'auteur
    $userSql = "SELECT lastname, firstname FROM cimes_utilisateurs WHERE id = :id";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bindParam(':id', $userId);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $thematiqueId = $_POST['thematique'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $texte = $_POST['texte'] ?? '';
    $imageName = null;

    if (!$thematiqueId || !$titre || !$date || !$texte) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    // Récupérer le lastname de la thématique en fonction de l'ID
    $thematiqueSql = "SELECT thematique FROM cimes_recherche_montagne WHERE parent_id = :parent_id ";
    $thematiqueStmt = $conn->prepare($thematiqueSql);
    $thematiqueStmt->bindParam(':parent_id', $thematiqueId);
    $thematiqueStmt->execute();
    $thematiqueRow = $thematiqueStmt->fetch(PDO::FETCH_ASSOC);
    $thematiquelastname = $thematiqueRow['thematique'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $imagePath = '../cimes_admin/img/img_recherche_montagne/' . $imageName;

        // Vérifier si l'image existe déjà
        if (file_exists($imagePath)) {
            http_response_code(409);
            echo json_encode(['error' => 'Image déjà existante']);
            exit();
        }

        // Déplacer le fichier téléchargé
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath) === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors du téléchargement de l\'image']);
            exit();
        }
    }

    // Combiner lastname et prélastname pour le champ auteur
    $auteur = ($user['lastname'] ?? '') . ' ' . ($user['prélastname'] ?? '');

    $insertSql = "
        INSERT INTO cimes_recherche_montagne (thematique, titre, date, image, texte, parent_id, id_auteur, auteur)
        VALUES (:thematique, :titre, :date, :image, :texte, :parent_id, :id_auteur, :auteur)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bindParam(':thematique', $thematiquelastname);
    $insertStmt->bindParam(':titre', $titre);
    $insertStmt->bindParam(':date', $date);
    $insertStmt->bindParam(':image', $imageName); // Utiliser uniquement le lastname du fichier
    $insertStmt->bindParam(':texte', $texte);
    $insertStmt->bindParam(':parent_id', $thematiqueId); // Ajouter le parent_id ici
    $insertStmt->bindParam(':id_auteur', $userId);
    $insertStmt->bindParam(':auteur', $auteur);
    $insertStmt->execute();

    echo json_encode(['success' => true]);
}


function updatePublication($conn, $publicationId, $userId)
{
    $thematique = $_POST['thematique'] ?? '';
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $texte = $_POST['texte'] ?? '';
    $massif = $_POST['massif'] ?? '';
    $currentImageName = $_POST['current_image'] ?? null; // lastname de l'image actuelle

    if (!$thematique || !$titre || !$date || !$texte || !$massif) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    // Initialiser le lastname de l'image à l'image actuelle
    $imageName = $currentImageName;

    // Vérifier si une nouvelle image a été téléchargée
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $newImageName = basename($_FILES['image']['name']);
        $newImagePath = '../cimes_admin/img/img_programme/' . $newImageName;

        // Déplacer le nouveau fichier téléchargé
        if (move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)) {
            // Supprimer l'ancienne image si elle existe et est différente de la nouvelle
            if ($currentImageName && file_exists('../cimes_admin/img/img_programme/' . $currentImageName) && $currentImageName !== $newImageName) {
                unlink('../cimes_admin/img/img_programme/' . $currentImageName);
            }

            // Mettre à jour le lastname de l'image à la nouvelle image téléchargée
            $imageName = $newImageName;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors du téléchargement de l\'image']);
            exit();
        }
    }

    $updateSql = "
        UPDATE cimes_publication
        SET thematique = :thematique, titre = :titre, date = :date, image = :image, texte = :texte, massif = :massif
        WHERE id = :id AND id_auteur = :id_auteur";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':thematique', $thematique);
    $updateStmt->bindParam(':titre', $titre);
    $updateStmt->bindParam(':date', $date);
    $updateStmt->bindParam(':image', $imageName);
    $updateStmt->bindParam(':texte', $texte);
    $updateStmt->bindParam(':massif', $massif);
    $updateStmt->bindParam(':id', $publicationId);
    $updateStmt->bindParam(':id_auteur', $userId);
    $updateStmt->execute();

    echo json_encode(['success' => true]);
}

function updateRecherche($conn, $rechercheId, $userId)
{
    $thematiqueName = $_POST['thematique'] ?? ''; // lastname de la thématique
    $titre = $_POST['titre'] ?? '';
    $date = $_POST['date'] ?? '';
    $texte = $_POST['texte'] ?? '';
    $currentImageName = $_POST['current_image'] ?? null;

    // Vérifie que toutes les données nécessaires sont présentes
    if (!$thematiqueName || !$titre || !$date || !$texte) {
        http_response_code(400);
        echo json_encode(['error' => 'Données manquantes']);
        exit();
    }

    // lastname de l'image actuel ou nouveau
    $imageName = $currentImageName;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $newImageName = basename($_FILES['image']['name']);
        $newImagePath = '../cimes_admin/img/img_recherche_montagne/' . $newImageName;

        // Déplace le fichier téléchargé
        if (move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)) {
            // Supprime l'ancienne image si elle existe et est différente de la nouvelle
            if ($currentImageName && file_exists('../cimes_admin/img/img_recherche_montagne/' . $currentImageName) && $currentImageName !== $newImageName) {
                unlink('../cimes_admin/img/img_recherche_montagne/' . $currentImageName);
            }

            $imageName = $newImageName;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors du téléchargement de l\'image']);
            exit();
        }
    }

    // Récupérer l'ID de la thématique en fonction du lastname
    $thematiqueQuery = "SELECT parent_id FROM cimes_recherche_montagne WHERE thematique = :thematique_name ";
    $thematiqueStmt = $conn->prepare($thematiqueQuery);
    $thematiqueStmt->bindParam(':thematique_name', $thematiqueName);
    $thematiqueStmt->execute();
    $thematiqueResult = $thematiqueStmt->fetch(PDO::FETCH_ASSOC);

    if ($thematiqueResult) {
        $thematiqueId = $thematiqueResult['parent_id'];
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Thématique non trouvée']);
        exit();
    }

    // Prépare la requête de mise à jour
    $updateSql = "
        UPDATE cimes_recherche_montagne
        SET parent_id = :thematique_id, thematique = :thematique_name, titre = :titre, date = :date, image = :image, texte = :texte
        WHERE id = :id AND id_auteur = :id_auteur
    ";
    $updateStmt = $conn->prepare($updateSql);

    // Corrige les lastnames des paramètres pour qu'ils correspondent à ceux de la requête SQL
    $updateStmt->bindParam(':thematique_id', $thematiqueId);
    $updateStmt->bindParam(':thematique_name', $thematiqueName);
    $updateStmt->bindParam(':titre', $titre);
    $updateStmt->bindParam(':date', $date);
    $updateStmt->bindParam(':image', $imageName);
    $updateStmt->bindParam(':texte', $texte);
    $updateStmt->bindParam(':id', $rechercheId);
    $updateStmt->bindParam(':id_auteur', $userId);

    // Exécute la requête
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la mise à jour']);
    }
}

<?php

function getConnexion()
{
    try {
        return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}
function ajouterModifRechercheMontagne($input)
{
    $pdo = getConnexion();
    if ($input['lien'] == 'ajout_recherche_montagne') {
        $recherche_montagne = $pdo->prepare('INSERT INTO cimes_recherche_montagne (parent_id, thematique, titre, date, image, texte) VALUES (:var1, :var2, :var3, :var4, :var5, :var6)');
    } elseif ($input['lien'] == 'modif_recherche_montagne') {
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

        if ($input['lien'] == 'ajout_recherche_montagne') {
            $data['var1'] = $input['id'];
        }

        if ($input['lien'] == 'modif_recherche_montagne') {
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

function ajouterModifValorisation($input)
{
    $pdo = getConnexion();
    if ($input['lien'] == 'ajout_valorisation') {
        $valorisation = $pdo->prepare('INSERT INTO cimes_valorisation (titre, texte, image, parent_id) VALUES (:var1, :var2, :var3, :var4)');
    } elseif ($input['lien'] == 'modif_valorisation') {
        $valorisation = $pdo->prepare('UPDATE cimes_valorisation SET titre = :var1, texte = :var2, image = :var3 WHERE id = :id');
    }

    try {
        $data = [

            'var1' => $input['titre'],
            'var2' => $input['texte'],
            'var3' => $input['image']
        ];

        if ($input['lien'] == 'ajout_valorisation') {
            $data['var4'] = $input['id'];
        }

        if ($input['lien'] == 'modif_valorisation') {
            $data['id'] = $input['id'];
        }

        if ($valorisation->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $valorisation->closeCursor();
}
function ajouterModifEcosysteme($input)
{
    $pdo = getConnexion();
    if ($input['lien'] == 'ajout_ecosysteme') {
        $ecosysteme = $pdo->prepare('INSERT INTO cimes_ecosysteme (titre, mail, discipline, etablissement, image, parent_id) VALUES (:var1, :var2, :var3, :var4, :var5, :var6)');
    } elseif ($input['lien'] == 'modif_ecosysteme') {
        $ecosysteme = $pdo->prepare('UPDATE cimes_ecosysteme SET titre = :var1, mail = :var2, discipline = :var3, etablissement = :var4, image = :var5 WHERE id = :id');
    }

    try {
        $data = [

            'var1' => $input['titre'],
            'var2' => $input['mail'],
            'var3' => $input['discipline'],
            'var4' => $input['etablissement'],
            'var5' => $input['image']
        ];

        if ($input['lien'] == 'ajout_ecosysteme') {
            $data['var6'] = $input['id'];
        }

        if ($input['lien'] == 'modif_ecosysteme') {
            $data['id'] = $input['id'];
        }

        if ($ecosysteme->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $ecosysteme->closeCursor();
}


function ajouterModifActu_event($input)
{
    $pdo = getConnexion();
    if ($input['lien'] == 'ajout_actu_event') {
        $actu_event = $pdo->prepare('INSERT INTO cimes_actu_event (titre,  texte, date, lieu, image, etablissement, parent_id) VALUES (:var1, :var3, :var4, :var5, :var6, :var7, :var8)');
    } elseif ($input['lien'] == 'modif_actu_event') {
        $actu_event = $pdo->prepare('UPDATE cimes_actu_event SET titre = :var1, texte = :var3, date = :var4, lieu = :var5, image = :var6, etablissement = :var7 WHERE id = :id');
    }

    try {
        $data = [
            'var1' => $input['titre'],
            'var3' => $input['texte'],
            'var4' => $input['date'],
            'var5' => $input['lieu'],
            'var6' => $input['image'],
            'var7' => $input['etablissement']
        ];

        if ($input['lien'] == 'ajout_actu_event') {
            $data['var8'] = $input['id'];
        }

        if ($input['lien'] == 'modif_actu_event') {
            $data['id'] = $input['id'];
        }

        if ($actu_event->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $actu_event->closeCursor();
}


function getParentId($id)
{
    $pdo = getConnexion();
    $req = "SELECT parent_id FROM nav_items WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}


function getDatabaseName($id)
{
    $pdo = getConnexion();
    $req = "SELECT database_name FROM nav_items WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}


function getName($id)
{
    $pdo = getConnexion();
    $req = "SELECT name FROM nav_items WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn();
}

function getContentData($id)
{
    $pdo = getConnexion();

    /*
    |--------------------------------------------------------------------------
    | Vérification ID
    |--------------------------------------------------------------------------
    */

    if (empty($id) || !is_numeric($id)) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Récupération des infos nav_items
    |--------------------------------------------------------------------------
    */

    $req = "
        SELECT database_name, parent_id
        FROM nav_items
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($req);

    $stmt->execute([
        'id' => $id
    ]);

    $nav = $stmt->fetch(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Vérifie si nav_item existe
    |--------------------------------------------------------------------------
    */

    if (!$nav) {
        return [];
    }

    $database_name = $nav['database_name'];
    $parent_id = $nav['parent_id'];

    /*
    |--------------------------------------------------------------------------
    | Vérifie database_name
    |--------------------------------------------------------------------------
    */

    if (empty($database_name)) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Sécurisation du nom de table
    |--------------------------------------------------------------------------
    */

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $database_name)) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Vérifie si la table existe
    |--------------------------------------------------------------------------
    */

    $checkTable = "
        SHOW TABLES LIKE :table
    ";

    $stmt = $pdo->prepare($checkTable);

    $stmt->execute([
        'table' => $database_name
    ]);

    if (!$stmt->fetch()) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Vérifie si la colonne parent_id existe
    |--------------------------------------------------------------------------
    */

    $checkColumn = "
        SHOW COLUMNS
        FROM `$database_name`
        LIKE 'parent_id'
    ";

    $stmt = $pdo->prepare($checkColumn);

    $stmt->execute();

    $columnExists = $stmt->fetch();

    /*
    |--------------------------------------------------------------------------
    | Création automatique si la colonne n'existe pas
    |--------------------------------------------------------------------------
    */

    if (!$columnExists) {

        $alter = "
            ALTER TABLE `$database_name`
            ADD COLUMN parent_id INT NULL
        ";

        $pdo->exec($alter);

        /*
        |--------------------------------------------------------------------------
        | Remplissage automatique parent_id
        |--------------------------------------------------------------------------
        */

        if ($parent_id !== null) {

            $update = "
                UPDATE `$database_name`
                SET parent_id = :parent_id
            ";

            $stmt = $pdo->prepare($update);

            $stmt->execute([
                'parent_id' => $parent_id
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Récupération des données
    |--------------------------------------------------------------------------
    */

    if ($parent_id !== null) {
        // Vérifie si la colonne parent_id a des données réelles
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `$database_name` WHERE parent_id = :nav_id");
        $countStmt->execute(['nav_id' => $id]); // $id = l'id du nav_item courant
        $count = (int)$countStmt->fetchColumn();

        if ($count > 0) {
            $req = "SELECT * FROM `$database_name` WHERE parent_id = :nav_id";
            $stmt = $pdo->prepare($req);
            $stmt->execute(['nav_id' => $id]);
        } else {
            $req = "SELECT * FROM `$database_name`";
            $stmt = $pdo->prepare($req);
            $stmt->execute();
        }
    } else {
        $req = "SELECT * FROM `$database_name`";
        $stmt = $pdo->prepare($req);
        $stmt->execute();
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /*
    |--------------------------------------------------------------------------
    | Retour des données
    |--------------------------------------------------------------------------
    */

    return $data ?: [];
}





function getRecordById($id)
{
    $pdo = getConnexion();

    if (empty($id) || !is_numeric($id)) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Récupère table + parent_id
    |--------------------------------------------------------------------------
    */

    $req = "
        SELECT database_name, parent_id
        FROM nav_items
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($req);

    $stmt->execute([
        'id' => $id
    ]);

    $nav = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$nav) {
        return [];
    }

    $database_name = $nav['database_name'];
    $parent_id = $nav['parent_id'];

    if (!$database_name) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Sécurisation
    |--------------------------------------------------------------------------
    */

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $database_name)) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Vérifie table
    |--------------------------------------------------------------------------
    */

    $check = $pdo->prepare("
        SHOW TABLES LIKE :table
    ");

    $check->execute([
        'table' => $database_name
    ]);

    if (!$check->fetch()) {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Récupère UNE ligne
    |--------------------------------------------------------------------------
    */

    if ($parent_id !== null) {

        /*
        |--------------------------------------------------------------------------
        | Cas avec parent_id
        |--------------------------------------------------------------------------
        */

        $req = "
            SELECT *
            FROM `$database_name`
            WHERE parent_id = :parent_id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($req);

        $stmt->execute([
            'parent_id' => $parent_id
        ]);
    } else {

        /*
        |--------------------------------------------------------------------------
        | Cas sans parent_id
        |--------------------------------------------------------------------------
        */

        $req = "
            SELECT *
            FROM `$database_name`
            LIMIT 1
        ";

        $stmt = $pdo->prepare($req);

        $stmt->execute();
    }

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}



// function getActuById($id)
// {
//     $pdo = getConnexion();
//     $req = "SELECT * FROM cimes_actu_event WHERE id = :id";
//     $stmt = $pdo->prepare($req);
//     $stmt->execute(['id' => $id]);
//     return $stmt->fetchAll();
// }


function getDisplayField($database_name)
{
    $pdo = getConnexion();

    $req = "SHOW COLUMNS FROM `$database_name`";
    $stmt = $pdo->prepare($req);
    $stmt->execute();

    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    /*
    |--------------------------------------------------------------------------
    | Ordre de priorité
    |--------------------------------------------------------------------------
    */

    $priorities = [
        'titre',
        'nom',
        'name',
        'prenom',
        'thematique',
        'mail',
        'texte'
    ];

    foreach ($priorities as $field) {
        if (in_array($field, $columns)) {
            return $field;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fallback : première colonne texte trouvée
    |--------------------------------------------------------------------------
    */

    foreach ($columns as $column) {

        if ($column !== 'id' && $column !== 'parent_id') {
            return $column;
        }
    }

    return null;
}


function getValorisById($id)
{
    $pdo = getConnexion();
    $req = "SELECT * FROM cimes_valorisation WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll();
}
function getEcosystemeById($id)
{
    $pdo = getConnexion();
    $req = "SELECT * FROM cimes_ecosysteme WHERE id = :id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll();
}


function sendJSON($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
}




if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {

    $id = $_GET['id'];

    /*
    |--------------------------------------------------------------------------
    | Données obligatoires
    |--------------------------------------------------------------------------
    */

    $parent_id = getParentId($id);
    $contentData = getContentData($id);
    $data = getRecordById($id);

    /*
    |--------------------------------------------------------------------------
    | Réponse de base (toujours présente)
    |--------------------------------------------------------------------------
    */

    $response = [
        'parent_id' => $parent_id,
        'content_data' => $contentData,
        'id' => $data
    ];

    /*
    |--------------------------------------------------------------------------
    | Données indépendantes
    |--------------------------------------------------------------------------
    */

    // $actu_event = getActuById($id);
    // if (!empty($actu_event)) {
    //     $response['actu_event'] = $actu_event;
    // }

    $valorisation = getValorisById($id);
    if (!empty($valorisation)) {
        $response['valorisation'] = $valorisation;
    }

    $ecosysteme = getEcosystemeById($id);
    if (!empty($ecosysteme)) {
        $response['ecosysteme'] = $ecosysteme;
    }

    /*
    |--------------------------------------------------------------------------
    | Infos nav_items
    |--------------------------------------------------------------------------
    */

    $database_name = getDatabaseName($id);
    $display_field = getDisplayField($database_name);
    $response['display_field'] = $display_field;

    if ($database_name) {
        $response['database_name'] = $database_name;
        $response['base_slug'] = str_replace('cimes_', '', $database_name);
    }


    $name = getName($id);
    if ($name) {
        $response['name'] = $name;
    }

    $prevName = getName($parent_id);
    if ($prevName) {
        $response['prevName'] = $prevName;
    }

    /*
    |--------------------------------------------------------------------------
    | JSON final
    |--------------------------------------------------------------------------
    */

    sendJSON($response);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input['lien'] == 'ajout_recherche_montagne' || $input['lien'] == 'modif_recherche_montagne') {
        ajouterModifRechercheMontagne($input);
    }

    if ($input['lien'] == 'ajout_valorisation' || $input['lien'] == 'modif_valorisation') {
        ajouterModifValorisation($input);
    }
    if ($input['lien'] == 'ajout_ecosysteme' || $input['lien'] == 'modif_ecosysteme') {
        ajouterModifEcosysteme($input);
    }
    if ($input['lien'] == 'ajout_actu_event' || $input['lien'] == 'modif_actu_event') {
        ajouterModifActu_event($input);
    }
}

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
function ajouterModifCimes($input)
{
    $pdo = getConnexion();
    if ($input['lien'] == 'ajout_cimes') {
        $cimes = $pdo->prepare('INSERT INTO cimes (titre, texte, image, parent_id) VALUES (:var1, :var2, :var3, :var4)');
    } elseif ($input['lien'] == 'modif_cimes') {
        $cimes = $pdo->prepare('UPDATE cimes SET titre = :var1, texte = :var2, image = :var3 WHERE id = :id');
    }

    try {
        $data = [

            'var1' => $input['titre'],
            'var2' => $input['texte'],
            'var3' => $input['image']
        ];

        if ($input['lien'] == 'ajout_cimes') {
            $data['var4'] = $input['id'];
        }

        if ($input['lien'] == 'modif_cimes') {
            $data['id'] = $input['id'];
        }

        if ($cimes->execute($data)) {
            echo 'ok';
        } else {
            echo 'ko';
        }
    } catch (Exception $e) {
        echo 'ko';
    }

    $cimes->closeCursor();
}
function getParentId($id)
{
    $pdo = getConnexion();
    $req = "SELECT parent_id FROM nav_items WHERE id = :id";
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
function getContentData($parent_id)
{
    $pdo = getConnexion();

    $req = "SELECT * FROM cimes WHERE parent_id = :parent_id";
    $stmt = $pdo->prepare($req);
    $stmt->execute(['parent_id' => $parent_id]);
    return $stmt->fetchAll();
}
function getCimesById($id)
{

    $pdo = getConnexion();
    $req = "SELECT * FROM cimes WHERE id = :id";
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
    $parent_id = getParentId($id);
    $contentData = getContentData($id);


    $cimesValue = getCimesById($id);

    $name = getName($id);
    $prevName = getName($parent_id);

    $response = [
        'parent_id' => $parent_id,
        'content_data' => $contentData,

        'cimes' => $cimesValue,
        'name' => $name,
        'prevName' => $prevName,
    ];
    sendJSON($response);
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input['lien'] == 'ajout_cimes' || $input['lien'] == 'modif_cimes') {
        ajouterModifCimes($input);
    }
}

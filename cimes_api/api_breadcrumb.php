<?php
require_once("./api.php");



if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $parent_id = getParentId($id);

    $name = getName($id);
    $prevName = getName($parent_id);

    $response = [
        'parent_id' => $parent_id,
        'name' => $name,
        'prevName' => $prevName,
    ];
    echo json_encode($response); // Envoi de la réponse JSON
    exit;
}

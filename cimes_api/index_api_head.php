<?php
session_start();

function getConnexion()
{
    try {
        return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

function sendJSON($data)
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

function getdonnees($adminMode = false)
{
    $pdo = getConnexion();
    $whereVisible = $adminMode ? '' : ' AND visible = 1';

    $stmt = $pdo->prepare(
        "SELECT * FROM nav_items WHERE parent_id IS NULL" . $whereVisible . " ORDER BY position, id"
    );
    $stmt->execute();
    $resultat_req = $stmt->fetchAll();

    foreach ($resultat_req as &$row) {
        $subWhere = $adminMode ? '' : ' AND visible = 1';
        $sub_stmt = $pdo->prepare(
            "SELECT * FROM nav_items WHERE parent_id = :parent_id" . $subWhere . " ORDER BY position, id"
        );
        $sub_stmt->execute(['parent_id' => $row['id']]);
        $sub_items = $sub_stmt->fetchAll();
        if (!empty($sub_items)) {
            $row['sub_items'] = $sub_items;
        }
    }

    $stmt->closeCursor();
    sendJSON($resultat_req);
}

function updateNavbar($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare("UPDATE nav_items SET name = :name WHERE id = :id");
    $stmt->execute(['name' => $data['name'], 'id' => $data['id']]);
    sendJSON(['status' => 'success', 'message' => 'Element updated']);
}

function deleteNavbar($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare("DELETE FROM nav_items WHERE id = :id");
    $stmt->execute(['id' => $data['id']]);
    sendJSON(['status' => 'success', 'message' => 'Element deleted']);
}

function addSubMenu($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare(
        "INSERT INTO nav_items (parent_id, name, url, url_admin, database_name)
         VALUES (:parent_id, :name, :url, :url_admin, :database_name)"
    );
    $stmt->execute([
        'parent_id'     => $data['parent_id'],
        'name'          => $data['name'],
        'url'           => $data['url']           ?? '',
        'url_admin'     => $data['url_admin']     ?? 'dynam.php',
        'database_name' => $data['database_name'] ?? ''
    ]);
    sendJSON(['status' => 'success', 'message' => 'Sous-menu ajouté avec succès']);
}

function visible_invisible($data)
{
    $pdo = getConnexion();
    $id  = isset($data['id']) ? (int)$data['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        sendJSON(['error' => 'ID invalide']);
        return;
    }

    $stmt = $pdo->prepare("SELECT visible, parent_id FROM nav_items WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        sendJSON(['error' => 'Élément introuvable']);
        return;
    }

    $newVisible = $row['visible'] ? 0 : 1;

    $stmt = $pdo->prepare("UPDATE nav_items SET visible = ? WHERE id = ?");
    $stmt->execute([$newVisible, $id]);

    $affectedChildren = 0;
    if ($row['parent_id'] === null) {
        $stmt = $pdo->prepare("UPDATE nav_items SET visible = ? WHERE parent_id = ?");
        $stmt->execute([$newVisible, $id]);
        $affectedChildren = $stmt->rowCount();
    }

    sendJSON([
        'success'           => true,
        'id'                => $id,
        'visible'           => $newVisible,
        'affected_children' => $affectedChildren,
    ]);
}


function reorderNavbar($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare("UPDATE nav_items SET position = :position WHERE id = :id");
    foreach ($data['order'] as $item) {
        $stmt->execute([
            'position' => $item['position'],
            'id'       => $item['id']
        ]);
    }
    sendJSON(['status' => 'success', 'updated' => count($data['order'])]);
}

// ── ROUTER ──────────────────────────────────────────────────────────────────
if (isset($_GET['query'])) {
    switch ($_GET['query']) {

        case 'navbar':
            getdonnees(false);
            break;

        case 'navbar_admin':
            if (empty($_SESSION['id'])) {
                http_response_code(403);
                sendJSON(['error' => 'Accès refusé']);
                exit;
            }
            getdonnees(true);
            break;

        case 'update_navbar':
            $data = json_decode(file_get_contents('php://input'), true);
            updateNavbar($data);
            break;

        case 'delete_navbar':
            $data = json_decode(file_get_contents('php://input'), true);
            deleteNavbar($data);
            break;

        case 'add_submenu':
            $data = json_decode(file_get_contents('php://input'), true);
            addSubMenu($data);
            break;

        case 'toggle_visibility':
            $data = json_decode(file_get_contents('php://input'), true);
            visible_invisible($data);
            break;

        case 'reorder_navbar':
            $data = json_decode(file_get_contents('php://input'), true);
            reorderNavbar($data);
            break;
    }
}

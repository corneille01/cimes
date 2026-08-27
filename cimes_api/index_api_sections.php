<?php
session_start();

function getConnexion()
{
    try {
        return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

function sendJSON($data)
{
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}

// ── GET : liste toutes les sections (triées par position) ──────────────────
function getSections()
{
    $pdo  = getConnexion();
    $stmt = $pdo->query('SELECT * FROM cimes_sections_page_accueil ORDER BY position, id');
    sendJSON($stmt->fetchAll());
}

// ── POST : bascule visible ↔ invisible ─────────────────────────────────────
function toggleSection($data)
{
    $pdo = getConnexion();
    $id  = isset($data['id']) ? (int)$data['id'] : 0;

    if ($id <= 0) {
        http_response_code(400);
        sendJSON(['error' => 'ID invalide']);
        return;
    }

    $stmt = $pdo->prepare('SELECT visible FROM cimes_sections_page_accueil WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) {
        http_response_code(404);
        sendJSON(['error' => 'Section introuvable']);
        return;
    }

    $newVisible = $row['visible'] ? 0 : 1;
    $pdo->prepare('UPDATE cimes_sections_page_accueil SET visible = ? WHERE id = ?')
        ->execute([$newVisible, $id]);

    sendJSON(['success' => true, 'id' => $id, 'visible' => $newVisible]);
}

// ── POST : ajoute une section ──────────────────────────────────────────────
function addSection($data)
{
    $pdo = getConnexion();

    // Position = max + 1
    $max = $pdo->query('SELECT COALESCE(MAX(position),0) FROM cimes_sections_page_accueil')->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO cimes_sections_page_accueil (section_key, label, visible, position)
         VALUES (:section_key, :label, 1, :position)'
    );
    $stmt->execute([
        'section_key' => trim($data['section_key'] ?? ''),
        'label'       => trim($data['label']       ?? ''),
        'position'    => (int)$max + 1,
    ]);

    sendJSON(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
}

// ── POST : modifie le label et/ou le section_key ───────────────────────────
function updateSection($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare(
        'UPDATE cimes_sections_page_accueil SET label = :label, section_key = :section_key WHERE id = :id'
    );
    $stmt->execute([
        'label'       => trim($data['label']       ?? ''),
        'section_key' => trim($data['section_key'] ?? ''),
        'id'          => (int)$data['id'],
    ]);
    sendJSON(['success' => true]);
}

// ── POST : supprime une section ────────────────────────────────────────────
function deleteSection($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare('DELETE FROM cimes_sections_page_accueil WHERE id = ?');
    $stmt->execute([(int)$data['id']]);
    sendJSON(['success' => true]);
}

// ── POST : réordonne ───────────────────────────────────────────────────────
function reorderSections($data)
{
    $pdo  = getConnexion();
    $stmt = $pdo->prepare(
        'UPDATE cimes_sections_page_accueil SET position = :position WHERE id = :id'
    );
    foreach ($data['order'] as $item) {
        $stmt->execute(['position' => (int)$item['position'], 'id' => (int)$item['id']]);
    }
    sendJSON(['success' => true, 'updated' => count($data['order'])]);
}

// ── ROUTER ─────────────────────────────────────────────────────────────────
if (!isset($_GET['query'])) exit;

$isAdmin = !empty($_SESSION['id']);

switch ($_GET['query']) {

    // Lecture publique (utilisée par index.php côté visiteur)
    case 'get_sections':
        getSections();
        break;

    // Toutes les routes suivantes nécessitent d'être connecté
    case 'toggle_section':
        if (!$isAdmin) {
            http_response_code(403);
            sendJSON(['error' => 'Accès refusé']);
            exit;
        }
        toggleSection(json_decode(file_get_contents('php://input'), true));
        break;

    case 'add_section':
        if (!$isAdmin) {
            http_response_code(403);
            sendJSON(['error' => 'Accès refusé']);
            exit;
        }
        addSection(json_decode(file_get_contents('php://input'), true));
        break;

    case 'update_section':
        if (!$isAdmin) {
            http_response_code(403);
            sendJSON(['error' => 'Accès refusé']);
            exit;
        }
        updateSection(json_decode(file_get_contents('php://input'), true));
        break;

    case 'delete_section':
        if (!$isAdmin) {
            http_response_code(403);
            sendJSON(['error' => 'Accès refusé']);
            exit;
        }
        deleteSection(json_decode(file_get_contents('php://input'), true));
        break;

    case 'reorder_sections':
        if (!$isAdmin) {
            http_response_code(403);
            sendJSON(['error' => 'Accès refusé']);
            exit;
        }
        reorderSections(json_decode(file_get_contents('php://input'), true));
        break;
}

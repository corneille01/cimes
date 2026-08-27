<?php
header('Access-Control-Allow-Origin: *'); // Autoriser l'accès depuis n'importe quel domaine (CORS)
header('Content-Type: application/json');

// Afficher toutes les erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction de connexion à la base de données
function getConnexion()
{
    try {
        return new PDO('mysql:host=localhost;dbname=ehou_db;charset=utf8', 'ehoudb_user', 'NVS*64gpr', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
    }
}

// Connexion à la base de données
$conn = getConnexion();

// Vérifier si la requête est pour récupérer les filtres
if (isset($_GET['filters']) && $_GET['filters'] === 'true') {
    // Récupérer les valeurs distinctes pour les filtres
    $filters = [
        'thematiques' => [],
        'auteurs' => [],
        'massifs' => [],
        'chaines' => [],
        'regions' => [],
        'pays' => [],
        'continents' => []
    ];

    // Requête pour chaque filtre
    $filterQueries = [
        'thematiques' => "SELECT DISTINCT thematique AS value FROM cimes_publication",
        'auteurs' => "SELECT DISTINCT auteur AS value FROM cimes_publication",
        'massifs' => "SELECT DISTINCT nom AS value FROM cimes_massifs",
        'chaines' => "SELECT DISTINCT chaine AS value FROM cimes_massifs",
        'regions' => "SELECT DISTINCT région AS value FROM cimes_massifs",
        'pays' => "SELECT DISTINCT pays AS value FROM cimes_massifs",
        'continents' => "SELECT DISTINCT continent AS value FROM cimes_massifs"
    ];

    foreach ($filterQueries as $key => $query) {
        $stmt = $conn->query($query);
        $filters[$key] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    echo json_encode($filters);
    exit;
}

// Requête SQL pour récupérer les données des massifs et leurs publications
$sql = "
    SELECT m.id, m.nom, m.latitude, m.longitude, p.id as publication_id, p.titre as publication_titre, p.date as publication_date, p.auteur as publication_auteur
    FROM cimes_massifs m
    LEFT JOIN cimes_publication p ON m.id = p.massif
";

// Ajout du filtrage par nom de massif si le paramètre est présent
$filters = [];
$params = [];

if (isset($_GET['query']) && !empty($_GET['query'])) {
    $filters[] = "m.nom LIKE :query";
    $params['query'] = '%' . $_GET['query'] . '%';
}

if (isset($_GET['thematique']) && !empty($_GET['thematique'])) {
    $filters[] = "p.thematique = :thematique";
    $params['thematique'] = $_GET['thematique'];
}

if (isset($_GET['auteur']) && !empty($_GET['auteur'])) {
    $filters[] = "p.auteur = :auteur";
    $params['auteur'] = $_GET['auteur'];
}

if (isset($_GET['massif']) && !empty($_GET['massif'])) {
    $filters[] = "m.nom = :massif";
    $params['massif'] = $_GET['massif'];
}

if (isset($_GET['chaine']) && !empty($_GET['chaine'])) {
    $filters[] = "m.chaine = :chaine";
    $params['chaine'] = $_GET['chaine'];
}

if (isset($_GET['region']) && !empty($_GET['region'])) {
    $filters[] = "m.région = :region";
    $params['region'] = $_GET['region'];
}

if (isset($_GET['pays']) && !empty($_GET['pays'])) {
    $filters[] = "m.pays = :pays";
    $params['pays'] = $_GET['pays'];
}

if (isset($_GET['continent']) && !empty($_GET['continent'])) {
    $filters[] = "m.continent = :continent";
    $params['continent'] = $_GET['continent'];
}

if (count($filters) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $filters);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

// Fermer la connexion à la base de données
$conn = null;

// Formatage des résultats pour inclure les publications sous chaque massif
$massifs = [];
foreach ($results as $id => $massifData) {
    $massifInfo = [
        'id' => $id,
        'nom' => $massifData[0]['nom'],
        'latitude' => $massifData[0]['latitude'],
        'longitude' => $massifData[0]['longitude'],
        'publications' => []
    ];
    foreach ($massifData as $data) {
        if ($data['publication_id']) {
            $massifInfo['publications'][] = [
                'id' => $data['publication_id'],
                'titre' => $data['publication_titre'],
                'date' => $data['publication_date'],
                'auteur' => $data['publication_auteur']
            ];
        }
    }
    $massifs[] = $massifInfo;
}

// Renvoyer les résultats en format JSON
echo json_encode($massifs);

<?php
// publication.php — Affichage des publications HAL (fallback sécurisé)
require_once 'db_connect.php';

$pdo = getConnexion();
$membresStmt = $pdo->query("SELECT id, id_hal, nom, prenom, CONCAT(prenom, ' ', nom) AS nom_complet FROM cimes_annuaire_personnes");
$membres = $membresStmt->fetchAll(PDO::FETCH_ASSOC);

$docid       = $_GET['docid']       ?? '';
$auteur      = $_GET['auteur']      ?? '';
$personId    = $_GET['person_id']   ?? null;
$page        = max(1, intval($_GET['page'] ?? 1));
$perPage     = 20;

$filtreAuteur = $_GET['filtre_auteur'] ?? '';
$filtreAnnee  = $_GET['filtre_annee']  ?? '';
$filtreType   = $_GET['filtre_type']   ?? '';
$recherche    = $_GET['recherche']     ?? '';

$currentMember = null;
if ($personId) {
    foreach ($membres as $m) {
        if ($m['id'] == $personId) {
            $currentMember = $m;
            break;
        }
    }
}
if (!$currentMember && $auteur) {
    $auteurLower = strtolower($auteur);
    foreach ($membres as $m) {
        if (!empty($m['id_hal']) && strtolower($m['id_hal']) === $auteurLower) {
            $currentMember = $m;
            break;
        }
    }
}
if (!$currentMember && $filtreAuteur) {
    foreach ($membres as $m) {
        if ($m['id'] == $filtreAuteur) {
            $currentMember = $m;
            break;
        }
    }
}

function queryHAL($queryParts, $fq = [], $start = 0, $rows = 20, $fl = '*')
{
    $base = 'https://api.hal.science/search/?wt=json';
    $q = urlencode(implode(' ', $queryParts));
    $url = $base . "&q=$q&rows=$rows&start=$start&sort=producedDate_s+desc&fl=$fl";
    if (!empty($fq)) {
        $url .= "&fq=" . implode('&fq=', array_map('urlencode', $fq));
    }
    return fetchJson($url);
}

function fetchJson($url)
{
    $response = @file_get_contents($url);
    if ($response === false && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);
    }
    if ($response === false || empty($response)) return null;
    return json_decode($response, true);
}

$fq = [];
if ($filtreAnnee) $fq[] = "producedDateY_i:$filtreAnnee";
if ($filtreType)  $fq[] = "docType_s:$filtreType";

$titrePage = "Toutes les publications des membres";

if ($docid) {
    $data = queryHAL(["docid:$docid"], [], 0, 1, '*');
} elseif ($currentMember) {
    $member = $currentMember;
    $titrePage = "Publications de " . htmlspecialchars($member['nom_complet']);
    $data = null;
    if (!empty($member['id_hal'])) {
        $idHalLower = strtolower($member['id_hal']);
        $searchParts = ["authIdHal_s:$idHalLower"];
        if ($recherche) $searchParts[] = $recherche;
        $start = ($page - 1) * $perPage;
        $data = queryHAL($searchParts, $fq, $start, $perPage, '*');
    }
    // Fallback uniquement si l'id_hal n'a rien donné ET qu'on a un nom/prénom
    if ((empty($member['id_hal']) || !$data || !isset($data['response']['docs']) || empty($data['response']['docs'])) && !empty($member['nom']) && !empty($member['prenom'])) {
        // Utilisation de authLastName_t exact + authFirstName_t préfixe
        $lastName  = str_replace(' ', '\ ', $member['nom']); // échappe les espaces
        $firstName = $member['prenom'];
        $searchParts = ['authLastName_t:"' . $lastName . '" AND authFirstName_t:' . $firstName . '*'];
        if ($recherche) $searchParts[] = $recherche;
        $start = ($page - 1) * $perPage;
        $data = queryHAL($searchParts, $fq, $start, $perPage, '*');
    }
} else {
    $idsWithHal = [];
    foreach ($membres as $m) {
        if (!empty($m['id_hal'])) $idsWithHal[] = $m['id_hal'];
    }
    if (empty($idsWithHal)) {
        die("<p style='text-align:center;padding:4rem'>Aucun membre avec identifiant HAL trouvé.</p>");
    }
    $halClauses = array_map(function ($id) {
        return "authIdHal_s:" . strtolower($id);
    }, $idsWithHal);
    $searchParts = ['(' . implode(' OR ', $halClauses) . ')'];
    if ($recherche) $searchParts[] = $recherche;
    $start = ($page - 1) * $perPage;
    $data = queryHAL($searchParts, $fq, $start, $perPage, '*');
}

if (!$data || !isset($data['response']['docs'])) {
    die("<p style='text-align:center;padding:4rem'>Réponse inattendue de l'API HAL.</p>");
}

$docs       = $data['response']['docs'];
$numFound   = $data['response']['numFound'] ?? count($docs);
$totalPages = ceil($numFound / $perPage);

$typeLabels = [
    'ART'           => 'Article',
    'COMM'          => 'Communication',
    'THESE'         => 'Thèse',
    'HDR'           => 'HDR',
    'LECTURE'       => 'Cours',
    'COUV'          => 'Couverture',
    'OTHER'         => 'Autre publication',
    'REPORT'        => 'Rapport',
    'MEM'           => 'Mémoire',
    'ETABTHESE'     => 'Thèse d\'établissement',
    'POSTER'        => 'Poster',
    'IMG'           => 'Image',
    'MAP'           => 'Carte',
    'SOFTWARE'      => 'Logiciel',
    'VIDEO'         => 'Vidéo',
    'PATENT'        => 'Brevet',
    'UNDEFINED'     => 'Non défini',
    'ISSUE'         => 'Numéro de revue',
    'BLOG'          => 'Billet de blog',
    'OTHERREPORT'   => 'Autre rapport',
    'PROCEEDINGS'   => 'Actes de conférence',
    'PRESCONF'      => 'Présentation de conférence',
    'REVS'          => 'Revue systématique',
    'MASTERTHESIS'  => 'Mémoire de master',
    'BACHELORTHESIS' => 'Mémoire de licence',
    'CHAPTER'       => 'Chapitre d\'ouvrage',
    'BOOK'          => 'Ouvrage',
    'DOC'           => 'Document',
    'HEARING'       => 'Audition',
    'NOTE'          => 'Note',
    'MEMINST'       => 'Mémoire institutionnel',
    'ETABLECT'      => 'Leçon d\'établissement',
];

function formatAuthors($authors, $highlightName = '')
{
    $authors = is_array($authors) ? $authors : [];
    if (empty($authors)) return 'Auteur inconnu';
    $firstAuthors = array_slice($authors, 0, 5);
    $escaped = array_map('htmlspecialchars', $firstAuthors);
    $text = implode(', ', $escaped);
    if (count($authors) > 5) $text .= ' <em>et al.</em>';
    if ($highlightName) {
        $safe = htmlspecialchars($highlightName);
        $text = preg_replace('/(' . preg_quote($safe, '/') . ')/i', '<span class="highlight-auth">$1</span>', $text);
    }
    return $text;
}

$highlightAuthorName = '';
if ($currentMember) {
    $highlightAuthorName = $currentMember['nom_complet'];
}
?>
<?php include('include/head.html') ?>
<title><?php echo htmlspecialchars($titrePage); ?> | CIMES</title>
<style>
    :root {
        --vert: #0F766E;
        --vert-dark: #134e4a;
        --vert-light: #E1F5EE;
        --vert-mid: #9FE1CB;
        --bg: #f0f4f3;
        --surface: #ffffff;
        --border: #e2e8f0;
        --text: #1e293b;
        --muted: #64748b;
        --hint: #94a3b8;
        --radius: 5px;
        --radius-sm: 5px;
        --shadow-card: 0 1px 3px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04);
        --shadow-hover: 0 4px 16px rgba(0, 0, 0, .10), 0 2px 6px rgba(0, 0, 0, .06);
    }

    body {
        background: var(--bg);
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        line-height: 1.6;
    }

    .publi-page {
        max-width: 1500px;
        margin: 5rem auto 3rem;
        padding: 0 1.5rem;
    }

    .publi-page h1 {
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 2.5rem;
        color: var(--vert-dark);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
        padding-top: 70px;
    }

    .results-count {
        color: var(--muted);
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    .publi-filters {
        position: sticky;
        top: 80px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-card);
        z-index: 50;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }

    .filter-group label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 0.3rem;
    }

    label[for="filtre_auteur"],
    label[for="filtre_annee"] {
        color: #000000 !important;
        font-weight: 700;
        font-size: 1rem;
        border-bottom: 2px solid #000000;
        padding-bottom: 2px;
        margin-bottom: 0.5rem;
        letter-spacing: 0.02em;
    }

    .filter-group select,
    .filter-group input {
        padding: 0.5rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        background: var(--bg);
        outline: none;
        transition: border-color 0.2s;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        border-color: var(--vert);
        box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }

    .btn-filter {
        padding: 0.5rem 1.2rem;
        background: var(--vert);
        color: white;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }

    .btn-filter:hover {
        background: var(--vert-dark);
    }

    .btn-reset {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--border);
    }

    .btn-reset:hover {
        background: var(--border);
    }

    .highlight-auth {
        font-weight: 700;
        color: var(--vert-dark);
    }

    .publi-detail {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-card);
    }

    .publi-detail h2 {
        font-family: 'Oswald', sans-serif;
        font-size: 1.8rem;
        color: var(--vert-dark);
        margin-bottom: 1rem;
    }

    .publi-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        font-size: 0.9rem;
        color: var(--muted);
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border);
    }

    .publi-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .publi-abstract {
        background: var(--bg);
        border-radius: var(--radius-sm);
        padding: 1rem 1.2rem;
        margin: 1.5rem 0;
        font-size: 0.95rem;
        line-height: 1.7;
        text-align: justify;
    }

    .publi-abstract h3 {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--vert-dark);
        margin-bottom: 0.5rem;
    }

    .btn-hal,
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 1rem;
        padding: 0.5rem 1.2rem;
        border-radius: 30px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: background 0.2s;
        margin-right: 0.5rem;
    }

    .btn-hal {
        background: var(--vert);
        color: white;
    }

    .btn-hal:hover {
        background: var(--vert-dark);
        color: white;
    }

    .btn-back {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--border);
    }

    .btn-back:hover {
        background: var(--border);
    }

    .scrollable-results {
        max-height: calc(100vh - 300px);
        overflow-y: auto;
        padding-right: 0.5rem;
        margin-top: 1rem;
    }

    .publi-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .publi-item {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-card);
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .publi-item:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }

    .card-overlay-link {
        position: absolute;
        inset: 0;
        z-index: 2;
        text-decoration: none;
        cursor: pointer;
    }

    .publi-item h3,
    .publi-item .authors,
    .publi-item .meta,
    .publi-item .abstract {
        position: relative;
        z-index: 1;
    }

    .publi-item h3 {
        font-family: 'Oswald', sans-serif;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--vert-dark);
    }

    .publi-item .authors {
        font-size: 0.9rem;
        color: var(--muted);
        margin-bottom: 0.4rem;
    }

    .publi-item .meta {
        display: flex;
        gap: 1rem;
        font-size: 0.8rem;
        color: var(--hint);
        margin-bottom: 0.8rem;
    }

    .publi-item .meta .publi-date {
        background: var(--vert-light);
        color: var(--vert-dark);
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .publi-item .abstract {
        font-size: 0.9rem;
        color: var(--text);
        text-align: justify;
        line-height: 1.5;
        background: var(--bg);
        padding: 0.8rem 1rem;
        border-radius: var(--radius-sm);
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination strong {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.85rem;
        text-decoration: none;
        border: 1px solid var(--border);
        color: var(--text);
        background: var(--surface);
        transition: all 0.2s;
        font-weight: 500;
    }

    .pagination a:hover {
        background: var(--vert-light);
        border-color: var(--vert);
        color: var(--vert-dark);
    }

    .pagination strong {
        background: var(--vert);
        color: white;
        border-color: var(--vert);
    }

    .disabled {
        opacity: 0.4;
        pointer-events: none;
    }

    @media (max-width:640px) {
        .publi-page {
            padding: 0 1rem;
            margin: 3rem auto;
        }

        .publi-page h1 {
            font-size: 1.8rem;
        }

        .publi-filters {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
</head>

<body>
    <?php include('include/header.html'); ?>
    <div class="publi-page">
        <?php if ($docid): ?>
            <?php
            $doc = $docs[0] ?? null;
            if (!$doc): ?>
                <p>Publication introuvable.</p>
            <?php else:
                $authors = $doc['authFullName_s'] ?? [];
                $highlight = $highlightAuthorName;
            ?>
                <div class="publi-detail">
                    <h2><?php echo htmlspecialchars($doc['title_s'][0] ?? 'Sans titre'); ?></h2>
                    <div class="publi-meta">
                        <span><i class="fa-solid fa-user"></i> <?php echo formatAuthors($authors, $highlight); ?></span>
                        <span><i class="fa-solid fa-calendar"></i> <?php echo htmlspecialchars($doc['producedDate_s'] ?? 'Inconnue'); ?></span>
                        <span><i class="fa-solid fa-tag"></i> <?php
                                                                $rawType = $doc['docType_s'] ?? '';
                                                                echo htmlspecialchars($typeLabels[$rawType] ?? $rawType ?: 'Publication');
                                                                ?></span>
                    </div>
                    <?php if (!empty($doc['abstract_s'])): ?>
                        <div class="publi-abstract">
                            <h3>Résumé</h3>
                            <?php echo nl2br(htmlspecialchars($doc['abstract_s'][0] ?? '')); ?>
                        </div>
                    <?php endif; ?>
                    <a href="javascript:history.back()" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Retour à la carte</a>
                    <a href="<?php echo htmlspecialchars($doc['uri_s'] ?? '#'); ?>" target="_blank" rel="noopener" class="btn-hal"><i class="fa-solid fa-external-link"></i> Voir sur HAL</a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <h1><?php echo htmlspecialchars($titrePage); ?></h1>
            <p class="results-count"><?php echo $numFound; ?> publication(s) trouvée(s)</p>

            <form method="get" class="publi-filters" id="filterForm">
                <div class="filter-group">
                    <label for="filtre_auteur">Auteur</label>
                    <select name="filtre_auteur" id="filtre_auteur">
                        <option value="">Tous les membres</option>
                        <?php foreach ($membres as $m): ?>
                            <option value="<?php echo $m['id']; ?>"
                                <?php if (($filtreAuteur && $filtreAuteur == $m['id']) || ($currentMember && $currentMember['id'] == $m['id'])) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($m['nom_complet']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtre_annee">Année</label>
                    <select name="filtre_annee" id="filtre_annee">
                        <option value="">Toutes</option>
                        <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                            <option value="<?= $y ?>" <?= $filtreAnnee == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtre_type">Type</label>
                    <select name="filtre_type" id="filtre_type">
                        <option value="">Tous</option>
                        <?php foreach ($typeLabels as $code => $lib):
                            echo '<option value="' . $code . '" ' . ($filtreType === $code ? 'selected' : '') . '>' . $lib . '</option>';
                        endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="flex:1;">
                    <label for="recherche">Recherche</label>
                    <input type="text" name="recherche" id="recherche" placeholder="Titre, résumé…" value="<?= htmlspecialchars($recherche) ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-magnifying-glass"></i> Filtrer</button>
                    <a href="publication.php" class="btn-filter btn-reset">Réinitialiser</a>
                </div>
            </form>

            <div class="scrollable-results">
                <div class="publi-list">
                    <?php foreach ($docs as $doc): ?>
                        <?php
                        $docDate = $doc['producedDate_s'] ?? '';
                        $rawType = $doc['docType_s'] ?? '';
                        $typeLabel = $typeLabels[$rawType] ?? $rawType ?: 'Publication';
                        ?>
                        <article class="publi-item">
                            <a href="publication.php?docid=<?= urlencode($doc['docid']) ?>&auteur=<?= urlencode($currentMember['id_hal'] ?? '') ?>&person_id=<?= $currentMember['id'] ?? '' ?>"
                                class="card-overlay-link"
                                aria-label="<?= htmlspecialchars($doc['title_s'][0] ?? 'Voir la publication') ?>"></a>
                            <h3><?= htmlspecialchars($doc['title_s'][0] ?? 'Sans titre') ?></h3>
                            <div class="authors"><?= formatAuthors($doc['authFullName_s'] ?? [], $highlightAuthorName) ?></div>
                            <div class="meta">
                                <span class="publi-date"><i class="fa-solid fa-calendar"></i> <?= htmlspecialchars($docDate) ?: '—' ?></span>
                                <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($typeLabel) ?></span>
                            </div>
                            <?php if (!empty($doc['abstract_s'])): ?>
                                <div class="abstract"><?= htmlspecialchars(mb_strimwidth($doc['abstract_s'][0] ?? '', 0, 300, '…')) ?></div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php
                        $queryParams = $_GET;
                        unset($queryParams['auteur']);
                        if ($page > 1) {
                            $queryParams['page'] = $page - 1;
                            echo '<a href="?' . http_build_query($queryParams) . '">&laquo; Précédent</a>';
                        } else {
                            echo '<span class="disabled">&laquo; Précédent</span>';
                        }
                        echo '<strong>' . $page . '</strong>';
                        if ($page < $totalPages) {
                            $queryParams['page'] = $page + 1;
                            echo '<a href="?' . http_build_query($queryParams) . '">Suivant &raquo;</a>';
                        } else {
                            echo '<span class="disabled">Suivant &raquo;</span>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include('include/footer.html') ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const inputs = form.querySelectorAll('select, input[type="text"]');
            let timeout;
            inputs.forEach(el => {
                el.addEventListener('input', () => {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => form.submit(), 500);
                });
                el.addEventListener('change', () => {
                    clearTimeout(timeout);
                    form.submit();
                });
            });
        });
    </script>
</body>

</html>
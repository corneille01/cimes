<?php
require_once("./api.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if (!empty($_GET['query'])) {
            if (empty($_GET['id'])) {
                getdonnees($_GET['query']);
            } else {
                getdonnees_par_id($_GET['query'], $_GET['id']);
            }
        }
    } catch (Exception $e) {
        $erreur = [
            "message" => $e->getMessage(),
            "code" => $e->getCode()
        ];
        echo json_encode($erreur); // Utilisation de json_encode
        exit;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);

    // DEBUG JSON cassé
    if (!$input) {
        echo json_encode([
            "status" => "error",
            "message" => "JSON invalide ou vide",
            "raw" => $raw
        ]);
        exit;
    }

    try {

        switch ($input['lien'] ?? '') {

            // ======================
            // ANNUAIRE PERSONNES
            // ======================
            case 'cree_annuaire':
            case 'modif_annuaire':
                ajouter_modif_annuaire($input);
                break;

            // ======================
            // MISSION
            // ======================
            case 'cree_mission':
            case 'modif_mission':
                ajouter_modif_mission($input);
                break;

            // ======================
            // PROGRAMME
            // ======================
            case 'cree_programme':
            case 'modif_programme':
                ajouter_modif_programme($input);
                break;

            case 'supp_cimes_programme':
                supprimer_programme($input);
                break;


            case 'cree_logo_partenaire':
            case 'modif_logo_partenaire':
                ajouter_modif_logo_partenaire($input);
                break;

            case 'supprimer_logo_partenaire':
                supprimer_logo_partenaire($input);
                break;

            case 'cree_contact':
            case 'modif_contact':
                ajouter_modif_contact($input);
                break;

            case 'supp_cimes_contact':
                supprimer_contact($input);
                break;

            // ======================
            // GOUVERNANCE
            // ======================
            case 'cree_gouvernance':
            case 'modif_gouvernance':
                ajouter_modif_gouvernance($input);
                break;

            case 'cree_gouvernance_schema':
            case 'modif_gouvernance_schema':
                ajouter_modif_gouvernance_schema($input);
                break;
            case 'supprimer_gouvernance_schema':
                supprimer_gouvernance_schema($input);
                break;

            case 'cree_gouvernance_entite':
            case 'modif_gouvernance_entite':
                ajouter_modif_gouvernance_entite($input);
                break;
            case 'supprimer_gouvernance_entite':
                supprimer_gouvernance_entite($input);
                break;


            case 'cree_projet':
            case 'modif_projet':
                ajouter_modif_projet($input);
                break;
            case 'supprimer_projet':
                supprimer_projet($input);
                break;

            case 'cree_partenaire':
            case 'modif_partenaire':
                ajouter_modif_partenaire($input);
                break;
            case 'supprimer_partenaire':
                supprimer_partenaire($input);
                break;

            // ======================
            // STRUCTURE
            // ======================
            case 'cree_structure':
            case 'modif_structure':
                ajouter_modif_annuaire_structure($input);
                break;

            // ======================
            // ETABLISSEMENT
            // ======================
            case 'cree_etablissement':
            case 'modif_etablissement':
                ajouter_modif_etablissement($input);
                break;

            // ======================
            // MASSIF
            // ======================
            case 'ajout_massif':
            case 'modif_massif':
                ajouter_modif_massif($input);
                break;

            // ======================
            // PUBLICATION
            // ======================
            case 'cree_publication':
            case 'modif_publication':
                ajouter_modif_publication($input);
                break;

            // ======================
            // PRESENTATION
            // ======================
            case 'modif_presentation':
                modif_presentation($input);
                break;

            // ======================
            // PROFILS
            // ======================
            case 'modif_profil_admin':
            case 'modif_profil_user':
                modifier_profil($input);
                break;

            // ======================
            // EVENEMENTS (si utilisé)
            // ======================
            case 'cree_evenement':
            case 'modif_evenement':
                ajouter_modif_evenement($input);
                break;

            // ======================
            // INDEX PAGE (si utilisé)
            // ======================
            case 'modif_index':
                ajouter_modif_index($input);
                break;

            // ======================
            // ACTUALITÉS
            // ======================
            case 'cree_actu':
            case 'modif_actu':
                ajouter_modif_actu($input);
                break;

            case 'cree_activites':
            case 'modif_activites':
                ajouter_modif_activites($input);
                break;

            // Dans index_api.php, ajouter ce case dans le switch :
            case 'cree_axes':
            case 'modif_axes':
                ajouter_modif_axes($input);
                break;
            // Dans le switch de index_api.php, ajouter :
            case 'modif_carrousel':
                modif_carrousel($input);
                break;

            default:
                echo json_encode([
                    "status" => "error",
                    "message" => "lien inconnu",
                    "lien" => $input['lien'] ?? null
                ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }
}

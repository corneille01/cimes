<?php
$file = $_FILES['image_envoyee'];
$lien = $_POST['lien'];

if ($lien == 'cree_mission' || $lien == 'modif_mission') {
    $target_dir = "../cimes_admin/img/img_mission/";
} elseif ($lien == 'cree_programme' || $lien == 'modif_programme') {
    $target_dir = "../cimes_admin/img/img_programme/";
} elseif ($lien == 'cree_gouvernance' || $lien == 'modif_gouvernance') {
    $target_dir = "../cimes_admin/img/img_gouvernance/";
} 
elseif ($lien == 'cree_etablissement' || $lien == 'modif_etablissement') {
    $target_dir = "../cimes_admin/img/img_etablissement/";
} elseif ($lien == 'cree_publication' || $lien == 'modif_publication') {
    $target_dir = "../cimes_admin/img/img_publication/";
}elseif ($lien == 'ajout_cimes' || $lien == 'modif_cimes') {
    $target_dir = "../cimes_admin/img/img_cimes/";

} elseif ($lien == 'ajout_recherche_montagne' || $lien == 'modif_recherche_montagne') {
    $target_dir = "../cimes_admin/img/img_recherche_montagne/";
} 
elseif ($lien == 'ajout_valorisation' || $lien == 'modif_valorisation') {
    $target_dir = "../cimes_admin/img/img_valorisation/";
} elseif ($lien == 'ajout_ecosysteme' || $lien == 'modif_ecosysteme') {
    $target_dir = "../cimes_admin/img/img_ecosysteme/";
} elseif ($lien == 'ajout_actu_event' || $lien == 'modif_actu_event') {
    $target_dir = "../cimes_admin/img/img_actu_event/";
}


$target_file = $target_dir . basename($file["name"]);

if ($file["size"] < 100000000) {
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        if (file_exists($target_file)) {
            echo 'fichier_existant';
        } else {
            move_uploaded_file($file["tmp_name"], $target_file);
            echo 'ok';
        }
    } else {
        echo 'fichier_pas_image';
    }
} else {
    echo 'trop_lourd';
}
?>

<?php
session_start();
if(!isset($_SESSION['ok'])){
    header("Location:index.php");
}
include("connexion.php");
$message = "";
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $matricule   = trim($_POST['matricule']);
    $nom_prenom  = trim($_POST['nom_employe']);
    $login       = trim($_POST['login']);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $poste       = trim($_POST['poste']);
    $date_embauche = $_POST['date_embauche'];
    $departement_id = $_POST['departement_id'];
    if($matricule === '' || $nom_prenom === '' || $login === '' || $mot_de_passe === '' || $poste === '' || !$departement_id || !$date_embauche){
        $message = "Tous les champs sont obligatoires";
    } else {
        $checkEmp = $connexion->prepare("SELECT * FROM employe WHERE matricule = ?");
        $checkEmp->execute([$matricule]);
        $checkUser = $connexion->prepare("SELECT * FROM utilisateur WHERE login = ?");
        $checkUser->execute([$login]);
        if($checkEmp->rowCount() > 0){
            $message = "Matricule déjà existant";
        } elseif($checkUser->rowCount() > 0){
            $message = "Login déjà utilisé";
        } else {
            $insertUser = $connexion->prepare("INSERT INTO utilisateur (matricule, login, mot_de_passe, type_utilisateur)
            VALUES (?, ?, ?, 'EMPLOYE')");
            $insertUser->execute([$matricule, $login, $mot_de_passe]);
            $insertEmp = $connexion->prepare("INSERT INTO employe (matricule, nom_employe, poste, departement_id, date_embauche)
            VALUES (?, ?, ?, ?, ?)");
            $insertEmp->execute([$matricule, $nom_prenom, $poste, $departement_id, $date_embauche]);
            $insertSolde = $connexion->prepare("INSERT INTO solde_conge (employe_matricule, jours_restants, jours_pris_annee_courante, annee_reference)
            VALUES (?, 30, 0, ?)");
            $insertSolde->execute([$matricule, date('Y')]);
            header("Location:espace_admin1.php");
            echo("employee ajouter avec succees");
        }
    }
}
?>

<?php
include("connexion.php");
$matricule = $_POST['matricule'];
$date_debut = $_POST['date_debut'];
$date_fin = $_POST['date_fin'];
$jours = $_POST['nombre_jours'];
$req = $connexion->prepare("SELECT jours_restants FROM solde_conge
    WHERE employe_matricule = ? AND annee_reference = YEAR(CURDATE())");
$req->execute([$matricule]);
$solde = $req->fetch(PDO::FETCH_ASSOC);
if ($solde && $jours > $solde['jours_restants']) {
    die("Solde insuffisant");
}
$reqInsert = $connexion->prepare("INSERT INTO demande_conge (employe_matricule, date_debut, date_fin, nombre_jours)
    VALUES (?,?,?,?) ");
$reqInsert->execute([$matricule,$date_debut,$date_fin,$jours]);
header("Location:espace_employe.php");

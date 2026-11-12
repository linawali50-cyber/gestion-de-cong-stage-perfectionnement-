<?php
session_start();
include("connexion.php");
if (isset($_POST['login']) && isset($_POST['mp'])) {
    $login=$_POST['login'];
    $pwd=$_POST['mp'];
    $req=$connexion->prepare( "SELECT login,type_utilisateur FROM utilisateur WHERE login = ? AND mot_de_passe = ?");
    $req->execute([$login, $pwd]);
    if ($req->rowCount() == 1) {
        $enreg = $req->fetch(PDO::FETCH_ASSOC);
        $_SESSION['ok'] = "ok";
        $_SESSION['user'] = $enreg['login'];
        $_SESSION['type'] = $enreg['type_utilisateur'];
        if ($enreg['type_utilisateur'] == 'RH') {
            header("Location:espace_admin1.php");
        } else {
            header("Location:espace_employe.php");
        }
    } else {
        echo "Échec d'authentification !";
        header("Location:index.php");
    }
}
?>



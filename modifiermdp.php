<?php
session_start();
if (!isset($_SESSION['ok'])) {
    header("Location:index.php");
}
include("connexion.php");
$matricule = $_GET['matricule'];
if (!$matricule) {
    die("Matricule manquant");
}
$req = $connexion->prepare("SELECT login FROM utilisateur WHERE matricule = ?");
$req->execute([$matricule]);
$user = $req->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("Utilisateur introuvable");
}
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login=trim($_POST['login']);
    $mdp=trim($_POST['mot_de_passe']);
    if ($login=='') {
        $message="Login obligatoire";
    }
    else {
        if ($mdp != '') {
            $update=$connexion->prepare("UPDATE utilisateur SET login = ?, mot_de_passe = ? WHERE matricule = ?");
            $update->execute([$login, $mdp, $matricule]);
        } 
        else {
            $update = $connexion->prepare("UPDATE utilisateur SET login = ? WHERE matricule = ?");
            $update->execute([$login, $matricule]);
        }
        header("Location:espace_admin1.php");
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier login / mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Modifier login ou mot de passe
            </h5>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Login</label>
                    <input type="text" name="login" class="form-control"
                           value="<?= $user['login']?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="mot_de_passe" class="form-control"
                           placeholder="Laisser vide pour ne pas changer">
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                    </button>
                    <a href="espace_admin1.php" class="btn btn-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>


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
$req = $connexion->prepare("SELECT e.poste, e.departement_id, d.nom FROM employe e ,departement d where d.id_departement = e.departement_id
    and e.matricule = ? ");
$req->execute([$matricule]);
$emp = $req->fetch(PDO::FETCH_ASSOC);
if (!$emp) {
    die("Employé introuvable");
}
$deps = $connexion->query("SELECT * FROM departement")->fetchAll(PDO::FETCH_ASSOC);
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poste = trim($_POST['poste']);
    $departement_id = $_POST['departement_id'];
    if ($poste === '' || !$departement_id) {
        $message = "Poste et département obligatoires";
    } else {
        $update = $connexion->prepare(
            "UPDATE employe 
             SET poste = ?, departement_id = ?
             WHERE matricule = ?"
        );
        $update->execute([$poste, $departement_id, $matricule]);
        header("Location:espace_admin1.php");
    }
}?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier poste et département</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="bi bi-briefcase-fill me-2"></i>
                Modifier poste et département
            </h5>
        </div>
        <div class="card-body">
            <?php if ($message): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Poste</label>
                    <input type="text" name="poste" class="form-control"
                           value="<?= $emp['poste'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Département</label>
                    <select name="departement_id" class="form-control" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($deps as $d): ?>
                            <option value="<?= $d['id_departement'] ?>"
                                <?= $d['id_departement'] == $emp['departement_id'] ? 'selected' : '' ?>>
                                <?= $d['nom'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

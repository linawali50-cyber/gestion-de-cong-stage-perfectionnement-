
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 380px; border-radius: 15px;">
        <h3 class="text-center mb-4">Ajouter utilisateur</h3>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Login :</label>
                <input type="text" class="form-control" name="login" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe :</label>
                <input type="password" class="form-control" name="mp" required>
            </div>
            <div class="mb-3">
                <label class="form-label">grad:</label>
                 <select name="tputl" class="form-select">
                   <option value="RH">RH</option>
                   <option value="EMPLOYE">EMPLOYE</option>
                </select>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success">ajouter</button>
            </div>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none">⬅ Retour à la connexion</a>
        </div>
        <div class="mt-3">
            <?php
             //<a href="ajouterUtilisateur.php" class="btn btn-outline-secondary">Ajouter Utilisateur</a>
            include("connexion.php");
            if(isset($_POST["mp"])&& isset($_POST["login"])&& isset($_POST["tputl"])) {
                $login=$_POST["login"];
                $pwd=$_POST["mp"];
                $tp=$_POST["tputl"];
                $stm=$connexion->prepare("INSERT INTO utilisateur (login,mot_de_passe,type_utilisateur) VALUES (?,?,?)");
                $stm->execute([$login,$pwd,$tp]);
                echo '<div class="alert alert-success mt-2">ajouté avec succès ✔</div>';
            }
            ?>
        </div>

    </div>
</div>

</body>
</html>

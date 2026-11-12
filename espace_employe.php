<?php
session_start();
if (!isset($_SESSION['ok'])) {
    header("Location:index.php");
}
include("connexion.php");
$login = $_SESSION['user'];
$req_emp = $connexion->prepare("SELECT e.*, u.login, d.nom AS departement_nom
    FROM employe e, utilisateur u,departement d WHERE u.matricule = e.matricule
    and d.id_departement = e.departement_id and u.login = ? ");
$req_emp->execute([$login]);
$emp = $req_emp->fetch(PDO::FETCH_ASSOC);
if (!$emp) {
    die("Employé non trouvé");
}
$req_conge = $connexion->prepare("SELECT * FROM demande_conge
    WHERE employe_matricule = ? ORDER BY date_soumission DESC");
$req_conge->execute([$emp['matricule']]);
$demandes = $req_conge->fetchAll(PDO::FETCH_ASSOC);
$reqSolde = $connexion->prepare(" SELECT jours_restants, jours_pris_annee_courante, annee_reference
    FROM solde_conge WHERE employe_matricule = ? AND annee_reference = YEAR(CURDATE())");
$reqSolde->execute([$emp['matricule']]);
$solde = $reqSolde->fetch(PDO::FETCH_ASSOC);
if (!$solde) {
    $solde = [
        'jours_restants' => 30,
        'jours_pris_annee_courante' => 0,
        'annee_reference' => date('Y')];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Espace Employé | Télévision Tunisienne</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --primary-red: #b30000;
    --primary-dark: #8f0000;
    --secondary-blue: #2c3e50;
    --light-gray: #f8f9fa;
    --medium-gray: #e9ecef;
    --dark-gray: #6c757d;
    --success: #28a745;
    --warning: #ffc107;
    --danger: #dc3545;
    --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    --border-radius: 12px;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    background: linear-gradient(135deg, #f4f6f9 0%, #eef1f5 100%);
    font-family: 'Open Sans', sans-serif;
    color: #333;
    min-height: 100vh;
    padding-bottom: 2rem;
}
.navbar-custom {
    background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-dark) 100%) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 0.8rem 0;
}
.navbar-brand {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.navbar-brand i {
    font-size: 1.8rem;
}
.btn-logout {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-logout:hover {
    background-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}
.card-custom {
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: none;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: white;
}
.card-custom:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
}
.card-header-custom {
    background: linear-gradient(90deg, var(--secondary-blue) 0%, #34495e 100%);
    color: white;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}
.card-header-custom i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}
.section-title {
    color: var(--primary-red);
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 3px solid var(--primary-red);
    display: inline-block;
}
.info-card {
    border-left: 5px solid var(--primary-red);
    padding-left: 1rem;
}
.info-list {
    list-style: none;
    padding-left: 0;
}
.info-list li {
    padding: 1rem 0;
    border-bottom: 1px solid var(--medium-gray);
    display: flex;
    justify-content: space-between;
}
.info-list li:last-child {
    border-bottom: none;
}
.info-label {
    font-weight: 600;
    color: var(--secondary-blue);
}
.info-value {
    font-weight: 500;
    color: #333;
}
.badge-status {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
}
.table-custom {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
.table-custom thead {
    background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-dark) 100%);
    color: white;
}
.table-custom th {
    font-weight: 600;
    padding: 1rem;
    border: none;
}
.table-custom tbody tr {
    transition: background-color 0.2s ease;
}
.table-custom tbody tr:hover {
    background-color: rgba(179, 0, 0, 0.05);
}
.table-custom td {
    padding: 1rem;
    border-bottom: 1px solid var(--medium-gray);
    vertical-align: middle;
}
.btn-primary-custom {
    background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-dark) 100%);
    border: none;
    border-radius: 50px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(179, 0, 0, 0.2);
}
.btn-primary-custom:hover {
    background: linear-gradient(90deg, var(--primary-dark) 0%, #720000 100%);
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(179, 0, 0, 0.3);
}
.form-control-custom {
    border-radius: 10px;
    border: 1px solid #ced4da;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}
.form-control-custom:focus {
    border-color: var(--primary-red);
    box-shadow: 0 0 0 0.25rem rgba(179, 0, 0, 0.15);
}
.solde-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-dark) 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    margin: 0 auto 1.5rem;
    box-shadow: 0 8px 20px rgba(179, 0, 0, 0.2);
}
.solde-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
}
.solde-label {
    font-size: 0.9rem;
    opacity: 0.9;
}
.stats-container {
    display: flex;
    justify-content: space-between;
    text-align: center;
    margin-top: 1.5rem;
}
.stat-item {
    flex: 1;
    padding: 0 1rem;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-red);
}
.stat-label {
    font-size: 0.9rem;
    color: var(--dark-gray);
    margin-top: 0.25rem;
}
.no-data {
    text-align: center;
    padding: 3rem;
    color: var(--dark-gray);
}
.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
.date-input-group {
    position: relative;
}
.date-input-group i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-red);
    pointer-events: none;
}
@media (max-width: 768px) {
    .navbar-brand {
        font-size: 1.2rem;
    }
    .section-title {
        font-size: 1.3rem;
    }
    .solde-circle {
        width: 100px;
        height: 100px;
    }
    .solde-number {
        font-size: 2rem;
    }
}
</style>
<script>
function calculerJours() {
    let d1Input = document.getElementById('date_debut').value;
    let d2Input = document.getElementById('date_fin').value;
    
    if(!d1Input || !d2Input) return;
    
    let d1 = new Date(d1Input);
    let d2 = new Date(d2Input);
    
    if(d2 < d1) {
        showAlert("La date de fin doit être après la date de début", "danger");
        document.getElementById('nombre_jours').value = "";
        return false;
    }
    let diff = Math.floor((d2 - d1)/(1000*60*60*24)) + 1;
    document.getElementById('nombre_jours').value = diff;
    highlightField('nombre_jours');
    return true;
}
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 350px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`; 
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
function highlightField(fieldId) {
    const field = document.getElementById(fieldId);
    field.classList.add('highlighted');
    
    setTimeout(() => {
        field.classList.remove('highlighted');
    }, 1000);
}
function validateForm() {
    const jours = document.getElementById('nombre_jours').value;
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin').value;
    if (!dateDebut || !dateFin) {
        showAlert("Veuillez sélectionner les dates de début et de fin", "warning");
        return false;
    } 
    if (!jours || jours <= 0) {
        showAlert("Le nombre de jours doit être supérieur à zéro", "warning");
        return false;
    }
    return calculerJours();
}
</script>
</head>
<body>
<nav class="navbar navbar-dark navbar-expand-lg navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="#">
      <i class="bi bi-tv"></i>
      <span>Télévision Tunisienne – Espace Employé</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a href="logout.php" class="btn btn-logout text-white">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 fw-bold text-dark">Bienvenue</h1>
        <p class="text-muted mb-0">Consultez vos informations et gérez vos demandes de congé</p>
      </div>
      <div class="text-end">
        <div class="badge bg-light text-dark p-2">
          <i class="bi bi-calendar-check me-1"></i>
          <?= date('d/m/Y') ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-custom">
      <div class="card-header-custom">
        <i class="bi bi-person-circle"></i>Mes informations
      </div>
      <div class="card-body">
        <ul class="info-list">
          <li>
            <span class="info-label">Matricule :</span>
            <span class="info-value"><?= $emp['matricule'] ?></span>
          </li>
          <li>
            <span class="info-label">Nom _Prenom :</span>
            <span class="info-value"><?= $emp['nom_employe'] ?></span>
          </li>
          <li>
            <span class="info-label">Poste :</span>
            <span class="info-value"><?=$emp['poste'] ?></span>
          </li>
          <li>
            <span class="info-label">Date d'embauche :</span>
            <span class="info-value"><?= $emp['date_embauche']?></span>
          </li>
          <li>
            <span class="info-label">Département :</span>
            <span class="info-value"><?= $emp['departement_nom'] ?></span>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card card-custom">
      <div class="card-header-custom">
        <i class="bi bi-calendar-week"></i>Solde de congés
      </div>
      <div class="card-body text-center">
        <div class="solde-circle">
          <div class="solde-number"><?= $solde['jours_restants'] ?></div>
          <div class="solde-label">Jours restants</div>
        </div>       
        <div class="stats-container">
          <div class="stat-item">
            <div class="stat-value"><?= $solde['jours_pris_annee_courante'] ?></div>
            <div class="stat-label">Jours pris</div>
          </div>
          <div class="stat-item">
            <div class="stat-value"><?= $solde['annee_reference'] ?></div>
            <div class="stat-label">Année</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card card-custom mt-4">
  <div class="card-header-custom d-flex justify-content-between align-items-center">
    <div>
      <i class="bi bi-list-task"></i>Mes demandes de congé
    </div>
    <span class="badge bg-light text-dark">
      <?= count($demandes) ?> demande(s)
    </span>
  </div>
  <div class="card-body p-0">
    <?php if (count($demandes) == 0): ?>
      <div class="no-data">
        <i class="bi bi-inbox"></i>
        <h5 class="mt-3">Aucune demande de congé</h5>
        <p class="text-muted">Vous n'avez pas encore soumis de demande de congé.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-custom mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Date début</th>
              <th>Date fin</th>
              <th>Nb jours</th>
              <th>Date soumission</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($demandes as $d): ?>
              <tr>
                <td><strong>#<?=$d['id_demandeConge'] ?></strong></td>
                <td><?= $d['date_debut']?></td>
                <td><?=$d['date_fin']?></td>
                <td><span class="badge bg-secondary"><?= $d['nombre_jours'] ?> jours</span></td>
                <td><?= $d['date_soumission']?></td>
                <td>
                  <?php 
                    $badgeClass = '';
                    if ($d['statut'] == 'ACCEPTEE') $badgeClass = 'bg-success';
                    elseif ($d['statut'] == 'REFUSEE') $badgeClass = 'bg-danger';
                    else $badgeClass = 'bg-warning text-dark';
                  ?>
                  <span class="badge badge-status <?= $badgeClass ?>">
                    <?php if ($d['statut'] == 'ACCEPTEE'): ?>
                      <i class="bi bi-check-circle me-1"></i>
                    <?php elseif ($d['statut'] == 'REFUSEE'): ?>
                      <i class="bi bi-x-circle me-1"></i>
                    <?php else: ?>
                      <i class="bi bi-clock me-1"></i>
                    <?php endif; ?>
                    <?= htmlspecialchars($d['statut']) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<div class="card card-custom mt-4">
  <div class="card-header-custom">
    <i class="bi bi-plus-circle"></i>Nouvelle demande de congé
  </div>
  <div class="card-body">
    <form method="post" action="ajouter_conge.php" onsubmit="return validateForm()">
      <input type="hidden" name="matricule" value="<?= $emp['matricule'] ?>">
      
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de début</label>
          <div class="date-input-group">
            <input type="date" name="date_debut" id="date_debut" class="form-control form-control-custom" onchange="calculerJours()" required>
            <i class="bi bi-calendar-event"></i>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Date de fin</label>
          <div class="date-input-group">
            <input type="date" name="date_fin" id="date_fin" class="form-control form-control-custom" onchange="calculerJours()" required>
            <i class="bi bi-calendar-event"></i>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nombre de jours</label>
          <input type="number" name="nombre_jours" id="nombre_jours" class="form-control form-control-custom" readonly required>
          <div class="form-text text-end">
            <span id="jours-texte">Sélectionnez les dates</span>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="form-text">
          <i class="bi bi-info-circle me-1"></i>
          Votre demande sera soumise à validation
        </div>
        <button type="submit" class="btn btn-primary-custom">
          <i class="bi bi-send me-2"></i>Envoyer la demande
        </button>
      </div>
    </form>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const joursField = document.getElementById('nombre_jours');
const joursTexte = document.getElementById('jours-texte');
joursField.addEventListener('input', function() {
  if (this.value && this.value > 0) {
    joursTexte.textContent = this.value + ' jour(s) de congé';
    joursTexte.classList.add('text-success');
    joursTexte.classList.remove('text-muted');
  } else {
    joursTexte.textContent = 'Sélectionnez les dates';
    joursTexte.classList.remove('text-success');
    joursTexte.classList.add('text-muted');
  }
});
const style = document.createElement('style');
style.textContent = `
  .highlighted {
    animation: highlight 1s ease;
  }
  @keyframes highlight {
    0% { background-color: rgba(179, 0, 0, 0.2); }
    100% { background-color: transparent; }
  }`;
document.head.appendChild(style);
</script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['ok'])) {
    header("Location:index.php");
}
include("connexion.php");
if (!isset($_GET['matricule'])) {
    die("Matricule manquant");
}
$matricule = $_GET['matricule'];
$req_emp = $connexion->prepare("SELECT e.matricule, e.poste, e.date_embauche,d.nom ,s.jours_restants 
    FROM employe e,departement d, solde_conge s where d.id_departement = e.departement_id and s.employe_matricule=e.matricule and e.matricule = ? ");
$req_emp->execute([$matricule]);
$emp = $req_emp->fetch(PDO::FETCH_ASSOC);
if (!$emp) {
    die("Employé non trouvé");
}
$req_conges = $connexion->prepare("SELECT * FROM demande_conge WHERE employe_matricule= ? ORDER BY date_soumission DESC");
$req_conges->execute([$matricule]);
$conges = $req_conges->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historique des Congés | Télévision Tunisienne</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
    --info: #17a2b8;
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
    font-family: 'Inter', sans-serif;
    color: #333;
    min-height: 100vh;
    padding-bottom: 2rem;
}
.navbar-custom {
    background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-dark) 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 1rem 0;
}
.navbar-brand {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.navbar-brand i {
    font-size: 1.8rem;
}
.btn-back {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-back:hover {
    background-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}
.main-container {
    padding: 2rem 0;
}
.page-header {
    margin-bottom: 2rem;
}
.page-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    color: var(--secondary-blue);
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.page-subtitle {
    color: var(--dark-gray);
    font-size: 1.1rem;
}
.employee-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: none;
    overflow: hidden;
    margin-bottom: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.employee-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
}
.card-header-custom {
    background: linear-gradient(90deg, var(--secondary-blue) 0%, #34495e 100%);
    color: white;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}
.card-header-custom i {
    margin-right: 0.5rem;
    font-size: 1.2rem;
}
.employee-info {
    padding: 1.5rem;
}
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}
.info-item {
    background: var(--light-gray);
    border-radius: 10px;
    padding: 1.25rem;
    transition: all 0.3s ease;
    border-left: 4px solid var(--primary-red);
}
.info-item:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transform: translateY(-3px);
}
.info-label {
    font-size: 0.9rem;
    color: var(--dark-gray);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}
.info-value {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--secondary-blue);
}
.history-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: none;
    overflow: hidden;
}
.table-container {
    overflow-x: auto;
    border-radius: var(--border-radius);
}
.table-custom {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.table-custom thead {
    background: linear-gradient(90deg, var(--primary-red) 0%, var(--primary-dark) 100%);
    color: white;
}
.table-custom th {
    font-weight: 600;
    padding: 1.25rem 1rem;
    border: none;
    font-family: 'Poppins', sans-serif;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.table-custom tbody tr {
    transition: background-color 0.2s ease;
    border-bottom: 1px solid var(--medium-gray);
}
.table-custom tbody tr:last-child {
    border-bottom: none;
}
.table-custom tbody tr:hover {
    background-color: rgba(179, 0, 0, 0.05);
}
.table-custom td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    color: #555;
}
.badge-status {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.badge-accepted {
    background-color: rgba(40, 167, 69, 0.15);
    color: var(--success);
    border: 1px solid rgba(40, 167, 69, 0.3);
}
.badge-pending {
    background-color: rgba(255, 193, 7, 0.15);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.badge-rejected {
    background-color: rgba(220, 53, 69, 0.15);
    color: var(--danger);
    border: 1px solid rgba(220, 53, 69, 0.3);
}
.days-badge {
    background-color: var(--info);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
}
.date-cell {
    display: flex;
    flex-direction: column;
}
.date-label {
    font-size: 0.85rem;
    color: var(--dark-gray);
    margin-bottom: 0.25rem;
}
.date-value {
    font-weight: 600;
    color: var(--secondary-blue);
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
.stats-summary {
    display: flex;
    justify-content: space-around;
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.stat-item {
    text-align: center;
    padding: 0 1rem;
    flex: 1;
    min-width: 150px;
}
.stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-red);
    margin-bottom: 0.5rem;
}
.stat-label {
    font-size: 0.9rem;
    color: var(--dark-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
@media (max-width: 768px) {
    .navbar-brand {
        font-size: 1.2rem;
    }
    .page-title {
        font-size: 1.5rem;
    }
    .info-grid {
        grid-template-columns: 1fr;
    }
    .stats-summary {
        flex-direction: column;
        align-items: center;
    }    
    .stat-item {
        min-width: auto;
        width: 100%;
        max-width: 250px;
    } 
    .table-custom th,
    .table-custom td {
        padding: 1rem 0.75rem;
    }
}
</style>
</head>
<body>
<nav class="navbar navbar-dark navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="#">
      <i class="bi bi-calendar-week"></i>
      <span>Télévision Tunisienne – Historique des Congés</span>
    </a>
    <a href="espace_admin1.php" class="btn btn-back text-white">
      <i class="bi bi-arrow-left me-1"></i>Retour à l'Espace RH
    </a>
  </div>
</nav>
<div class="container main-container">
  <div class="page-header">
    <h1 class="page-title">Historique des Congés</h1>
    <p class="page-subtitle">Consultation des demandes de congé de l'employé</p>
  </div>
  <?php
  $total_conges = count($conges);
  $conges_acceptes = 0;
  $conges_refuses = 0;
  $conges_en_attente = 0;
  $jours_total = 0; 
  foreach($conges as $c) {
      if($c['statut'] == 'ACCEPTEE') $conges_acceptes++;
      elseif($c['statut'] == 'REFUSEE') $conges_refuses++;
      else $conges_en_attente++;
      
      if($c['statut'] == 'ACCEPTEE') {
          $jours_total += $c['nombre_jours'];
      }
  }
  ?>
  <div class="stats-summary">
    <div class="stat-item">
      <div class="stat-value"><?= $total_conges ?></div>
      <div class="stat-label">Total demandes</div>
    </div>
    <div class="stat-item">
      <div class="stat-value"><?= $conges_acceptes ?></div>
      <div class="stat-label">Demandes acceptées</div>
    </div>
    <div class="stat-item">
      <div class="stat-value"><?= $jours_total ?></div>
      <div class="stat-label">Jours de congé</div>
    </div>
    <div class="stat-item">
      <div class="stat-value"><?= $conges_en_attente ?></div>
      <div class="stat-label">En attente</div>
    </div>
  </div>
  <div class="employee-card">
    <div class="card-header-custom">
      <i class="bi bi-person-badge"></i>Informations de l'employé
    </div>
    <div class="employee-info">
      <div class="info-grid">
        <div class="info-item">
          <div class="info-label">Matricule</div>
          <div class="info-value"><?= $emp['matricule'] ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Poste</div>
          <div class="info-value"><?= $emp['poste']?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Département</div>
          <div class="info-value"><?= $emp['nom'] ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">Date d'embauche</div>
          <div class="info-value"><?= $emp['date_embauche'] ?></div>
        </div>
        <div class="info-item">
          <div class="info-label">solde restants</div>
          <div class="info-value"><?= $emp['jours_restants'] ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="history-card">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
      <div>
        <i class="bi bi-clock-history"></i>Historique des congés
      </div>
      <span class="badge bg-light text-dark">
        <?= $total_conges ?> demande(s)
      </span>
    </div>
    
    <div class="table-container">
      <?php if(count($conges) == 0): ?>
        <div class="no-data">
          <i class="bi bi-inbox"></i>
          <h5 class="mt-3">Aucun congé trouvé</h5>
          <p class="text-muted">Cet employé n'a pas encore soumis de demande de congé.</p>
        </div>
      <?php else: ?>
        <table class="table table-custom">
          <thead>
            <tr>
              <th>ID</th>
              <th>Période</th>
              <th>Durée</th>
              <th>Soumission</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($conges as $c): ?>
            <tr>
              <td>
                <strong>#<?= htmlspecialchars($c['id_demandeConge']) ?></strong>
              </td>
              <td>
                <div class="date-cell">
                  <span class="date-label">Début</span>
                  <span class="date-value"><?= htmlspecialchars($c['date_debut']) ?></span>
                </div>
                <div class="date-cell mt-2">
                  <span class="date-label">Fin</span>
                  <span class="date-value"><?= htmlspecialchars($c['date_fin']) ?></span>
                </div>
              </td>
              <td>
                <span class="days-badge"><?= htmlspecialchars($c['nombre_jours']) ?> jours</span>
              </td>
              <td>
                <div class="date-cell">
                  <span class="date-label">Soumis le</span>
                  <span class="date-value"><?= htmlspecialchars($c['date_soumission']) ?></span>
                </div>
              </td>
              <td>
                <?php
                $badge_class = '';
                $badge_icon = '';
                if($c['statut'] == 'ACCEPTEE') {
                    $badge_class = 'badge-accepted';
                    $badge_icon = 'bi-check-circle';
                } elseif($c['statut'] == 'REFUSEE') {
                    $badge_class = 'badge-rejected';
                    $badge_icon = 'bi-x-circle';
                } else {
                    $badge_class = 'badge-pending';
                    $badge_icon = 'bi-clock';
                }
                ?>
                <span class="badge-status <?= $badge_class ?>">
                  <i class="bi <?= $badge_icon ?>"></i>
                  <?= htmlspecialchars($c['statut']) ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.employee-card, .history-card, .stats-summary');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
});
</script>
</body>
</html>
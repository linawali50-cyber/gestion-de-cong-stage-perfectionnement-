<?php
session_start();
if (!isset($_SESSION['ok'])) {
    header("Location:index.php");
}
include("connexion.php");
$login = $_SESSION['user'];
$req_rh = $connexion->prepare("SELECT u.matricule, u.login, r.nom_prenom
    FROM utilisateur u ,rh r where r.matricule = u.matricule and  u.login = ? ");
$req_rh->execute([$login]);
$rh = $req_rh->fetch(PDO::FETCH_ASSOC);
if (!$rh) die("RH non trouvé");
$employes = [];
$searchTerm = isset($_GET['recherche']) ? trim($_GET['recherche']) : '';
if ($searchTerm !== '') {
    $term = "%$searchTerm%";
    $req_emp = $connexion->prepare("SELECT * FROM employe  WHERE nom_employe LIKE ? OR poste LIKE ?");
    $req_emp->execute([$term, $term]);
    $employes = $req_emp->fetchAll(PDO::FETCH_ASSOC);
}
$req_demandes = $connexion->prepare("SELECT dc.id_demandeConge,dc.date_debut,dc.date_fin,dc.nombre_jours,dc.date_soumission,dc.statut, e.poste, u.login, s.jours_restants
    FROM demande_conge dc , employe e,solde_conge s,utilisateur u  
    where e.matricule = dc.employe_matricule and u.matricule = dc.employe_matricule 
    and s.employe_matricule = dc.employe_matricule and dc.statut='EN_ATTENTE' order by dc.date_soumission desc; ");
$req_demandes->execute();
$demandes = $req_demandes->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id_demande = $_GET['id'];
    $req = $connexion->prepare("SELECT dc.id_demandeConge, dc.nombre_jours, dc.statut, dc.employe_matricule, s.jours_restants 
    FROM demande_conge dc ,solde_conge s 
    where s.employe_matricule = dc.employe_matricule and dc.id_demandeConge = ?");
    $req->execute([$id_demande]);
    $demande = $req->fetch(PDO::FETCH_ASSOC);
    if (!$demande || $demande['statut'] !== 'EN ATTENTE') {
        header("Location:espace_admin1.php");
    }
    if ($action === 'accepter') {
        if ($demande['jours_restants'] < $demande['nombre_jours']) {
            echo "<script>alert('Solde insuffisant');</script>";
        } else {
            $connexion->prepare(" UPDATE demande_conge SET statut = 'ACCEPTEE' WHERE id_demandeConge = ?")->execute([$id_demande]);
            $connexion->prepare(" UPDATE solde_conge SET  jours_restants = jours_restants - ?, jours_pris_annee_courante = jours_pris_annee_courante + ?
             WHERE employe_matricule = ? ")->execute([ $demande['nombre_jours'], $demande['nombre_jours'], $demande['employe_matricule']]);
             header("location:espace_admin1.php");
        }
    }
    elseif ($action === 'refuser') {
        $connexion->prepare("UPDATE demande_conge SET statut = 'REFUSEE' WHERE id_demandeConge = ?")->execute([$id_demande]);
    }
    header("Location:espace_admin1.php");}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace RH | Télévision Tunisienne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> 
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #1abc9c;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #95a5a6;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 10px;
            --transition: all 0.3s ease;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        } 
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
            box-shadow: var(--shadow);
            padding: 1rem 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--secondary);
        }
        .user-info {
            color: white;
            font-weight: 500;
            margin-right: 1rem;
        }
        .btn-logout {
            background-color: var(--danger);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-logout:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .main-container {
            padding: 2rem 0;
        }
        .page-header {
            background: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border-left: 5px solid var(--secondary);
        }
        .page-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 0.5rem;
        } 
        .page-subtitle {
            color: var(--gray);
            font-size: 0.95rem;
        }
        .custom-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: var(--transition);
        }
        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: linear-gradient(90deg, var(--secondary) 0%, #2980b9 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: none;
            font-weight: 600;
        }
        .card-header i {
            margin-right: 10px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .search-box {
            background: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        .search-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .form-control-custom {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            transition: var(--transition);
        }
        .form-control-custom:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        .btn-search {
            background: linear-gradient(90deg, var(--secondary) 0%, #2980b9 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }
        .table-custom {
            margin-bottom: 0;
        }
        .table-custom thead {
            background-color: #f8f9fa;
        }
        .table-custom th {
            font-weight: 600;
            color: var(--primary);
            border-top: none;
            padding: 1rem;
        }
        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #eee;
        }
        .table-custom tbody tr {
            transition: var(--transition);
        }
        .table-custom tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        .btn-action {
            padding: 0.4rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        } 
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-modify {
            background-color: var(--warning);
            border: none;
            color: white;
        }
        .btn-modify:hover {
            background-color: #e67e22;
            color: white;
        }
        .btn-view {
            background-color: var(--info);
            border: none;
            color: white;
        }
        .btn-view:hover {
            background-color: #16a085;
            color: white;
        } 
        .btn-accept {
            background-color: var(--success);
            border: none;
            color: white;
        }
        .btn-accept:hover {
            background-color: #219653;
            color: white;
        }
        .btn-reject {
            background-color: var(--danger);
            border: none;
            color: white;
        }
        .btn-reject:hover {
            background-color: #c0392b;
            color: white;
        }
        .btn-add {
            background: linear-gradient(90deg, var(--success) 0%, #219653 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(39, 174, 96, 0.3);
        }
        .badge-custom {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .badge-waiting {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-refused {
            background-color: #f8d7da;
            color: #721c24;
        }
        .form-add-employee {
            background-color: #f8f9fa;
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-top: 1rem;
            border-left: 4px solid var(--success);
        }  
        .form-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
        }
        .no-results i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
            .table-responsive {
                margin-bottom: 1rem;
            }
            .btn-action {
                margin-bottom: 0.5rem;
                display: block;
                width: 100%;
            }}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-briefcase-fill"></i>
                <span>Espace RH</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="ms-auto d-flex align-items-center">
                    <span class="user-info d-none d-md-block">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= $rh['nom_prenom'] ?>
                    </span>
                    <a href="logout.php" class="btn btn-logout" onclick="return confirm('Voulez-vous vous déconnecter ?')">
                        <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container main-container">
        <div class="page-header">
            <h1 class="page-title">Gestion des Employés et Congés</h1>
            <p class="page-subtitle">Télévision Tunisienne - Interface d'administration des ressources humaines</p>
        </div>
        <div class="search-box">
            <h5 class="search-title"><i class="bi bi-search me-2"></i> Rechercher un employé</h5>
            <form method="get" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-person-lines-fill text-primary"></i>
                        </span>
                        <input type="text" name="recherche" class="form-control form-control-custom" 
                               placeholder="Entrez un nom employe ou un poste..." 
                               value="<?= $searchTerm ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-search w-100">
                        <i class="bi bi-search me-2"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>
        <?php if(count($employes) > 0): ?>
        <div class="custom-card">
            <div class="card-header">
                <i class="bi bi-people-fill"></i> Résultats de la recherche
                <span class="badge bg-light text-dark ms-2"><?= count($employes) ?> trouvé(s)</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
        <table class="table table-custom">
                <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom & Prénom</th>
                    <th>Poste</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employes)): ?>
                    <?php foreach($employes as $e): ?>
                        <tr>
                            <td><strong><?= $e['matricule'] ?></strong></td>
                            <td><?= $e['nom_employe'] ?></td>
                            <td><?= $e['poste'] ?></td>
                            <td>
                               <div class="d-flex gap-2 flex-wrap">
                                <a href="modifiermdp.php?matricule=<?= $e['matricule'] ?>"
                                class="btn btn-modify btn-action">
                                    <i class="bi bi-pencil-square me-1"></i>Modifier login ou mot de passe
                                </a>
                                <a href="modifierposte.php?matricule=<?= $e['matricule'] ?>"
                                class="btn btn-modify btn-action">
                                    <i class="bi bi-pencil-square me-1"></i>Modifier poste
                                </a>
                                <a href="conges_employe.php?matricule=<?= $e['matricule'] ?>" 
                                class="btn btn-view btn-action">
                                    <i class="bi bi-calendar-week me-1"></i>
                                    Congés
                                </a>
                            </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Aucun employé trouvé
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
                </div>
            </div>
        </div>
        <?php elseif($searchTerm): ?>
        <div class="custom-card">
            <div class="card-body">
                <div class="no-results">
                    <i class="bi bi-person-x"></i>
                    <h5 class="mb-2">Aucun employé trouvé</h5>
                    <p class="mb-0">Aucun résultat pour "<?= $searchTerm ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-person-plus-fill"></i> Ajouter un nouvel employé
                </div>
                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#formAjoutEmploye">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            
            <div class="collapse" id="formAjoutEmploye">
                <div class="card-body">
                    <form method="post" action="ajouter_employe.php" class="form-add-employee">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Matricule</label>
                                <input type="text" name="matricule" class="form-control form-control-custom" 
                                       placeholder="Ex: EMP00001" required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Nom employe</label>
                                <input type="text" name="nom_employe" class="form-control form-control-custom" 
                                    placeholder="Nom d'utilisateur" required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Login</label>
                                <input type="text" name="login" class="form-control form-control-custom" 
                                       placeholder="login " required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" name="mot_de_passe" class="form-control form-control-custom" 
                                       placeholder="******" required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Poste</label>
                                <input type="text" name="poste" class="form-control form-control-custom" 
                                       placeholder="Ex: Journaliste" required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Date embauche</label>
                                <input type="date" name="date_embauche" class="form-control form-control-custom" required>
                            </div>
                            <div class="col-md-6 col-lg-2">
                                <label class="form-label">Département</label>
                                <select name="departement_id" class="form-control form-control-custom" required>
                                    <option value="">Sélectionner...</option>
                                    <?php
                                    $deps = $connexion->query("SELECT * FROM departement")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($deps as $dep){
                                        echo "<option value='".$dep['id_departement']."'>".$dep['nom']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <button class="btn btn-add">
                                    <i class="bi bi-person-plus me-2"></i> Ajouter l'employé
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="custom-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-calendar-check-fill"></i> Demandes de congé en attente
                </div>
                <span class="badge bg-warning text-dark">
                    <?= count($demandes) ?> demande(s)
                </span>
            </div>    
            <div class="card-body">
                <?php if(count($demandes) == 0): ?>
                <div class="no-results py-4">
                    <i class="bi bi-check2-circle"></i>
                    <h5 class="mb-2">Aucune demande en attente</h5>
                    <p class="mb-0">Toutes les demandes ont été traitées</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employé</th>
                                <th>Poste</th>
                                <th>Jours restants</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Nb.jours</th>
                                <th>Soumission</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($demandes as $d): ?>
                            <tr>
                                <td><strong>#<?=$d['id_demandeConge'] ?></strong></td>
                                <td><?= $d['login'] ?></td>
                                <td><?= $d['poste'] ?></td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        <?=$d['jours_restants'] ?>
                                    </span>
                                </td>
                                <td><?= $d['date_debut'] ?></td>
                                <td><?= $d['date_fin'] ?></td>
                                <td>
                                    <span class="badge bg-secondary text-white">
                                        <?=$d['nombre_jours'] ?>
                                    </span>
                                </td>
                                <td><?= $d['date_soumission'] ?></td>
                                <td>
                                    <span class="badge-custom badge-waiting">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= $d['statut'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="?action=accepter&id=<?=$d['id_demandeConge'] ?>" 
                                           class="btn btn-accept btn-action"
                                           onclick="return confirm('Confirmer l acceptation de cette demande ?')">
                                            <i class="bi bi-check-lg me-1"></i> Accepter
                                        </a>
                                        <a href="?action=refuser&id=<?= $d['id_demandeConge'] ?>" class="btn btn-reject btn-action"
                                           onclick="return confirm('Confirmer le refus de cette demande ?')"><i class="bi bi-x-lg me-1"></i> Refuser
                                        </a></div></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>   
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addEmployeeBtn = document.querySelector('[data-bs-target="#formAjoutEmploye"]');
            const form = document.getElementById('formAjoutEmploye');
            addEmployeeBtn.addEventListener('click', function() {
                if (form.classList.contains('show')) {
                    this.innerHTML = '<i class="bi bi-chevron-down"></i>';
                } else {
                    this.innerHTML = '<i class="bi bi-chevron-up"></i>';
                }
            });
        });
    </script>
</body>
</html>
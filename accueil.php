<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  // Statistiques globales pour les compteurs
  try {
    $totalEtudiants = (int)$db->query('SELECT COUNT(*) FROM etudiant')->fetchColumn();
    $totalClasses   = (int)$db->query('SELECT COUNT(*) FROM classe')->fetchColumn();
    $totalMatieres  = (int)$db->query('SELECT COUNT(*) FROM matiere')->fetchColumn();
    $totalProfs     = (int)$db->query('SELECT COUNT(*) FROM professeur')->fetchColumn();

    // Effectif par classe pour le graphique Chart.js
    $stmtChart = $db->query(
      'SELECT CONCAT(c.CodeNiveau, " ", c.CodeMention) AS Classe, COUNT(e.NumEtu) AS NbEtu ' .
      'FROM classe c ' .
      'LEFT JOIN etudiant e ON c.CodeNiveau = e.CodeNiveau AND c.CodeMention = e.CodeMention ' .
      'GROUP BY c.CodeNiveau, c.CodeMention ' .
      'ORDER BY c.CodeNiveau, c.CodeMention'
    );
    $chartClasses = [];
    $chartCounts = [];
    while ($row = $stmtChart->fetch()) {
      $chartClasses[] = $row['Classe'];
      $chartCounts[] = (int)$row['NbEtu'];
    }

    // Répartition par genre
    $stmtGenre = $db->query('SELECT SexeEtu, COUNT(*) as nb FROM etudiant GROUP BY SexeEtu');
    $genreData = ['Masculin' => 0, 'Feminin' => 0];
    while ($row = $stmtGenre->fetch()) {
      $genreData[$row['SexeEtu']] = (int)$row['nb'];
    }

    // Requête pour les cartes de classes détaillées
    $queryClasses = 'SELECT c.CodeNiveau, c.CodeMention, COUNT(e.NumEtu) AS NbEtu ' .
                    'FROM classe c ' .
                    'LEFT JOIN etudiant e ON c.CodeNiveau = e.CodeNiveau AND c.CodeMention = e.CodeMention ' .
                    'GROUP BY c.CodeNiveau, c.CodeMention ' .
                    'ORDER BY c.CodeNiveau, c.CodeMention';
    $classesList = $db->query($queryClasses)->fetchAll();

    // Derniers étudiants inscrits
    $stmtRecent = $db->query('SELECT NumEtu, NomEtu, PrenomEtu, CodeNiveau, CodeMention, DateInscr FROM etudiant ORDER BY NumEtu DESC LIMIT 5');
    $recentEtudiants = $stmtRecent->fetchAll();

  } catch (Exception $e) {
    $totalEtudiants = 0; $totalClasses = 0; $totalMatieres = 0; $totalProfs = 0;
    $chartClasses = []; $chartCounts = [];
    $genreData = ['Masculin' => 0, 'Feminin' => 0];
    $classesList = [];
    $recentEtudiants = [];
  }
?>

  <!-- Dashboard Header & Welcome -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-2">
    <div>
      <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
        <i class="fas fa-tachometer-alt text-primary mr-2"></i>Tableau de bord universitaire
      </h1>
      <p class="text-muted small mb-0">Bienvenue sur le portail de gestion intégrée de l'EMIT.</p>
    </div>
    <div class="mt-3 mt-sm-0">
      <a href="inscription.php" class="btn btn-sm btn-primary shadow-sm font-weight-bold mr-2">
        <i class="fas fa-user-plus fa-sm text-white-50 mr-1"></i>Nouvelle inscription
      </a>
      <a href="emploi_du_temps.php" class="btn btn-sm btn-info shadow-sm font-weight-bold mr-2">
        <i class="fas fa-calendar-alt fa-sm text-white-50 mr-1"></i>Emploi du temps
      </a>
      <a href="bulletin.php" class="btn btn-sm btn-success shadow-sm font-weight-bold">
        <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i>Relevés & Bulletins
      </a>
    </div>
  </div>

  <!-- Metric Statistics Cards -->
  <div class="row mb-4">
    <!-- Total Étudiants -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Étudiants</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalEtudiants) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Classes -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-success shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Filières & Classes</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalClasses) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Matières -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Matières Enseignées</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalMatieres) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-book-reader fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Enseignants -->
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card border-left-warning shadow h-100 py-2">
        <div class="card-body">
          <div class="row no-gutters align-items-center">
            <div class="col mr-2">
              <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Corps Enseignant</div>
              <div class="h4 mb-0 font-weight-bold text-gray-800"><?= number_format($totalProfs) ?></div>
            </div>
            <div class="col-auto">
              <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Interactive Analytical Charts Row -->
  <div class="row mb-4">
    <!-- Bar Chart: Effectifs par classe -->
    <div class="col-xl-8 col-lg-7 mb-4">
      <div class="card shadow">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar mr-1"></i>Répartition des effectifs par classe
          </h6>
        </div>
        <div class="card-body">
          <div style="height: 300px;">
            <canvas id="chartEffectifs"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Pie Chart: Répartition par genre -->
    <div class="col-xl-4 col-lg-5 mb-4">
      <div class="card shadow">
        <div class="card-header py-3 bg-white">
          <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-pie mr-1"></i>Répartition par genre
          </h6>
        </div>
        <div class="card-body">
          <div style="height: 250px;">
            <canvas id="chartGenre"></canvas>
          </div>
          <div class="mt-3 text-center small text-muted">
            <span class="mr-3"><i class="fas fa-circle text-primary"></i> Masculin (<?= $genreData['Masculin'] ?>)</span>
            <span><i class="fas fa-circle text-info"></i> Féminin (<?= $genreData['Feminin'] ?>)</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Class Access Grid -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-th-large mr-1"></i>Accès rapide par promotion et mention
      </h6>
      <span class="badge badge-primary"><?= count($classesList) ?> classes</span>
    </div>
    <div class="card-body">
      <div class="row">
        <?php foreach ($classesList as $classe): ?>
          <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
            <div class="card border-left-primary h-100 shadow-sm p-3">
              <div class="d-flex justify-content-between align-items-center">
                <a href="etudiant.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="text-decoration-none">
                  <span class="h4 font-weight-bold text-primary"><?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?></span>
                </a>
                <span class="badge badge-light border text-dark font-weight-bold p-2">
                  <i class="fas fa-users text-primary mr-1"></i><?= h($classe['NbEtu']) ?>
                </span>
              </div>
              <div class="mt-2 pt-2 border-top d-flex justify-content-between text-muted small">
                <a href="matiere.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="text-muted"><i class="fas fa-book mr-1"></i>Matières</a>
                <a href="examen.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="text-muted"><i class="fas fa-edit mr-1"></i>Examens</a>
                <a href="resultat.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="text-muted"><i class="fas fa-award mr-1"></i>Résultats</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Inscriptions récentes Table -->
  <div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-user-clock mr-1"></i>Dernières inscriptions enregistrées
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th>N° Matricule</th>
              <th>Nom & Prénoms</th>
              <th>Classe</th>
              <th>Date d'inscription</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentEtudiants)): ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">Aucune inscription pour le moment.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentEtudiants as $etu): ?>
                <tr>
                  <td class="font-weight-bold">#<?= h($etu['NumEtu']) ?></td>
                  <td><?= h($etu['NomEtu']) ?> <?= h($etu['PrenomEtu']) ?></td>
                  <td><span class="badge badge-primary"><?= h($etu['CodeNiveau']) ?> <?= h($etu['CodeMention']) ?></span></td>
                  <td><?= h(format_date_fr($etu['DateInscr'])) ?></td>
                  <td class="text-right">
                    <a href="detailnote.php?numetu=<?= h($etu['NumEtu']) ?>" class="btn btn-sm btn-outline-primary" title="Relevé">
                      <i class="fas fa-file-alt"></i>
                    </a>
                    <a href="modifetu.php?numetu=<?= h($etu['NumEtu']) ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Chart.js Scripts Initialization -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // 1. Bar Chart Effectifs
      var ctxBar = document.getElementById("chartEffectifs");
      if (ctxBar) {
        new Chart(ctxBar, {
          type: 'bar',
          data: {
            labels: <?= json_encode($chartClasses) ?>,
            datasets: [{
              label: "Nombre d'étudiants",
              backgroundColor: "#4e73df",
              hoverBackgroundColor: "#2e59d9",
              borderColor: "#4e73df",
              borderRadius: 4,
              data: <?= json_encode($chartCounts) ?>,
            }],
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: { precision: 0 }
              }
            }
          }
        });
      }

      // 2. Doughnut Chart Genre
      var ctxPie = document.getElementById("chartGenre");
      if (ctxPie) {
        new Chart(ctxPie, {
          type: 'doughnut',
          data: {
            labels: ["Masculin", "Féminin"],
            datasets: [{
              data: [<?= (int)$genreData['Masculin'] ?>, <?= (int)$genreData['Feminin'] ?>],
              backgroundColor: ['#4e73df', '#36b9cc'],
              hoverBackgroundColor: ['#2e59d9', '#2c9faf'],
              hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: { display: false }
            },
            cutout: '70%',
          },
        });
      }
    });
  </script>

<?php
  include __DIR__ . '/inclus/modal.php';
?>

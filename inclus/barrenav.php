<?php
  require_once __DIR__ . '/bd.php';
  require_once __DIR__ . '/../session.php';
  confirm_logged_in();

  // Obtenir la liste des classes et mentions pour les menus
  try {
    $stmtClasses = $db->query('SELECT * FROM classe ORDER BY CodeNiveau, CodeMention');
    $allClasses = $stmtClasses->fetchAll();

    $stmtNiveaux = $db->query('SELECT * FROM niveau ORDER BY NumNiveau');
    $allNiveaux = $stmtNiveaux->fetchAll();

    $stmtMentions = $db->query('SELECT * FROM mention ORDER BY CodeMention');
    $allMentions = $stmtMentions->fetchAll();

    $stmtAnnee = $db->query('SELECT * FROM anneeuniv ORDER BY AnneeUniv DESC LIMIT 1');
    $currentAnnee = $stmtAnnee->fetch();
  } catch (Exception $e) {
    $allClasses = [];
    $allNiveaux = [];
    $allMentions = [];
    $currentAnnee = ['AnneeUniv' => date('Y') . '-' . (date('Y') + 1)];
  }

  $unreadCount = get_unread_notifications($db);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="css/css/all.min.css">
  <link rel="stylesheet" href="css/sb-admin-2.min.css">
  <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <!-- Chart.js pour les graphiques analytiques -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body id="page-top">
  <!-- Top Navigation Bar -->
  <nav class="navbar sticky-top navbar-expand-lg topbar navbar-dark bg-primary shadow-sm" style="padding-left:240px">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <!-- Inscription -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropInscr" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-clipboard-list mr-1"></i>Inscription
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropInscr">
            <a class="dropdown-item text-light h6 bg-primary pl-3 py-2" href="inscription.php">
              <i class="fas fa-fw fa-user-plus mr-1"></i>Nouvelle inscription
            </a>
            <div class="dropdown-divider m-0"></div>
            <h6 class="dropdown-header text-muted bg-light">Réinscription par classe</h6>
            <?php foreach ($allClasses as $classe): ?>
              <a class="dropdown-item text-light text-center h6 bg-primary py-2" href="etudiant.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>">
                <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>

        <!-- Étudiant -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropEtu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-users mr-1"></i>Étudiant
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropEtu">
            <?php foreach ($allClasses as $classe): ?>
              <a class="dropdown-item text-light text-center h6 bg-primary py-2" href="etudiant.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>">
                <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>

        <!-- Matière -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropMat" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-book mr-1"></i>Matière
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropMat">
            <?php foreach ($allClasses as $classe): ?>
              <a class="dropdown-item text-light h6 bg-primary py-2" href="matiere.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>">
                <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>

        <!-- Enseignants (NOUVEAU) -->
        <li class="nav-item">
          <a class="nav-link text-light" href="professeur.php">
            <i class="fas fa-fw fa-chalkboard-teacher mr-1"></i>Enseignants
          </a>
        </li>

        <!-- Emploi du Temps (NOUVEAU) -->
        <li class="nav-item">
          <a class="nav-link text-light" href="emploi_du_temps.php">
            <i class="fas fa-fw fa-calendar-alt mr-1"></i>Emploi du temps
          </a>
        </li>

        <!-- Présences (NOUVEAU) -->
        <li class="nav-item">
          <a class="nav-link text-light" href="presence.php">
            <i class="fas fa-fw fa-user-check mr-1"></i>Assiduité
          </a>
        </li>

        <!-- Examen -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropExam" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-edit mr-1"></i>Examen
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropExam">
            <?php foreach ($allClasses as $classe): ?>
              <a class="dropdown-item text-light h6 bg-primary py-2" href="examen.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>">
                <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>

        <!-- Résultat -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropRes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-fw fa-tasks mr-1"></i>Résultat
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="navbarDropRes">
            <?php foreach ($allClasses as $classe): ?>
              <a class="dropdown-item text-light h6 bg-primary py-2" href="resultat.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>">
                <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>
      </ul>

      <!-- Recherche globale (NOUVEAU) -->
      <form class="form-inline my-2 my-lg-0 mr-3" action="student_search.php" method="get">
        <div class="input-group input-group-sm">
          <input class="form-control" type="search" name="q" placeholder="Rechercher étudiant..." aria-label="Search" style="border-radius:20px 0 0 20px; border:none; padding-left:12px;">
          <div class="input-group-append">
            <button class="btn btn-light btn-sm text-primary" type="submit" style="border-radius:0 20px 20px 0;">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </form>

      <ul class="navbar-nav ml-auto align-items-center">
        <!-- Année Universitaire -->
        <li class="nav-item">
          <div class="nav-link text-light font-weight-bold mr-3 py-0">
            <span class="d-none d-xl-inline text-white-50">Année univ : </span>
            <a href="#" class="badge badge-light text-primary px-2 py-1 font-weight-bold" data-toggle="modal" data-target="#anneeuniv" title="Cliquer pour changer l'année">
              <?= h($currentAnnee['AnneeUniv'] ?? 'N/A') ?>
            </a>
          </div>
        </li>

        <!-- Notifications (NOUVEAU) -->
        <li class="nav-item mx-1">
          <a class="nav-link text-light position-relative" href="notifications.php" title="Notifications">
            <i class="fas fa-bell fa-fw"></i>
            <?php if ($unreadCount > 0): ?>
              <span class="badge badge-danger badge-counter"><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
            <?php endif; ?>
          </a>
        </li>

        <!-- Journal d'Audit (NOUVEAU - Admin & CSCO) -->
        <?php if (in_array($_SESSION['AbrRole'] ?? '', ['CSCO', 'ADMIN'])): ?>
        <li class="nav-item mx-1">
          <a class="nav-link text-light" href="audit_log.php" title="Journal d'audit de sécurité">
            <i class="fas fa-shield-alt fa-fw"></i>
          </a>
        </li>
        <?php endif; ?>

        <!-- Compte Utilisateur -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-light font-weight-bold" href="#" id="navbarDropUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle fa-fw mr-1"></i><?= h($_SESSION['NomCGU'] ?? 'Utilisateur') ?>
          </a>
          <div class="dropdown-menu dropdown-menu-right shadow pb-0" aria-labelledby="navbarDropUser">
            <div class="dropdown-header text-muted">Connecté en tant que :<br><strong><?= h($_SESSION['RoleUGU'] ?? 'Utilisateur') ?></strong></div>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item text-light h6 bg-primary py-2" data-toggle="modal" data-target="#compte">
              <i class="fas fa-fw fa-sm fa-user mr-2"></i>Mon Profil
            </a>
            <a href="#" class="dropdown-item text-light h6 bg-primary py-2" data-toggle="modal" data-target="#modifiercompte">
              <i class="fas fa-fw fa-sm fa-cogs mr-2"></i>Modifier mot de passe
            </a>
            <div class="dropdown-divider m-0"></div>
            <a href="#" class="dropdown-item text-danger py-2" data-toggle="modal" data-target="#deconnexion">
              <i class="fas fa-fw fa-sm fa-sign-out-alt mr-2"></i>Se déconnecter
            </a>
          </div>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Sidebar & Main Content Layout -->
  <div class="row no-gutters">
    <div class="col-lg-1">
      <ul class="navbar-nav fixed-top sidebar sidebar-dark bg-primary shadow-sm" style="width:230px; height:100vh; overflow-y:auto; z-index:1000;">
        <a class="sidebar-brand d-flex align-items-center justify-content-center text-light text-decoration-none my-3" href="accueil.php">
          <div class="sidebar-brand-icon"><i class="fas fa-university fa-2x"></i></div>
          <div class="sidebar-brand-text mx-2 h3 font-weight-bold mb-0">EMIT</div>
        </a>
        <hr class="sidebar-divider my-1">
        <li class="nav-item">
          <a class="nav-link text-center py-2" href="accueil.php">
            <i class="fas fa-fw fa-home mr-1"></i><span>Accueil / Tableau de bord</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-center py-2" href="classe.php">
            <i class="fas fa-fw fa-layer-group mr-1"></i><span>Toutes les Classes</span>
          </a>
        </li>
        <hr class="sidebar-divider my-2">
        <div class="sidebar-heading text-light text-center font-weight-bold" style="font-size:0.75rem; opacity:0.8;">
          PAR NIVEAU
        </div>
        <hr class="sidebar-divider my-1">

        <?php foreach ($allNiveaux as $niveau): ?>
        <li class="nav-item dropdown dropright no-arrow">
          <a class="nav-link dropdown-toggle text-light py-2" href="#" id="sideNav<?= h($niveau['CodeNiveau']) ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="d-flex align-items-center justify-content-between px-2">
              <span><i class="fas fa-fw fa-graduation-cap mr-2"></i><?= h($niveau['CodeNiveau']) ?></span>
              <i class="fas fa-chevron-right fa-xs text-white-50"></i>
            </div>
          </a>
          <div class="dropdown-menu pb-0 shadow" aria-labelledby="sideNav<?= h($niveau['CodeNiveau']) ?>">
            <div class="dropdown-header text-primary font-weight-bold"><?= h($niveau['NomNiveau']) ?></div>
            <?php foreach ($allMentions as $mention): ?>
              <a class="dropdown-item text-light bg-primary h6 py-2" href="etudiant.php?codeniv=<?= h($niveau['CodeNiveau']) ?>&codemen=<?= h($mention['CodeMention']) ?>">
                <?= h($niveau['CodeNiveau']) ?> <?= h($mention['CodeMention']) ?> - <?= h($mention['NomMention']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Modal Changement Année Universitaire -->
    <div class="modal fade" id="anneeuniv" tabindex="-1" role="dialog" aria-labelledby="modalAnneeTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title font-weight-bold" id="modalAnneeTitle"><i class="fas fa-calendar mr-2"></i>Nouvelle année universitaire</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="majauniv.php" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
              <div class="form-group row align-items-center">
                <label class="col-sm-5 col-form-label font-weight-bold">Format (ex: 2025-2026) :</label>
                <div class="col-sm-7">
                  <input class="form-control" name="anneeuniv" placeholder="2025-2026" pattern="[0-9]{4}-[0-9]{4}" maxlength="9" required>
                </div>
              </div>
            </div>
            <div class="modal-footer bg-light">
              <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-fw fa-check mr-1"></i>Enregistrer</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-fw fa-times mr-1"></i>Annuler</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Main Container -->
    <div class="col-lg-11" style="padding-left:110px; padding-right:20px; padding-top:20px; padding-bottom:50px;">
      <?php include __DIR__ . '/toast.php'; ?>

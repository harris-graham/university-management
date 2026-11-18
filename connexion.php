<?php
  require_once __DIR__ . '/inclus/config.php';
  require_once __DIR__ . '/session.php';

  if (logged_in()) {
      set_flash('info', 'Vous êtes déjà connecté en tant que ' . ($_SESSION['RoleUGU'] ?? 'Utilisateur'));
      redirect('accueil.php');
  }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - <?= h(APP_NAME) ?></title>
  <link rel="stylesheet" href="css/css/all.min.css">
  <link rel="stylesheet" href="css/sb-admin-2.min.css">
  <link rel="stylesheet" href="css/custom.css">
</head>
<body class="bg-gradient-primary d-flex align-items-center" style="min-height: 100vh;">
  <?php include __DIR__ . '/inclus/toast.php'; ?>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-9 col-lg-10 col-md-11">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row no-gutters">
              <!-- Left brand presentation side -->
              <div class="col-lg-5 d-none d-lg-flex bg-primary text-white flex-column justify-content-center align-items-center p-5 text-center" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <i class="fas fa-university fa-5x mb-4 text-white-50"></i>
                <h1 class="font-weight-bold mb-2">EMIT</h1>
                <p class="h6 font-weight-light text-white-50 mb-4">École de Management et d'Innovation Technologique</p>
                <div class="badge badge-light text-primary px-3 py-2 font-weight-bold">
                  Portail Universitaire v<?= h(APP_VERSION) ?>
                </div>
              </div>

              <!-- Right form side -->
              <div class="col-lg-7">
                <div class="p-5">
                  <div class="text-center mb-4">
                    <h2 class="h3 text-gray-900 font-weight-bold">Espace d'authentification</h2>
                    <p class="text-muted small">Veuillez renseigner vos identifiants pour accéder à l'application.</p>
                  </div>

                  <form action="seconnecter.php" method="post" class="user">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                      <label class="small font-weight-bold text-dark mb-1">Nom d'utilisateur</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nom" maxlength="30" placeholder="ex: admin" required autofocus style="border-left:none;">
                      </div>
                    </div>

                    <div class="form-group mb-4">
                      <label class="small font-weight-bold text-dark mb-1">Mot de passe</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                        </div>
                        <input type="password" class="form-control" name="mdp" maxlength="50" placeholder="••••••••" required style="border-left:none;">
                      </div>
                    </div>

                    <button type="submit" name="seconnecter" class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm">
                      <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                    </button>
                  </form>

                  <hr class="my-4">
                  <div class="text-center">
                    <small class="text-muted">Compte de test par défaut : <code>admin</code> / <code>admin123</code></small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

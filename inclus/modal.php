    </div> <!-- Fin du col-lg-11 -->
  </div> <!-- Fin du row no-gutters -->

  <!-- Modal Affichage Profil -->
  <div class="modal fade" id="compte" tabindex="-1" role="dialog" aria-labelledby="modalProfilTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title font-weight-bold" id="modalProfilTitle"><i class="fas fa-user-circle mr-2"></i>Profil utilisateur</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-4 text-center">
          <div class="mb-3">
            <i class="fas fa-user-shield fa-4x text-primary"></i>
          </div>
          <h4 class="font-weight-bold text-dark mb-1"><?= h($_SESSION['NomCGU'] ?? 'Utilisateur') ?></h4>
          <span class="badge badge-pill badge-primary px-3 py-2 text-uppercase"><?= h($_SESSION['RoleUGU'] ?? '') ?> (<?= h($_SESSION['AbrRole'] ?? '') ?>)</span>
          <p class="text-muted small mt-3 mb-0">ID de compte : #<?= h($_SESSION['IdUGU'] ?? '1') ?></p>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal"><i class="fas fa-fw fa-check mr-1"></i>Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Modification Profil -->
  <div class="modal fade" id="modifiercompte" tabindex="-1" role="dialog" aria-labelledby="modalEditTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title font-weight-bold" id="modalEditTitle"><i class="fas fa-user-edit mr-2"></i>Modifier mes identifiants</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="modifprofil.php" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="idutil" value="<?= h($_SESSION['IdUGU'] ?? '') ?>">
          <div class="modal-body py-3">
            <div class="form-group">
              <label class="font-weight-bold text-dark">Nom d'utilisateur :</label>
              <input class="form-control" name="nomcompte" maxlength="30" value="<?= h($_SESSION['NomCGU'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="font-weight-bold text-dark">Nouveau mot de passe :</label>
              <input type="password" class="form-control" name="mdpcompte" maxlength="50" placeholder="Entrez un nouveau mot de passe" required>
              <small class="form-text text-muted">Le mot de passe sera sécurisé et chiffré selon les normes modernes (bcrypt).</small>
            </div>
            <div class="form-group mb-0">
              <label class="font-weight-bold text-dark">Rôle :</label>
              <input class="form-control bg-light" value="<?= h($_SESSION['RoleUGU'] ?? '') ?>" readonly>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-fw fa-save mr-1"></i>Sauvegarder</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-fw fa-times mr-1"></i>Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Déconnexion -->
  <div class="modal fade" id="deconnexion" tabindex="-1" role="dialog" aria-labelledby="modalLogoutTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title font-weight-bold" id="modalLogoutTitle"><i class="fas fa-sign-out-alt mr-2"></i>Confirmation de déconnexion</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body py-4 text-center">
          <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
          <h5>Êtes-vous sûr de vouloir vous déconnecter ?</h5>
          <p class="text-muted small mb-0">Toute session active sera immédiatement fermée.</p>
        </div>
        <div class="modal-footer bg-light">
          <a href="sedeconnecter.php" class="btn btn-danger font-weight-bold"><i class="fas fa-fw fa-sign-out-alt mr-1"></i>Oui, me déconnecter</a>
          <button class="btn btn-secondary" type="button" data-dismiss="modal"><i class="fas fa-fw fa-times mr-1"></i>Annuler</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts JavaScript requis -->
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>
  <script src="js/datatables-demo.js"></script>
</body>
</html>

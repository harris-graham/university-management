# 🤝 Guide de Contribution à GestUniv

Merci de vous intéresser à **GestUniv** ! Ce guide détaille les conventions de code et le processus de contribution via GitHub.

---

## 🚀 Processus de Contribution

1. **Forkez** le dépôt sur votre compte GitHub.
2. **Clonez** votre fork en local :
   ```bash
   git clone https://github.com/VOTRE_UTILISATEUR/gestuniv.git
   cd gestuniv
   ```
3. **Créez une branche thématique** suivant la convention :
   - `feat/nom-fonctionnalite` pour un ajout
   - `fix/nom-du-bogue` pour une correction
   - `docs/amelioration-doc` pour la documentation
   ```bash
   git checkout -b feat/gestion-stages
   ```
4. **Apportez vos modifications** en respectant les standards de sécurité :
   - **Toujours** utiliser les requêtes préparées PDO (`$stmt = $db->prepare(...)`) avec marqueurs `?`.
   - **Toujours** échapper les sorties HTML avec `h($chaine)`.
   - **Toujours** inclure `<?= csrf_field() ?>` dans les formulaires POST.
5. **Commitez vos changements** en suivant la norme [Conventional Commits](https://www.conventionalcommits.org/) :
   ```bash
   git commit -m "feat(stages): ajout du formulaire d'inscription aux stages de fin d'études"
   ```
6. **Poussez** vers votre fork :
   ```bash
   git push origin feat/gestion-stages
   ```
7. **Ouvrez une Pull Request** sur la branche principale du dépôt d'origine en remplissant le formulaire de PR.

---

## 🔒 Règles de Sécurité Impératives

Toute Pull Request ne respectant pas les règles suivantes sera automatiquement refusée :
- ❌ **Aucune concaténation de variables utilisateur dans du SQL** (`SELECT * WHERE id = " . $id` est strictement banni).
- ❌ **Aucun mot de passe en clair** (utilisation exclusive de `password_hash()` et `password_verify()`).
- ❌ **Aucun stockage de mot de passe dans les sessions PHP**.

---

## 💡 Signaler un Bogue ou une Idée

Utilisez l'onglet [Issues](../../issues) de GitHub et choisissez le template approprié (**Bug Report** ou **Feature Request**).

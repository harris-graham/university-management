# Changelog - GestUniv

All notable changes to this project are documented in this file.

## [2.0.0] - Modernization & Security Overhaul

### Added
- **Database Setup**: Added complete SQL schema (`database/schema.sql`) and initial seed data (`database/seed.sql`).
- **Corps Enseignant (Professeurs)**: Complete CRUD module for academic staff management (`professeur.php`, `ajoutprof.php`, `modifprof.php`, `suppprof.php`).
- **Emploi du Temps (Course Scheduling)**: Weekly interactive timetable system with class filtering (`emploi_du_temps.php`, `ajoutseance.php`, `suppseance.php`).
- **Assiduité & Présences**: Attendance tracking system with fast batch registration and absence justifications (`presence.php`, `marquerpresence.php`).
- **Bulletins Officiels & Relevés de Notes**: Printable official academic transcripts with automatic weighted average calculation and jury decisions (`bulletin.php`).
- **Centre de Notifications**: In-app notification center for academic alerts and announcements (`notifications.php`).
- **Journal d'Audit & Traçabilité**: Security event logging tracking logins, edits, and deletions (`audit_log.php`).
- **Recherche Globale**: Live student search across names, IDs, CIN, and phone numbers (`student_search.php`).
- **Tableau de Bord Analytique**: Interactive dashboard with Chart.js charts for class headcounts and gender distribution (`accueil.php`).
- **Export Données**: CSV and Excel export utilities for students and results (`export.php`).
- **Système Toast UI**: Modern non-intrusive Bootstrap 4 notifications replacing legacy JavaScript alerts (`inclus/toast.php`).
- **GitHub Suite**: Complete CI/CD workflows, issue templates, PR template, Dependabot configuration, and documentation.

### Security
- Replaced all legacy `mysqli` raw queries with modern PDO prepared statements with parameter binding.
- Implemented robust CSRF token generation and validation on all POST requests.
- Added universal output escaping helper `h()` to protect against Cross-Site Scripting (XSS).
- Replaced plaintext password storage with `password_hash()` and `password_verify()` using bcrypt.
- Removed password storage from session variables and added session regeneration on login.

---

## [1.0.0] - Legacy Prototype
- Initial university management scripts for students, subjects, exams, and grades.

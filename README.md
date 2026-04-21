Tests d'Acceptation — Création Compte Étudiant (Behat)

Contexte
Ce projet met en place des tests d’acceptation automatisés pour valider la fonctionnalité de création de compte étudiant dans une application backend PHP.

Objectifs
- Vérifier le bon fonctionnement de l’API
- Valider les règles métier
- Tester avec base de données réelle
- Automatiser les tests

Technologies
- PHP 8.2
- Behat 3.13
- MySQL
- XAMPP

Structure du projet
backend/
- behat.phar
- behat.yml
- features/
- dashbord-backend/

Fonctionnement
1. Lecture des scénarios
2. Correspondance avec code PHP
3. Envoi requête HTTP
4. Traitement API
5. Vérification résultat

Scénarios testés
- Création valide (201)
- Email déjà utilisé (409)
- Mot de passe généré
- Matricule retourné

Exécution
Commande : php behat.phar

Résultats
- 4 scénarios passés
- 48 étapes validées
- 0 erreur

Conclusion
Les tests sont automatisés, fiables et valident correctement la fonctionnalité.

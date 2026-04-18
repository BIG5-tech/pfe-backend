Feature: Creation compte etudiant via API reelle

  Background:
    Given je suis connecte en tant que coordonnateur
    And je suis sur la page "Gestion des etudiants"

  Scenario: Creer un compte etudiant valide
    When je remplis le champ "Nom" avec "Ben Ali"
    And je remplis le champ "Prenom" avec "Ahmed"
    And je remplis le champ "Email" avec "ahmed.behat.api2@etudiant.tn"
    And je remplis le champ "Matricule" avec "ETU2024098"
    And je remplis le champ "Departement" avec "Informatique"
    And je remplis le champ "Annee" avec "2"
    And je clique sur le bouton "Enregistrer"
    Then je dois voir le message "Compte étudiant créé avec succès."
    And le code HTTP doit etre 201
    And l'etudiant "ahmed.behat.api2@etudiant.tn" doit exister dans le systeme

  Scenario: Echec si email deja utilise
    Given un etudiant avec l'email "doublon2.behat@etudiant.tn" existe deja
    When je remplis le champ "Nom" avec "Dupont"
    And je remplis le champ "Prenom" avec "Marie"
    And je remplis le champ "Email" avec "doublon2.behat@etudiant.tn"
    And je remplis le champ "Matricule" avec "ETU2024087"
    And je remplis le champ "Departement" avec "Informatique"
    And je remplis le champ "Annee" avec "1"
    And je clique sur le bouton "Enregistrer"
    Then je dois voir le message d'erreur "Cet email est déjà utilisé."
    And le code HTTP doit etre 409
    And aucun nouveau compte ne doit etre cree

  Scenario: Mot de passe temporaire genere automatiquement
    When je remplis le champ "Nom" avec "Karoui"
    And je remplis le champ "Prenom" avec "Fatma"
    And je remplis le champ "Email" avec "fatma.behat2@etudiant.tn"
    And je remplis le champ "Matricule" avec "ETU2024066"
    And je remplis le champ "Departement" avec "Genie Civil"
    And je remplis le champ "Annee" avec "1"
    And je clique sur le bouton "Enregistrer"
    Then le code HTTP doit etre 201
    And je dois voir le message "Compte étudiant créé avec succès."
    And la reponse doit contenir un mot de passe temporaire

  Scenario: Le matricule est retourne dans la reponse API
    When je remplis le champ "Nom" avec "Jebali"
    And je remplis le champ "Prenom" avec "Nour"
    And je remplis le champ "Email" avec "nour.behat2@etudiant.tn"
    And je remplis le champ "Matricule" avec "ETU2024055"
    And je remplis le champ "Departement" avec "Mecanique"
    And je remplis le champ "Annee" avec "2"
    And je clique sur le bouton "Enregistrer"
    Then le code HTTP doit etre 201
    And la reponse doit contenir le matricule "ETU2024055"
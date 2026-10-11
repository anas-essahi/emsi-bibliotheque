# Bibliothèque EMSI

Application web de gestion de bibliothèque universitaire en PHP (POO) et MySQL.

## Fonctionnalités
- Inscription / connexion (mots de passe hashés, sessions)
- Catalogue avec recherche et filtres
- Emprunt et retour de livres (quota par profil, gestion du stock)
- Espace personnel avec historique et détection des retards
- Espace admin : dashboard, CRUD livres, gestion des utilisateurs et des emprunts

## Points techniques
- POO : classes abstraites, interface, héritage, exceptions personnalisées
- PDO avec requêtes préparées, singleton de connexion
- Transactions SQL pour les emprunts et retours
- Protection des pages par rôle

## Installation
1. Importer `sql/bibliotheque.sql`
2. Copier `includes/config.example.php` en `includes/config.php` et renseigner vos identifiants
3. Lancer sur XAMPP

## Démo
https://emsi-bibliotheque.fwh.is/bibliotheque/

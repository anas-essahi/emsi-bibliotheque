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



<?php
require_once __DIR__ . '/includes/admin-auth.php';
$pdo=Database::getConnexion();
$nb_livres=$pdo->query("SELECT COUNT(*)FROM livres")->fetchColumn();
$nb_utilisateur=$pdo->query("SELECT COUNT(*) FROM utilisateurs where type!='admin'")->fetchColumn();
$nb_emprunts=$pdo->query("SELECT COUNT(*) FROM emprunts WHERE statut='en_cours'")->fetchColumn();
$nb_emprunts_retards=$pdo->query("SELECT COUNT(*) FROM emprunts WHERE statut = 'en_cours' AND date_retour_prevue < CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Bibliothèque EMSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,500;0,600;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #101826;
            --gold: #B8934A;
            --cream: #F7F3EB;
            --ink: #1A1F2B;
            --grey: #5C6478;
            --red: #7A2F2F;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--cream);
            font-family: 'Inter', sans-serif;
            color: var(--ink);
        }

        header {
            background: var(--navy);
            color: var(--cream);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .brand {
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 1.15rem;
        }

        header .brand span {
            display: block;
            font-size: 0.65rem;
            color: var(--gold);
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            letter-spacing: 0.08em;
        }

        header nav a {
            color: var(--cream);
            text-decoration: none;
            font-size: 0.88rem;
            margin-left: 24px;
            transition: color 0.2s ease;
        }

        header nav a:hover { color: var(--gold); }

        header nav a.logout {
            border: 1px solid var(--gold);
            padding: 6px 14px;
            border-radius: 4px;
        }

        .wrap {
            max-width: 1000px;
            margin: 0 auto;
            padding: 36px 40px 60px;
        }

        .page-title {
            font-family: 'Lora', serif;
            font-size: 1.6rem;
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--grey);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        /* Cartes stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 44px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            padding: 22px 20px;
        }

        .stat-card .icon {
            width: 38px;
            height: 38px;
            border-radius: 6px;
            background: var(--navy);
            color: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            margin-bottom: 14px;
        }

        .stat-card.alert .icon {
            background: #F3E8E8;
            color: var(--red);
        }

        .stat-card .number {
            font-family: 'Lora', serif;
            font-size: 1.9rem;
            font-weight: 600;
            color: var(--ink);
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-card.alert .number {
            color: var(--red);
        }

        .stat-card .label {
            font-size: 0.82rem;
            color: var(--grey);
        }

        /* Section liens gestion */
        .section-title {
            font-family: 'Lora', serif;
            font-size: 1.15rem;
            margin: 0 0 16px;
        }

        .manage-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .manage-card {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            padding: 24px 20px;
            text-decoration: none;
            color: var(--ink);
            transition: border-color 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .manage-card:hover { border-color: var(--gold); }

        .manage-card i {
            font-size: 1.3rem;
            color: var(--gold);
        }

        .manage-card h3 {
            font-family: 'Lora', serif;
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0;
        }

        .manage-card p {
            font-size: 0.85rem;
            color: var(--grey);
            margin: 0;
        }

        @media (max-width: 750px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .wrap { padding: 28px 20px 50px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .manage-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <div class="brand">
            Bibliothèque
            <span>ADMINISTRATION</span>
        </div>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="gestion-livres.php">Livres</a>
            <a href="gestion-utilisateurs.php">Utilisateurs</a>
            <a href="gestion-emprunts.php">Emprunts</a>
            <a href="../logout.php" class="logout">Déconnexion</a>
        </nav>
    </header>

    <div class="wrap">
        <div class="page-title">Tableau de bord</div>
        <div class="page-subtitle">Vue d'ensemble de la bibliothèque.</div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fa-solid fa-book"></i></div>
                <div class="number"><?= $nb_livres ?></div>
                <div class="label">Livres au catalogue</div>
            </div>

            <div class="stat-card">
                <div class="icon"><i class="fa-solid fa-users"></i></div>
                <div class="number"><?= $nb_utilisateur ?></div>
                <div class="label">Utilisateurs inscrits</div>
            </div>

            <div class="stat-card">
                <div class="icon"><i class="fa-solid fa-book-open"></i></div>
                <div class="number"><?= $nb_emprunts ?></div>
                <div class="label">Emprunts en cours</div>
            </div>

            <div class="stat-card <?= $nb_emprunts_retards > 0 ? 'alert' : '' ?>">
                <div class="icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="number"><?= $nb_emprunts_retards ?></div>
                <div class="label">Emprunts en retard</div>
            </div>
        </div>

        <div class="section-title">Gestion</div>

        <div class="manage-grid">
            <a href="gestion-livres.php" class="manage-card">
                <i class="fa-solid fa-book"></i>
                <h3>Gérer les livres</h3>
                <p>Ajouter, modifier ou supprimer des ouvrages.</p>
            </a>

            <a href="gestion-utilisateurs.php" class="manage-card">
                <i class="fa-solid fa-users"></i>
                <h3>Gérer les utilisateurs</h3>
                <p>Consulter et administrer les comptes.</p>
            </a>

            <a href="gestion-emprunts.php" class="manage-card">
                <i class="fa-solid fa-book-open"></i>
                <h3>Gérer les emprunts</h3>
                <p>Suivre les emprunts et forcer des retours.</p>
            </a>
        </div>

    </div>

</body>
</html>
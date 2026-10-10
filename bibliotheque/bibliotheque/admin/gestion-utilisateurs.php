<?php
require_once __DIR__ . '/includes/admin-auth.php';

$pdo = Database::getConnexion();

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE type != 'admin' ORDER BY nom ASC");
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($utilisateurs as &$u) {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE utilisateur_id = ? AND statut = 'en_cours'");
    $stmtCount->execute([$u['id']]);
    $u['nb_emprunts_en_cours'] = $stmtCount->fetchColumn();
}
unset($u);

$erreurSuppression = $_GET['erreur'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs — Bibliothèque EMSI</title>
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
        }

        header nav a:hover { color: var(--gold); }

        header nav a.logout {
            border: 1px solid var(--gold);
            padding: 6px 14px;
            border-radius: 4px;
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 40px 60px;
        }

        .page-title {
            font-family: 'Lora', serif;
            font-size: 1.6rem;
            margin-bottom: 24px;
        }

        .table-box {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--navy);
            color: var(--cream);
        }

        th {
            text-align: left;
            padding: 13px 16px;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        td {
            padding: 13px 16px;
            font-size: 0.88rem;
            border-bottom: 1px solid #F0EBDE;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #FAF7F0; }

        td.nom { font-weight: 500; color: var(--ink); }

        .type-badge {
            display: inline-block;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: #EAE9E3;
            color: var(--grey);
        }

        .emprunts-count {
            font-weight: 500;
        }

        .emprunts-count.actif { color: var(--gold); }

        .actions a {
            font-size: 0.82rem;
            text-decoration: none;
            color: var(--red);
        }

        .actions a:hover { text-decoration: underline; }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--grey);
            font-size: 0.9rem;
        }

        @media (max-width: 800px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .wrap { padding: 28px 20px 50px; }
            .table-box { overflow-x: auto; }
            table { min-width: 750px; }
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
        <div class="page-title">Gestion des utilisateurs</div>

        <?php if ($erreurSuppression): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreurSuppression) ?></div>
        <?php endif; ?>

        <div class="table-box">
            <?php if (empty($utilisateurs)): ?>
                <div class="empty-state">Aucun utilisateur inscrit pour l'instant.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Inscrit le</th>
                            <th>Emprunts en cours</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $u): ?>
                            <tr>
                                <td class="nom"><?= htmlspecialchars($u['nom']) ?></td>
                                <td><?= htmlspecialchars($u['prenom']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="type-badge">
                                        <?= $u['type'] === 'enseignant' ? 'Enseignant' : 'Étudiant' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                                <td class="emprunts-count <?= $u['nb_emprunts_en_cours'] > 0 ? 'actif' : '' ?>">
                                    <?= $u['nb_emprunts_en_cours'] ?>
                                </td>
                                <td class="actions">
                                    <a href="supprimer-utilisateur.php?id=<?= $u['id'] ?>">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
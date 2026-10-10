<?php
require_once __DIR__ . '/includes/admin-auth.php';
$erreurSuppression = $_GET['erreur'] ?? null;
$sql = Database::getConnexion();
$stmt = $sql->prepare("SELECT * FROM livres ORDER BY titre ASC");
$stmt->execute();
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des livres — Bibliothèque EMSI</title>
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
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 40px 60px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .page-title {
            font-family: 'Lora', serif;
            font-size: 1.6rem;
        }

        .btn-add {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 11px 20px;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .btn-add:hover { background: #1C2740; color: var(--gold); }

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

        td.titre { font-weight: 500; color: var(--ink); }
        td.stock-vide { color: var(--red); font-weight: 500; }

        .type-badge {
            display: inline-block;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: #EAE9E3;
            color: var(--grey);
        }

        .actions a {
            font-size: 0.82rem;
            text-decoration: none;
            margin-right: 14px;
        }

        .actions a.modifier { color: var(--navy); }
        .actions a.modifier:hover { color: var(--gold); }
        .actions a.supprimer { color: var(--red); }
        .actions a.supprimer:hover { text-decoration: underline; }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--grey);
            font-size: 0.9rem;
        }

        @media (max-width: 750px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .wrap { padding: 28px 20px 50px; }
            .table-box { overflow-x: auto; }
            table { min-width: 700px; }
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
        <?php if ($erreurSuppression): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($erreurSuppression) ?>
    </div>
<?php endif; ?>
        <div class="page-header">
            <div class="page-title">Gestion des livres</div>
            <a href="ajouter-livre.php" class="btn-add">+ Ajouter un livre</a>
        </div>

        <div class="table-box">
            <?php if (empty($res)): ?>
                <div class="empty-state">Aucun livre au catalogue pour l'instant.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Catégorie</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($res as $livre): ?>
                            <tr>
                                <td class="titre"><?= htmlspecialchars($livre['titre']) ?></td>
                                <td><?= htmlspecialchars($livre['auteur']) ?></td>
                                <td><?= htmlspecialchars($livre['categorie']) ?></td>
                                <td>
                                    <span class="type-badge">
                                        <?= $livre['type'] === 'ebook' ? 'E-Book' : 'Papier' ?>
                                    </span>
                                </td>
                                <td class="<?= $livre['exemplaires_disponibles'] == 0 ? 'stock-vide' : '' ?>">
                                    <?= $livre['exemplaires_disponibles'] ?> / <?= $livre['exemplaires_total'] ?>
                                </td>
                                <td class="actions">
                                    <a href="modifier-livre.php?id=<?= $livre['id'] ?>" class="modifier">Modifier</a>
                                    <a href="supprimer-livre.php?id=<?= $livre['id'] ?>" class="supprimer">Supprimer</a>
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
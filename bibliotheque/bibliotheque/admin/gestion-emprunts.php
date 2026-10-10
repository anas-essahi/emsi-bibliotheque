<?php
require_once __DIR__ . '/includes/admin-auth.php';
require_once __DIR__ . '/../classes/Emprunt.php';

$pdo = Database::getConnexion();

$messageRetour = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emprunt_id'])) {
    try {
        Emprunt::retourner((int) $_POST['emprunt_id'], $pdo);
        $messageRetour = ['type' => 'succes', 'texte' => 'Retour enregistré avec succès.'];
    } catch (Exception $e) {
        $messageRetour = ['type' => 'erreur', 'texte' => $e->getMessage()];
    }
}

$stmt = $pdo->prepare("SELECT emprunts.*, livres.titre AS livre_titre, utilisateurs.nom, utilisateurs.prenom
                        FROM emprunts
                        JOIN livres ON emprunts.livre_id = livres.id
                        JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
                        ORDER BY emprunts.date_emprunt DESC");
$stmt->execute();
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des emprunts — Bibliothèque EMSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
            max-width: 1150px;
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

        td.livre { font-weight: 500; color: var(--ink); }

        .statut-badge {
            display: inline-block;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .statut-badge.en-cours { background: #EAF3EA; color: #2F5F2F; }
        .statut-badge.retard { background: #F3E8E8; color: var(--red); }
        .statut-badge.retourne { background: #EAE9E3; color: var(--grey); }

        .btn-retour {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 7px 14px;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .btn-retour:hover { background: #1C2740; color: var(--gold); }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--grey);
            font-size: 0.9rem;
        }

        @media (max-width: 850px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .wrap { padding: 28px 20px 50px; }
            .table-box { overflow-x: auto; }
            table { min-width: 800px; }
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
        <div class="page-title">Gestion des emprunts</div>

        <?php if ($messageRetour): ?>
            <div class="alert alert-<?= $messageRetour['type'] === 'succes' ? 'success' : 'danger' ?>">
                <?= htmlspecialchars($messageRetour['texte']) ?>
            </div>
        <?php endif; ?>

        <div class="table-box">
            <?php if (empty($emprunts)): ?>
                <div class="empty-state">Aucun emprunt enregistré pour l'instant.</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th>Emprunteur</th>
                            <th>Emprunté le</th>
                            <th>À rendre le</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprunts as $e):
                            $enRetard = $e['statut'] === 'en_cours' && $e['date_retour_prevue'] < date('Y-m-d');
                        ?>
                            <tr>
                                <td class="livre"><?= htmlspecialchars($e['livre_titre']) ?></td>
                                <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                                <td><?= date('d/m/Y', strtotime($e['date_emprunt'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?></td>
                                <td>
                                    <?php if ($e['statut'] === 'retourne'): ?>
                                        <span class="statut-badge retourne">Retourné</span>
                                    <?php elseif ($enRetard): ?>
                                        <span class="statut-badge retard">En retard</span>
                                    <?php else: ?>
                                        <span class="statut-badge en-cours">En cours</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($e['statut'] === 'en_cours'): ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="emprunt_id" value="<?= $e['id'] ?>">
                                            <button type="submit" class="btn-retour">Forcer le retour</button>
                                        </form>
                                    <?php endif; ?>
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
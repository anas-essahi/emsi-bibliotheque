<?php
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Emprunt.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$pdo = Database::getConnexion();
$messageRetour = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emprunt_id'])) {
    try {
        Emprunt::retourner((int) $_POST['emprunt_id'], $pdo);
        $messageRetour = ['type' => 'succes', 'texte' => 'Livre retourné avec succès.'];
    } catch (Exception $e) {
        $messageRetour = ['type' => 'erreur', 'texte' => $e->getMessage()];
    }
}


$stmt = $pdo->prepare("SELECT emprunts.*, livres.titre, livres.auteur 
                        FROM emprunts JOIN livres ON emprunts.livre_id = livres.id 
                        WHERE emprunts.utilisateur_id = ? AND emprunts.statut = 'en_cours'
                        ORDER BY emprunts.date_emprunt DESC");
$stmt->execute([$_SESSION['user_id']]);
$empruntsEnCours = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("SELECT emprunts.*, livres.titre, livres.auteur 
                        FROM emprunts JOIN livres ON emprunts.livre_id = livres.id 
                        WHERE emprunts.utilisateur_id = ? AND emprunts.statut = 'retourne'
                        ORDER BY emprunts.date_emprunt DESC");
$stmt->execute([$_SESSION['user_id']]);
$historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace — Bibliothèque EMSI</title>
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
            max-width: 950px;
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

        .section-title {
            font-family: 'Lora', serif;
            font-size: 1.15rem;
            margin: 32px 0 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .emprunt-card {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .emprunt-info h3 {
            font-family: 'Lora', serif;
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 4px;
        }

        .emprunt-info .auteur {
            font-size: 0.85rem;
            color: var(--grey);
        }

        .emprunt-dates {
            font-size: 0.8rem;
            color: var(--grey);
            margin-top: 6px;
        }

        .badge-retard {
            display: inline-block;
            background: #F3E8E8;
            color: #7A2F2F;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-left: 8px;
        }

        .badge-retourne {
            display: inline-block;
            background: #EAE9E3;
            color: var(--grey);
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .btn-retourner {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 9px 18px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-retourner:hover { background: #1C2740; color: var(--gold); }

        .empty-state {
            color: var(--grey);
            font-size: 0.9rem;
            padding: 20px 0;
        }

        @media (max-width: 600px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .wrap { padding: 28px 20px 50px; }
            .emprunt-card { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <header>
        <div class="brand">
            Bibliothèque
            <span>EMSI CASABLANCA</span>
        </div>
        <nav>
            <a href="catalogue.php">Catalogue</a>
            <a href="mon-espace.php">Mon espace</a>
            <a href="logout.php" class="logout">Déconnexion</a>
        </nav>
    </header>

    <div class="wrap">
        <div class="page-title">Mon espace</div>
        <div class="page-subtitle">Bonjour <?= htmlspecialchars($_SESSION['user_nom']) ?>, voici vos emprunts.</div>
         <?php if ($messageRetour): ?>
    <div class="alert alert-<?= $messageRetour['type'] === 'succes' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($messageRetour['texte']) ?>
    </div>
<?php endif; ?>
        <div class="section-title"><i class="fa-solid fa-book-open"></i> Emprunts en cours</div>

        <?php if (empty($empruntsEnCours)): ?>
            <div class="empty-state">Aucun emprunt en cours.</div>
        <?php else: ?>
            <?php foreach ($empruntsEnCours as $e): ?>
                <div class="emprunt-card">
                    <div class="emprunt-info">
                        <h3>
                            <?= htmlspecialchars($e['titre']) ?>
                            <?php if ($e['date_retour_prevue'] < date('Y-m-d')): ?>
                                <span class="badge-retard">En retard</span>
                            <?php endif; ?>
                        </h3>
                        <div class="auteur"><?= htmlspecialchars($e['auteur']) ?></div>
                        <div class="emprunt-dates">
                            Emprunté le <?= date('d/m/Y', strtotime($e['date_emprunt'])) ?>
                            — à rendre avant le <?= date('d/m/Y', strtotime($e['date_retour_prevue'])) ?>
                        </div>
                    </div>
                    <form method="post" action="">
                        <input type="hidden" name="emprunt_id" value="<?= $e['id'] ?>">
                        <button type="submit" class="btn-retourner">Retourner ce livre</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Historique</div>

        <?php if (empty($historique)): ?>
            <div class="empty-state">Aucun emprunt terminé pour l'instant.</div>
        <?php else: ?>
            <?php foreach ($historique as $e): ?>
                <div class="emprunt-card">
                    <div class="emprunt-info">
                        <h3><?= htmlspecialchars($e['titre']) ?></h3>
                        <div class="auteur"><?= htmlspecialchars($e['auteur']) ?></div>
                        <div class="emprunt-dates">
                            Emprunté le <?= date('d/m/Y', strtotime($e['date_emprunt'])) ?>
                            <?php if ($e['date_retour_effective']): ?>
                                — rendu le <?= date('d/m/Y', strtotime($e['date_retour_effective'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="badge-retourne">Retourné</span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</body>
</html>
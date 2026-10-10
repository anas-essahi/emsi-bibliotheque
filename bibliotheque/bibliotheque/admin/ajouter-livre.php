<?php
require_once __DIR__ . '/includes/admin-auth.php';

$erreur = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $titre = trim($_POST['titre'] ?? "");
    $auteur = trim($_POST['auteur'] ?? "");
    $isbn = trim($_POST['isbn'] ?? "");
    $categorie = trim($_POST['categorie'] ?? "");
    $type = $_POST['type'] ?? "";
    $exemplaires_total = trim($_POST['exemplaires_total'] ?? "");
    $couverture = null;
    $fichier_pdf = ($type === 'ebook') ? trim($_POST['fichier_pdf'] ?? "") : null;

    if (empty($titre)) {
        $erreur[] = "Veuillez saisir le titre.";
    }
    if (empty($auteur)) {
        $erreur[] = "Veuillez saisir l'auteur.";
    }
    if (empty($categorie)) {
        $erreur[] = "Veuillez saisir la catégorie.";
    }
    if ($type !== 'papier' && $type !== 'ebook') {
        $erreur[] = "Le type doit être papier ou ebook.";
    }
    if (!is_numeric($exemplaires_total) || (int) $exemplaires_total <= 0) {
        $erreur[] = "Le nombre d'exemplaires doit être un entier positif.";
    }
    if ($type === 'ebook' && empty($fichier_pdf)) {
        $erreur[] = "Veuillez indiquer le nom du fichier PDF pour un e-book.";
    }

    if (empty($erreur)) {
        $exemplaires_total = (int) $exemplaires_total;
        $exemplaires_disponibles = $exemplaires_total;

        $pdo = Database::getConnexion();
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, isbn, categorie, type, exemplaires_total, exemplaires_disponibles, couverture, fichier_pdf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $titre,
            $auteur,
            $isbn,
            $categorie,
            $type,
            $exemplaires_total,
            $exemplaires_disponibles,
            $couverture,
            $fichier_pdf
        ]);

        header("Location: gestion-livres.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre — Bibliothèque EMSI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
        }

        header nav a:hover { color: var(--gold); }

        header nav a.logout {
            border: 1px solid var(--gold);
            padding: 6px 14px;
            border-radius: 4px;
        }

        .wrap {
            max-width: 700px;
            margin: 0 auto;
            padding: 36px 40px 60px;
        }

        .back-link {
            display: inline-block;
            color: var(--grey);
            text-decoration: none;
            font-size: 0.88rem;
            margin-bottom: 18px;
        }

        .back-link:hover { color: var(--navy); }

        .page-title {
            font-family: 'Lora', serif;
            font-size: 1.6rem;
            margin-bottom: 24px;
        }

        .form-box {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            padding: 28px 30px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .form-control, .form-select {
            border: 1px solid #D8D2C4;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 0.92rem;
            background: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184, 147, 74, 0.15);
        }

        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        #ebook-field { display: none; }

        .btn-submit {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 8px;
        }

        .btn-submit:hover { background: #1C2740; color: var(--gold); }
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
        <a href="gestion-livres.php" class="back-link">&larr; Retour à la liste</a>
        <div class="page-title">Ajouter un livre</div>

        <div class="form-box">

            <?php if (!empty($erreur)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($erreur as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($titre ?? '') ?>">
                </div>

                <div class="row-2 mb-3">
                    <div>
                        <label class="form-label">Auteur</label>
                        <input type="text" name="auteur" class="form-control" value="<?= htmlspecialchars($auteur ?? '') ?>">
                    </div>
                    <div>
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($isbn ?? '') ?>">
                    </div>
                </div>

                <div class="row-2 mb-3">
                    <div>
                        <label class="form-label">Catégorie</label>
                        <input type="text" name="categorie" class="form-control" value="<?= htmlspecialchars($categorie ?? '') ?>">
                    </div>
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" id="type" class="form-select" onchange="document.getElementById('ebook-field').style.display = this.value === 'ebook' ? 'block' : 'none';">
                            <option value="papier" <?= (isset($type) && $type === 'papier') ? 'selected' : '' ?>>Papier</option>
                            <option value="ebook" <?= (isset($type) && $type === 'ebook') ? 'selected' : '' ?>>E-Book</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre d'exemplaires</label>
                    <input type="number" name="exemplaires_total" class="form-control" min="1" value="<?= htmlspecialchars($exemplaires_total ?? '1') ?>">
                </div>

                <div class="mb-3" id="ebook-field">
                    <label class="form-label">Nom du fichier PDF</label>
                    <input type="text" name="fichier_pdf" class="form-control" placeholder="ex: mon_livre.pdf" value="<?= htmlspecialchars($fichier_pdf ?? '') ?>">
                </div>

                <button type="submit" class="btn-submit">Ajouter le livre</button>
            </form>

        </div>
    </div>

    <script>

        if (document.getElementById('type').value === 'ebook') {
            document.getElementById('ebook-field').style.display = 'block';
        }
    </script>

</body>
</html>
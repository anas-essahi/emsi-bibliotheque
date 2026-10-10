<?php
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Livre.php';
require_once 'classes/LivrePapier.php';
require_once 'classes/EBook.php';
  

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$recherche = trim($_GET['recherche'] ?? "");
$categorie = trim($_GET['categorie'] ?? "");

$sql = "SELECT * FROM livres WHERE 1=1";
$params = [];

if (!empty($recherche)) {
    $sql .= " AND (titre LIKE ? OR auteur LIKE ?)";
    $params[] = "%" . $recherche . "%";
    $params[] = "%" . $recherche . "%";
}

if (!empty($categorie)) {
    $sql .= " AND categorie = ?";
    $params[] = $categorie;
}

$pdo = Database::getConnexion();
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
$livres=[];
foreach($resultats as $row){
    if($row['type']==="papier"){
        $livre=new LivrePapier($row['titre'],$row['auteur'],$row['isbn'],$row['categorie'],$row['exemplaires_total'],$row['exemplaires_disponibles'],$row['couverture']);
    }else{
        $livre=new EBook($row['titre'],$row['auteur'],$row['isbn'],$row['categorie'],$row['exemplaires_total'],$row['exemplaires_disponibles'],$row['couverture'],$row['fichier_pdf']);
    }
    $livre->setId($row['id']);
    $livres[]=$livre;
}
   
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue — Bibliothèque EMSI</title>
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

        /* Header */
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

        /* Barre recherche/filtre */
        .search-bar {
            max-width: 1100px;
            margin: 32px auto 0;
            padding: 0 40px;
            display: flex;
            gap: 12px;
        }

        .search-bar input, .search-bar select {
            border: 1px solid #D8D2C4;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 0.92rem;
            background: #fff;
        }

        .search-bar input {
            flex: 1;
        }

        .search-bar input:focus, .search-bar select:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184, 147, 74, 0.15);
        }

        .search-bar button {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 10px 22px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .search-bar button:hover { background: #1C2740; color: var(--gold); }

        /* Titre section */
        .section-title {
            max-width: 1100px;
            margin: 36px auto 18px;
            padding: 0 40px;
            font-family: 'Lora', serif;
            font-size: 1.3rem;
            color: var(--ink);
        }

        /* Grille */
        .grid {
            max-width: 1100px;
            margin: 0 auto 60px;
            padding: 0 40px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 22px;
        }

        .card-livre {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: border-color 0.2s ease;
        }

        .card-livre:hover { border-color: var(--gold); }

        .cover {
            height: 160px;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 2rem;
        }

        .card-body {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-body .categorie {
            font-size: 0.7rem;
            color: var(--gold);
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .card-body h3 {
            font-family: 'Lora', serif;
            font-size: 1.02rem;
            font-weight: 600;
            margin: 0 0 4px;
            color: var(--ink);
        }

        .card-body .auteur {
            font-size: 0.85rem;
            color: var(--grey);
            margin-bottom: 14px;
        }

        .badge-dispo {
            display: inline-block;
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 12px;
            width: fit-content;
        }

        .badge-dispo.oui {
            background: #EAF3EA;
            color: #2F5F2F;
        }

        .badge-dispo.non {
            background: #F3E8E8;
            color: #7A2F2F;
        }

        .card-body a {
            margin-top: auto;
            text-align: center;
            text-decoration: none;
            color: var(--navy);
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid var(--navy);
            border-radius: 4px;
            padding: 8px;
            transition: all 0.2s ease;
        }

        .card-body a:hover {
            background: var(--navy);
            color: var(--cream);
        }

        .cover {
    height: 160px;
    background: var(--navy);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
    font-size: 2rem;
    overflow: hidden;
}

.cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

        @media (max-width: 600px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .search-bar, .section-title, .grid { padding: 0 20px; }
            .search-bar { flex-direction: column; }
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

    <form class="search-bar" method="get" action="">
        <input type="text" name="recherche" placeholder="Rechercher un titre ou un auteur...">
        <select name="categorie">
            <option value="">Toutes les catégories</option>
            <option value="Informatique">Informatique</option>
            <option value="Roman">Roman</option>
        </select>
        <button type="submit">Rechercher</button>
    </form>

    <div class="section-title">Tous les livres</div>

        <div class="grid">
        <?php foreach($livres as $livre):?>
        <div class="card-livre">
            <div class="cover">
         <img src="https://covers.openlibrary.org/b/isbn/<?= $livre->getIsbn() ?>-M.jpg"
     alt="Couverture <?= htmlspecialchars($livre->getTitre()) ?>"
     onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa-solid fa-book\'></i>';">
            </div>
            <div class="card-body">
                <div class="categorie"><?=htmlspecialchars( $livre->getCategorie()) ?></div>
                <h3><?=  htmlspecialchars($livre->getTitre() )?></h3>
                <div class="auteur"><?=  htmlspecialchars($livre->getAuteur()) ?></div>
                <span class="badge-dispo <?= $livre->estDisponible() ? 'oui' : 'non' ?>">
    <?= $livre->estDisponible() ? 'Disponible' : 'Indisponible' ?>
</span>
                <a href="livre.php?id=<?= $livre->getId() ?>">Voir le détail</a>
            </div>
        </div>
        <?php endforeach;?>


      
        </div>

  

</body>
</html>
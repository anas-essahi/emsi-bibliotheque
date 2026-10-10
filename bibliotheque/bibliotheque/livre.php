<?php
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Livre.php';
require_once 'classes/LivrePapier.php';
require_once 'classes/EBook.php';
require_once 'classes/Emprunt.php';
require_once 'classes/exceptions/LivreIndisponibleException.php';
require_once 'classes/exceptions/QuotaDepasseException.php';
if(!isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit();
}
$id = (int) ($_GET['id'] ?? 0);
if($id<=0){
    header("Location:catalogue.php");
    exit();
}
$sql=Database::getConnexion();
$stmt=$sql->prepare("SELECT*FROM livres where id=?");
$stmt->execute([$id]);
$res=$stmt->fetch(PDO::FETCH_ASSOC);
if($res===false){
     header("Location:catalogue.php");
    exit();
}
if($res['type']==='papier'){
    $livre=new LivrePapier($res['titre'],$res['auteur'],$res['isbn'],$res['categorie'],$res['exemplaires_total'],$res['exemplaires_disponibles'],$res['couverture']);
}else{
     $livre=new EBook($res['titre'],$res['auteur'],$res['isbn'],$res['categorie'],$res['exemplaires_total'],$res['exemplaires_disponibles'],$res['couverture'],$res['fichier_pdf']);
}
 $livre->setId($res['id']);
 $messageEmprunt = null;
 if($_SERVER['REQUEST_METHOD']==='POST'){
       try {
       Emprunt::creer($_SESSION['user_id'], $livre, $sql);
       $messageEmprunt = ['type' => 'succes', 'texte' => 'Emprunt enregistré avec succès.'];
   } catch (LivreIndisponibleException $e) {
       $messageEmprunt = ['type' => 'erreur', 'texte' => $e->getMessage()];
   } catch (QuotaDepasseException $e) {
       $messageEmprunt = ['type' => 'erreur', 'texte' => $e->getMessage()];
   }
 }


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($livre->getTitre()) ?> — Bibliothèque EMSI</title>
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

        .back-link {
            max-width: 1000px;
            margin: 28px auto 0;
            padding: 0 40px;
        }

        .back-link a {
            color: var(--grey);
            text-decoration: none;
            font-size: 0.88rem;
        }

        .back-link a:hover { color: var(--navy); }

        .detail-wrap {
            max-width: 1000px;
            margin: 20px auto 60px;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 40px;
        }

        .cover-box {
            background: var(--navy);
            border-radius: 6px;
            overflow: hidden;
            height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 3rem;
        }

        .cover-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info .categorie {
            font-size: 0.75rem;
            color: var(--gold);
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .info h1 {
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 2rem;
            margin: 0 0 6px;
            color: var(--ink);
        }

        .info .auteur {
            font-size: 1rem;
            color: var(--grey);
            margin-bottom: 18px;
        }

        .badge-dispo {
            display: inline-block;
            font-size: 0.78rem;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 24px;
        }

        .badge-dispo.oui { background: #EAF3EA; color: #2F5F2F; }
        .badge-dispo.non { background: #F3E8E8; color: #7A2F2F; }

        .meta-table {
            border-top: 1px solid #E0DACB;
            margin-bottom: 24px;
        }

        .meta-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #E0DACB;
            font-size: 0.92rem;
        }

        .meta-row .label {
            width: 160px;
            color: var(--grey);
        }

        .meta-row .value {
            color: var(--ink);
            font-weight: 500;
        }

        .description-box {
            background: #fff;
            border: 1px solid #E8E2D4;
            border-radius: 6px;
            padding: 18px 20px;
            font-size: 0.9rem;
            color: var(--grey);
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .btn-emprunter {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 13px 28px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-emprunter:hover { background: #1C2740; color: var(--gold); }

        .btn-indisponible {
            display: inline-block;
            background: #EDE8DC;
            color: var(--grey);
            border-radius: 4px;
            padding: 13px 28px;
            font-size: 0.95rem;
        }

        @media (max-width: 700px) {
            header { padding: 16px 20px; flex-direction: column; gap: 10px; }
            .back-link, .detail-wrap { padding: 0 20px; }
            .detail-wrap { grid-template-columns: 1fr; }
            .cover-box { height: 260px; }
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

    <div class="back-link">
        <a href="catalogue.php">&larr; Retour au catalogue</a>
    </div>

    <div class="detail-wrap">

        <div class="cover-box">
            <img src="https://covers.openlibrary.org/b/isbn/<?= htmlspecialchars($livre->getIsbn()) ?>-L.jpg"
                 alt="Couverture <?= htmlspecialchars($livre->getTitre()) ?>"
                 onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa-solid fa-book\'></i>';">
        </div>

        <div class="info">
            <div class="categorie"><?= htmlspecialchars($livre->getCategorie()) ?></div>
            <h1><?= htmlspecialchars($livre->getTitre()) ?></h1>
            <div class="auteur">par <?= htmlspecialchars($livre->getAuteur()) ?></div>

            <span class="badge-dispo <?= $livre->estDisponible() ? 'oui' : 'non' ?>">
                <?= $livre->estDisponible() ? 'Disponible' : 'Indisponible' ?>
            </span>

            <div class="meta-table">
                <div class="meta-row">
                    <div class="label">ISBN</div>
                    <div class="value"><?= htmlspecialchars($livre->getIsbn()) ?></div>
                </div>
                <div class="meta-row">
                    <div class="label">Type</div>
                    <div class="value"><?= $livre->getType() === 'ebook' ? 'E-Book' : 'Livre papier' ?></div>
                </div>
                <div class="meta-row">
                    <div class="label">Exemplaires</div>
                    <div class="value"><?= $livre->getExemplairesDisponibles() ?> / <?= $livre->getExemplairesTotal() ?></div>
                </div>
            </div>

            <div class="description-box">
                <?= htmlspecialchars($livre->afficherDetail()) ?>
            </div>
            <?php if ($messageEmprunt): ?>
    <div class="alert alert-<?= $messageEmprunt['type'] === 'succes' ? 'success' : 'danger' ?>">
        <?= htmlspecialchars($messageEmprunt['texte']) ?>
    </div>
<?php endif; ?>

            <?php if ($livre->estDisponible()): ?>
               <form method="post" action="">
    <input type="hidden" name="livre_id" value="<?= $livre->getId() ?>">
    <button type="submit" class="btn-emprunter">Emprunter ce livre</button>
</form>
            <?php else: ?>
                <span class="btn-indisponible">Aucun exemplaire disponible</span>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>
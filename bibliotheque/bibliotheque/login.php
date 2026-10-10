<?php
require_once 'includes/config.php';   
require_once 'classes/Database.php';
require_once 'classes/Etudiant.php';
require_once 'classes/Enseignant.php';
$erreur=[];
if($_SERVER['REQUEST_METHOD']==="POST"){
    $email=trim($_POST['email']??"");
    $password=$_POST['password']??"";
    if(empty($email)){
        $erreur[]="veuillez saisire email";
    }if(empty($password)){
        $erreur[]="veuillez saisire password";
    }
    if(empty($erreur)){
        $pdo=Database::getConnexion();
        $stmt=$pdo->prepare("SELECT*FROM utilisateurs where email=?");
        $stmt->execute([$email]);
        $resultat=$stmt->fetch(PDO::FETCH_ASSOC);
       if ($resultat === false || !Utilisateur::verifierMotDePasse($password,$resultat['mot_de_passe']) ){
            $erreur[]="email ou mot de pass invalid";
       }else{
        $_SESSION['user_id']=$resultat['id'];
        $_SESSION['user_nom']  = $resultat['nom'];
         $_SESSION['user_type'] = $resultat['type'];
                    if ($resultat['type'] === 'admin') {
        header("Location: /bibliotheque/bibliotheque/admin/dashboard.php");
    } else {
        header("Location: catalogue.php");
    }
    exit();
       }
  

    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Bibliothèque EMSI</title>
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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--navy);
            font-family: 'Inter', sans-serif;
            padding: 24px;
        }

        .panel {
            max-width: 780px;
            width: 100%;
            background: var(--cream);
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            min-height: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }

        .panel-side {
            background: var(--navy);
            color: var(--cream);
            flex: 0 0 38%;
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid var(--gold);
        }

        .panel-side .brand {
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 1.15rem;
        }

        .panel-side .brand span {
            display: block;
            font-size: 0.7rem;
            color: var(--gold);
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            letter-spacing: 0.08em;
            margin-top: 4px;
        }

        .panel-side blockquote {
            font-family: 'Lora', serif;
            font-style: italic;
            font-size: 1.05rem;
            line-height: 1.6;
            color: #DCD6C8;
            border-left: 2px solid var(--gold);
            padding-left: 16px;
            margin: 0;
        }

        .panel-form {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .panel-form h1 {
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 1.8rem;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .panel-form .subtitle {
            color: var(--grey);
            font-size: 0.92rem;
            margin-bottom: 24px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .form-control {
            border: 1px solid #D8D2C4;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 0.94rem;
            background: #fff;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184, 147, 74, 0.15);
        }

        .btn-connecter {
            background: var(--navy);
            color: var(--cream);
            border: none;
            border-radius: 4px;
            padding: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 8px;
            transition: background 0.2s ease;
        }

        .btn-connecter:hover {
            background: #1C2740;
            color: var(--gold);
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.88rem;
            color: var(--grey);
        }

        .register-link a {
            color: var(--navy);
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px solid var(--gold);
        }

        .alert-success-custom {
            background: #EAF3EA;
            border: 1px solid #8FBB8F;
            color: #2F5F2F;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 0.88rem;
            margin-bottom: 18px;
        }

        @media (max-width: 700px) {
            .panel { flex-direction: column; min-height: auto; }
            .panel-side { flex: none; border-right: none; border-bottom: 1px solid var(--gold); }
        }
    </style>
</head>
<body>

    <div class="panel">
        <div class="panel-side">
            <div class="brand">
                Bibliothèque
                <span>EMSI CASABLANCA</span>
            </div>
            <blockquote>
                « Un livre est un jardin qu'on porte dans sa poche. »
            </blockquote>
        </div>

        <div class="panel-form">
            <h1>Se connecter</h1>
            <p class="subtitle">Accédez à votre espace et vos emprunts.</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert-success-custom">
                    Inscription réussie. Vous pouvez vous connecter.
                </div>
            <?php endif; ?>

            <?php if (!empty($erreur)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($erreur as $e): ?>
                        <div><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <button type="submit" class="btn-connecter">Se connecter</button>

                <span class="register-link">
                    Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
                </span>
            </form>
        </div>
    </div>

</body>
</html>
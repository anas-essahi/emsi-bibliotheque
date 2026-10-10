<?php
require_once 'includes/config.php';   
require_once 'classes/Database.php';
require_once 'classes/Etudiant.php';
require_once 'classes/Enseignant.php';
$erreur=[];
if($_SERVER['REQUEST_METHOD']==="POST"){
    $nom=trim($_POST['nom']??"");
    $prenom=trim($_POST['prenom']??"");
    $email=($_POST['email']??"");
    $password=($_POST['password']??"");
    $confirmer=trim($_POST['password_confirm']??"");
    $type=$_POST['type'];
    if(empty($nom)){
        $erreur[]="VEUILLEZ SAISIR LE NOM";
   } if(empty($prenom)){
        $erreur[]="VEUILLEZ SAISIR LE PRENOM";
    }if(empty($email)){
        $erreur[]="VEUILLEZ SAISIR L'Email";
    }if(empty($password)){
        $erreur[]="VEUILLEZ SAISIR LE PASSWORD";
    }if(empty($confirmer)){
        $erreur[]="VEUILLEZ SAISIR LE PASSWORD A CONFIRMER";
    }if(empty($type)){
        $erreur[]="VEUILLEZ SAISIR LE TYPE";
    }if(!(filter_var($email,FILTER_VALIDATE_EMAIL))){
        $erreur[]="EMAIL INVALID";
    }if(strlen($password)<6){
        $erreur[]="PASSWORD DOIT CONTENIR AU MOINS 6 CARACTERE";
    }if(strlen($confirmer)<6){
        $erreur[]="PASSWORD A CONFIRMER DOIT CONTENIR AU MOINS 6 CARACTERE";
    }if($password!==$confirmer){
        $erreur[]="LES 2 PASSWORDS DOIT ETRE IDENTIQUE";
    }if($type!=="etudiant"&&$type!=="enseignant"){
        $erreur[]="LE TYPE DOIT ETRE ETUDIANT OU ENSEIGNANT";
    }
    if(empty($erreur)){
        $pdo=Database::getConnexion();
        $stmt=$pdo->prepare("SELECT id FROM utilisateurs where email=?");
        $stmt->execute([$email]);
        $resultat=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($resultat)){
            $erreur[]="CE EMAIL DEJA UTILISE.";
        }else{
            $password=password_hash($password,PASSWORD_DEFAULT);
            if($type==="etudiant"){
                $etudiant= new Etudiant($nom,$prenom,$email,$password);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type, quota_max) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$etudiant->getNom(),$etudiant->getPrenom(),$etudiant->getEmail(),$etudiant->getMotDePass(),$etudiant->getType(),$etudiant->getQuotaMax()]);
            }else{
                $enseignant=new Enseignant($nom,$prenom,$email,$password);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, type, quota_max) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$enseignant->getNom(),$enseignant->getPrenom(),$enseignant->getEmail(),$enseignant->getMotDePass(),$enseignant->getType(),$enseignant->getQuotaMax()]);
            }
            header("Location:login.php?success=1");
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
    <title>Inscription — Bibliothèque EMSI</title>
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
            max-width: 880px;
            width: 100%;
            background: var(--cream);
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            min-height: 560px;
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
            position: relative;
            border-right: 1px solid var(--gold);
        }

        .panel-side .brand {
            font-family: 'Lora', serif;
            font-weight: 600;
            font-size: 1.15rem;
            letter-spacing: 0.02em;
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
            margin-bottom: 28px;
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
            font-size: 0.94rem;
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

        .btn-inscrire {
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

        .btn-inscrire:hover {
            background: #1C2740;
            color: var(--gold);
        }

        .login-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 0.88rem;
            color: var(--grey);
        }

        .login-link a {
            color: var(--navy);
            font-weight: 500;
            text-decoration: none;
            border-bottom: 1px solid var(--gold);
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
                « Une bibliothèque, c'est une salle d'attente pour l'imaginaire. »
            </blockquote>
        </div>

        <div class="panel-form">
            <h1>Créer un compte</h1>
            <p class="subtitle">Accédez au catalogue et gérez vos emprunts.</p>
            <?php if (!empty($erreur)): ?>
           <div class="alert alert-danger">
           <?php foreach ($erreur as $e): ?>
           <ul>
            <li><?= htmlspecialchars($e) ?></li>
           </ul>
          <?php endforeach; ?>
         </div>
<?php endif; ?>

            <form action="" method="post">
                <div class="row-2 mb-3">
                    <div>
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" name="nom" id="nom" required>
                    </div>
                    <div>
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" name="prenom" id="prenom" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <div class="row-2 mb-3">
                    <div>
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" name="password" id="password" required minlength="6">
                    </div>
                    <div>
                        <label for="password_confirm" class="form-label">Confirmer</label>
                        <input type="password" class="form-control" name="password_confirm" id="password_confirm" required minlength="6">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Statut</label>
                    <select name="type" id="type" class="form-select">
                        <option value="etudiant">Étudiant</option>
                        <option value="enseignant">Enseignant</option>
                    </select>
                </div>

                <button type="submit" class="btn-inscrire">S'inscrire</button>

                <span class="login-link">
                    Déjà inscrit ? <a href="login.php">Se connecter</a>
                </span>
            </form>
        </div>
    </div>

</body>
</html>
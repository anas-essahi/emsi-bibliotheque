<?php
require_once __DIR__ . '/includes/admin-auth.php';
$id=(int)($_GET['id']??0);
if($id<=0){
      header("Location: gestion-utilisateurs.php");
    exit();
}
$pdo=Database::getConnexion();
$stmt=$pdo->prepare("SELECT*FROM utilisateurs where id=?");
$stmt->execute([$id]);
$utilisateur=$stmt->fetch(PDO::FETCH_ASSOC);
if($utilisateur===false){
      header("Location: gestion-utilisateurs.php");
    exit(); 
}
$stmt=$pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE  utilisateur_id = ?");
$stmt->execute([$id]);
$nb=$stmt->fetchColumn();
if($nb>0){
header("Location: gestion-utilisateurs.php?erreur=" . urlencode("Cet utilisateur a des emprunts associés, suppression impossible."));
    exit();
}
$stmt=$pdo->prepare("DELETE FROM utilisateurs where id=?");
$stmt->execute([$id]);
   header("Location: gestion-utilisateurs.php");
    exit(); 
<?php
require_once __DIR__ . '/includes/admin-auth.php';
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: gestion-livres.php");
    exit();
}
$pdo=Database::getConnexion();
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livreActuel = $stmt->fetch(PDO::FETCH_ASSOC);
if ($livreActuel === false) {
    header("Location: gestion-livres.php");
    exit();
}
$stmt=$pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE livre_id = ?");
$stmt->execute([$id]);
$nb=$stmt->fetchColumn();
if($nb>0){
header("Location: gestion-livres.php?erreur=" . urlencode("Ce livre a des emprunts associés, suppression impossible."));
    exit();
}
$stmt=$pdo->prepare("DELETE FROM livres where id=?");
$stmt->execute([$id]);
header("Location: gestion-livres.php");
exit();

<?php
require_once __DIR__. "/Livre.php";
require_once __DIR__. "/Utilisateur.php";
require_once __DIR__ . "/exceptions/LivreIndisponibleException.php";
require_once __DIR__ . "/exceptions/QuotaDepasseException.php";
class Emprunt{
    protected int $id;
    protected int $utilisateurId;
    protected int $livreId;
    protected string $dateEmprunt;
    protected string $dateRetourPrevue;
    protected ?string $dateRetourEffective;
    protected string $statut;

    public function __construct(int $utilisateurId,int $livreId)
    {
        $this->utilisateurId=$utilisateurId;
        $this->livreId=$livreId;
        $this->dateEmprunt=date('Y-m-d H:i:s');
        $this->statut="en_cours";
        $dateretour=new DateTime();
        $dateretour->modify("+14 days");
        $this->dateRetourPrevue=$dateretour->format('Y-m-d');
         $this->dateRetourEffective = null;
    }
        public function getId():int{
        return $this->id;
    }
    public function setId(int $id): void {
    if (isset($this->id)) {
        throw new Exception("L'id ne peut être modifié une fois défini.");
    }
    $this->id = $id;
}

public function getUtilisateurId():int{
    return $this->utilisateurId;
}
public function getLivreId():int{
     return $this->livreId;
}
public function getDateEmprunt():string{
    return $this->dateEmprunt;
}
public function getDateRetourPrevue():string{
    return $this->dateRetourPrevue;
}
public function getStatut():string{
    return $this->statut;
}
   public static function creer(int $utilisateurId, Livre $livre, PDO $pdo): Emprunt{
    if(!($livre->estDisponible())){
        throw new LivreIndisponibleException("LE LIVRE N EST PAS DISPONIBLE");
    }
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE utilisateur_id = ? AND statut = 'en_cours'");
    $stmt->execute([$utilisateurId]);
    $res=$stmt->fetch(PDO::FETCH_COLUMN);
    $stmt=$pdo->prepare("SELECT quota_max from utilisateurs where id=? ");
    $stmt->execute([$utilisateurId]);
    $quota=$stmt->fetch(PDO::FETCH_COLUMN);
    if($res>=$quota){
        throw new QuotaDepasseException("LE NOMBRE D EMPRUNTS EST DEPASSE LE QUOTA");
    }
    $e=new Emprunt($utilisateurId,$livre->getId());
    $pdo->beginTransaction();

try {
     $livre->emprunter();
    $stmt = $pdo->prepare("INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt, date_retour_prevue, statut) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $e->getUtilisateurId(),
        $e->getLivreId(),
        $e->getDateEmprunt(),
        $e->getDateRetourPrevue(),
        $e->getStatut()
    ]);
       
    $e->setId($pdo->lastInsertId());

    $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = ? WHERE id = ?");
    $stmt->execute([$livre->getExemplairesDisponibles(), $livre->getId()]);

    $pdo->commit();
    return $e;

} catch (Exception $ex) {
    $pdo->rollBack();
    throw $ex;
}
    

   }
   public static function retourner(int $empruntId, PDO $pdo): void{
    $stmt=$pdo->prepare("SELECT*FROM emprunts where id=?");
    $stmt->execute([$empruntId]);
    $res=$stmt->fetch(PDO::FETCH_ASSOC);
    if($res===false||$res['statut']!=='en_cours'){
        throw new Exception("Cet emprunt ne peut pas être retourné.");
    }
    $pdo->beginTransaction();
    try{
        $stmt=$pdo->prepare("UPDATE emprunts SET statut = 'retourne', date_retour_effective = ? WHERE id = ?");
        $stmt->execute([date('Y-m-d H:i:s'),$empruntId]);
        $stmt = $pdo->prepare("UPDATE livres SET exemplaires_disponibles = exemplaires_disponibles + 1 WHERE id = ?");
        $stmt->execute([$res['livre_id']]);
        $pdo->commit();
    }catch(Exception $ex){
        $pdo->rollBack();
        throw $ex;
    }
   }

}
<?php
 abstract class Utilisateur{
    protected int $id;
    protected string $nom;
    protected string $prenom;
    protected string $email;
    protected string $motDePasse;
    protected string $type;
    protected int $quotaMax;

    public function __construct(string $nom,string $prenom,string $email,string $motDePass,string $type,int $quotaMax)
    {
       $this->nom=$nom;
       $this->prenom=$prenom;
       $this->email=$email;
       $this->motDePasse=$motDePass;
       $this->type=$type;
       $this->quotaMax=$quotaMax;
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
    public function getNom():string{
        return $this->nom;
    }
    public function getPrenom():string{
        return $this->prenom;
    }
    public function getEmail() : string {
        return $this->email;
    }
    public function getMotDePass():string{
        return $this->motDePasse;
    }
    public function getType():string{
        return $this->type;
    }
    public function getQuotaMax():int{
        return $this->quotaMax;
    }
     abstract public function getRole(): string;
     public function peutEmprunter(int $nbEmpruntsEnCours): bool{
        return $this->quotaMax>$nbEmpruntsEnCours;
     }
     public static function  verifierMotDePasse(string $motDePasseSaisi, string $hash): bool{
        return password_verify($motDePasseSaisi,$hash);
     }
}
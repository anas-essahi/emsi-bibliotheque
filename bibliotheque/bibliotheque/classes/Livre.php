<?php
require_once __DIR__. "/Emprintable.php";
abstract class Livre implements Emprintable{
    protected int $id;
    protected string $titre;
    protected string $auteur;
    protected string $isbn;
    protected string $categorie;
    protected string $type;
    protected int $exemplairesTotal;
    protected int $exemplairesDisponibles;
    protected ?string $couverture = null;
    public function __construct(string $titre,string $auteur,string $isbn,string $categorie,string $type,int $exemplairesTotal,int $exemplairesDisponibles,?string $couverture )
    {
        $this->titre=$titre;
        $this->auteur=$auteur;
        $this->isbn=$isbn;
        $this->categorie=$categorie;
        $this->type=$type;
        $this->exemplairesTotal=$exemplairesTotal;
        $this->exemplairesDisponibles=$exemplairesDisponibles;
        $this->couverture=$couverture;
    }
    public function getTitre():string{
        return $this->titre;
    }
     public function getAuteur():string{
        return $this->auteur;
    }
     public function getIsbn():string{
        return $this->isbn;
    }
     public function getCategorie():string{
        return $this->categorie;
    }
     public function getType():string{
        return $this->type;
    }
     public function getExemplairesTotal():int{
        return $this->exemplairesTotal;
    }
     public function getExemplairesDisponibles():int{
        return $this->exemplairesDisponibles;
    }
     public function getCouverture():?string{
        return $this->couverture;
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
#[Override]
	public function estDisponible(): bool
    {
       return $this->exemplairesDisponibles>0;
    }
    #[Override]
    public function emprunter(): void
    {
       if(!($this->estDisponible())){
        throw new Exception("LE LIVRE N EST PAS DISPONIBLE");
       }
       $this->exemplairesDisponibles--;
    }
    #[Override]
    public function retourner(): void
    {
        $this->exemplairesDisponibles++;
    }
   abstract public function afficherDetail(): string;
}
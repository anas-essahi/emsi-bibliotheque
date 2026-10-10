<?php
require_once __DIR__. "/Livre.php";
class LivrePapier extends Livre{
   
    public function __construct(string $titre, string $auteur, string $isbn, string $categorie, int $exemplairesTotal, int $exemplairesDisponibles,? string $couverture)
    {
         parent::__construct($titre, $auteur, $isbn, $categorie, "papier", $exemplairesTotal, $exemplairesDisponibles, $couverture);
    }
    public function afficherDetail(): string {
        return sprintf(
            "Livre Physique (Papier) : %s par %s | ISBN : %s | Catégorie : %s | Stock : %d/%d",
            $this->titre,
            $this->auteur,
            $this->isbn,
            $this->categorie,
            $this->exemplairesDisponibles,
            $this->exemplairesTotal
        );
    }
}
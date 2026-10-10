<?php
require_once __DIR__. "/Livre.php";
 class EBook extends Livre{
   private ?string $fichierPdf;
    public function __construct(string $titre, string $auteur, string $isbn, string $categorie, int $exemplairesTotal, int $exemplairesDisponibles, ?string $couverture,?string $fichierPdf)
    {
         parent::__construct($titre, $auteur, $isbn, $categorie,"ebook" , $exemplairesTotal, $exemplairesDisponibles, $couverture);
         $this->fichierPdf=$fichierPdf;

    }
    
   public function afficherDetail(): string {
        return sprintf(
            "[E-Book Téléchargeable] %s par %s | ISBN: %s | Fichier: %s",
            $this->titre,
            $this->auteur,
            $this->isbn,
            $this->fichierPdf
        );
    }
    public function telecharger(): ?string{
        return $this->fichierPdf;
    }
}
<?php
require_once __DIR__. "/Utilisateur.php";
class Enseignant extends Utilisateur{
   
     public function __construct(string $nom, string $prenom, string $email, string $motDePass)
     {
         parent::__construct($nom, $prenom, $email, $motDePass, "enseignant", 5);
     }
    
     public function getRole(): string
     {
        return "enseignant";
     }
}
<?php

require_once __DIR__. "/Utilisateur.php";
class Etudiant extends Utilisateur{
   
     public function __construct(string $nom, string $prenom, string $email, string $motDePass)
     {
         parent::__construct($nom, $prenom, $email, $motDePass, "etudiant", 3);
     }
   
     public function getRole(): string
     {
        return "etudiant";
     }
}
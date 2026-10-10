<?php
interface Emprintable{
   public function emprunter(): void;
   public function retourner(): void;
   public function estDisponible(): bool;
}
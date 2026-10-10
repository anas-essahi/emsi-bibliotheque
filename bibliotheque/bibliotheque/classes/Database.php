<?php 
require_once __DIR__ . "/../includes/config.php";
class Database{
    private static ?PDO $pdo=null;
    private function __construct(){}
    public static function getConnexion():?PDO{
        if(self::$pdo===null){
            try{
               $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
                self::$pdo=new PDO($dsn,DB_USER,DB_PASS,[
                    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
                ]);
            }catch(PDOException $E){
                die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
            }
           
        }
        return self::$pdo;


    }
}
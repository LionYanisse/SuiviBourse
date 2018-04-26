<?php
/**
 * Fonctions MySQL & PDO
 *
 * @author Yanisse FEKRAOUI
 */
/** 
 * Classe d'accès aux données. 
 * Utilise les services de la classe PDO
 *
 */
class PdoLien
{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=jy5aeuq3';   		
      	private static $user='root';		
      	private static $mdp='';
	private static $monPdo;
	private static $monPdoLien = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct()
	{
        try {
    		PdoLien::$monPdo = new PDO(PdoLien::$serveur.';'.PdoLien::$bdd, PdoLien::$user, PdoLien::$mdp); 
		PdoLien::$monPdo->query("SET CHARACTER SET utf8");
                PdoLien::$monPdo->setAttribute(PDO::ATTR_ERRMODE,  PDO::ERRMODE_EXCEPTION);
	}
        catch (PDOException $e) {
            throw new Exception("Erreur à  la connexion \n" . $e->getMessage());
        }
        }
	public function _destruct(){
		PdoEmployes::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 *
 * Appel : $instancePdoEmployes = PdoEmployes::getPdoEmployes();
 * @return l'unique objet de la classe PdoEmployes
 */
    public  static function getPdoLien()
	{
		if(PdoLien::$monPdoLien == null)
		{
			PdoLien::$monPdoLien= new PdoLien();
		}
		return PdoLien::$monPdoLien;  
	}
          
   
    public function executer($requete) {
        return PdoLien::$monPdo->query($requete)->fetchAll(PDO::FETCH_ASSOC);
    }
	public function Connexion($email, $echelon, $lastmonth)
	{
		$req = "SELECT * FROM etudiants WHERE email = :pemail";
		$prep = PdoLien::$monPdo->prepare($req);
		$prep->bindValue(':pemail', $email, PDO::PARAM_STR);
		$prep->execute();
		$row = $prep->fetch(PDO::FETCH_COLUMN);
		if(!$row)
		{
			$this->InsererNouvelEtudiant($email, $echelon);
		}
	}
	public function InsererNouvelEtudiant($email, $echelon)
	{
		try
		{  
			$req = 'INSERT INTO etudiants(id,email,echelon)
			VALUES (NULL,:pemail,:pechelon)';
			$prep = PdoLien::$monPdo->prepare($req);
			$prep->bindValue(':pemail', $email, PDO::PARAM_STR);
			$prep->bindValue(':pechelon', $echelon, PDO::PARAM_INT);
			$prep->execute(); 
			$prep = NULL;
			$this->InsererNouvelleMensualite($this->lastInsertId());
		}   
		catch (PDOException $uneException) {
			echo "ERREUR .... " . $uneException->getMessage();
			exit();
		}
	}
	public function InsererNouvelleMensualite($idEtudiant)
	{
		try
		{  
			$req = 'INSERT INTO mensualites(id,idEtudiant,septembre,octobre,novembre,decembre,janvier,fevrier,mars,avril,mai,juin)
			VALUES (NULL,:pidetudiant,0,0,0,0,0,0,0,0,0,0)';
			$prep = PdoLien::$monPdo->prepare($req);
			$prep->bindValue(':pidetudiant', $idEtudiant, PDO::PARAM_INT);
			$prep->execute(); 
			$prep = NULL;
		}   
		catch (PDOException $uneException) {
			echo "ERREUR .... " . $uneException->getMessage();
			exit();
		}
	}
	public function retournerInformationsEtudiant($email, $champs)
	{
			 $req="select $champs from etudiants WHERE email = :pemail";
			 $prep = PdoLien::$monPdo->prepare($req);
			 $prep->bindValue(':pemail', $email, PDO::PARAM_STR);
			 $prep->execute();
			 $ligne = $prep->fetch(PDO::FETCH_ASSOC);
			 return $ligne;
	}
	public function retournerInformationsMensualite($idEtudiant, $champs)
	{
			 $req="select $champs from mensualites WHERE idEtudiant = :pid";
			 $prep = PdoLien::$monPdo->prepare($req);
			 $prep->bindValue(':pid', $idEtudiant, PDO::PARAM_INT);
			 $prep->execute();
			 $ligne = $prep->fetch(PDO::FETCH_ASSOC);
			 return $ligne;
	}
	public function mettreAJourMensualite($idEtudiant, $mois)
	{
		$req="UPDATE mensualites SET $mois=1 WHERE idEtudiant = :pid";
		$prep = PdoLien::$monPdo->prepare($req);
		$prep->bindValue(':pid', $idEtudiant, PDO::PARAM_INT);
		$prep->execute();  
	}
    public function lastInsertId(){
        return PdoLien::$monPdo->lastInsertId();
    }
}
?>
<?php
class Expediteur{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $id;
    public $personne_a_contacte;
    public $email;
    public $telephone;
    public $adresse;
    public $ville_id;
    public $active;
    public $expediteur;
    public $customer_id;
    public $affiliation_code;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    
    // createclientenattente
    function createclientenattente(){
    
       // create query

$query="INSERT INTO `tblrejoindreexpediteur`
(`id`, `societe`, `personne_a_contacte`, `email`, `telephone`, `adresse`, `complement_adresse`,
 `code_postale`, `ville_id`, `pays`, `secteur_activite`,
  `horaires`, `message`, `active`,`expediteur`,`affiliation_code`) VALUES(:id,:personne_a_contacte,:personne_a_contacte,:email,:telephone
  ,:adresse,'',0,:ville_id,'','','','',0,NULL,:affiliation_code)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id),ENT_QUOTES, 'UTF-8');
        $this->personne_a_contacte=htmlspecialchars(strip_tags($this->personne_a_contacte),ENT_QUOTES, 'UTF-8');
        $this->email=htmlspecialchars(strip_tags($this->email),ENT_QUOTES, 'UTF-8');
        $this->telephone=htmlspecialchars(strip_tags($this->telephone),ENT_QUOTES, 'UTF-8');
        $this->adresse=htmlspecialchars(strip_tags($this->adresse),ENT_QUOTES, 'UTF-8');
        $this->ville_id=htmlspecialchars(strip_tags($this->ville_id),ENT_QUOTES, 'UTF-8');
        $this->affiliation_code=htmlspecialchars(strip_tags($this->affiliation_code),ENT_QUOTES, 'UTF-8');
    
        // bind id of record to add
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":personne_a_contacte", $this->personne_a_contacte);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":adresse", $this->adresse);
        $stmt->bindParam(":ville_id", $this->ville_id);
        $stmt->bindParam(":affiliation_code", $this->affiliation_code);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


      // getlastid
public function getlastid(){
    $query = "SELECT id+1 as 'id' FROM `tblrejoindreexpediteur` ORDER by id DESC LIMIT 1";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'];
}
function check_client_existe($username){
    
    $query="SELECT id as 'customer_id'  FROM tblrejoindreexpediteur where email=? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['customer_id'];
}


   
}
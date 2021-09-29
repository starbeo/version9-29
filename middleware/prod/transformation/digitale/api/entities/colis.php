<?php
class Colis{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $id;
    public $codeenvoi;
    public $destinataire;
    public $crbt;
    public $telephone;
    public $adresse;
    public $ville_id;
    public $commentaire;
    public $client_id;
    public $num_commande;

    //
    public $livreur_id;
    public $colis_id;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    
    // createcolisenattente
    function createcolisenattente(){
    
       // create query
        $query="INSERT INTO `tblcolisenattente`(id,`code_barre`,`num_commande`, `nom_complet`, `crbt`, `telephone`, `adresse`, `quartier`, `ville`, `etat_id`, `status_id`, `date_creation`, `commentaire`, `id_expediteur`, `colis_id`, `num_bonlivraison`, `id_entreprise`)
        VALUES (:id,:codeenvoi,:num_commande,:destinataire,:crbt,:telephone,:adresse,0,:ville_id,1,12,Now(),:commentaire,:client_id,NULL,NULL,0)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->codeenvoi=htmlspecialchars(strip_tags($this->codeenvoi),ENT_QUOTES, 'UTF-8');
        $this->destinataire=htmlspecialchars(strip_tags($this->destinataire),ENT_QUOTES, 'UTF-8');
        $this->crbt=htmlspecialchars(strip_tags($this->crbt),ENT_QUOTES, 'UTF-8');
        $this->telephone=htmlspecialchars(strip_tags($this->telephone),ENT_QUOTES, 'UTF-8');
        $this->adresse=htmlspecialchars(strip_tags($this->adresse),ENT_QUOTES, 'UTF-8');
        $this->ville_id=htmlspecialchars(strip_tags($this->ville_id),ENT_QUOTES, 'UTF-8');
        $this->commentaire=htmlspecialchars(strip_tags($this->commentaire),ENT_QUOTES, 'UTF-8');
        $this->client_id=htmlspecialchars(strip_tags($this->client_id),ENT_QUOTES, 'UTF-8');
        $this->id=htmlspecialchars(strip_tags($this->id),ENT_QUOTES, 'UTF-8');
    
        // bind id of record to add
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":num_commande", $this->codeenvoi);
        $stmt->bindParam(":destinataire", $this->destinataire);
        $stmt->bindParam(":crbt", $this->crbt);
        $stmt->bindParam(":telephone", $this->telephone);
        $stmt->bindParam(":adresse", $this->adresse);
        $stmt->bindParam(":ville_id", $this->ville_id);
        $stmt->bindParam(":commentaire", $this->commentaire);
        $stmt->bindParam(":client_id", $this->client_id);
        //traitement code envoi 
        $CodeEnvoigenerer = "";
        $alias = 'PWC';
        $CodeEnvoigenerer .= $alias;
        $CodeEnvoigenerer .= $this->client_id;
        $CodeEnvoigenerer .= 'MA';
        $CodeEnvoigenerer .= $this->getNombreColis($this->client_id);
        $stmt->bindParam(":codeenvoi", $CodeEnvoigenerer);
        
        // execute query
        if($stmt->execute()){
            $this->update_Nombre_Colis($this->client_id);
            return true;
        }
    
        return false;
    }


    // updatecolisenattente
    function updatecolisenattente(){
    
        // update query
         $query="update tblcolisenattente set nom_complet=:destinataire,crbt=:crbt,telephone=:telephone,
         adresse=:adresse,commentaire=:commentaire where id=:id ";
         // prepare query
         $stmt = $this->conn->prepare($query);
     
         // sanitize
         $this->destinataire=htmlspecialchars(strip_tags($this->destinataire));
         $this->crbt=htmlspecialchars(strip_tags($this->crbt));
         $this->telephone=htmlspecialchars(strip_tags($this->telephone));
         $this->adresse=htmlspecialchars(strip_tags($this->adresse));
         $this->id=htmlspecialchars(strip_tags($this->id));
         $this->commentaire=htmlspecialchars(strip_tags($this->commentaire));
     
         // bind id of record to add
         $stmt->bindParam(":id", $this->id);
         $stmt->bindParam(":destinataire", $this->destinataire);
         $stmt->bindParam(":crbt", $this->crbt);
         $stmt->bindParam(":telephone", $this->telephone);
         $stmt->bindParam(":adresse", $this->adresse);
         $stmt->bindParam(":commentaire", $this->commentaire);
         // execute query
         if($stmt->execute()){
             return true;
         }
     
         return false;
     }



     // enregitrerappel
    function enregitrerappel($livreur_id,$code_envoi){

        //récupérer client et colis id 

        $queryselect = " SELECT id as 'colis_id',id_expediteur as 'client_id' FROM `tblcolis` where code_barre=?
                    ORDER by tblcolis.id desc
                    LIMIT 0,1 ";
        // prepare query statement
        $stmtselect = $this->conn->prepare( $queryselect );
         // bind id of product to be updated
        $stmtselect->bindParam(1,$code_envoi);
        // execute query
        $stmtselect->execute();
        // get retrieved row
        $row = $stmtselect->fetch(PDO::FETCH_ASSOC);
        $client_id = $row['client_id'];
        $colis_id = $row['colis_id'];
    
        // insertion enregistrement appele 
         $query="INSERT INTO `tblappelslivreur`(`livreur_id`, `client_id`, `colis_id`, `date_created`, `id_entreprise`) 
         VALUES (:livreur_id,:client_id,:colis_id,Now(),0) ";
         // prepare query
         $stmt = $this->conn->prepare($query);
     
         // bind id of record to add
         $stmt->bindParam(":livreur_id", $livreur_id);
         $stmt->bindParam(":client_id", $client_id);
         $stmt->bindParam(":colis_id", $colis_id);
         // execute query
         if($stmt->execute()){
             return true;
         }
     
         return false;
     }


      // positionlivreur
    function positionlivreur($latitude,$longitude,$staffid){

        
        // update positionlivreur
         $query="update tblstaff set latitude=:latitude , longitude=:longitude where staffid=:staffid ";
         // prepare query
         $stmt = $this->conn->prepare($query);
     
         // bind id of record to add
         $stmt->bindParam(":latitude", $latitude);
         $stmt->bindParam(":longitude", $longitude);
         $stmt->bindParam(":staffid", $staffid);
         // execute query
         if($stmt->execute()){
             return true;
         }
     
         return false;
     }

      // getlastid
public function getlastid(){
    $query = "SELECT id+1 as 'id' FROM `tblcolisenattente` ORDER by id DESC LIMIT 1";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'];
}

  // getNombreColis
public function getNombreColis($client_id){
    $query = "SELECT nbr_colis+1 as nombrecolis FROM `tblexpediteurs` where id  =?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $client_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row['nombrecolis']==null)
    {
        $row['nombrecolis']=1;
        $this->initialiser_Nombre_Colis($client_id);
    }
    return $row['nombrecolis'];
}
//update_Nombre_Colis
function update_Nombre_Colis($client_id){
 
    // delete query
    $query = "UPDATE tblexpediteurs SET nbr_colis=nbr_colis+1  WHERE id =?";
    // prepare query
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $client_id);
    // execute query
    $stmt->execute();
     
}

//update_Nombre_Colis
function initialiser_Nombre_Colis($client_id){
 
    // delete query
    $query = "UPDATE tblexpediteurs SET nbr_colis=1  WHERE id =?";
    // prepare query
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $client_id);
    // execute query
    $stmt->execute();
     
}

//update_Nombre_Colis
function update_notification_Client($notification_id){
 
    // delete query
    $query = "UPDATE tblnotificationscustomer SET isread=1  WHERE tblnotificationscustomer.id =?";
    // prepare query
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $notification_id);
    // execute query
    if($stmt->execute()){

        return true;
    }
    return false;
     
}

}
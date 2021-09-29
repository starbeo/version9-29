<?php
class Reclamation{
 
    // database connection and table name
    private $conn;
 
    // object properties
    public $id;
    public $objet;
    public $message;
    public $reponse;
    public $etat;
    public $relation_id;
    public $date_created;
    public $staff_id;
    public $date_traitement;
    public $id_entreprise;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    
    // createreclamation
    function createreclamation(){
    
       // create query
        $query ="INSERT INTO `tblreclamations`( id,`objet`, `message`, `reponse`, `etat`, `relation_id`, `date_created`, `staff_id`, `date_traitement`, `id_entreprise`)
         VALUES (:id,:objet,:message,'',0,:relation_id,Now(),NULL,NULL,0)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->objet=htmlspecialchars(strip_tags($this->objet));
        $this->message=htmlspecialchars(strip_tags($this->message));
        $this->relation_id=htmlspecialchars(strip_tags($this->relation_id));
        $this->id=htmlspecialchars(strip_tags($this->id));
    
        // bind id of record to add
        $stmt->bindParam(":id",$this->id);
        $stmt->bindParam(":objet", $this->objet);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":relation_id", $this->relation_id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }


     // getlastid
public function getlastid(){
    $query = "SELECT id+1 as 'id' FROM `tblreclamations` ORDER by id DESC LIMIT 0,1";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['id'];
}



    // create_log_activity
    function create_log_activity($message){
    
        // create query
         $query ="INSERT INTO `tblreclamations`( id,`objet`, `message`, `reponse`, `etat`, `relation_id`, `date_created`, `staff_id`, `date_traitement`, `id_entreprise`)
          VALUES (:id,:objet,:message,'',0,:relation_id,Now(),NULL,NULL,0)";
          $query="";
         // prepare query
         $stmt = $this->conn->prepare($query);
     
         // sanitize
         $this->objet=htmlspecialchars(strip_tags($this->objet));
         $this->message=htmlspecialchars(strip_tags($this->message));
         $this->relation_id=htmlspecialchars(strip_tags($this->relation_id));
         $this->id=htmlspecialchars(strip_tags($this->id));
     
         // bind id of record to add
         $stmt->bindParam(":id",$this->id);
         $stmt->bindParam(":objet", $this->objet);
         $stmt->bindParam(":message", $this->message);
         $stmt->bindParam(":relation_id", $this->relation_id);
     
         // execute query
         $stmt->execute();
     }


     
    // update_colis
    function update_colis($message){
    
        // create query
         $query ="INSERT INTO `tblreclamations`( id,`objet`, `message`, `reponse`, `etat`, `relation_id`, `date_created`, `staff_id`, `date_traitement`, `id_entreprise`)
          VALUES (:id,:objet,:message,'',0,:relation_id,Now(),NULL,NULL,0)";
          $query="";
         // prepare query
         $stmt = $this->conn->prepare($query);
     
         // sanitize
         $this->objet=htmlspecialchars(strip_tags($this->objet));
         $this->message=htmlspecialchars(strip_tags($this->message));
         $this->relation_id=htmlspecialchars(strip_tags($this->relation_id));
         $this->id=htmlspecialchars(strip_tags($this->id));
     
         // bind id of record to add
         $stmt->bindParam(":id",$this->id);
         $stmt->bindParam(":objet", $this->objet);
         $stmt->bindParam(":message", $this->message);
         $stmt->bindParam(":relation_id", $this->relation_id);
     
         // execute query
         $stmt->execute();
     }

}
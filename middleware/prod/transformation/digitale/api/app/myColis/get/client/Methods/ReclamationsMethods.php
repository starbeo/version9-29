<?php
class ReclamationsMethods{
 
    // database connection and table name
    private $conn;
    private $table_name = "tblreclamations";
 
    // object properties
    public $id;
    public $objet;
    public $message;
    public $reponse;
    public $etat;
    public $relation_id;
    public $date_created;
    public $date_traitement;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

// read GetAllReclamationTraiteByExpediteurPaging
public function GetAllReclamationTraiteByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblreclamations.`id`,`objet`,`message`,`reponse`,`etat`,`relation_id`,tblreclamations.`date_created`,`date_traitement`
            FROM `tblreclamations`,tblexpediteurs WHERE etat=1 and `relation_id`=?
            and tblexpediteurs.password=?
            and tblreclamations.relation_id=tblexpediteurs.id
            ORDER by id desc
            LIMIT ?, ?";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2, $tocken);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}


// read GetAllReclamationNonTraiteByExpediteurPaging
public function GetAllReclamationNonTraiteByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblreclamations.`id`,`objet`,`message`,`reponse`,`etat`,`relation_id`,tblreclamations.`date_created`,`date_traitement`
    FROM `tblreclamations`,tblexpediteurs WHERE etat=0 and `relation_id`=?
    and tblexpediteurs.password=?
    and tblreclamations.relation_id=tblexpediteurs.id
    ORDER by id desc
    LIMIT ?, ?";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2, $tocken);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}

  // used for paging nombrereclamationtraite
  public function nombrereclamationtraite($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblreclamations where etat=1 and  relation_id=?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

  // used for paging nombrereclamationnontraite
  public function nombrereclamationnontraite($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblreclamations where etat=0 and  relation_id=?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

public  function etat($etat_id)
{
    $etat="";
    switch ($etat_id) {
        case 0:
        $etat= "Non Traité";
        return $etat;
            break;
        case 1:
        $etat= "Traité";
        return $etat;
            break;    
    }
}

// readone 
function readOneByid($id){

    $query = "SELECT `id`,`objet`,`message`,`reponse`,`etat`,`relation_id`,`date_created`,`date_traitement`
              FROM `tblreclamations` WHERE `id`=?
              ORDER by id desc
              LIMIT 0,1 ";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties

    $this->id = $row['id'];
    $this->objet = $row['objet'];
    $this->message = $row['message'];
    $this->reponse = $row['reponse'];
    $this->etat = $row['etat'];
    $this->date_created = $row['date_created'];
    $this->date_traitement=$row['date_traitement'];
}

}
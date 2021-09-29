<?php
class FactureMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $facture_id;
    public $nom;
    public $status;
    public $type;
    public $total_crbt;
    public $total_frais;
    public $total_refuse;
    public $total_net;
    public $commentaire;
    public $date_created;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function getFacturesByNom($client_id,$tocken,$nomfacture){

        $query = "SELECT tblfactures.id as 'facture_id',tblfactures.nom,tblfactures.type,tblfactures.status,total_crbt,total_frais,
                    total_refuse,total_net,commentaire,DATE_FORMAT(tblfactures.date_created,'%d-%m-%Y') as date_created
                    FROM tblfactures,tblexpediteurs where tblfactures.id_expediteur=tblexpediteurs.id 
                    and tblexpediteurs.id=?
                    and tblexpediteurs.password=?
                    and tblfactures.nom=?
                    ORDER by tblfactures.id desc
                    LIMIT 0, 1";
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind id of product to be updated
       $stmt->bindParam(1,$client_id);
       $stmt->bindParam(2,$tocken);
       $stmt->bindParam(3,$nomfacture);
     
        // execute query
        $stmt->execute();
     
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        // set values to object properties

        $this->facture_id = $row['facture_id'];
        $this->nom = $row['nom'];
        $this->status = $row['status'];
        $this->type = $row['type'];
        $this->total_crbt = $row['total_crbt'];
        $this->total_frais = $row['total_frais'];
        $this->total_refuse = $row['total_refuse'];
        $this->total_net = $row['total_net'];
        $this->commentaire = $row['commentaire'];
        $this->date_created = $row['date_created'];
    }




    // read GetAllFacturesByExpediteurPaging
public function GetAllFacturesByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblfactures.id as 'facture_id',tblfactures.nom,tblfactures.type,tblfactures.status,total_crbt,total_frais,
            total_refuse,total_net,commentaire, DATE_FORMAT(tblfactures.date_created,'%d-%m-%Y') as date_created
            FROM tblfactures,tblexpediteurs where tblfactures.id_expediteur=tblexpediteurs.id 
            and tblexpediteurs.id=?
            and tblexpediteurs.password=?
            ORDER by tblfactures.id desc
            LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2,$tocken);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
    
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}



 // read GetAllFacturesLivrerByExpediteurPaging
 public function GetAllFacturesLivrerByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblfactures.id as 'facture_id',tblfactures.nom,tblfactures.type,tblfactures.status,total_crbt,total_frais,
            total_refuse,total_net,commentaire,DATE_FORMAT(tblfactures.date_created,'%d-%m-%Y') as date_created
            FROM tblfactures,tblexpediteurs where tblfactures.id_expediteur=tblexpediteurs.id 
            and  tblfactures.type=2 
            and tblfactures.status=2
            and tblexpediteurs.id=?
            and tblexpediteurs.password=?
            ORDER by tblfactures.id desc
            LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2,$tocken);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
    
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}



// read GetAllFacturesRetournerByExpediteurPaging
public function GetAllFacturesRetournerByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblfactures.id as 'facture_id',tblfactures.nom,tblfactures.type,tblfactures.status,total_crbt,total_frais,
            total_refuse,total_net,commentaire,DATE_FORMAT(tblfactures.date_created,'%d-%m-%Y') as date_created
            FROM tblfactures,tblexpediteurs where tblfactures.id_expediteur=tblexpediteurs.id 
            and  tblfactures.type=3
            and tblexpediteurs.id=?
            and tblexpediteurs.password=?
            ORDER by tblfactures.id desc
            LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$id);
    $stmt->bindParam(2,$tocken);
    $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
    
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}


// read GetAllFacturesItemsByExpediteurPaging
public function GetAllFacturesItemsByExpediteurPaging($from_record_num, $records_per_page,$client_id,$tocken,$facture_id){
 
    // select query
    $query = "SELECT tblcolis.id as 'colis_id',tblcolis.code_barre,
    DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage, 
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour ,
                    tblcolis.crbt,tblcolis.frais
                    FROM tblcolisfacture,tblcolis,tblexpediteurs  where tblcolisfacture.colis_id=tblcolis.id  
                    and tblexpediteurs.id=tblcolis.id_expediteur 
                    and tblexpediteurs.id=? 
                    and tblexpediteurs.password=?
                    and tblcolisfacture.facture_id=? 
                    ORDER by tblcolisfacture.id desc
                    LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$client_id);
    $stmt->bindParam(2,$tocken);
    $stmt->bindParam(3,$facture_id);
    $stmt->bindParam(4, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(5, $records_per_page, PDO::PARAM_INT);
    
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}


// used for paging nombrefactures
public function nombrefactures($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblfactures  WHERE tblfactures.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombrefacturesLivrer
public function nombrefacturesLivrer($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblfactures  WHERE tblfactures.id_expediteur =? and tblfactures.type=2  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrefacturesLivrer
public function nombrefacturesRetourner($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblfactures  WHERE tblfactures.id_expediteur =? and tblfactures.type=3  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombrefacturesItems
public function nombrefacturesItems($client_id,$facture_id){
    $query = "SELECT  count(*) as 'total_rows'
                FROM tblcolisfacture,tblcolis,tblexpediteurs  where tblcolisfacture.colis_id=tblcolis.id  
                and tblexpediteurs.id=tblcolis.id_expediteur 
                and tblexpediteurs.id=? 
                and tblcolisfacture.facture_id=? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $client_id);
    $stmt->bindParam(2, $facture_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


public  function type_facture($type_facture)
{
    $type="";
    switch ($type_facture) {
        case 2:
        $type= "Livré";
        return $type;
            break;
        case 3:
        $type= "Retourné";
        return $type;
            break;
            
    }
}


public  function statut_facture($statut_facture)
{
    $statut="";
    switch ($statut_facture) {
        case 1:
        $statut= "Non Réglé";
        return $statut;
            break;
        case 2:
        $statut= "Réglé";
        return $statut;
            break;
            
    }
}






}
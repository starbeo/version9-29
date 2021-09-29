<?php
class EtatColisLivrerMethods{
 
    // database connection 
    private $conn;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }






    // read GetAllEtatColisLivrerPaging
public function GetAllEtatColisLivrerPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT  tbletatcolislivre.id,tbletatcolislivre.nom,tbletatcolislivre.total,tbletatcolislivre.total_received,
                    tbletatcolislivre.manque,tbletatcolislivre.commision,tbletatcolislivre.etat,
                    tbletatcolislivre.status,tbletatcolislivre.justif,DATE_FORMAT(tbletatcolislivre.date_created,'%d-%m-%Y') as date_created
                    from tbletatcolislivre,tblstaff  
                    where tblstaff.staffid=? 
                    and tblstaff.password=?
                    and tblstaff.staffid=tbletatcolislivre.id_livreur 
                    ORDER by tbletatcolislivre.id desc
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


    // read GetAllversementPaging
    public function GetAllversementPaging($from_record_num, $records_per_page,$etat_colis_livre_id,$tocken){
 
        // select query
        $query = "SELECT tbllivreurversements.name,total, DATE_FORMAT(date_created,'%d-%m-%Y') date_created FROM tbllivreurversements,tblstaff 
                        where tbllivreurversements.etat_colis_livre_id=? 
                        and tbllivreurversements.livreur_id=tblstaff.staffid 
                        and tblstaff.password =?
                        ORDER by tbllivreurversements.id desc
                        LIMIT ?, ? ";
     
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind variable values
        $stmt->bindParam(1,$etat_colis_livre_id);
        $stmt->bindParam(2,$tocken);
        $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
        
     
        // execute query
        $stmt->execute();
     
        // return values from database
        return $stmt;
    }


     // read GetAllItemsEtatColisLivrerPaging
     public function GetAllItemsEtatColisLivrerPaging($from_record_num, $records_per_page,$etat_colis_livre_id,$tocken){
 
        // select query
        $query = "SELECT tblcolis.id as 'colis_id',tblcolis.code_barre,tblcolis.num_commande, 
                        tblcolis.crbt ,tblcolis.nom_complet,tblcolis.telephone
                        FROM tbletatcolislivreitems,tblcolis,tblstaff
                        where tbletatcolislivreitems.etat_id=? 
                        and tblcolis.id=tbletatcolislivreitems.colis_id 
                        and tblstaff.staffid=tblcolis.livreur
                        and tblstaff.password=?
                        ORDER by tblcolis.id desc
                        LIMIT ?, ? ";
     
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind variable values
        $stmt->bindParam(1,$etat_colis_livre_id);
        $stmt->bindParam(2,$tocken);
        $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
        
     
        // execute query
        $stmt->execute();
     
        // return values from database
        return $stmt;
    }

// used for paging nombreetatcolislivrer
public function nombreetatcolislivrer($id_livreur){
    $query = "SELECT  count(*) as 'total_rows' 
                    from tbletatcolislivre,tblstaff  
                    where tblstaff.staffid=? 
                    and tblstaff.staffid=tbletatcolislivre.id_livreur  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id_livreur);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombreversementEtatColisLivrer
public function nombreversementEtatColisLivrer($etat_colis_livre_id){
    $query = "SELECT  count(*) as 'total_rows' 
                    FROM `tbllivreurversements` where etat_colis_livre_id=?  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $etat_colis_livre_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombreetatcolislivrerItems
public function nombreetatcolislivrerItems($etat_colis_livre_id){
    $query = "SELECT  count(*) as 'total_rows'
                FROM tbletatcolislivreitems,tblcolis,tblstaff
                where tbletatcolislivreitems.etat_id=? 
                and tblcolis.id=tbletatcolislivreitems.colis_id 
                and tblstaff.staffid=tblcolis.livreur ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $etat_colis_livre_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


public  function etat_etatcolislivrer($etat_etatcolislivrer)
{
    $etat="";
    switch ($etat_etatcolislivrer) {
        case 1:
        $etat= "Non Réglé";
        return $etat;
            break;
        case 2:
        $etat= "Réglé";
        return $etat;
            break;
            
    }
}


public  function statut_etatcolislivrer($statut_etatcolislivrer)
{
    $statut="";
    switch ($statut_etatcolislivrer) {
        case 1:
        $statut= "En attente";
        return $statut;
            break;
        case 2:
        $statut= "Valider";
        return $statut;
            break;
            
    }

    
}


public  function type_bonlivraison($type_bonlivraison)
{
    $type="";
    switch ($type_bonlivraison) {
        case 1:
        $type= "Sortie";
        return $type;
            break;
        case 2:
        $type= "Retourné";
        return $type;
            break;
            
    }
}


 // read GetAllversementListPaging
    public function GetAllversementListPaging($from_record_num, $records_per_page,$livreur_id,$tocken){
 
        // select query
        $query = "SELECT tbllivreurversements.etat_colis_livre_id,tbllivreurversements.name,total,DATE_FORMAT(date_created,'%d-%m-%Y') as date_created FROM tbllivreurversements,tblstaff 
                    where 
                tbllivreurversements.livreur_id=tblstaff.staffid
                and tblstaff.staffid=?
                and tblstaff.password =?
                ORDER by tbllivreurversements.id desc
                LIMIT ?, ? ";
     
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind variable values
        $stmt->bindParam(1,$livreur_id);
        $stmt->bindParam(2,$tocken);
        $stmt->bindParam(3, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(4, $records_per_page, PDO::PARAM_INT);
        
     
        // execute query
        $stmt->execute();
     
        // return values from database
        return $stmt;
    }
    
    
    // used for paging nombreversement
public function nombreversement($livreur_id){
    $query = "SELECT  count(*) as 'total_rows' 
                    FROM `tbllivreurversements` where tbllivreurversements.livreur_id=?  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $livreur_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}






}
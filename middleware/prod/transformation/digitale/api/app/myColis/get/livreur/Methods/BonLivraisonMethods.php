<?php
class BonLivraisonMethods{
 
    // database connection 
    private $conn;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }






    // read GetAllBonLivraisonByExpediteurPaging
public function GetAllBonLivraisonPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblbonlivraison.id,tblbonlivraison.nom,DATE_FORMAT(tblbonlivraison.date_created,'%d-%m-%Y') as date_created,tblbonlivraison.type,tblbonlivraison.status
                    FROM tblbonlivraison,tblstaff 
                    where id_livreur=? and tblbonlivraison.id_livreur=tblstaff.staffid 
                    and tblstaff.password=?
                    ORDER by tblbonlivraison.id desc
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


   // read GetAllBonLivraisonSortiePaging
   public function GetAllBonLivraisonSortiePaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblbonlivraison.id,tblbonlivraison.nom,DATE_FORMAT(tblbonlivraison.date_created,'%d-%m-%Y') as date_created ,tblbonlivraison.type,tblbonlivraison.status
                    FROM tblbonlivraison,tblstaff 
                    where id_livreur=? 
                    and tblbonlivraison.id_livreur=tblstaff.staffid 
                    and tblstaff.password=?
                    and tblbonlivraison.type=1
                    ORDER by tblbonlivraison.id desc
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

 // read GetAllBonLivraisonRetournerPaging
 public function GetAllBonLivraisonRetournerPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblbonlivraison.id,tblbonlivraison.nom,DATE_FORMAT(tblbonlivraison.date_created,'%d-%m-%Y') as date_created ,tblbonlivraison.type,tblbonlivraison.status
                    FROM tblbonlivraison,tblstaff 
                    where id_livreur=? and tblbonlivraison.id_livreur=tblstaff.staffid 
                    and tblstaff.password=?
                    and tblbonlivraison.type=2
                    ORDER by tblbonlivraison.id desc
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



// read GetAllBonLivraisonItemsPaging
public function GetAllBonLivraisonItemsPaging($from_record_num, $records_per_page,$livreur_id,$tocken,$bonlivraison_id){
 
    // select query
    $query = "SELECT tblcolis.id as 'colis_id',tblcolis.code_barre,tblcolis.num_commande,
                tblcolis.crbt ,tblcolis.nom_complet,tblcolis.telephone,tblcolis.status_reel as 'status_reel'
                FROM tblcolisbonlivraison,tblcolis ,tblstaff
                where 
                tblstaff.staffid=tblcolis.livreur
                and tblcolis.livreur=?
                and tblcolisbonlivraison.colis_id=tblcolis.id 
                and tblstaff.password=?
                and tblcolisbonlivraison.bonlivraison_id=?
                    ORDER by tblcolis.id asc
                    LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$livreur_id);
    $stmt->bindParam(2,$tocken);
    $stmt->bindParam(3,$bonlivraison_id);
    $stmt->bindParam(4, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(5, $records_per_page, PDO::PARAM_INT);
    
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}


// used for paging nombrebonlivraison
public function nombrebonlivraison($id_livreur){
    $query = "SELECT  count(*) as 'total_rows' from  tblbonlivraison,tblstaff 
              WHERE  tblbonlivraison.id_livreur=tblstaff.staffid and id_livreur=?  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id_livreur);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrebonlivraisonSortie
public function nombrebonlivraisonSortie($id_livreur){
    $query = "SELECT  count(*) as 'total_rows' from  tblbonlivraison,tblstaff 
              WHERE  tblbonlivraison.id_livreur=tblstaff.staffid 
              and id_livreur=? and tblbonlivraison.type=1 ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id_livreur);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombrebonlivraison
public function nombrebonlivraisonretourner($id_livreur){
    $query = "SELECT  count(*) as 'total_rows' from  tblbonlivraison,tblstaff 
              WHERE  tblbonlivraison.id_livreur=tblstaff.staffid 
              and id_livreur=? and tblbonlivraison.type=2 ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id_livreur);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}






// used for paging nombrefacturesItems
public function nombrebonlivraisonItems($livreur_id,$bonlivraison_id){
    $query = "SELECT   count(*) as 'total_rows'
                FROM tblcolisbonlivraison,tblcolis ,tblstaff
                where 
                tblstaff.staffid=tblcolis.livreur
                and tblcolis.livreur=?
                and tblcolisbonlivraison.colis_id=tblcolis.id 
                and tblcolisbonlivraison.bonlivraison_id=?  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $livreur_id);
    $stmt->bindParam(2, $bonlivraison_id);
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


public  function statuts($statut_id)
{
    $statut="";
    switch ($statut_id) {
        case 1:
        $statut= "En cours";
        return $statut;
            break;
        case 2:
        $statut= "Livré";
        return $statut;
            break;
        case 3:
        $statut= "Retourné";
        return $statut;
            break;
        case 4:
        $statut= "Expédié";
        return $statut;
            break;
        case 5:
        $statut= "Ramassé";
        return $statut;
            break;
        case 6:
        $statut= "Pas  de réponse";
        return $statut;
            break;
        case 7:
        $statut= "Injoignable";
        return $statut;
            break;
        case 8:
        $statut= "Numéro erroné";
        return $statut;
            break;
        case 9:
        $statut= "Refusé";
        return $statut;
            break;
        case 10:
        $statut= "Annulé";
        return $statut;
            break;
        case 11:
        $statut= "Reporté";
        return $statut;
            break;
        case 12:
        $statut= "Attente de ramassage";
        return $statut;
            break;
        case 13:
        $statut= "Retourner à l'agence";
        return $statut;
             break;
    }
}





}
<?php
class BonLivraisonMethods{
 
    // database connection 
    private $conn;
    // object properties
    public $id;
    public $nom;
    public $date_created;


 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }


    function getBonLivraisonByNom($client_id,$tocken,$nombonlivraison){

        $query = "SELECT tblbonlivraisoncustomer.id,tblbonlivraisoncustomer.nom, DATE_FORMAT(tblbonlivraisoncustomer.date_created,'%d-%m-%Y') as date_created
                        FROM tblbonlivraisoncustomer,tblexpediteurs 
                        where tblexpediteurs.id=tblbonlivraisoncustomer.id_expediteur
                        and tblbonlivraisoncustomer.id_expediteur=?
                        and  tblexpediteurs.password=?
                        and tblbonlivraisoncustomer.nom=?
                        ORDER by tblbonlivraisoncustomer.id desc
                        LIMIT 0, 1";
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind id of product to be updated
       $stmt->bindParam(1,$client_id);
       $stmt->bindParam(2,$tocken);
       $stmt->bindParam(3,$nombonlivraison);
     
        // execute query
        $stmt->execute();
     
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        // set values to object properties

        $this->id = $row['id'];
        $this->nom = $row['nom'];
        $this->date_created = $row['date_created'];
    }






    // read GetAllBonLivraisonByExpediteurPaging
public function GetAllBonLivraisonByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblbonlivraisoncustomer.id,tblbonlivraisoncustomer.nom,DATE_FORMAT(tblbonlivraisoncustomer.date_created,'%d-%m-%Y') as date_created
                FROM tblbonlivraisoncustomer,tblexpediteurs 
                where tblexpediteurs.id=tblbonlivraisoncustomer.id_expediteur
                and tblbonlivraisoncustomer.id_expediteur=?
                and  tblexpediteurs.password=?
                ORDER by tblbonlivraisoncustomer.id desc
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
public function GetAllBonLivraisonItemsByExpediteurPaging($from_record_num, $records_per_page,$client_id,$tocken,$bonlivraison_id){
 
    // select query
    $query = "SELECT tblcolisenattente.id as 'colis_id',tblcolisenattente.code_barre,tblcolisenattente.num_commande,
                    tblcolisenattente.crbt ,tblcolisenattente.nom_complet,tblcolisenattente.telephone
                    from tblcolisenattente,tblexpediteurs
                    where tblcolisenattente.id_expediteur=tblexpediteurs.id
                    and tblexpediteurs.id=? 
                    and tblexpediteurs.password=?
                    and num_bonlivraison=? 
                    ORDER by tblcolisenattente.id asc
                    LIMIT ?, ? ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1,$client_id);
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
public function nombrebonlivraison($id){
    $query = "SELECT  count(*) as 'total_rows' from tblbonlivraisoncustomer,tblexpediteurs
    where tblexpediteurs.id=tblbonlivraisoncustomer.id_expediteur
    and tblbonlivraisoncustomer.id_expediteur=? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}


// used for paging nombrefacturesItems
public function nombrebonlivraisonItems($client_id,$bonlivraison_id){
    $query = "SELECT   count(*) as 'total_rows'
                from tblcolisenattente,tblexpediteurs
                where tblcolisenattente.id_expediteur=tblexpediteurs.id
                and tblexpediteurs.id=? 
                and num_bonlivraison=?  ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $client_id);
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






}
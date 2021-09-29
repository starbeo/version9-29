<?php
class ColisMethods{

    // object properties
    public $code_envoi;
    public $Destinataire;
    public $client;
    public $crbt;
    public $ville;
    public $telephone;
    public $date_ramassage;
    public $date_livraison;
    public $date_creation;
    public $adresse;
    public $frais;
    public $livreur;
    public $colis_id;
    public $status_id;
    public $status_reel;
    public $ville_id;
    public $name;
    public $id;
    public $numeroCommande;
    public $bonLivraison;
    public $typeBonLivarsion;
    public $nomFacture;
    public $etatColisLivre;
    public $commentaire;
    public $date_retour;
    public $table_name="tblcolis";







    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    

  // readone 
function readOne($id,$tocken,$client_id){

    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel as 'status_reel' ,
    tblcolis.num_commande as 'numeroCommande',
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'BonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'typeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'nomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'EtatColisLivre',
    tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id=?
    and tblexpediteurs.password=?
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    ORDER by tblcolis.id desc
    LIMIT 0,1 ";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$id);
   $stmt->bindParam(2,$tocken);
   $stmt->bindParam(3,$client_id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    $this->colis_id = $row['colis_id'];
    $this->code_envoi = $row['code_envoi'];
    $this->Destinataire = $row['Destinataire'];
    $this->client = $row['client'];
    $this->crbt = $row['crbt'];
    $this->ville = $row['ville'];
    $this->telephone = $row['telephone'];
    $this->date_ramassage = $row['date_ramassage'];
    $this->date_livraison = $row['date_livraison'];
    $this->frais = $row['frais'];
    $this->livreur = $row['livreur'];
    $this->status_id = $row['status_id'];
    $this->status_reel = $row['status_reel'];
    $this->numeroCommande = $row['numeroCommande'];
    $this->bonLivraison = $row['BonLivraison'];
    $this->typeBonLivarsion = $row['typeBonLivarsion'];
    $this->nomFacture = $row['nomFacture'];
    $this->etatColisLivre = $row['EtatColisLivre'];
    $this->commentaire = $row['commentaire'];
    $this->date_retour = $row['date_retour'];
    $this->adresse = $row['adresse'];
}


  // readOneByCodeEnvoi  
  function readOneByCodeEnvoi($id,$tocken,$client_id){

    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel as 'status_reel' ,
    tblcolis.num_commande as 'numeroCommande',
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'BonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'typeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'nomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'EtatColisLivre',
    tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.code_barre=?
    and tblexpediteurs.password=?
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    ORDER by tblcolis.id desc
    LIMIT 0,1 ";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$id);
   $stmt->bindParam(2,$tocken);
   $stmt->bindParam(3,$client_id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    $this->colis_id = $row['colis_id'];
    $this->code_envoi = $row['code_envoi'];
    $this->Destinataire = $row['Destinataire'];
    $this->client = $row['client'];
    $this->crbt = $row['crbt'];
    $this->ville = $row['ville'];
    $this->telephone = $row['telephone'];
    $this->date_ramassage = $row['date_ramassage'];
    $this->date_livraison = $row['date_livraison'];
    $this->frais = $row['frais'];
    $this->livreur = $row['livreur'];
    $this->status_id = $row['status_id'];
    $this->status_reel = $row['status_reel'];
    $this->numeroCommande = $row['numeroCommande'];
    $this->bonLivraison = $row['BonLivraison'];
    $this->typeBonLivarsion = $row['typeBonLivarsion'];
    $this->nomFacture = $row['nomFacture'];
    $this->etatColisLivre = $row['EtatColisLivre'];
    $this->commentaire = $row['commentaire'];
    $this->date_retour = $row['date_retour'];
    $this->adresse = $row['adresse'];
}

// read Colis Livré
function GetAllColislivreByExpediteur($id){
 
    // select all query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, 
    DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour ,
    `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
            and tblcolis.id_expediteur = ?
            and tblexpediteurs.password=?
            and tblcolis.`status_id`=2 
            ORDER by tblcolis.date_livraison desc";
        
    // prepare query statement
    $stmt = $this->conn->prepare($query);
     // bind id of product to be updated
     $stmt->bindParam(1, $id);
    // execute query
    $stmt->execute();
  
    return $stmt;
  }
// read GetAllColislivreByExpediteurPaging
public function GetAllColislivreByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, 
    DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour ,
    `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_id`=2 
    ORDER by tblcolis.date_livraison desc
    LIMIT ?, ?";
 
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





// read GetAllColislivreByExpediteurPaging
public function GetAllColisretourneByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`,DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_id`=3 
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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

// read GetAllColisencoursByExpediteurPaging
public function GetAllColisencoursByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur  
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_id`=1 
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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

// read GetAllColisExpedieByExpediteurPaging
public function GetAllColisExpedieByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=4
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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

// read GetAllColisAnnulerByExpediteurPaging
public function GetAllColisAnnulerByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur   
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=10
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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



// read GetAllColisRefuserByExpediteurPaging
public function GetAllColisRefuserByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=9
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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


// read GetAllColisPasReponseByExpediteurPaging
public function GetAllColisPasReponseByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`,DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur  
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=6
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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



// read GetAllColisReporterByExpediteurPaging
public function GetAllColisReporterByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur  
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=11
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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


// read GetAllColisReporterByExpediteurPaging
public function GetAllColisInjoignableByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur  
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblcolis.status_reel=7
    and tblexpediteurs.password=?
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
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

// read GetAllColisEnAttenteByExpediteurPaging
public function GetAllColisEnAttenteByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT tblcolisenattente.id as 'colis_id',tblcolisenattente.code_barre as 'code_envoi',nom_complet
              as 'Destinataire',tblcolisenattente.crbt,tblcolisenattente.telephone,
              tblcolisenattente.adresse,DATE_FORMAT(tblcolisenattente.date_creation,'%d-%m-%Y') as date_creation,tblvilles.name as 'ville'
    FROM `tblcolisenattente`,tblexpediteurs,tblvilles
    where colis_id is null 
    and id_expediteur=?
    and tblexpediteurs.id=tblcolisenattente.id_expediteur
    and tblexpediteurs.password=?
    and tblcolisenattente.ville=tblvilles.id
    ORDER by tblcolisenattente.id desc
    LIMIT ?, ?";
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




// read GetAllColisretourneaLagnceByExpediteurPaging
public function GetAllColisretourneaLagnceByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_reel`=13
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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


// read GetAllColisRamasserByExpediteurPaging
public function GetAllColisRamasserByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_reel`=5
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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


// read GetAllColisNumeroErronerByExpediteurPaging
public function GetAllColisNumeroErronerByExpediteurPaging($from_record_num, $records_per_page,$id,$tocken){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, DATE_FORMAT(tblcolis.date_ramassage,'%d-%m-%Y') as date_ramassage,
    DATE_FORMAT(tblcolis.date_livraison,'%d-%m-%Y') as date_livraison,
    DATE_FORMAT(tblcolis.date_retour,'%d-%m-%Y') as date_retour , `tblcolis`.`frais`,
    CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id ,tblcolis.status_reel ,tblcolis.num_commande,
	(SELECT nom FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'NomBonLivraison',
	(SELECT type FROM tblbonlivraison where id=tblcolis.num_bonlivraison) as 'TypeBonLivarsion',
	(SELECT nom FROM tblfactures where id=tblcolis.num_facture) as 'NomFacture',
	(SELECT nom FROM tbletatcolislivre where id=tblcolis.num_etatcolislivrer) as 'NomEtatColisLivre',tblcolis.commentaire,tblcolis.adresse
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblcolis.id_expediteur =?
    and tblstaff.staffid=tblcolis.livreur 
    and tblexpediteurs.password=?
    and tblcolis.`status_reel`=8
    ORDER by tblcolis.id desc
    LIMIT ?, ?";
 
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


// read GetEnregistrementAppelsByCodeEnvoi
public function GetEnregistrementAppelsByCodeEnvoi($Code_Envoi){
 
    // select query
    $query = "SELECT tblappelslivreur.id,DATE_FORMAT(tblappelslivreur.date_created,'%d-%m-%Y') as date_appel,
                    tblcolis.code_barre,CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' 
                     FROM tblappelslivreur,tblcolis,tblstaff 
                     where tblappelslivreur.colis_id=tblcolis.id 
                     and tblappelslivreur.livreur_id=tblstaff.staffid 
                     and tblcolis.code_barre=?
               order by tblappelslivreur.id DESC";
    // prepare query statementss
    $stmt = $this->conn->prepare( $query );

    // bind variable values
    $stmt->bindParam(1,$Code_Envoi);
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}



// getOne colis en attente 
function readOnecolisEnAttenteById($id){

    $query = "SELECT id as 'colis_id',code_barre as 'code_envoi',nom_complet as 'Destinataire',crbt,telephone,adresse,DATE_FORMAT(date_creation,'%d-%m-%Y') as date_creation
              FROM `tblcolisenattente`
              where colis_id is null and id=?
              ORDER by id desc
             LIMIT 0, 1";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    $this->colis_id = $row['colis_id'];
    $this->code_envoi = $row['code_envoi'];
    $this->Destinataire = $row['Destinataire'];
    $this->crbt = $row['crbt'];
    $this->telephone = $row['telephone'];
    $this->adresse = $row['adresse'];
    $this->date_creation = $row['date_creation'];
}





public function nombrecolis(){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

  // used for paging nombrecolislivre
public function nombrecolislivre($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE status_id=2 AND tblcolis.id_expediteur =?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisRetourne
public function nombrecolisRetourne($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_id`=3 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisEncours
public function nombrecolisEncours($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_id`=1 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisExpedie
public function nombrecolisExpedie($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=4 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisAnnuler
public function nombrecolisAnnuler($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis  WHERE `status_reel`=10 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisRefuser
public function nombrecolisRefuse($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=9 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging product livre
public function nombrecolisPasResponse($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=6 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisReporte
public function nombrecolisReporte($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=11 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// used for paging nombrecolisInjoignable
public function nombrecolisInjoignable($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=7 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrecolisRamasser
public function nombrecolisRamasser($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=5 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrecolisRetournerAlagence
public function nombrecolisRetournerAlagence($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=13 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrecolisNumeroErroner
public function nombrecolisNumeroErroner($id){
    $query = "SELECT count(*) as 'total_rows' FROM tblcolis WHERE `status_reel`=8 AND tblcolis.id_expediteur =? ";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

// used for paging nombrecolisEnAttente
public function nombrecolisInAttente($id){
    $query = "SELECT count(*) as 'total_rows' FROM `tblcolisenattente` where colis_id is null and id_expediteur=?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}

public function nombrecolisrecercher($keywords){
    $query = "SELECT  count(tblcolis.id) as 'total_rows' 
    FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
    WHERE  tblcolis.ville=tblvilles.id 
    and tblexpediteurs.id=tblcolis.id_expediteur 
    and tblstaff.staffid=tblcolis.livreur 
    and( tblcolis.code_barre like ? OR `tblvilles`.`name` like ? OR tblcolis.date_ramassage like ? OR tblcolis.date_livraison like ?)
    ORDER by tblcolis.id desc LIMIT 1000";
    $stmt = $this->conn->prepare( $query );
    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";
    // bind
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);
    $stmt->bindParam(4, $keywords);
    // execute query
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// search products
function search($keywords){
 
    // select all query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, tblcolis.date_ramassage,tblcolis.date_livraison, `tblcolis`.`frais`,
     CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id
     FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
     WHERE  tblcolis.ville=tblvilles.id 
     and tblexpediteurs.id=tblcolis.id_expediteur 
     and tblstaff.staffid=tblcolis.livreur
     and( tblcolis.code_barre like ? OR `tblvilles`.`name` like ? OR tblcolis.date_ramassage like ? OR tblcolis.date_livraison like ?)
     ORDER by tblcolis.id desc
     LIMIT 1000
     ";
    // prepare query statement
    $stmt = $this->conn->prepare($query);

    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";
    // bind
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);
    $stmt->bindParam(4, $keywords);
    // execute query
    $stmt->execute();
    return $stmt;
}

public function RecherchePaging($from_record_num, $records_per_page,$keywords){
 
    // select query
    $query = "SELECT DISTINCT tblcolis.id as 'colis_id',tblexpediteurs.nom as 'client',`tblcolis`.`code_barre` as 
    'code_envoi',tblcolis.nom_complet as 'Destinataire', `tblcolis`.`crbt`,`tblvilles`.`name` AS 'ville',
    `tblcolis`.`telephone`, tblcolis.date_ramassage,tblcolis.date_livraison, `tblcolis`.`frais`,
     CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as 'livreur' ,tblcolis.status_id
     FROM `tblcolis`,tblvilles ,tblstaff,tblstatuscolis,tblexpediteurs
     WHERE  tblcolis.ville=tblvilles.id 
     and tblexpediteurs.id=tblcolis.id_expediteur 
     and tblstaff.staffid=tblcolis.livreur 
     and( tblcolis.code_barre like ? OR `tblvilles`.`name` like ? OR tblcolis.date_ramassage like ? OR tblcolis.date_livraison like ?)
     ORDER by tblcolis.id desc
     LIMIT ?, ?";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";
    // bind variable values
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);
    $stmt->bindParam(4, $keywords);
    $stmt->bindParam(5, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(6, $records_per_page, PDO::PARAM_INT);
    // execute query
    $stmt->execute();
    // return values from database
    return $stmt;
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


//Get All Villes
public function getAllvilles(){
    $query = "SELECT `id` as 'ville_id',`name` FROM tblvilles where active=1 ";
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    return $stmt;
}

}
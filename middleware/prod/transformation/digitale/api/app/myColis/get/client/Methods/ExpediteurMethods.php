<?php
class ExpediteurMethods{
 
    // database connection 
    private $conn;
 
    // object properties
    public $expediteur_id;
    public $client;
    public $contact;
    public $email;
    public $telephone1;
    public $telephone2;
    public $adresse;
    public $ville;
    public $rib;
    public $frais_livraison_interieur;
    public $frais_livraison_exterieur;
    public $frais_retourne;
    public $frais_refuse;
    public $frais_supplementaire;
    public $frais_stockage;
    public $frais_emballage;
    public $frais_etiquette;
    public $frais_assurance;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function getInformationExpediteur($id,$tocken){

        $query = "SELECT tblexpediteurs.`id` as 'expediteur_id',tblexpediteurs.`nom` as 'client',tblexpediteurs.`contact`
        ,tblexpediteurs.`email`,tblexpediteurs.`telephone`,tblexpediteurs.`telephone2`,tblexpediteurs.`adresse`
        ,(SELECT name FROM `tblvilles` where tblexpediteurs.ville_id=tblvilles.id) as 'ville',
        tblexpediteurs.rib,tblexpediteurs.frais_livraison_interieur,tblexpediteurs.frais_livraison_exterieur,
        tblexpediteurs.frais_retourne,tblexpediteurs.frais_refuse,tblexpediteurs.frais_supplementaire,tblexpediteurs.frais_stockage,
        tblexpediteurs.frais_emballage,tblexpediteurs.frais_etiquette
        FROM `tblexpediteurs` ,tblvilles 
        WHERE  tblexpediteurs.id=?
        and tblexpediteurs.password=?
                 LIMIT 0,1";
        // prepare query statement
        $stmt = $this->conn->prepare( $query );
     
        // bind id of product to be updated
       $stmt->bindParam(1,$id);
       $stmt->bindParam(2,$tocken);
     
        // execute query
        $stmt->execute();
     
        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
        // set values to object properties
        $this->expediteur_id = $row['expediteur_id'];
        $this->client = $row['client'];
        $this->contact = $row['contact'];
        $this->email = $row['email'];
        $this->telephone1 = $row['telephone'];
        $this->telephone2 = $row['telephone2'];
        $this->adresse = $row['adresse'];
        $this->ville = $row['ville'];
        $this->rib = $row['rib'];
        $this->frais_livraison_interieur = $row['frais_livraison_interieur'];
        $this->frais_livraison_exterieur = $row['frais_livraison_exterieur'];
        $this->frais_retourne = $row['frais_retourne'];
        $this->frais_refuse = $row['frais_refuse'];
        $this->frais_stockage = $row['frais_stockage'];
        $this->frais_emballage = $row['frais_emballage'];
        $this->frais_etiquette = $row['frais_etiquette'];
        //$this->frais_assurance = $row['frais_assurance'];
    }
}
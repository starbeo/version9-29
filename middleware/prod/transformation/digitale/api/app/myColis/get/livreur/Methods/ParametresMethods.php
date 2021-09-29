<?php
class ParametresMethods{

    // object properties
    public $name;
    public $zone_id;
    public $country_id;
    public $ville;
    public $pays;
    public $status;
    public $order_status_id;


    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }


    

    public function getAllville(){
        $query = "SELECT `name`,zone_id,country_id,status FROM oc_zone WHERE status=1 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        return $stmt;
    }
    public function getAllvillelistes(){
        
        $query = "SELECT oc_zone.name as 'ville',oc_zone.zone_id,oc_zone.country_id,oc_country.name as 'pays' 
                  FROM oc_zone,oc_country 
                  where oc_zone.country_id=oc_country.country_id AND oc_zone.status=1";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        return $stmt;
    }
    
    public function getAllpays(){
        $query = "SELECT `name`,country_id,status FROM oc_country where status=1 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        return $stmt;
    }

    public function getAllStatutsCommande(){
        $query = "SELECT name,order_status_id FROM oc_order_status WHERE language_id=1 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
        return $stmt;
    }

}
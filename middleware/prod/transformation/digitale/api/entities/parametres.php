<?php
class Parametres{
 
    // database connection and table name
    private $conn;
 
    // object properties
    //public $id;
    public $name;
    public $iso_code_2;
    public $country_id;
    public $order_status_id;
    public $zone_id;
    public $status;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
    
    // createpays 
    function createpays(){
    
        // create query
        $query = "INSERT INTO `oc_country`(`name`, `iso_code_2`, `iso_code_3`, `address_format`, `postcode_required`, `status`)
                  VALUES (?,?,'','','',1)";
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->iso_code_2=htmlspecialchars(strip_tags($this->iso_code_2));
        $stmt->bindParam(1, $this->name);
        $stmt->bindParam(2, $this->iso_code_2);
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // createville
    function createville(){
    
       // create query
        $query = "INSERT INTO `oc_zone`( `country_id`, `name`, `code`, `status`) 
                  VALUES (:country_id,:name,'',1)";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->country_id=htmlspecialchars(strip_tags($this->country_id));
        $this->name=htmlspecialchars(strip_tags($this->name));
    
        // bind id of record to delete
        $stmt->bindParam(":country_id", $this->country_id);
        $stmt->bindParam(":name", $this->name);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

     // create_order_status
     function create_order_status(){
        // create query
        $query = "INSERT INTO `oc_order_status`( `language_id`, `name`) VALUES (1,:name)";
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
    
        $stmt->bindParam(":name", $this->name);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // delete_order_status
    function delete_order_status(){
    
        // delete query
        $query = "DELETE FROM `oc_order_status` WHERE `order_status_id`=:order_status_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->order_status_id=htmlspecialchars(strip_tags($this->order_status_id));
    
        $stmt->bindParam(":order_status_id", $this->order_status_id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // deleteville
    function deleteville(){
    
        // delete query
        $query = "DELETE FROM `oc_zone` WHERE `zone_id`=:zone_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->zone_id=htmlspecialchars(strip_tags($this->zone_id));
    
        $stmt->bindParam(":zone_id", $this->zone_id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // deletepays
    function deletepays(){
    
        // delete query
        $query = "DELETE FROM `oc_country` WHERE `country_id`=:country_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->country_id=htmlspecialchars(strip_tags($this->country_id));
    
        $stmt->bindParam(":country_id", $this->country_id);
    
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
    // create_order_status
    function updatepays(){
    
        // update query
        $query = "UPDATE `oc_country` SET `name`=:name,`iso_code_2`=:iso_code_2,status=:status
                   WHERE country_id=:country_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->country_id=htmlspecialchars(strip_tags($this->country_id));
        $this->iso_code_2=htmlspecialchars(strip_tags($this->iso_code_2));
        $this->status=htmlspecialchars(strip_tags($this->status));
    
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":iso_code_2", $this->iso_code_2);
        $stmt->bindParam(":country_id", $this->country_id);
        $stmt->bindParam(":status", $this->status);
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
    // updatestatuts
    function updatestatuts(){
    
        // update query
        $query = "UPDATE `oc_order_status` SET `name`=:name WHERE order_status_id=:order_status_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->order_status_id=htmlspecialchars(strip_tags($this->order_status_id));
    
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":order_status_id", $this->order_status_id);
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
     // updateville
     function updateville(){
    
        // update query
        $query = "UPDATE `oc_zone` SET `country_id`=:country_id,`name`=:name,status=:status WHERE zone_id:zone_id";
    
        // prepare query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->name=htmlspecialchars(strip_tags($this->name));
        $this->zone_id=htmlspecialchars(strip_tags($this->zone_id));
        $this->country_id=htmlspecialchars(strip_tags($this->country_id));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $stmt->bindParam(":country_id", $this->country_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":zone_id", $this->zone_id);
        $stmt->bindParam(":status", $this->status);
        // execute query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
    
     

}
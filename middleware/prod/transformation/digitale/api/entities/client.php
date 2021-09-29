<?php
class Client{
 
    // database connection and table name
    private $conn;
 
    // object properties
    //public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $telephone;
    public $ville;
    public $address_1;
    public $address_2;
    public $postcode;
    public $country_id;
    public $zone_id;
    public $customer_id;
    public $fax;
    public $company;
    public $address_id;

 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

// create Client
function create(){

        $adresse_ip=$_SERVER['REMOTE_ADDR'];
        $query=" INSERT INTO `oc_customer` ( `customer_group_id`, `store_id`, `firstname`,`lastname`, `email`,
        `telephone`, `fax`, `password`, `salt`,`cart`, `wishlist`, `newsletter`, `address_id`, `custom_field`,
         `ip`, `status`, `approved`,`safe`, `token`, `date_added`)
         VALUES ('1', '0', :firstname, :lastname, :email, :telephone, '',
         '', '', '', '', '1', '2', '', '$adresse_ip', '1', '1', '0', '', Now())";
    // prepare query
    $stmt = $this->conn->prepare($query);
    // sanitize
    $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->telephone=htmlspecialchars(strip_tags($this->telephone));
    $this->address_1=htmlspecialchars(strip_tags($this->address_1));
    $this->address_2=htmlspecialchars(strip_tags($this->address_2));
    $this->postcode=htmlspecialchars(strip_tags($this->postcode));
    $this->country_id=htmlspecialchars(strip_tags($this->country_id));
    $this->zone_id=htmlspecialchars(strip_tags($this->zone_id));
    // bind values
    $stmt->bindParam(":firstname", $this->firstname);
    $stmt->bindParam(":lastname", $this->lastname);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":telephone", $this->telephone);

    if($stmt->execute()){
        $last_id_customer_id = $this->conn->lastInsertId();
        $query1="INSERT INTO `oc_address` ( `customer_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`,
                `city`, `postcode`, `country_id`, `zone_id`, `custom_field`) 
        VALUES ( '$last_id_customer_id',:firstname,:lastname , :country, :address_1, :address_2, :ville, :postcode, :country_id, :zone_id, '')";
        // prepare query
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(":firstname", $this->firstname);
        $stmt1->bindParam(":lastname", $this->lastname);
        $stmt1->bindParam(":address_1", $this->address_1);
        $stmt1->bindParam(":address_2", $this->address_2);
        $this->ville=htmlspecialchars(strip_tags($this->villeByZone_id($this->zone_id)));
        $stmt1->bindParam(":ville",$this->ville);
        $stmt1->bindParam(":postcode", $this->postcode);
        $stmt1->bindParam(":country_id", $this->country_id);
        $this->company=htmlspecialchars(strip_tags($this->paysByCountry_id($this->country_id)));
        $stmt1->bindParam(":country",$this->company);
        $stmt1->bindParam(":zone_id", $this->zone_id);
        $stmt1->execute();
        $last_id_adresse_id = $this->conn->lastInsertId();
        $query2="UPDATE oc_customer SET address_id = :address_id WHERE customer_id = :customer_id ";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(":address_id", $last_id_adresse_id);
        $stmt2->bindParam(":customer_id", $last_id_customer_id);
        $stmt2->execute();
        return true;
    }
    return false;
}

// update the Client
function affecterAdresseClient(){
    $query="UPDATE oc_customer SET address_id = :address_id WHERE customer_id = :customer_id ";
        $stmt = $this->conn->prepare($query);
        $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
        $this->address_id=htmlspecialchars(strip_tags($this->address_id));
        $stmt->bindParam(":address_id", $this->address_id);
        $stmt->bindParam(":customer_id", $this->customer_id);
    // execute the query
    if($stmt->execute()){
        return true;
    }
    return false;
}


// update the Client
function updateClient(){

    // update query
    $query = "UPDATE
                oc_customer
            SET
            firstname=:firstname,
            lastname=:lastname,
            email=:email,
            telephone=:telephone,
            fax=:fax
            WHERE
            customer_id = :customer_id";
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    // sanitize
    $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->telephone=htmlspecialchars(strip_tags($this->telephone));
    $this->fax=htmlspecialchars(strip_tags($this->fax));
    $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
    // bind new values
    $stmt->bindParam(':firstname', $this->firstname);
    $stmt->bindParam(':lastname', $this->lastname);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(":telephone", $this->telephone);
    $stmt->bindParam(":fax", $this->fax);
    $stmt->bindParam(':customer_id', $this->customer_id,PDO::PARAM_INT);
    // execute the query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}

// update the Client
function updateAdresse(){

   // update query
    $query = "UPDATE
                oc_address
            SET
            firstname=:firstname,
            lastname=:lastname,
            company=:company,
            address_1=:address_1,
            address_2=:address_2,
            city=:ville,
            postcode=:postcode,
            country_id=:country_id,
            zone_id=:zone_id
            WHERE
            customer_id = :customer_id 
            and 
            address_id=:address_id";
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    // sanitize
    $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    $this->address_1=htmlspecialchars(strip_tags($this->address_1));
    $this->address_2=htmlspecialchars(strip_tags($this->address_2));
    $this->postcode=htmlspecialchars(strip_tags($this->postcode));
    $this->country_id=htmlspecialchars(strip_tags($this->country_id));
    $this->zone_id=htmlspecialchars(strip_tags($this->zone_id));
    $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
    $this->address_id=htmlspecialchars(strip_tags($this->address_id));
    // bind new values
    $stmt->bindParam(':firstname', $this->firstname);
    $stmt->bindParam(':lastname', $this->lastname);
    $this->company=htmlspecialchars(strip_tags($this->paysByCountry_id($this->country_id)));
    $stmt->bindParam(':company', $this->company);
    $stmt->bindParam(":address_1", $this->address_1);
    $stmt->bindParam(":address_2", $this->address_2);
    $this->ville=htmlspecialchars(strip_tags($this->villeByZone_id($this->zone_id)));
    $stmt->bindParam(":ville",$this->ville);
    $stmt->bindParam(":postcode", $this->postcode);
    $stmt->bindParam(":country_id", $this->country_id,PDO::PARAM_INT);
    $stmt->bindParam(':zone_id', $this->zone_id,PDO::PARAM_INT);
    $stmt->bindParam(":customer_id", $this->customer_id,PDO::PARAM_INT);
    $stmt->bindParam(':address_id', $this->address_id,PDO::PARAM_INT);
    // execute the query
    if($stmt->execute()){
        return true;
    }
    return false;
}

// create Client
function createAdresse(){
    
    $query="INSERT INTO `oc_address` ( `customer_id`, `firstname`, `lastname`, `company`, `address_1`, `address_2`,
            `city`, `postcode`, `country_id`, `zone_id`, `custom_field`) 
    VALUES ( :customer_id,:firstname,:lastname ,:company, :address_1, :address_2, :ville, :postcode, :country_id, :zone_id, '')";

    // sanitize
    $this->firstname=htmlspecialchars(strip_tags($this->firstname));
    $this->lastname=htmlspecialchars(strip_tags($this->lastname));
    $this->address_1=htmlspecialchars(strip_tags($this->address_1));
    $this->address_2=htmlspecialchars(strip_tags($this->address_2));
    $this->postcode=htmlspecialchars(strip_tags($this->postcode));
    $this->country_id=htmlspecialchars(strip_tags($this->country_id));
    $this->zone_id=htmlspecialchars(strip_tags($this->zone_id));
    $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
    // prepare query
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":firstname", $this->firstname);
    $stmt->bindParam(":lastname", $this->lastname);
    $this->company=htmlspecialchars(strip_tags($this->paysByCountry_id($this->country_id)));
    $stmt->bindParam(":company", $this->company);
    $stmt->bindParam(":address_1", $this->address_1);
    $stmt->bindParam(":address_2", $this->address_2);
    $this->ville=htmlspecialchars(strip_tags($this->villeByZone_id($this->zone_id)));
    $stmt->bindParam(":ville", $this->ville);
    $stmt->bindParam(":postcode", $this->postcode);
    $stmt->bindParam(":country_id", $this->country_id,PDO::PARAM_INT);
    $stmt->bindParam(":zone_id", $this->zone_id,PDO::PARAM_INT);
    $stmt->bindParam(":customer_id", $this->customer_id,PDO::PARAM_INT);
    if($stmt->execute()){
        return true;
    }
    return false;
    }

// delete the product
function deleteClient(){
 
    // delete query
    $query = "DELETE FROM oc_customer WHERE customer_id = ?";
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
 
    // bind id of record to delete
    $stmt->bindParam(1, $this->customer_id);
 
    // execute query
    if($stmt->execute()){
        $query1 = "DELETE FROM oc_address WHERE customer_id = ?";
        $stmt1 = $this->conn->prepare($query1);
        // sanitize
        $this->customer_id=htmlspecialchars(strip_tags($this->customer_id));
        // bind id of record to delete
        $stmt1->bindParam(1, $this->customer_id);
        $stmt1->execute();
        return true;
    }
 
    return false;
     
}
// delete the product
function deleteAdresse(){
 
    // delete query
    $query = "DELETE FROM oc_address WHERE address_id = ?";
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->address_id=htmlspecialchars(strip_tags($this->address_id));
 
    // bind id of record to delete
    $stmt->bindParam(1, $this->address_id);
 
    // execute query
    if($stmt->execute()){
        return true;
    }
 
    return false;
}

public function villeByZone_id($zone){
    $query = "SELECT `name` FROM oc_zone WHERE zone_id=?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $zone);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['name'];
}

public function paysByCountry_id($country){
    $query = "SELECT `name` FROM oc_country WHERE country_id=?";
    $stmt = $this->conn->prepare( $query );
    $stmt->bindParam(1, $country);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['name'];
}


}
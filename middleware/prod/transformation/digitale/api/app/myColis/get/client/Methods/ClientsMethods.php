<?php
class ClientsMethods{

    // object properties
    public $customer_id;
    public $firstname;
    public $lastname;
    public $email;
    public $telephone;
    public $fax;
    public $date_added;
    public $address_id;
    public $company;
    public $address_1;
    public $address_2;
    public $city;
    public $postcode;
    public $country_id;
    public $zone_id;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
  // readone 
function readOneClient($customer_id){

    $query="SELECT DISTINCT `customer_id`,`firstname`,`lastname`,`email`,`telephone`,`fax`,address_id ,date_added
    FROM oc_customer 
    WHERE customer_id=?
    ORDER by customer_id desc
    LIMIT 0,1";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$customer_id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // set values to object properties
    $this->customer_id = $row['customer_id'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->email = $row['email'];
    $this->telephone = $row['telephone'];
    $this->fax = $row['fax'];
    $this->address_id = $row['address_id'];
    $this->date_added = $row['date_added'];
}
  // readone 
  function readOneAdresseClient($address_id){

    $query="SELECT `address_id`,`customer_id`,`firstname`,`lastname`,`company`,`address_1`,
    `address_2`,`city`,`postcode`,`country_id`,`zone_id` 
    FROM `oc_address` 
    WHERE `address_id`= ?
    ORDER by address_id desc
    LIMIT 0,1";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
   $stmt->bindParam(1,$address_id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // set values to object properties
    $this->address_id = $row['address_id'];
    $this->customer_id = $row['customer_id'];
    $this->firstname = $row['firstname'];
    $this->lastname = $row['lastname'];
    $this->company = $row['company'];
    $this->address_1 = $row['address_1'];
    $this->address_2 = $row['address_2'];
    $this->city = $row['city'];
    $this->postcode = $row['postcode'];
    $this->country_id = $row['country_id'];
    $this->zone_id = $row['zone_id'];
}

// read AllClients
function GetAllClients(){
 
    // select all query

    $query="SELECT DISTINCT `customer_id`,`firstname`,`lastname`,`email`,`telephone`,`fax`,address_id ,date_added
           FROM oc_customer 
           ORDER by customer_id desc";
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    // execute query
    $stmt->execute();
  
    return $stmt;
  }

  // read AllClients
function GetAllAdresse($customer_id){
 
    // select all query
    $query="SELECT `address_id`,`customer_id`,`firstname`,`lastname`,`company`,`address_1`,
                   `address_2`,`city`,`postcode`,`country_id`,`zone_id` 
            FROM `oc_address`
            WHERE customer_id=?
            ORDER by address_id desc
             ";
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1,$customer_id);
    // execute query
    $stmt->execute();
  
    return $stmt;
  }

  // read AllClients
function GetAllAdresseByClient($customer_id){
 
    // select all query
    $query="SELECT `address_id`,`customer_id`,`firstname`,`lastname`,`company`,`address_1`,
           `address_2`,`city`,`postcode`,`country_id`,`zone_id` 
           FROM `oc_address` 
           WHERE `customer_id`= ?
           ORDER by customer_id desc";
    // prepare query statement
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1,$customer_id);
    // execute query
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $getAllAdresseByClient = $stmt->fetchAll();
    return $getAllAdresseByClient;
  }
  
// read GetAllColislivreByExpediteurPaging
public function GetAllClientsPaging($from_record_num, $records_per_page){
 
    // select query
    $query = "SELECT DISTINCT `customer_id`,`firstname`,`lastname`,`email`,`telephone`,`fax`,address_id ,date_added
    FROM oc_customer 
    ORDER by customer_id desc
    LIMIT ?, ?";
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    // execute query
    $stmt->execute();
    // return values from database
    return $stmt;
}

public function nombreclients(){
    $query = "SELECT count(*) as 'total_rows' FROM oc_customer";
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total_rows'];
}
// search products
function searchclient($keywords){
    $query = "SELECT DISTINCT `customer_id`,`firstname`,`lastname`,`email`,`telephone`,`fax` ,date_added
    FROM oc_customer 
    WHERE ( firstname like ? OR lastname like ? OR email like ? OR telephone like ?OR fax like ?OR date_added like ?)
    ORDER by customer_id desc
    LIMIT 100";
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
    $stmt->bindParam(5, $keywords);
    $stmt->bindParam(6, $keywords);
    // execute query
    $stmt->execute();
    return $stmt;
}

public function getAllzone(){
    $query = "SELECT `name`,zone_id,country_id FROM oc_zone ";
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    return $stmt;
}

public function getAllcountry(){
    $query = "SELECT `name`,country_id FROM oc_country ";
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    return $stmt;
}

}
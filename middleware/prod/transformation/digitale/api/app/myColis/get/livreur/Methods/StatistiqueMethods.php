<?php
class StatistiqueMethods{
 
    // database connection 
    private $conn;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

      /**
     * get total  getnombrecolisTotal
     *
    */ 
    public function getnombrecolisTotal($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur and tblstaff.password=?
        and date_ramassage>= '2019-01-01' ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


      /**
     * get total  getnombrecolisLivrer
     *
    */ 
    public function getnombrecolisLivrer($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_id=2 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


       /**
     * get total  getnombrecolisRetourner
     *
    */ 
    public function getnombrecolisRetourner($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_id=3 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

      /**
     * get total  getnombrecolisExpedier
     *
    */ 
    public function getnombrecolisExpedier($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=4  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

       /**
     * get total  getnombrecolisExpedier
     *
    */ 
    public function getnombrecolisAnnuler($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=10  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


      /**
     * get total  getnombrecolisRefuse
     *
    */ 
    public function getnombrecolisRefuser($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=9  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


     /**
     * get total  getnombrecolisPasResponse
     *
    */ 
    public function getnombrecolisPasResponse($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=6  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

     /**
     * get total  getnombrecolisReporte
     *
    */ 
    public function getnombrecolisReporte($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=11 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

     /**
     * get total  getnombrecolisInjoignable
     *
    */ 
    public function getnombrecolisInjoignable($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=7  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

     /**
     * get total  getnombrecolisRamasser
     *
    */ 
    public function getnombrecolisRamasser($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=5  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

     /**
     * get total  getnombrecolisRetournerAlagence
     *
    */ 
    public function getnombrecolisRetournerAlagence($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=13  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

     /**
     * get total  getnombrecolisNumeroErroner
     *
    */ 
    public function getnombrecolisNumeroErroner($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblstaff 
        WHERE  tblcolis.livreur =? 
        and  tblstaff.staffid=tblcolis.livreur 
        and tblstaff.password=?
        and tblcolis.status_reel=8  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


     /**
     * get total  getnombreBonlivraison
     *
    */ 
    public function getnombreBonlivraison($id,$tocken){
        $query = "SELECT  count(*) as 'total' from  tblbonlivraison,tblstaff 
        WHERE  tblbonlivraison.id_livreur=tblstaff.staffid and id_livreur=? and tblstaff.password=?  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

    /**
     * get total  getnombreBonlivraisonSortie
     *
    */ 
    public function getnombreBonlivraisonSortie($id,$tocken){
        $query = "SELECT  count(*) as 'total' from  tblbonlivraison,tblstaff 
        WHERE  tblbonlivraison.id_livreur=tblstaff.staffid and id_livreur=? and tblstaff.password=? and tblbonlivraison.type=1  ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

    /**
     * get total  getnombreBonlivraisonRetourner
     *
    */ 
    public function getnombreBonlivraisonRetourner($id,$tocken){
        $query = "SELECT  count(*) as 'total' from  tblbonlivraison,tblstaff 
        WHERE  tblbonlivraison.id_livreur=tblstaff.staffid and id_livreur=? and tblstaff.password=?  and tblbonlivraison.type=2 ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }


     /**
     * get total  getnombreEtatColisLivrer
     *
    */ 
    public function getnombreEtatColisLivrer($id,$tocken){
        $query = "SELECT  count(*) as 'total' from tbletatcolislivre,tblstaff  
        where tblstaff.staffid=? 
        and tblstaff.staffid=tbletatcolislivre.id_livreur
        and tblstaff.password=? ";
        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $tocken);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['total']==null)
        {
            $row['total']="0";
        }
        return $row['total'];
    }

   


    

    

    
}
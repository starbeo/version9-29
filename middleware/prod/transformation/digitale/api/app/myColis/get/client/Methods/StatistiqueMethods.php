<?php
class StatistiqueMethods{
 
    // database connection 
    private $conn;

    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    // get total  getnombrecolisTotal
    public function getnombrecolisTotal($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblexpediteurs 
        WHERE  tblcolis.id_expediteur =? 
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrefactureTotal
    public function getnombrefactureTotal($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM `tblfactures`,tblexpediteurs WHERE  tblfactures.id_expediteur =?
        and tblexpediteurs.id=tblfactures.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrereclamationTotal
    public function getnombrereclamationTotal($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblreclamations,tblexpediteurs WHERE   tblreclamations.relation_id =? 
        and tblexpediteurs.id=tblreclamations.relation_id and tblexpediteurs.password=?";
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
    // get total  getnombrecolislivre
    public function getnombrecolislivre($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=2 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisRetourne
    public function getnombrecolisRetourne($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_id`=3 AND tblcolis.id_expediteur =? 
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getnombrecolisEncours
    public function getnombrecolisEncours($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_id`=1 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisExpedie
    public function getnombrecolisExpedie($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=4 AND tblcolis.id_expediteur =? 
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getnombrecolisAnnuler
    public function getnombrecolisAnnuler($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=10 AND tblcolis.id_expediteur =? 
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getnombrecolisRefuse
    public function getnombrecolisRefuse($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=9 AND tblcolis.id_expediteur =? 
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getnombrecolisPasResponse
    public function getnombrecolisPasResponse($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=6 AND tblcolis.id_expediteur =? 
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getnombrecolisReporte
    public function getnombrecolisReporte($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=11 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisInjoignable
    public function getnombrecolisInjoignable($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=7 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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

    // get total  getnombrecolisRamasser
    public function getnombrecolisRamasser($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=5 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisRetourneralagence
    public function getnombrecolisRetourneralagence($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE status_reel=13 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisNumeroErronner
    public function getnombrecolisNumeroErronner($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblcolis,tblexpediteurs WHERE `status_reel`=8 AND tblcolis.id_expediteur =?
         and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrecolisEnAttente
    public function getnombrecolisEnAttente($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM `tblcolisenattente`,tblexpediteurs where colis_id is null and id_expediteur=?
        and tblexpediteurs.id=tblcolisenattente.id_expediteur and tblexpediteurs.password=? ";
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
    // get total  getnombrefactureLivre
    public function getnombrefactureLivre($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM `tblfactures`,tblexpediteurs WHERE tblfactures.type=2 AND  tblfactures.id_expediteur =?
        and tblexpediteurs.id=tblfactures.id_expediteur and tblexpediteurs.password=?  ";
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
    // get total  getnombrefactureRetourne
    public function getnombrefactureRetourne($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM `tblfactures`,tblexpediteurs WHERE tblfactures.type=3 AND  tblfactures.id_expediteur =?
        and tblexpediteurs.id=tblfactures.id_expediteur and tblexpediteurs.password=?  ";
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
    // get total  getnombrereclamationtraite
    public function getnombrereclamationtraite($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM `tblreclamations`,tblexpediteurs WHERE tblreclamations.etat=1 AND  tblreclamations.relation_id =?
        and tblexpediteurs.id=tblreclamations.relation_id and tblexpediteurs.password=?  ";
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
    
    // get total  getnombrereclamationnontraite
    public function getnombrereclamationnontraite($id,$tocken){
        $query = "SELECT count(*) as 'total' FROM tblreclamations,tblexpediteurs WHERE tblreclamations.etat=0 AND  tblreclamations.relation_id =? 
        and tblexpediteurs.id=tblreclamations.relation_id and tblexpediteurs.password=? ";
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
    /* update 08/09/2018*/

    // get total  getsommecrbtcolislivre
    public function getsommecrbtcolislivre($id,$tocken){
        $query = "SELECT SUM(`crbt`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=2 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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

   
    // get total  getsommefraiscolislivre
    public function getsommefraiscolislivre($id,$tocken){
        $query = "SELECT SUM(`frais`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=2 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getsommeprixnetcolislivre
    public function getsommeprixnetcolislivre($id,$tocken){
        $query = "SELECT SUM(crbt-frais) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=2 AND tblcolis.id_expediteur =? 
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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

    // get total  getsommecrbtcolisretourner
    public function getsommecrbtcolisretourner($id,$tocken){
        $query = "SELECT SUM(`crbt`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=3 AND tblcolis.id_expediteur =? 
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
     
    // get total  getsommefraiscolisretourner
    public function getsommefraiscolisretourner($id,$tocken){
        $query = "SELECT SUM(`frais`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=3 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getsommeprixnetcolisretourner
    public function getsommeprixnetcolisretourner($id,$tocken){
        $query = "SELECT SUM(crbt-frais) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=3 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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

    // get total  getsommecrbtcolisencours
    public function getsommecrbtcolisencours($id,$tocken){
        $query = "SELECT SUM(`crbt`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=1 AND tblcolis.id_expediteur =? 
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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
    // get total  getsommefraiscolisencours
    public function getsommefraiscolisencours($id,$tocken){
        $query = "SELECT SUM(`frais`) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=1 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=?";
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

    // get total  getsommeprixnetcolisencours
    public function getsommeprixnetcolisencours($id,$tocken){
        $query = "SELECT SUM(crbt-frais) as 'total' FROM tblcolis,tblexpediteurs WHERE `status_id`=1 AND tblcolis.id_expediteur =?
        and tblexpediteurs.id=tblcolis.id_expediteur and tblexpediteurs.password=? ";
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


     // get total  getbonlivraisonclient
     public function getbonlivraisonclient($id,$tocken){
        $query = "SELECT count(*) as 'total' from tblbonlivraisoncustomer,tblexpediteurs
        where tblexpediteurs.id=tblbonlivraisoncustomer.id_expediteur
        and tblbonlivraisoncustomer.id_expediteur=?
        and tblexpediteurs.password=? ";
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

    /*FIN */

    
}
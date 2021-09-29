<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function v4()
    {
        $this->v4_sql();
        $this->v4_init_token_expediteur();
        $this->v4_init_total_factures_delivred();
    }
    
    public function v4_sql()
    {
        //$this->db->query("");
    }
    
    public function v4_init_token_expediteur()
    {
        // Get Expediteurs
        $expediteurs = $this->db->get('tblexpediteurs')->result_array();

        $cpt = 0;
        $this->load->helper('phpass');
        foreach ($expediteurs as $exp) {
            // Generate token
            $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $token = $hasher->HashPassword($exp['date_created'] . '-' . $exp['id']);
            //Update Expediteur
            $this->db->where('id', $exp['id']);
            $this->db->update('tblexpediteurs', array('token' => $token));
            if ($this->db->affected_rows() > 0) {
                $cpt++;
            }
        }
        echo 'Nbr : ' . $cpt;
        exit;
    }
    
    public function v4_init_total_factures_delivred()
    {
        echo "Début : " . date('d/m/Y H:i:s');
        echo "<br><br>";
        
        $this->db->where('total_net', 0);
        $this->db->where('type', 2);
        $this->db->order_by('id', 'DESC');
        $factures = $this->db->get('tblfactures')->result_array();

        echo "Nbr Factures : " . count($factures);
        echo "<br><br>";

        $cpt = 0;
        foreach ($factures as $key => $f) {
            //Get Total CRBT, Total Frais et Total NET Colis Facture
            $this->db->select('SUM(tblcolis.crbt) as total_crbt, SUM(tblcolis.frais) as total_frais, SUM(tblcolis.crbt) - SUM(tblcolis.frais) as total_net');
            $this->db->join('tblcolis', 'tblcolis.id = tblcolisfacture.colis_id', 'left');
            $this->db->where('tblcolisfacture.facture_id', $f['id']);
            $result = $this->db->get('tblcolisfacture')->row();
            $totalCrbt = 0;
            $totalFrais = 0;
            $totalNet = 0;
            if ($result) {
                if(is_numeric($result->total_crbt)) {
                    $totalCrbt = $result->total_crbt;
                }
                if(is_numeric($result->total_frais)) {
                    $totalFrais = $result->total_frais;
                }
                if(is_numeric($result->total_net)) {
                    $totalNet = $result->total_net;
                }
                //Update facture
                $this->db->where('id', $f['id']);
                $this->db->update('tblfactures', array('total_crbt' => $totalCrbt, 'total_frais' => $totalFrais, 'total_net' => $totalNet));
                if ($this->db->affected_rows() > 0) {
                    $cpt++;
                }
            }
        }

        echo "<br><br>";
        echo "Nbr Factures Modifié : " . $cpt;
        echo "<br><br>";
        echo "Fin : " . date('d/m/Y H:i:s');

        exit();
    }
    
}

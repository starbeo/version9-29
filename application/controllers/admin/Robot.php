<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 3600);

class Robot extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->db->where('status_id', 3);
        $this->db->where('date_livraison IS NULL');
        $colis = $this->db->get('tblcolis')->result_array();
        var_dump($colis);
        exit;

        $cpt = 0;
        $cpt1 = 0;
        foreach ($colis as $key => $coli) {
            $this->db->where('code_barre', $coli['code_barre']);
            $this->db->order_by('date_created', 'desc');
            $this->db->limit(1);
            $statut = $this->db->get('tblstatus')->row();
            if (is_numeric($statut->type) && $statut->type == 3) {
                $date = date('Y-m-d', strtotime($statut->date_created));
                $this->db->where('code_barre', $coli['code_barre']);
                $this->db->update('tblcolis', array('date_livraison' => $date));
                $cpt1++;
            }
            $cpt++;
        }

        echo "Nbr Colis :" . count($colis);
        echo "<br><br>";
        echo "Cpt :" . $cpt;
        echo "<br><br>";
        echo "Cpt1 :" . $cpt1;
        echo "<br><br>";
        exit();
    }

    public function quartiers()
    {
        echo "On commence !!";
        exit();

        $quartiers = array("Al Kasaba", "Aviation", "Cap spartel", "Centre ville", "Cité californie", "Girari", "Ibn Taymia", "M'nar", "M'sallah", "Makhoukha", "Malabata", "Marchane", "Marjane", "Moujahidine", "Moulay Youssef", "Santa", "Val Fleuri", "Vieille montagne", "Ziatene", "Achennad", "Aharrarine", "Ahlane", "Aida", "Al Anbar", "Al Warda", "Aouama Gharbia", "Beausejour", "Behair", "Ben Dibane", "Beni Makada Lakdima", "Beni Said", "Beni Touzine", "Bir Aharchoune", "Bir Chifa", "Bir El Ghazi", "Bouchta-Abdelatif", "Bouhout", "Dher Ahjjam", "Dher Lahmam", "El Baraka", "El Haj El Mokhtar", "El Khair", "El Mers", "El Mrabet", "Ennasr", "Gourziana", "Haddad", "Hanaa", "Jirrari", "Les Rosiers", "Zemmouri", "Zouitina", "Al Amal", "Al Mandar Al Jamil", "Alia", "Benkirane", "Charf", "Draoua", "Drissia", "El Majd", "El Oued", "Mghogha", "Nzaha", "Sania", "Tanger City Center", "Tanja Balia", "Azib Haj Kaddour", "Bel Air - Val fleuri", "Bir Chairi", "Branes", "Casabarata", "Castilla", "Hay Al Bassatine", "Hay El Boughaz", "Hay Zaoudia", "Lalla Chafia", "Souani", "Achakar", "Administratif", "Ahammar", "Ain El Hayani", "Algerie", "Boukhalef", "Branes Kdima", "Californie", "Centre", "De La Plage", "Du Golf", "Hay Hassani", "Iberie", "Jbel Kbir", "Laaouina", "Marchan", "Mediouna", "Mesnana", "Mghayer", "Mister Khouch", "Mozart", "Msala", "Médina", "Port Tanger ville", "Rmilat", "Star Hill", "manar");

        foreach ($quartiers as $key => $quartier) {
            $data['ville_id'] = 3;
            $data['affecter_livreur'] = 0;
            $data['id_entreprise'] = 0;
            $data['name'] = $quartier;
            $this->db->insert('tblquartiers', $data);
        }

        echo "Fin";
        exit();
    }

    public function check_telephone()
    {
        //echo "On commence !!";
        //exit();

        $this->db->select('id, telephone');
        $this->db->where('colis_id IS NULL');
        $this->db->where("telephone LIKE '06%'");
        //$this->db->like('telephone', "+2125");
        //$this->db->like('telephone', "+2126");
        //$this->db->like('telephone', "+2127");
        //$this->db->like('telephone', "+212 6");
        //$this->db->like('telephone', "'+212 6");
        //$this->db->like('telephone', "+212 06");
        //$this->db->like('telephone', "+21206");
        //$this->db->like('telephone', "+212 7");
        //$this->db->like('telephone', "'+212 7");
        //$this->db->like('telephone', "+212 07");
        //$this->db->like('telephone', "+21207");
        $colis = $this->db->get('tblcolisenattente')->result_array();
        //echo $this->db->last_query();
        var_dump($colis);
        exit();

        echo "Nbr Colis : " . count($colis);
        echo "<br><br>";
        $cpt = 0;
        foreach ($colis as $key => $c) {
            $c['telephone'] = str_replace(" ", "", $c['telephone']);
            $c['telephone'] = str_replace("-", "", $c['telephone']);
            $c['telephone'] = str_replace("'", "", $c['telephone']);
            $c['telephone'] = str_replace(".", "", $c['telephone']);
            $c['telephone'] = str_replace("+2120", "+212", $c['telephone']);
            if (strlen($c['telephone']) == 13) {
                $c['telephone'] = str_replace("+212", "0", $c['telephone']);
                $this->db->where('id', $c['id']);
                $this->db->update('tblcolisenattente', array('telephone' => $c['telephone']));
                $cpt++;
            } else if (strlen($c['telephone']) == 10) {
                $this->db->where('id', $c['id']);
                $this->db->update('tblcolisenattente', array('telephone' => $c['telephone']));
                $cpt++;
            }
        }

        echo "Nbr Modifié : " . $cpt;
        echo "<br><br>";
        echo "FIN";

        exit();
    }

    //Check if numéro de facture colis is NULL and the id colis is in the table "tblcolisfacture"
    public function check()
    {
        $this->db->where('num_facture IS NULL');
        $colis = $this->db->get('tblcolis')->result_array();
        foreach ($colis as $key => $value) {
            $this->db->where('colis_id', $value['id']);
            $colisfacture = $this->db->get('tblcolisfacture')->row();
            if ($colisfacture) {
                $this->db->where('id', $colisfacture->id);
                $this->db->delete('tblcolisfacture');
            }
        }
        exit();
    }

    public function get_colis_without_num_bonlivraison_and_colis_id_in_tblcolisbonlivraison()
    {
        $this->db->where('num_bonlivraison IS NULL');
        $this->db->where('id IN (SELECT colis_id FROM `tblcolisbonlivraison` WHERE colis_id = tblcolis.id)');
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        echo "Nbr Colis : " . count($colis);
        echo "<br><br>";

        $cpt = 0;
        foreach ($colis as $key => $c) {
            //Get Colis Bon Livraison
            $this->db->where('colis_id', $c['id']);
            $colisbonlivraison = $this->db->get('tblcolisbonlivraison')->result_array();
            if ($colisbonlivraison) {
                if (count($colisbonlivraison) == 1) {
                    $this->db->where('id', $colisbonlivraison[0]['colis_id']);
                    $this->db->update('tblcolis', array('num_bonlivraison' => $colisbonlivraison[0]['bonlivraison_id']));
                    $cpt++;
                } else if (count($colisbonlivraison) > 1) {
                    for ($i = count($colisbonlivraison) - 1; $i >= 0; $i--) {
                        if ($i == (count($colisbonlivraison) - 1)) {
                            $this->db->where('id', $colisbonlivraison[$i]['colis_id']);
                            $this->db->update('tblcolis', array('num_bonlivraison' => $colisbonlivraison[$i]['bonlivraison_id']));
                            $cpt++;
                        }
                    }
                }
            }
        }

        echo "<br>";
        echo "Nbr Colis Modifié : " . $cpt;
        echo "<br>";
        echo "FIN";

        exit();
    }

    public function init_total_factures_delivred()
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

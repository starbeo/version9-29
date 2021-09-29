<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_expediteur_id_by_token($storeToken)
    {
        $this->db->where('token', $storeToken);
        $expediteur = $this->db->get('tblexpediteurs')->row();
        $expediteurId = '';
        if ($expediteur) {
            $expediteurId = $expediteur->id;
        }

        return $expediteurId;
    }
    /**
     * @param array $_POST data
     * @return integer
     * Add new colis en attente
     */
    /* public function check_barcode_colis_en_attente_already_exists($barcode)
      {
      $totalRows = 0;

      $this->db->where('code_barre', $barcode);
      $totalRowsColisEnAttente = $this->db->count_all_results('tblcolisenattente');
      if ($totalRowsColisEnAttente > 0) {
      $totalRows++;
      }

      $this->db->where('code_barre', $barcode);
      $totalRowsColis = $this->db->count_all_results('tblcolis');
      if ($totalRowsColis > 0) {
      $totalRows++;
      }

      return $totalRows;
      } */

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis en attente
     */
    public function add_colis_en_attente($data)
    {
        $data['etat_id'] = 1;
        $data['status_id'] = 12;
        $data['date_creation'] = date('Y-m-d');
        $data['id_entreprise'] = 0;
        //Génération du code à barre
        $data['code_barre'] = get_option('alias_barcode') . $data['id_expediteur'] . 'MA' . get_nbr_coli_by_expediteur($data['id_expediteur']);

        if (isset($data['commentaire']) && empty($data['commentaire'])) {
            unset($data['commentaire']);
        }

        $this->db->insert('tblcolisenattente', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Add Notification to admin
            $_data['description'] = "Nouveau Colis en attente Ajouté [Code d'envoi: <b>" . $data['code_barre'] . "</b>]";
            $_data['link'] = admin_url('colis_en_attente/index/' . $data['code_barre']);
            add_notification_to_admin($_data);

            logActivityCustomer("Nouveau Colis en attente Ajouté [Code d'envoi: " . $data['code_barre'] . ", ID: " . $insert_id . "]", $data['id_expediteur']);

            return $insert_id;
        }

        return false;
    }
}

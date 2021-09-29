<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis_en_attente_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_expediteur_logged_in()) {
            $id_expediteur = get_expediteur_user_id();
        } else {
            return false;
        }

        $this->db->where('id_expediteur', $id_expediteur);

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblcolisenattente')->row();
        }

        return $this->db->get('tblcolisenattente')->result_array();
    }

    public function get_colis_en_attente_by_date($dateStart, $dateEnd)
    {
        $this->db->select('tblcolisenattente.code_barre, tblcolisenattente.num_commande, tblexpediteurs.nom, tblcolisenattente.telephone, tblcolisenattente.date_creation, tblstatuscolis.name as statut, tblvilles.name as ville, tblcolisenattente.crbt');
        $this->db->join('tblexpediteurs', 'tblexpediteurs.id = tblcolisenattente.id_expediteur', 'left');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblcolisenattente.status_id', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolisenattente.id_expediteur', 'left');
        $this->db->where('tblcolisenattente.date_creation >= "' . $dateStart . '" AND tblcolisenattente.date_creation <= "' . $dateEnd . '"');
        $colisEnAttente = $this->db->get('tblcolisenattente')->result_array();

        return $colisEnAttente;
    }

    public function get_info_colis($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $this->db->where('id_entreprise', $id_E);

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblcolisenattente')->row();
        }

        return $this->db->get('tblcolisenattente')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new colis en attente
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        unset($data['id']);

        if (is_expediteur_logged_in()) {
            $data['id_expediteur'] = get_expediteur_user_id();
            //Add delivery men to colis en attente
            if (is_numeric($data['id_expediteur'])) {
                $this->load->model('expediteurs_model');
                $expediteur = $this->expediteurs_model->get($data['id_expediteur']);
                if ($expediteur && is_numeric($expediteur->livreur)) {
                    $data['id_livreur'] = $expediteur->livreur;
                }
            }
        }

        if (isset($data['num_commande']) && !empty($data['num_commande']) && _startsWith(strtoupper($data['num_commande']), 'TA') && endsWith(strtoupper($data['num_commande']), 'MA')) {
            $data['code_barre'] = $data['num_commande'];
        } else {
            $data['code_barre'] = get_option('alias_barcode') . $data['id_expediteur'] . 'MA' . get_nbr_coli_by_expediteur($data['id_expediteur']);
        }
        $data['etat_id'] = 1;
        $data['status_id'] = 12;
        $data['date_creation'] = date('Y-m-d');
        $data['id_entreprise'] = $id_E;
        
        if (isset($data['adresse']) && !empty($data['adresse'])) {
            $data['adresse'] = trim($data['adresse']);
        } else {
            $data['adresse'] = '';
        }
        
        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }

        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }

        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }

        if (isset($data['type_livraison']) && $data['type_livraison'] == 'point_relai' && isset($data['point_relai_id']) && is_numeric($data['point_relai_id'])) {
            // Get point relai
            $this->load->model('points_relais_model');
            $point_relai = $this->points_relais_model->get($data['point_relai_id']);
            if ($point_relai) {
                $data['ville'] = $point_relai->ville;
            }
        }

        $this->db->insert('tblcolisenattente', $data);
 
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Add Notification to admin
            $_data['description'] = "Nouveau Colis en attente Ajouté [Code d'envoi: <b>" . $data['code_barre'] . "</b>]";
            $_data['link'] = admin_url('colis_en_attente/index/' . $data['code_barre']);
            add_notification_to_admin($_data);
            //Add log Activity Customer
            logActivityCustomer("Nouveau Colis en attente Ajouté [Code d'envoi: " . $data['code_barre'] . ", ID: " . $insert_id . "]");

            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update colis en attente to database
     */
    public function update($data, $id)
    {
        $barcode = $data['code_barre'];
        unset($data['code_barre']);
        unset($data['id_expediteur']);
        
        if (isset($data['adresse']) && !empty($data['adresse'])) {
            $data['adresse'] = trim($data['adresse']);
        } else {
            $data['adresse'] = '';
        }
        
        if (isset($data['ouverture'])) {
            $data['ouverture'] = 1;
        } else {
            $data['ouverture'] = 0;
        }

        if (isset($data['option_frais'])) {
            $data['option_frais'] = 1;
        } else {
            $data['option_frais'] = 0;
        }

        if (isset($data['option_frais_assurance'])) {
            $data['option_frais_assurance'] = 1;
        } else {
            $data['option_frais_assurance'] = 0;
        }

        if (isset($data['type_livraison']) && $data['type_livraison'] == 'point_relai' && isset($data['point_relai_id']) && is_numeric($data['point_relai_id'])) {
            // Get point relai
            $this->load->model('points_relais_model');
            $point_relai = $this->points_relais_model->get($data['point_relai_id']);
            if ($point_relai) {
                $data['ville'] = $point_relai->ville;
            }
        }

        $this->db->where('id', $id);
        $this->db->update('tblcolisenattente', $data);
        if ($this->db->affected_rows() > 0) {
            //Add log Activity Customer
            logActivityCustomer("Colis en attente Modifié [Code d'envoi: " . $barcode . ", ID: " . $id . "]");
            
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete colis en attente from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_expediteur_logged_in()) {
            $this->db->where('id_expediteur', get_expediteur_user_id());
        }

        $this->db->where('id', $id);
        $this->db->delete('tblcolisenattente');
        if ($this->db->affected_rows() > 0) {
            logActivityCustomer('Colis en attente' . _l('deleted') . ' [ID: ' . $id . ']');
            
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     * Delete colis en attente from database, if used return array with key referenced
     */
    public function delete_colis_en_attente_by_date($start, $end)
    {
        $this->db->where('date_creation >= "' . $start . '" AND date_creation <= "' . $end . '"');
        $this->db->delete('tblcolisenattente');
        if ($this->db->affected_rows() > 0) {
            logActivity('Colis en attente' . _l('deleted') . ' [' . _l('start_date') . ': ' . $start . ', ' . _l('end_date') . ' : ' . $end . ']');
            
            return true;
        }

        return false;
    }
}

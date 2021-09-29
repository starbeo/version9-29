<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apis_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Get statuses api
     */
    public function get_statuses()
    {
        $statuses = array(
            array('id' => 1, 'name' => _l('requested'), 'color_text' => 'info'),
            array('id' => 2, 'name' => _l('validate'), 'color_text' => 'success'),
            array('id' => 3, 'name' => _l('blocked'), 'color_text' => 'danger')
        );

        return $statuses;
    }

    /**
     * @return array
     * Get packs api
     */
    public function get_packs($id = '', $where = array())
    {
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }
        
        if (is_numeric($id)) {
            $this->db->where('tblapipacks.id', $id);
            return $this->db->get('tblapipacks')->row();
        }
        
        return $this->db->get('tblapipacks')->result_array();
    }

    /**
     * @return array
     * Get access
     */
    public function get_access($id, $where = array())
    {
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }
        
        if (is_numeric($id)) {
            $this->db->where('tblapiaccess.id', $id);
            return $this->db->get('tblapiaccess')->row();
        }
        
        return $this->db->get('tblapiaccess')->row();
    }
    
    /**
     * @return array
     * Get access by token
     */
    public function get_access_by_token($token)
    {
        $this->db->where('token', $token);
        return $this->db->get('tblapiaccess')->row();
    }
    
    /**
     * @return array
     * Get access by client id
     */
    public function get_last_access_by_client_id($id)
    {
        $this->db->where('client_id', $id);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        return $this->db->get('tblapiaccess')->row();
    }

    /**
     * @return boolean
     * Add access api
     */
    public function add_access($packId)
    {
        if (total_rows('tblapiaccess', 'client_id = ' . get_expediteur_user_id() . ' AND status IN (1, 2)') > 0) {
            return array('access_already_exist' => true);
        }

        //Data
        $dataInsert = array();
        $dataInsert['client_id'] = get_expediteur_user_id();
        $dataInsert['pack_id'] = $packId;
        $dataInsert['status'] = 1;
        $dataInsert['nbr_appels'] = 0;
        $dataInsert['addedfrom'] = 0;
        $dataInsert['id_entreprise'] = get_entreprise_id();

        $this->db->insert('tblapiaccess', $dataInsert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //$this->log_activity('Nouveau accès API crée [ID:' . $insert_id . ', Client ID:' . $dataInsert['client_id'] . ', Pack ID:' . $dataInsert['pack_id'] . ']');

            return true;
        }

        return false;
    }

    /**
     * @return boolean
     * Generate access api
     */
    public function generate_access($clientId, $accessId)
    {
        if (total_rows('tblapiaccess', 'client_id = ' . $clientId . ' AND status = 2') > 0) {
            return array('access_already_exist' => true);
        } else if (total_rows('tblapiaccess', 'client_id = ' . $clientId . ' AND status = 1') == 0) {
            return array('access_does_not_exists' => true);
        }
        
        //Data
        $dataUpdate = array();
        $dataUpdate['token'] = randomTokenApiClient();
        $dataUpdate['status'] = 2;
        $dataUpdate['date_start'] = date('Y-m-d H:i:s');

        $this->db->where('status', 1);
        $this->db->where('client_id', $clientId);
        $this->db->where('id', $accessId);
        $this->db->update('tblapiaccess', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            //$this->log_activity('Accès API généré [Accès ID:' . $accessId . ', Client ID:' . $clientId . ']');

            return true;
        }

        return false;
    }
    
    /**
     * @return boolean
     * Increment number of appels access
     */
    public function increment_number_of_appels_access($accessId)
    {
        $this->db->set('nbr_appels', 'nbr_appels + 1', FALSE);
        $this->db->where('id', $accessId);
        $this->db->update('tblapiaccess');
        if ($this->db->affected_rows() > 0) {
            //$this->log_activity('Nombre d\'appels accès API augmenté [ID:' . $accessId . ']');

            return true;
        }

        return false;
    }
    
    /**
     * @return boolean
     * Block access api
     */
    public function block_access($accessId)
    {
        //Data
        $dataUpdate['status'] = 3;
        $dataUpdate['date_end'] = date('Y-m-d H:i:s');

        $this->db->where('id', $accessId);
        $this->db->update('tblapiaccess', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            //$this->log_activity('Accès API Bloqué [ID:' . $accessId . ']');

            return true;
        }

        return false;
    }
}

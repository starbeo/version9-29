<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contrats_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer $id (optional)
     * @param  array $where (optional)
     * @return mixed
     * Get contract object based on passed id if not passed id return array of all contracts
     */
    public function get($id = false, $where = array())
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $this->db->where('id_entreprise', $id_E);

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblcontrats')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tblcontrats')->result_array();
    }

    /**
     * @param  integer $clientId (optional)
     * @return object
     * Get contract by client
     */
    public function get_contrat_by_client($clientId)
    {
        if (is_numeric($clientId)) {
            $this->db->where('client_id', $clientId);
            return $this->db->get('tblcontrats')->row();
        }
    }

    /**
     * @return array
     * Get clients not have contract
     */
    public function get_clients_not_have_contract()
    {
        $this->db->select('tblexpediteurs.*');
        $this->db->join('tblcontrats', 'tblcontrats.client_id = tblexpediteurs.id', 'left');
        $this->db->where('tblcontrats.client_id IS NULL');
        $this->db->where('tblexpediteurs.active', 1);
        return $this->db->get('tblexpediteurs')->result_array();
    }

    /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new contract
     */
    public function add($data)
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();
        $data['subject'] = _l('contract') . ' ' . strtoupper(get_client_full_name($data['client_id']));

        if (empty($data['datestart'])) {
            unset($data['datestart']);
        } else {
            $data['datestart'] = to_sql_date($data['datestart']);
        }

        if (empty($data['dateend'])) {
            unset($data['dateend']);
        } else {
            $data['dateend'] = to_sql_date($data['dateend']);
        }

        if (empty($data['date_created_client'])) {
            unset($data['date_created_client']);
        } else {
            $data['date_created_client'] = to_sql_date($data['date_created_client']);
        }

        if (isset($data['trash'])) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }

        if (isset($data['not_visible_to_client'])) {
            $data['not_visible_to_client'] = 1;
        } else {
            $data['not_visible_to_client'] = 0;
        }

        $this->db->insert('tblcontrats', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_contract_added') . ' [' . _l('id') . ' : ' . $insert_id . ', ' . _l('subject') . ' : ' . $data['subject'] . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer Contract ID
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['subject'] = _l('contract') . ' ' . strtoupper(get_client_full_name($data['client_id']));

        if (empty($data['datestart'])) {
            unset($data['datestart']);
        } else {
            $data['datestart'] = to_sql_date($data['datestart']);
        }

        if (empty($data['dateend'])) {
            unset($data['dateend']);
        } else {
            $data['dateend'] = to_sql_date($data['dateend']);
        }

        if (empty($data['date_created_client'])) {
            unset($data['date_created_client']);
        } else {
            $data['date_created_client'] = to_sql_date($data['date_created_client']);
        }

        if (isset($data['trash'])) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }

        if (isset($data['not_visible_to_client'])) {
            $data['not_visible_to_client'] = 1;
        } else {
            $data['not_visible_to_client'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update('tblcontrats', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('contract_updated') . ' [' . _l('id') . ' : ' . $id . ', ' . _l('subject') . ' : ' . $data['subject'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete contract, also attachment will be removed if any found
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcontrats');
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('contract_deleted') . ' [' . $id . ']');
            return true;
        }

        return false;
    }
}

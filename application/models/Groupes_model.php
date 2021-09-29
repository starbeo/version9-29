<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groupes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get groupe
     * @return mixed
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblgroupes')->row();
        }

        return $this->db->get('tblgroupes')->result_array();
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Add new groupe
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tblgroupes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Groupe Crée [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Update groupe
     */
    public function update($data)
    {
        $dataUpdateId = $data['id'];
        $dataUpdate['name'] = $data['name'];

        $this->db->where('id', $dataUpdateId);
        $this->db->update('tblgroupes', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Groupe Modifié [ID:' . $dataUpdateId . ']');
            return true;
        }
        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * assignment group to customer
     */
    public function affectation($data)
    {
        if (!isset($data['clients']) || !is_array($data['clients']) || count($data['clients']) <= 0) {
            return false;
        }

        $this->load->model('expediteurs_model');
        $clientUpdated = 0;
        foreach ($data['clients'] as $clientId) {
            //Get client
            $client = $this->expediteurs_model->get($clientId);
            $nameClient = '';
            if ($client) {
                $nameClient = $client->nom;
            }

            //Update client
            $this->db->where('id', $clientId);
            $this->db->update('tblexpediteurs', array('groupe_id' => $data['groupe']));
            if ($this->db->affected_rows() > 0) {
                logActivity('Client affecté au groupe [ID:' . $clientId . ', Name : ' . $nameClient . ']');
                $clientUpdated++;
            }
        }

        if ($clientUpdated > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param integer $_GET id
     * @return boolean
     * Delete groupe
     */
    public function delete($id)
    {
//        if (is_reference_in_table('emplacement', 'tblstatus', $id)) {
//            return array('referenced' => true);
//        }

        $this->db->where('id', $id);
        $this->db->delete('tblgroupes');
        if ($this->db->affected_rows() > 0) {
            logActivity('Groupe Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

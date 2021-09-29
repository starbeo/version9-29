<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banques_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get banque
     * @return mixed
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblbanques')->row();
        }

        return $this->db->get('tblbanques')->result_array();
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Add new banque
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tblbanques', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Banque Crée [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return boolean
     * Update banque
     */
    public function update($data)
    {
        $dataUpdateId = $data['id'];
        $dataUpdate['name'] = $data['name'];

        $this->db->where('id', $dataUpdateId);
        $this->db->update('tblbanques', $dataUpdate);
        if ($this->db->affected_rows() > 0) {
            logActivity('Banque Modifié [ID:' . $dataUpdateId . ']');
            return true;
        }
        return false;
    }

    /**
     * @param integer $_GET id
     * @return boolean
     * Delete banque
     */
    public function delete($id)
    {
//        if (is_reference_in_table('emplacement', 'tblstatus', $id)) {
//            return array('referenced' => true);
//        }

        $this->db->where('id', $id);
        $this->db->delete('tblbanques');
        if ($this->db->affected_rows() > 0) {
            logActivity('Banque Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

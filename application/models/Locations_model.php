<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get location
     * @return mixed
     */
    public function get($id = '', $depot = false)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($depot)) {
            $this->db->where('depot', $depot);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbllocations')->row();
        }

        return $this->db->get('tbllocations')->result_array();
    }

    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['default'] = 0;
        
        $this->db->insert('tbllocations', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('Nouveau Location Crée [ID:' . $insert_id . ', Name:' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tbllocations', array('name' => $data['name']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Location Modifié [ID:' . $data['id'] . ']');

            return true;
        }
        
        return false;
    }

    public function delete($id)
    {
        if (is_reference_in_table('emplacement_id', 'tblstatus', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->where('is_default', 0);
        $this->db->delete('tbllocations');
        if ($this->db->affected_rows() > 0) {
            logActivity('Location Supprimé [ID:' . $id . ']');

            return true;
        }

        return false;
    }
}

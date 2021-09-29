<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quartiers_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get quartiers
     * @return mixed
     * */
    public function get($id = '', $where = array())
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('id_entreprise', $id_E);

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblquartiers')->row();
        }

        return $this->db->get('tblquartiers')->result_array();
    }

    public function add($data)
    {

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $data['affecter_livreur'] = 0;

        $this->db->insert('tblquartiers', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Quartier Ajouté [ID:' . $insert_id . ', Nom:' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update($data)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $this->db->where('id', $data['id']);
        $this->db->update('tblquartiers', array('name' => $data['name'], 'ville_id' => $data['ville_id']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Quartier Modifié [ID:' . $data['id'] . ']');

            return true;
        }

        return false;
    }

    public function delete($id)
    {

        $this->db->where('id', $id, '');
        $this->db->delete('tblquartiers');

        if ($this->db->affected_rows() > 0) {
            logActivity('Quartier Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

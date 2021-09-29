<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_en_attente_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get clients en attente
     * @return mixed
     */
    public function get($id = '', $active = 1, $where = array(), $select = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $_select = 'tblvilles.name as ville_name, tblrejoindreexpediteur.*';
        if (!empty($select)) {
            $_select .= ', ' . $select;
        }
        $this->db->select($_select);

        $this->db->join('tblvilles', 'tblvilles.id = tblrejoindreexpediteur.ville_id');

        if (is_int($active)) {
            $this->db->where('tblrejoindreexpediteur.active', $active);
        }

        if (is_numeric($id)) {
            $this->db->where('tblrejoindreexpediteur.id', $id);
            return $this->db->get('tblrejoindreexpediteur')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tblrejoindreexpediteur')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new client en attente
     */
    public function add($data)
    {
        $data['active'] = 0;
        $data['pays'] = 'Maroc';

        $this->db->insert('tblrejoindreexpediteur', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Client en attente Ajouté [' . $data['societe'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete client en attente
     */
    public function delete($id)
    {
        do_action('before_expediteur_deleted', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblrejoindreexpediteur');
        if ($this->db->affected_rows() > 0) {
            logActivity('Client en attente supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}

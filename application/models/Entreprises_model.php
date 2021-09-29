<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entreprises_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_entreprises($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id != '') {
            $this->db->where('id_entreprise', $id);
            return $this->db->get('tblentreprise')->row();
        }

        return $this->db->get('tblentreprise')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new department
     */
    public function add($data)
    {
        unset($data['id']);
        $data = do_action('before_entreprise_added', $data);
        $this->db->insert('tblentreprise', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {

            $this->db->query('INSERT INTO `tbloptions` (`name`, `value`, `id_entreprise`) select name, value, ' . $insert_id . ' from tbloptions where id_entreprise = 0 ');

            do_action('after_entreprise_added', $insert_id);
            logActivity('Nouveau Entreprise Ajouté [' . $data['name_entreprise'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update department to database
     */
    public function update($data, $id)
    {

        if ($data['name_entreprise'] == '') {
            $data['name_entreprise'] = NULL;
        }

        $this->db->where('id_entreprise', $id);
        $this->db->update('tblentreprise', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Entreprise Modifié [Nom: ' . $data['name_entreprise'] . ', ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete department from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $current = $this->get_entreprises($id);
        if (is_reference_in_table('id_entreprise', 'tblexpediteurs', $id)) {
            return array(
                'referenced' => true
            );
        }

        do_action('before_entreprise_deleted', $id);
        $this->db->where('id_entreprise', $id);
        $this->db->delete('tblentreprise');

        if ($this->db->affected_rows() > 0) {
            logActivity('Entreprise Supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update entreprise status Active/Inactive
     */
    public function change_entreprise_status($id, $status)
    {
        $this->db->where('id_entreprise', $id);
        $this->db->update('tblentreprise', array(
            'active' => $status
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Entreprise Status Changé [EntrepriseID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }
}

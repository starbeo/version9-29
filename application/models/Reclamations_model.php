<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reclamations_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblreclamations')->row();
        }

        return $this->db->get('tblreclamations')->result_array();
    }

    public function get_etats()
    {
        $priorities = array(
            array('id' => 'non_traite', 'name' => _l('untreated')),
            array('id' => 'traite', 'name' => _l('treaty'))
        );

        return $priorities;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new reclamation
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $data['etat'] = 0;
        $data['relation_id'] = get_expediteur_user_id();
        $data['date_created'] = date('Y-m-d');

        $this->db->insert('tblreclamations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivityCustomer('Nouveau Reclamation Ajouté [' . $data['objet'] . ', ID: ' . $insert_id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update Expediteur to database
     */
    public function update($data, $id)
    {
        if (isset($data['reponse'])) {
            if (!empty($data['reponse']) && $data['reponse'] !== ' ') {
                $data['etat'] = 1;
                $data['staff_id'] = get_staff_user_id();
                $data['date_traitement'] = date('Y-m-d H:i:s');
            } else {
                $data['reponse'] = "";
            }
        }

        $this->db->where('id', $id);
        $this->db->update('tblreclamations', $data);
        if ($this->db->affected_rows() > 0) {
            if (is_expediteur_logged_in()) {
                logActivityCustomer('Reclamation Modifié [ID: ' . $id . ']');
            } else {
                logActivity('Reclamation Modifié [ID: ' . $id . ']');
            }
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete reclamation from database, if used return array with key referenced
     */
    public function delete($id)
    {
        do_action('before_reclamation_deleted', $id);

        if (is_expediteur_logged_in()) {
            $this->db->where('relation_id', get_expediteur_user_id());
        }
        $this->db->where('id', $id);
        $this->db->delete('tblreclamations');
        if ($this->db->affected_rows() > 0) {
            if (is_expediteur_logged_in()) {
                logActivityCustomer('Reclamation Supprimé [ID: ' . $id . ']');
            } else {
                logActivity('Reclamation Supprimé [ID: ' . $id . ']');
            }
            
            return true;
        }

        return false;
    }
}

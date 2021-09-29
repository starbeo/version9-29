<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quartiers_livreur_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get quartiers livreur
     * @return mixed
     * */
    public function get($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbllivreurquartiers')->row();
        }

        return $this->db->get('tbllivreurquartiers')->result_array();
    }

    /**
     * Get livreurs by quartier id
     * @return mixed
     * */
    public function get_livreurs_by_quartier($id)
    {
        if (is_numeric($id)) {
            $this->db->select('livreur_id as id, tblstaff.firstname as firstname, tblstaff.lastname as lastname');
            $this->db->from('tbllivreurquartiers');
            $this->db->join('tblstaff', 'tblstaff.staffid = tbllivreurquartiers.livreur_id', 'left');
            $this->db->where('quartier_id', $id);
            return $this->db->get()->result_array();
        }
    }

    /**
     * Get livreur by quartier id
     * @return mixed
     * */
    public function get_livreur_by_quartier($id)
    {
        if (is_numeric($id)) {
            $this->db->select('livreur_id');
            $this->db->from('tbllivreurquartiers');
            $this->db->where('quartier_id', $id);
            $this->db->limit(1);
            return $this->db->get()->row();
        }
    }

    public function add($data)
    {

        $exists = total_rows('tbllivreurquartiers', array('quartier_id' => $data['quartier_id'], 'livreur_id' => $data['livreur_id']));

        if ($exists == 0) {
            //Insertion de l'ID de l'entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');
            $data['id_entreprise'] = $id_E;

            $this->db->insert('tbllivreurquartiers', $data);
            $insert_id = $this->db->insert_id();

            if ($insert_id) {
                $this->db->where('id', $data['quartier_id']);
                $this->db->update('tblquartiers', array('affecter_livreur' => 1));

                logActivity("Nouveau affectation d'un quartier pour un livreur ajouté [ID:" . $insert_id . ", Quartier ID:" . $data['quartier_id'] . ", Livreur ID: " . $data['livreur_id'] . "]");
                return $insert_id;
            }

            return false;
        } else {
            return array('already_exists' => _l('quartier_livreur_already_exists'));
        }

        return false;
    }

    public function update($data)
    {

        $exists = total_rows('tbllivreurquartiers', array('quartier_id' => $data['quartier_id'], 'livreur_id' => $data['livreur_id']));

        if ($exists == 0) {

            $this->db->where('id', $data['id']);
            $quartier_livreur = $this->db->get('tbllivreurquartiers')->row();

            $this->db->where('id', $data['id']);
            $this->db->update('tbllivreurquartiers', array('quartier_id' => $data['quartier_id'], 'livreur_id' => $data['livreur_id']));
            if ($this->db->affected_rows() > 0) {
                if (!is_null($quartier_livreur)) {
                    $quartier_id = $quartier_livreur->quartier_id;
                    //Si on change le quartier
                    if ($quartier_id !== $data['quartier_id']) {
                        $this->db->where('id', $quartier_id);
                        $this->db->update('tblquartiers', array('affecter_livreur' => 0));

                        $this->db->where('id', $data['quartier_id']);
                        $this->db->update('tblquartiers', array('affecter_livreur' => 1));
                    }
                }
                logActivity("Modification de l'affectation du quartier pour le livreur [ID:" . $id . ", Quartier ID:" . $data['quartier_id'] . ", Livreur ID: " . $data['livreur_id'] . "]");
                return $data['id'];
            }

            return false;
        } else {
            return array('already_exists' => _l('quartier_livreur_already_exists'));
        }

        return false;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $quartier_livreur = $this->db->get('tbllivreurquartiers')->row();

        $this->db->where('id', $id, '');
        $this->db->delete('tbllivreurquartiers');

        if ($this->db->affected_rows() > 0) {
            if (!is_null($quartier_livreur)) {
                $quartier_id = $quartier_livreur->quartier_id;
                $this->db->where('id', $quartier_id);
                $this->db->update('tblquartiers', array('affecter_livreur' => 0));
            }

            logActivity('Affectation du quartier pour le livreur Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

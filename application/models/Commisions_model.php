<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commisions_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Commisions
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        if (is_numeric($id_E)) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tbllivreurcommisions')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        return $this->db->get('tbllivreurcommisions')->result_array();
    }

    /**
     * Get Commisions
     * @return mixed
     */
    public function get_commision_livreur($livreurId = '', $cityId = '')
    {
        $this->db->where('livreur', $livreurId);
        $this->db->where('city', $cityId);
        $commision = $this->db->get('tbllivreurcommisions')->row();

        $totalCommision = 0;
        if ($commision) {
            $totalCommision = $commision->commision;
        }

        return $totalCommision;
    }


    public function get_refuse_commision_livreur($livreurId = '', $cityId = '')
    {
        $this->db->where('livreur', $livreurId);
        $this->db->where('city', $cityId);
        $commision = $this->db->get('tbllivreurcommisions')->row();

        $totalrefuseCommision = 0;
        if ($commision) {
            $totalrefuseCommision = $commision->refuse_commision;
        }

        return $totalrefuseCommision;
    }



    /**
     * Add Commision
     * @return boolean
     */
    public function add($data)
    {
        //Check if commision livreur already  exist with same city & same delivery men
        $commision = $this->get('', 'livreur = ' . $data['livreur'] . ' AND city = ' . $data['city']);
        if ($commision) {
            return array('already_exist' => true);
        }

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        if (isset($data['ville'])) {
            $data['city'] = $data['ville'];
            unset($data['ville']);
        }
        if (isset($data['commision_refuse'])) {
            $data['refuse_commision'] = $data['commision_refuse'];
            unset($data['commision_refuse']);
        }




        $this->db->insert('tbllivreurcommisions', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Commission Crée [ID:' . $insert_id . ']');
            return true;
        }

        return false;
    }

    /**
     * Update Commision
     * @return boolean
     */
    public function update($data)
    {
        if (isset($data['ville'])) {
            $data['city'] = $data['ville'];
            unset($data['ville']);
        }

        if (isset($data['commision_refuse'])) {
            $data['refuse_commision'] = $data['commision_refuse'];
            unset($data['commision_refuse']);
        }

        $this->db->where('id', $data['id']);
        $this->db->update('tbllivreurcommisions', array('livreur' => $data['livreur'], 'city' => $data['city'], 'commision' => $data['commision'], 'refuse_commision' =>  $data['refuse_commision']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Commission Modifié [ID:' . $data['id'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete Commision
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tbllivreurcommisions');
        if ($this->db->affected_rows() > 0) {
            logActivity('Commission Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}



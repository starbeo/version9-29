<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Points_relais_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $active = '', $where = array(), $select = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        
        $_select = 'tblpointsrelais.*, tblpointsrelaissocietes.name as societe, tblvilles.name as name_ville';
        if (!empty($select)) {
            $_select .= ', ' . $select;
        }

        $this->db->select($_select);
            
        $this->db->join('tblpointsrelaissocietes', 'tblpointsrelaissocietes.id = tblpointsrelais.societe_id', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblpointsrelais.ville', 'left');

        if (is_int($active)) {
            $this->db->where('tblpointsrelais.active', $active);
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if (is_numeric($id)) {
            $this->db->where('tblpointsrelais.id', $id);
            return $this->db->get('tblpointsrelais')->row();
        }

        return $this->db->get('tblpointsrelais')->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new point relai
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        if (isset($data['nom'])) {
            $data['nom'] = ucwords($data['nom']);
        }
        if (isset($data['telephone']) && empty($data['telephone'])) {
            $data['telephone'] = NULL;
        }
        if (isset($data['latitude']) && $data['latitude'] == 0) {
            $data['latitude'] = NULL;
        }
        if (isset($data['longitude']) && $data['longitude'] == 0) {
            $data['longitude'] = NULL;
        }

        $this->db->insert('tblpointsrelais', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Add activity log
            logActivity('Nouveau point relai Ajouté [Name : ' . $data['nom'] . ', ID : ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update point relai to database
     */
    public function update($data, $id)
    {
        if (isset($data['nom'])) {
            $data['nom'] = ucwords($data['nom']);
        }
        if (isset($data['telephone']) && empty($data['telephone'])) {
            $data['telephone'] = NULL;
        }
        if (isset($data['latitude']) && $data['latitude'] == 0) {
            $data['latitude'] = NULL;
        }
        if (isset($data['longitude']) && $data['longitude'] == 0) {
            $data['longitude'] = NULL;
        }

        $this->db->where('id', $id);
        $this->db->update('tblpointsrelais', $data);
        if ($this->db->affected_rows() > 0) {
            //Add activity log
            logActivity('Point relai Modifié [ID : ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update point relai status Active/Inactive
     */
    public function change_point_relai_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblpointsrelais', array('active' => $status));
        if ($this->db->affected_rows() > 0) {
            //Add activity log
            logActivity('Point relai Status Changé [Point relai ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete point relai from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('point_relai_id', 'tblcolis', $id) || is_reference_in_table('point_relai_id', 'tblbonlivraison', $id) || is_reference_in_table('point_relai_id', 'tbletatcolislivre', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->delete('tblpointsrelais');
        if ($this->db->affected_rows() > 0) {
            //Add activity log
            logActivity('Point relai Supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    public function get_societes($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('tblpointsrelaissocietes.id', $id);
            return $this->db->get('tblpointsrelaissocietes')->row();
        }

        return $this->db->get('tblpointsrelaissocietes')->result_array();
    }
}

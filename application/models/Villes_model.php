<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Villes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get location
     * @return mixed
     */
    public function get($id = '', $active = 1)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_int($active)) {
            $this->db->where('active', $active);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblvilles')->row();
        }

        $this->db->order_by('name', 'asc');
        return $this->db->get('tblvilles')->result_array();
    }

    /**
     * Get shipping cost city
     * @return mixed
     */
    public function get_shipping_cost_city($cityId = '')
    {
        if (is_numeric($cityId)) {
            $this->db->select('shipping_cost');
            $this->db->join('tblshippingcost', 'tblshippingcost.id = tblvilles.category_shipping_cost', 'left');
            $this->db->where('tblvilles.id', $cityId);
            return $this->db->get('tblvilles')->row();
        }
    }

    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $this->db->insert('tblvilles', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Ville Ajouté [ID:' . $insert_id . ', Nom:' . $data['name'] . ']');
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
        $this->db->update('tblvilles', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Ville Modifié [ID:' . $data['id'] . ', Name : ' . $data['name'] . ']');
            return true;
        }
        return false;
    }

    public function affectation($data)
    {
        if (!isset($data['cities']) || !is_array($data['cities']) || count($data['cities']) <= 0) {
            return false;
        }

        $cpt = 0;
        foreach ($data['cities'] as $cityId) {
            //Get city
            $city = $this->get($cityId);
            $nameCity = '';
            if ($city) {
                $nameCity = $city->name;
            }

            //Update City
            $this->db->where('id', $cityId);
            $this->db->update('tblvilles', array('category_shipping_cost' => $data['shipping_cost']));
            if ($this->db->affected_rows() > 0) {
                logActivity('Frais de livraison affecté à la ville [ID:' . $cityId . ', Name : ' . $nameCity . ']');
                $cpt++;
            }
        }

        if ($cpt > 0) {
            return true;
        }

        return false;
    }

    /**
     * Change ville status / active / inactive
     * @param  mixed $id     ville id
     * @param  mixed $status status(0/1)
     */
    public function change_ville_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblvilles', array(
            'active' => $status
        ));

        logActivity('Ville Status Changé [ExpediteurID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
    }

    public function delete($id)
    {

        /* if(is_reference_in_table('city_id','tblstaff',$id)){
          return array('referenced'=>true);
          }
          if(is_reference_in_table('ville','tblcolis',$id)){
          return array('referenced'=>true);
          } */

        $this->db->where('id', $id, '');
        $this->db->delete('tblvilles');

        if ($this->db->affected_rows() > 0) {
            logActivity('Ville Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

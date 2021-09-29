<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shipping_cost_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Shipping Cost
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
            return $this->db->get('tblshippingcost')->row();
        }

        return $this->db->get('tblshippingcost')->result_array();
    }

    /**
     * Add Shipping Cost
     * @return boolean
     */
    public function add($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tblshippingcost', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('Nouveau Frais de livraison Crée [ID:' . $insert_id . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tblshippingcost', array('name' => $data['name'], 'shipping_cost' => $data['shipping_cost']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Frais de livraison Modifié [ID:' . $data['id'] . ', Name:' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        if (is_reference_in_table('category_shipping_cost', 'tblvilles', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->delete('tblshippingcost');
        if ($this->db->affected_rows() > 0) {
            logActivity('Frais de livraison Supprimé [ID:' . $id . ']');
            return true;
        }

        return false;
    }
}

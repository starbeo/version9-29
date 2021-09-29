<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_modes_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get payment mode
     * @param  integer $id payment mode id
     * @return mixed    if id passed return object else array
     */
    public function get($id = '', $all = false)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if (is_numeric($id)) {
            $this->db->where('id_entreprise', $id_E);
            $this->db->where('id', $id);
            return $this->db->get('tblinvoicepaymentsmodes')->row();
        }

        if ($all !== true) {
            $this->db->where('id_entreprise', $id_E);
            $this->db->where('active', 1);
        }

        return $this->db->get('tblinvoicepaymentsmodes')->result_array();
    }
}

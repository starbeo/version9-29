<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statuses_colis_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $order_by = 'ASC')
    {
        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblstatuscolis')->row();
        }
        
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }


        $this->db->order_by('name', $order_by);
        return $this->db->get('tblstatuscolis')->result_array();
    }
}
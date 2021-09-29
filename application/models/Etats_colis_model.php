<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etats_colis_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $order_by = 'ASC')
    {
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tbletatcolis')->row();
        }

        $this->db->order_by('name', $order_by);
        return $this->db->get('tbletatcolis')->result_array();
    }
}
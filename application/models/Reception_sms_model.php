<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reception_sms_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new sms recieved
     * @return int
     */
    public function add($data)
    {
        $this->db->insert('tblsmsreceived', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_sms_received_added') . ' [' . _l('id') . ':' . $insert_id . ']');
            return true;
        }

        return false;
    }
}

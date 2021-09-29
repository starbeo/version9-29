<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sliders_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get slider OR all sliders
     * @return mixed
     */
    public function get($id = '', $active = 1, $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where('tblsliders.id', $id);
            return $this->db->get('tblsliders')->row();
        }
        
        if (is_int($active)) {
            $this->db->where('active', $active);
        }
        
        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tblsliders.name', 'asc');
        return $this->db->get('tblsliders')->result_array();
    }

    /**
     * Add new slider
     * @return int
     */
    public function add($data)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tblsliders', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            //Handle file
            handle_file_slider_upload($insert_id);

            logActivity(_l('new_slider_added') . ' [' . _l('id') . ':' . $insert_id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update slider
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $this->db->where('id', $id);
        $this->db->update('tblsliders', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if (handle_file_slider_upload($id)) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity(_l('updated_slider') . ' [' . _l('id') . ':' . $id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete slider from database, if used return array with key referenced
     */
    public function remove_file($id)
    {
        if (file_exists(SLIDERS_FILE_FOLDER . $id)) {
            // Delete file slider
            delete_dir(SLIDERS_FILE_FOLDER . $id);
            // Update slider
            $this->db->where('id', $id);
            $this->db->update('tblsliders', array('file' => NULL, 'file_type' => NULL));
            if ($this->db->affected_rows() > 0) {
                logActivity(_l('remove_file_slider') . ' [' . _l('id') . ':' . $id . ']');
                return true;
            }
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update join shipper status Active/Inactive
     */
    public function change_slider_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblsliders', array('active' => $status));
        if ($this->db->affected_rows() > 0) {
            logActivity('Slider Status Changé [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            return true;
        }

        return false;
    }
    
    /**
     * @param  integer ID
     * @return mixed
     * Delete slider from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblsliders');
        if ($this->db->affected_rows() > 0) {
            // Delete file slider
            if (file_exists(SLIDERS_FILE_FOLDER . $id)) {
                delete_dir(SLIDERS_FILE_FOLDER . $id);
            }

            logActivity(_l('deleted_slider') . ' [' . _l('id') . ':' . $id . ']');
            return true;
        }

        return false;
    }
}

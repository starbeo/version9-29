<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departements_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get departement OR all departements
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where('tbldepartements.id', $id);
            return $this->db->get('tbldepartements')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tbldepartements.name', 'asc');
        return $this->db->get('tbldepartements')->result_array();
    }

    public function get_visibilities()
    {
        $espaces = array(
            array('id' => 'all', 'name' => _l('space_administration_and_client')),
            array('id' => 'administration', 'name' => _l('space_administration')),
            array('id' => 'client', 'name' => _l('space_client'))
        );

        return $espaces;
    }

    public function get_departement_by_objet($objetId)
    {
        if (is_numeric($objetId)) {
            $this->db->select('tbldepartements.*');
            $this->db->join('tbldepartements', 'tbldepartements.id = tbldepartementobjets.departement_id', 'left');
            $this->db->where('tbldepartementobjets.id', $objetId);
            return $this->db->get('tbldepartementobjets')->row();
        }
    }

    /**
     * Add new departement
     * @return int
     */
    public function add($data)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $data['addedfrom'] = get_staff_user_id();

        $this->db->insert('tbldepartements', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_departement_added') . ' [' . _l('id') . ':' . $insert_id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update departement
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }

        $this->db->where('id', $id);
        $this->db->update('tbldepartements', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('updated_departement') . ' [' . _l('id') . ':' . $id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete departement from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('departement_id', 'tbldepartementobjets', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->delete('tbldepartements');
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('deleted_departement') . ' [' . _l('id') . ':' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get departement OR all departements
     * @return mixed
     */
    public function get_objets($id = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where('tbldepartementobjets.id', $id);
            return $this->db->get('tbldepartementobjets')->row();
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('tbldepartementobjets.name', 'asc');
        return $this->db->get('tbldepartementobjets')->result_array();
    }

    /**
     * Get modules objet
     * @return mixed
     */
    public function get_modules()
    {
        $modules = array(
            array('id' => 'colis', 'name' => _l('colis')),
            array('id' => 'factures', 'name' => _l('invoices'))
        );

        return $modules;
    }

    /**
     * Add new objet
     * @return int
     */
    public function add_objet($data)
    {
        $data['addedfrom'] = get_staff_user_id();
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }
        // Check if send notification staff is checked
        if (isset($data['send_notification_staff'])) {
            $data['send_notification_staff'] = 1;
        } else {
            $data['send_notification_staff'] = 0;
        }
        // Check if send email staff is checked
        if (isset($data['send_email_staff'])) {
            $data['send_email_staff'] = 1;
        } else {
            $data['send_email_staff'] = 0;
            unset($data['subject_email_staff']);
            unset($data['email_staff']);
        }
        // Check if send sms staff is checked
        if (isset($data['send_sms_staff'])) {
            $data['send_sms_staff'] = 1;
        } else {
            $data['send_sms_staff'] = 0;
            unset($data['sms_staff']);
        }
        // Check if send notification livreur is checked
        if (isset($data['send_notification_livreur'])) {
            $data['send_notification_livreur'] = 1;
        } else {
            $data['send_notification_livreur'] = 0;
        }
        // Check if send email livreur is checked
        if (isset($data['send_email_livreur'])) {
            $data['send_email_livreur'] = 1;
        } else {
            $data['send_email_livreur'] = 0;
            unset($data['subject_email_livreur']);
            unset($data['email_livreur']);
        }
        // Check if send sms livreur is checked
        if (isset($data['send_sms_livreur'])) {
            $data['send_sms_livreur'] = 1;
        } else {
            $data['send_sms_livreur'] = 0;
            unset($data['sms_livreur']);
        }
        // Check if send notification client is checked
        if (isset($data['send_notification_client'])) {
            $data['send_notification_client'] = 1;
        } else {
            $data['send_notification_client'] = 0;
        }
        // Check if send email client is checked
        if (isset($data['send_email_client'])) {
            $data['send_email_client'] = 1;
        } else {
            $data['send_email_client'] = 0;
            unset($data['subject_email_client']);
            unset($data['email_client']);
        }
        // Check if send sms client is checked
        if (isset($data['send_sms_client'])) {
            $data['send_sms_client'] = 1;
        } else {
            $data['send_sms_client'] = 0;
            unset($data['sms_client']);
        }

        $this->db->insert('tbldepartementobjets', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_objet_added') . ' [' . _l('id') . ':' . $insert_id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update objet
     * @return boolean
     */
    public function update_objet($data, $id)
    {
        if (isset($data['name'])) {
            $data['name'] = ucfirst($data['name']);
        }
        // Check if send notification staff is checked
        if (isset($data['send_notification_staff'])) {
            $data['send_notification_staff'] = 1;
        } else {
            $data['send_notification_staff'] = 0;
        }
        // Check if send email staff is checked
        if (isset($data['send_email_staff'])) {
            $data['send_email_staff'] = 1;
        } else {
            $data['send_email_staff'] = 0;
            unset($data['subject_email_staff']);
            unset($data['email_staff']);
        }
        // Check if send sms staff is checked
        if (isset($data['send_sms_staff'])) {
            $data['send_sms_staff'] = 1;
        } else {
            $data['send_sms_staff'] = 0;
            unset($data['sms_staff']);
        }
        // Check if send notification livreur is checked
        if (isset($data['send_notification_livreur'])) {
            $data['send_notification_livreur'] = 1;
        } else {
            $data['send_notification_livreur'] = 0;
        }
        // Check if send email livreur is checked
        if (isset($data['send_email_livreur'])) {
            $data['send_email_livreur'] = 1;
        } else {
            $data['send_email_livreur'] = 0;
            unset($data['subject_email_livreur']);
            unset($data['email_livreur']);
        }
        // Check if send sms livreur is checked
        if (isset($data['send_sms_livreur'])) {
            $data['send_sms_livreur'] = 1;
        } else {
            $data['send_sms_livreur'] = 0;
            unset($data['sms_livreur']);
        }
        // Check if send notification client is checked
        if (isset($data['send_notification_client'])) {
            $data['send_notification_client'] = 1;
        } else {
            $data['send_notification_client'] = 0;
        }
        // Check if send email client is checked
        if (isset($data['send_email_client'])) {
            $data['send_email_client'] = 1;
        } else {
            $data['send_email_client'] = 0;
            unset($data['subject_email_client']);
            unset($data['email_client']);
        }
        // Check if send sms client is checked
        if (isset($data['send_sms_client'])) {
            $data['send_sms_client'] = 1;
        } else {
            $data['send_sms_client'] = 0;
            unset($data['sms_client']);
        }

        $this->db->where('id', $id);
        $this->db->update('tbldepartementobjets', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('updated_objet') . ' [' . _l('id') . ':' . $id . ', ' . _l('name') . ':' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete objet from database, if used return array with key referenced
     */
    public function delete_objet($id)
    {
        if (is_reference_in_table('object', 'tbldemandes', $id)) {
            return array('referenced' => true);
        }

        $this->db->where('id', $id);
        $this->db->delete('tbldepartementobjets');
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('deleted_objet') . ' [' . _l('id') . ':' . $id . ']');
            return true;
        }

        return false;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Currencies_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get currency object based on passed id if not passed id return array of all currencies
     */
    public function get($id = false)
    {
        //l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblcurrencies')->row();
        }

        return $this->db->get('tblcurrencies')->result_array();
    }

    /**
     * @param array $_POST data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['currencyid']);
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $this->db->insert('tblcurrencies', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Currency Added [ID: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @return boolean
     * Update currency values
     */
    public function edit($data)
    {

        $currencyid = $data['currencyid'];
        unset($data['currencyid']);
        $this->db->where('id', $currencyid);
        $this->db->update('tblcurrencies', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Currency Updated [' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete currency from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $currency = $this->get($id);
        if ($currency->isdefault == 1) {
            return array(
                'is_default' => true
            );
        }

        $this->db->where('id', $id);
        $this->db->delete('tblcurrencies');

        if ($this->db->affected_rows() > 0) {
            logActivity('Currency Deleted [' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Make currency your base currency for better using reports if found invoices with more then 1 currency
     */
    public function make_base_currency($id)
    {

        $this->db->where('id', $id);
        $this->db->update('tblcurrencies', array(
            'isdefault' => 1
        ));
        if ($this->db->affected_rows() > 0) {
            $this->db->where('id !=', $id);

            //Insertion de l'ID de l'entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            if ($id_E != 0) {
                $this->db->where('id_entreprise', $id_E);
            }

            $this->db->update('tblcurrencies', array(
                'isdefault' => 0
            ));
            return true;
        }
        return false;
    }

    /**
     * @return object
     * Get base currency
     */
    public function get_base_currency()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->where('isdefault', 1);
        $this->db->where('id_entreprise', $id_E);
        return $this->db->get('tblcurrencies')->row();
    }

    /**
     * @param  integer ID
     * @return string
     * Get the symbol from the currency
     */
    public function get_currency_symbol($id)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (!is_numeric($id)) {
            $id = $this->get_base_currency()->id;
        }
        $this->db->select('symbol');
        $this->db->from('tblcurrencies');
        $this->db->where('id', $id);
        return $this->db->get()->row()->symbol;
    }
}

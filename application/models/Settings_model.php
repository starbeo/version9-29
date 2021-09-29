<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get backgrounds authentication
     * @return array
     */
    public function get_backgrounds_authentication()
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $this->db->where('id_entreprise', $id_E);
        return $this->db->get('tblbackgroundsauthentication')->result_array();
    }

    /**
     * Update all settings
     * @param  array $data all settings
     * @return boolean
     */
    public function update($data)
    {
        $affectedRows = 0;
        $data = do_action('before_settings_updated', $data);

        $checkIssetShowDiscountInInvoicePdf = false;
        $checkIssetShowAddLineAdditionnalInInvoice = false;
        foreach ($data['settings'] as $name => $val) {
            if ($name == 'show_discount_in_invoice_pdf') {
                $checkIssetShowDiscountInInvoicePdf = true;
                if ($val == 'on') {
                    $val = 1;
                } else {
                    $val = 0;
                }
            } else if ($name == 'show_add_line_additionnal_in_invoice') {
                $checkIssetShowAddLineAdditionnalInInvoice = true;
                if ($val == 'on') {
                    $val = 1;
                } else {
                    $val = 0;
                }
            } else if ($name == 'the_statuses_of_colis_displayed_in_the_delivery_note_output') {
                $values = '';
                foreach ($val as $key => $v) {
                    $values .= $v;
                    if ((count($val) - 1) != $key) {
                        $values .= ',';
                    }
                }
                $val = $values;
            } else if ($name == 'the_statuses_of_colis_displayed_in_the_delivery_note_returned') {
                $values = '';
                foreach ($val as $key => $v) {
                    $values .= $v;
                    if ((count($val) - 1) != $key) {
                        $values .= ',';
                    }
                }
                $val = $values;
            } else if ($name == 'display_statuses_from_date') {
                $val = to_sql_date($val);
            }

            $success = update_option($name, $val);
            if ($success == true) {
                $affectedRows++;
            }
        }

        if (get_option('show_discount_in_invoice_pdf') == 1 && $checkIssetShowDiscountInInvoicePdf == false) {
            $success = update_option('show_discount_in_invoice_pdf', 0);
            if ($success == true) {
                $affectedRows++;
            }
        }
        if (get_option('show_add_line_additionnal_in_invoice') == 1 && $checkIssetShowAddLineAdditionnalInInvoice == false) {
            $success = update_option('show_add_line_additionnal_in_invoice', 0);
            if ($success == true) {
                $affectedRows++;
            }
        }

//        if (handle_store_logo_upload() == true) {
//            $affectedRows++;
//        }

        if ($affectedRows > 0) {
            logActivity(_l('settings_updated'));
            return true;
        }

        return false;
    }

    /**
     * Update all settings client
     * @param  array $data all settings
     * @return boolean
     */
    public function update_options_client($data)
    {
        $affectedRows = 0;
        $data = do_action('before_settings_updated', $data);

        foreach ($data['settings'] as $name => $val) {
            $success = update_option_client($name, $val);
            if ($success) {
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            logActivityCustomer(_l('settings_updated'));
            return true;
        }

        return false;
    }
}

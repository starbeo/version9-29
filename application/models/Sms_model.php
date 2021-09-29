<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get sms
     * @return mixed
     */
    public function get($id = '', $active = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblsmstemplates')->row();
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

        return $this->db->get('tblsmstemplates')->result_array();
    }

    /**
     * Change sms status / active / inactive
     * @param  mixed $id sms id
     * @param  mixed $status status(0/1)
     */
    public function change_sms_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblsmstemplates', array('active' => $status));
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('sms_status_changed') . ' [' . _l('id') . ': ' . $id . ' - ' . _l('status_actif_inactif') . ': ' . $status . ']');
            return true;
        }

        return false;
    }

    /**
     * Add new sms
     * @return int
     */
    public function add($data)
    {
        //Check if SMS exist with same status
        $sms = $this->get('', '', 'status_id = ' . $data['status_id']);
        if (is_array($sms) && count($sms) > 0) {
            return array('already_exist' => true);
        }

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        $data['addedfrom'] = get_staff_user_id();

        if (isset($data['title'])) {
            $data['title'] = ucfirst($data['title']);
        }

        if (isset($data['message'])) {
            $data['message'] = nl2br($data['message']);
        }

        if ($data['automatic_sending'] == 1) {
            $data['automatic_sending'] = 'Automatique';
        } else {
            $data['automatic_sending'] = 'Manuelle';
        }

        $this->db->insert('tblsmstemplates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_sms_added') . ' [' . _l('id') . ':' . $insert_id . ', ' . _l('title') . ':' . $data['title'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update sms
     * @return boolean
     */
    public function update($data, $id)
    {
        if (isset($data['title'])) {
            $data['title'] = ucfirst($data['title']);
        }

        if (isset($data['message'])) {
            $data['message'] = nl2br($data['message']);
        }

        if ($data['automatic_sending'] == 1) {
            $data['automatic_sending'] = 'Automatique';
        } else {
            $data['automatic_sending'] = 'Manuelle';
        }

        $this->db->where('id', $id);
        $this->db->update('tblsmstemplates', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('updated_sms') . ' [' . _l('id') . ':' . $id . ', ' . _l('title') . ':' . $data['title'] . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  string message
     * @param  integer (client ID)
     * @param  integer (colis ID)
     * @param  string (colis BARCODE)
     * @param  integer (invoice ID)
     * @param  integer (demande ID)
     * @return boolean
     * Parse message and replace all merge fields found in the message
     */
    public function parse_message($message = '', $clientid = false, $colisid = false, $colisbarcode = false, $invoiceid = false, $demandeid = false, $staffid = false)
    {
        $this->load->model('emails_model');
        $availableMergeFields = $this->emails_model->get_available_merge_fields();
        foreach ($availableMergeFields as $field) {
            foreach ($field as $val) {
                foreach ($val as $_field) {
                    foreach ($_field['available'] as $_available) {
                        if ($_available == 'client' || $_available == 'colis' || $_available == 'invoice' || $_available == 'demande' || $_available == 'staff') {
                            $text_replace = '';
                            if (mb_stripos($message, $_field['key']) !== false) {
                                // remove last } and get db key from second key
                                if (isset($_field['fromoptions'])) {
                                    $_tempfield = mb_substr(mb_substr($_field['key'], 1, strlen($_field['key'])), 0, -1);
                                } else {
                                    $_tempfield = mb_substr($_field['key'], strpos($_field['key'], "_") + 1);
                                    $_tempfield = mb_substr($_tempfield, 0, -1);
                                }

                                if (isset($_field['fromoptions'])) {
                                    if ($_tempfield == 'logo_url') {
                                        $text_replace = '<a href="' . site_url() . '" target="_blank"><img src="' . logo_pdf_url() . '" style="width: 300px;"></a>';
                                    } else {
                                        $text_replace = check_for_links(get_option($_tempfield));
                                    }
                                } else if (_startsWith($_field['key'], '{client')) {
                                    if (is_numeric($clientid)) {
                                        $this->db->where('id', $clientid);
                                        $_row = $this->db->get('tblexpediteurs')->row();
                                        if ($_row) {
                                            if ($_field['key'] == '{client_fullname}') {
                                                $text_replace = $_row->nom;
                                            } else if ($_field['key'] == '{client_phonenumber}') {
                                                $text_replace = $_row->telephone;
                                            } else if ($_field['key'] == '{client_city}') {
                                                $city = $_row->$_tempfield;
                                                if (is_numeric($city)) {
                                                    $this->db->where('id', $city);
                                                    $this->db->select('name')->from('tblvilles');
                                                    $villeName = $this->db->get()->row()->name;
                                                    if ($villeName) {
                                                        $text_replace = $villeName;
                                                    }
                                                }
                                            } else {
                                                $text_replace = $_row->$_tempfield;
                                                if (is_date($text_replace)) {
                                                    $text_replace = date(get_current_date_format(), strtotime($text_replace));
                                                }
                                            }
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{colis')) {
                                    if (is_numeric($colisid) || ($colisbarcode && !empty($colisbarcode))) {
                                        if (is_numeric($colisid)) {
                                            $this->db->where('id', $colisid);
                                        } else if ($colisbarcode && !empty($colisbarcode)) {
                                            $this->db->where('code_barre', $colisbarcode);
                                        }
                                        $_row = $this->db->get('tblcolis')->row();
                                        if ($_row) {
                                            if ($_field['key'] == '{colis_fullname}') {
                                                $text_replace = $_row->nom_complet;
                                            } else if ($_field['key'] == '{colis_telephone_livreur}') {
                                                $livreurId = $_row->livreur;
                                                if (is_numeric($livreurId)) {
                                                    $this->db->where('staffid', $livreurId);
                                                    $this->db->select('phonenumber')->from('tblstaff');
                                                    $livreurPhonenumber = $this->db->get()->row()->phonenumber;
                                                    if ($livreurPhonenumber && !empty($livreurPhonenumber)) {
                                                        $text_replace = $livreurPhonenumber;
                                                    }
                                                }
                                            } else {
                                                $text_replace = $_row->$_tempfield;
                                                if (is_date($text_replace)) {
                                                    $text_replace = date(get_current_date_format(), strtotime($text_replace));
                                                }
                                            }
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{invoice')) {
                                    if (is_numeric($invoiceid)) {
                                        $this->db->where('id', $invoiceid);
                                        $_row = $this->db->get('tblfactures')->row();
                                        if ($_row) {
                                            if ($_field['key'] == '{invoice_name}') {
                                                $text_replace = $_row->nom;
                                            } elseif ($_field['key'] == '{invoice_status}') {
                                                $text_replace = format_facture_status($_row->status);
                                            } else if ($_field['key'] == '{invoice_type}') {
                                                $text_replace = format_status_colis($_row->type);
                                            } else {
                                                $text_replace = $_row->$_tempfield;
                                                if (is_date($text_replace)) {
                                                    $text_replace = date(get_current_date_format(), strtotime($text_replace));
                                                }
                                            }
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{demande')) {
                                    if (is_numeric($demandeid)) {
                                        $this->db->where('id', $demandeid);
                                        $_row = $this->db->get('tbldemandes')->row();
                                        if ($_row) {
                                            if ($_field['key'] == '{demande_type}') {
                                                if ($_row->type == 'demande') {
                                                    $text_replace = _l('request');
                                                } else {
                                                    $text_replace = _l('reclamation');
                                                }
                                            } elseif ($_field['key'] == '{demande_priorite}') {
                                                $text_replace = format_priorite_demande($_row->priorite);
                                            } else if ($_field['key'] == '{demande_status}') {
                                                $text_replace = format_status_demande($_row->status);
                                            } else {
                                                $text_replace = $_row->$_tempfield;
                                                if (is_date($text_replace)) {
                                                    $text_replace = date(get_current_date_format(), strtotime($text_replace));
                                                }
                                            }
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{staff')) {
                                    if (is_numeric($staffid)) {
                                        $this->db->where('staffid', $staffid);
                                        $_row = $this->db->get('tblstaff')->row();
                                        if ($_row) {
                                            $text_replace = $_row->$_tempfield;
                                            if (is_date($text_replace)) {
                                                $text_replace = date(get_current_date_format(), strtotime($text_replace));
                                            }
                                        }
                                    }
                                } else {
                                    return $message;
                                }

                                // replace
                                $message = str_ireplace($_field['key'], $text_replace, $message);
                            }
                        }
                    }
                }
            }
        }

        return $message;
    }

    /**
     * Delete sms
     * @return boolean
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblsmstemplates');
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('deleted_sms') . ' [' . _l('id') . ':' . $id . ']');
            return true;
        }

        return false;
    }
}

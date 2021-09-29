<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define('EMAIL_TEMPLATE_SEND', true);

class Emails_model extends CRM_Model
{

    private $attachment = array();

    function __construct()
    {
        parent::__construct();
        $this->load->library('email');
    }

    /**
     * @param  string
     * @return array
     * Get email template by type
     */
    public function get($type)
    {
        $this->db->where('type', $type);
        return $this->db->get('tblemailtemplates')->result_array();
    }

    /**
     * @param  string
     * @return object
     * Get email template by slug
     */
    public function get_email_template_by_slug($slug)
    {
        $this->db->where('slug', $slug);
        return $this->db->get('tblemailtemplates')->row();
    }

    /**
     * @param  integer
     * @return object
     * Get email template by id
     */
    public function get_email_template_by_id($id)
    {
        $this->db->where('emailtemplateid', $id);
        return $this->db->get('tblemailtemplates')->row();
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update email template
     */
    public function update($data, $id)
    {
        if (isset($data['plaintext'])) {
            $data['plaintext'] = 1;
        } else {
            $data['plaintext'] = 0;
        }

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        $this->db->where('emailtemplateid', $id);
        $this->db->update('tblemailtemplates', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('email_template_updated') . ' [ID : ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Send email - No templates used only simple string
     * @param  string $email   email
     * @param  string $message message
     * @param  string $subject email subject
     * @return boolean
     */
    public function send_simple_email($email, $subject, $message)
    {
        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from(get_option('smtp_email'), get_option('companyname'));
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->set_alt_message(strip_tags($message));

        if (count($this->attachment) > 0) {
            foreach ($this->attachment as $attach) {
                if (!isset($attach['read'])) {
                    if(isset($attach['type'])) {
                        $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                    } else {
                        $this->email->attach($attach['attachment'], 'inline', $attach['filename']);
                    }
                } else {
                    $this->email->attach($attach['attachment'], '', $attach['filename']);
                }
            }
        }

        $this->clear_attachments();

        if ($this->email->send()) {
            return true;
        }

        return false;
    }

    /**
     * @param  string (email address)
     * @param  string (email subject)
     * @param  string (html email template type)
     * @param  array available data for the email template
     * @return boolean
     * Send email template from views/email
     */
    public function send_email($fromEmail = '', $fromName = '', $email, $subject, $type, &$data)
    {
        // Check if overide happens
        if (file_exists(APPPATH . 'views/email/' . $type . '.php')) {
            $template = $this->load->view('email/' . $type, $data, TRUE);

            if (empty($fromEmail)) {
                $fromEmail = get_option('smtp_email');
            }
            if (empty($fromName)) {
                $fromName = get_option('companyname');
            }
            $this->email->initialize();
            $this->email->clear(TRUE);
            $this->email->from($fromEmail, $fromName);
            $this->email->to($email);
            $this->email->subject($subject);
            $this->email->message($template);
            $this->email->set_alt_message(strip_tags($template));

            if (count($this->attachment) > 0) {
                foreach ($this->attachment as $attach) {
                    if (!isset($attach['read'])) {
                        $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                    } else {
                        $this->email->attach($attach['attachment'], '', $attach['filename']);
                    }
                }
            }

            $this->clear_attachments();
            if ($this->email->send()) {
                logActivity(_l('email_send_to') . ' [Email:' . $email . ', Type:' . $type . ']');
                return true;
            }
        }

        return false;
    }

    /**
     * @param  string (email template slug)
     * @param  string (email address)
     * @param  integer (client ID)
     * @param  integer (order ID)
     * @param  integer (staff ID)
     * @return boolean
     * Send email template
     */
    public function send_email_template($template, $email = '', $clientid = false, $invoiceid = false, $staffid = false, $colisid = false, $colisbarcode = false, $demandeid = false)
    {
        if (!empty($template)) {
            $template = $this->get_email_template_by_slug($template);
        } else {
            return false;
        }

        if ($template->active == 0) {
            return false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $template = $this->parse_template($template, $clientid, $invoiceid, $staffid, $colisid, $colisbarcode, $demandeid);

        // Email config
        if ($template->plaintext == 1) {
            $this->config->set_item('mailtype', 'text');
            $template->message = strip_tags($template->message);
        }

        $fromemail = $template->fromemail;
        $fromname = $template->fromname;
        if (empty($fromemail)) {
            $fromemail = get_option('smtp_email');
        }
        if (empty($fromname)) {
            $fromname = get_option('companyname');
        }

        $template = do_action('before_email_template_send', $template);
        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from($fromemail, $fromname);
        $this->email->subject($template->subject);
        $this->email->message($template->message);

        if ($template->plaintext == 0) {
            $this->email->set_alt_message(strip_tags($template->message));
        }

        $this->email->to($email);
        if (count($this->attachment) > 0) {
            foreach ($this->attachment as $attach) {
                if (!isset($attach['read'])) {
                    $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                    $this->email->attach($attach['attachment'], '', $attach['filename']);
                }
            }
        }

        $this->clear_attachments();
        if ($this->email->send()) {
            logActivity(_l('email_send_to') . ' [Email:' . $email . ', Template:' . _l($template->name) . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  string (email template slug)
     * @param  integer (client ID)
     * @param  integer (order ID)
     * @param  integer (staff ID)
     * @return boolean
     * Parse template and replace all merge fields found in the template BODY / SUBJECT / FROM NAME
     */
    public function parse_template($template = '', $clientid = false, $invoiceid = false, $staffid = false, $colisid = false, $colisbarcode = false, $demandeid = false)
    {
        $available_merge_fields = $this->get_available_merge_fields();

        if (!is_object($template)) {
            $template = $this->get_email_template_by_slug($template);
        }

        foreach ($available_merge_fields as $field) {
            foreach ($field as $key => $val) {
                // key staff
                foreach ($val as $_field) {
                    foreach ($_field['available'] as $_available) {
                        if ($_available == $template->type || $template->type == 'other') {
                            $text_replace = '';
                            //if ( preg_match('~\b{client_firstname}\b~',$template->message) ) {
                            if (mb_stripos($template->message, $_field['key']) !== false || mb_stripos($template->subject, $_field['key']) !== false || mb_stripos($template->fromname, $_field['key']) !== false) {
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
                                } else if (_startsWith($_field['key'], '{staff')) {
                                    if (is_numeric($staffid)) {
                                        $this->db->where('staffid', $staffid);
                                        $this->db->select($_tempfield)->from('tblstaff');
                                        $_user = $this->db->get()->row();
                                        $text_replace = '';
                                        if ($_user) {
                                            $text_replace = $_user->$_tempfield;
                                        }
                                    }
                                } else if (_startsWith($_field['key'], '{client')) {
                                    if (is_numeric($clientid)) {
                                        $this->db->where('id', $clientid);
                                        $_row = $this->db->get('tblexpediteurs')->row();
                                        if ($_row) {
                                            if ($_field['key'] == '{client_fullname}') {
                                                $text_replace = $_row->nom;
                                            } elseif ($_field['key'] == '{client_phonenumber}') {
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
                                            } else if ($_field['key'] == '{invoice_status}') {
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
                                            } else if ($_field['key'] == '{demande_priorite}') {
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
                                } else {
                                    return $template;
                                }

                                // replace
                                $template->message = str_ireplace($_field['key'], $text_replace, $template->message);

                                if (mb_stripos($template->subject, $_field['key']) !== false) {
                                    $template->subject = str_ireplace($_field['key'], $text_replace, $template->subject);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $template;
    }

    /**
     * @return array
     * All available merge fields for templates are defined here
     */
    public function get_available_merge_fields()
    {
        $available_merge_fields = array(
            array(
                'staff' => array(
                    array(
                        'name' => 'staff_firstname',
                        'key' => '{staff_firstname}',
                        'available' => array(
                            'staff'
                        )
                    ),
                    array(
                        'name' => 'staff_lastname',
                        'key' => '{staff_lastname}',
                        'available' => array(
                            'staff'
                        )
                    ),
                    array(
                        'name' => 'staff_email',
                        'key' => '{staff_email}',
                        'available' => array(
                            'staff'
                        )
                    ),
                    array(
                        'name' => 'staff_datecreated',
                        'key' => '{staff_datecreated}',
                        'available' => array(
                            'staff'
                        )
                    )
                )
            ),
            array(
                'clients' => array(
                    array(
                        'name' => 'client_fullname',
                        'key' => '{client_fullname}',
                        'available' => array(
                            'client',
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'client_email',
                        'key' => '{client_email}',
                        'available' => array(
                            'client',
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'client_phonenumber',
                        'key' => '{client_phonenumber}',
                        'available' => array(
                            'client',
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'client_city',
                        'key' => '{client_city}',
                        'available' => array(
                            'client',
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'client_adresse',
                        'key' => '{client_adresse}',
                        'available' => array(
                            'client',
                            'invoice'
                        )
                    )
                )
            ),
            array(
                'colis' => array(
                    array(
                        'name' => 'colis_code_barre',
                        'key' => '{colis_code_barre}',
                        'available' => array(
                            'colis'
                        )
                    ),
                    array(
                        'name' => 'colis_fullname',
                        'key' => '{colis_fullname}',
                        'available' => array(
                            'colis'
                        )
                    ),
                    array(
                        'name' => 'colis_crbt',
                        'key' => '{colis_crbt}',
                        'available' => array(
                            'colis'
                        )
                    ),
                    array(
                        'name' => 'colis_telephone_livreur',
                        'key' => '{colis_telephone_livreur}',
                        'available' => array(
                            'colis'
                        )
                    )
                )
            ),
            array(
                'invoice' => array(
                    array(
                        'name' => 'invoice_name',
                        'key' => '{invoice_name}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'invoice_date_created',
                        'key' => '{invoice_date_created}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'invoice_type',
                        'key' => '{invoice_type}',
                        'available' => array(
                            'invoice'
                        )
                    ),
                    array(
                        'name' => 'invoice_status',
                        'key' => '{invoice_status}',
                        'available' => array(
                            'invoice'
                        )
                    )
                )
            ),
            array(
                'demande' => array(
                    array(
                        'name' => 'demande_name',
                        'key' => '{demande_name}',
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'demande_datecreated',
                        'key' => '{demande_datecreated}',
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'demande_type',
                        'key' => '{demande_type}',
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'demande_priorite',
                        'key' => '{demande_priorite}',
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'demande_status',
                        'key' => '{demande_status}',
                        'available' => array(
                            'demande'
                        )
                    )
                )
            ),
            array(
                'other' => array(
                    array(
                        'name' => 'email_signature',
                        'key' => '{email_signature}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'logo_url',
                        'key' => '{logo_url}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'main_domain',
                        'key' => '{main_domain}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'companyname',
                        'key' => '{companyname}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'company_phonenumber',
                        'key' => '{company_phonenumber}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    ),
                    array(
                        'name' => 'company_phonenumber_2',
                        'key' => '{company_phonenumber_2}',
                        'fromoptions' => true,
                        'available' => array(
                            'demande'
                        )
                    )
                )
            )
        );

        return $available_merge_fields;
    }

    /**
     * @param resource
     * @param string
     * @param string (mime type)
     * @return none
     * Add attachment to property to check before an email is send
     */
    public function add_attachment($attachment)
    {
        $this->attachment[] = $attachment;
    }

    /**
     * @return none
     * Clear all attachment properties
     */
    private function clear_attachments()
    {
        $this->attachment = array();
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emails extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
        
        if(get_permission_module('email_templates') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all email templates
     */
    public function index()
    {
        if (!is_admin()) {
            access_denied('email_templates');
        }

        // Get template email
        $data['clients'] = $this->emails_model->get('client');
        $data['invoices'] = $this->emails_model->get('invoice');

        $data['title'] = _l('email_templates');
        $this->load->view('admin/emails/email_templates', $data);
    }

    /**
     * Edit email template
     */
    public function email_template($id)
    {
        if (!is_admin()) {
            access_denied('email_templates');
        }
        if (!$id) {
            redirect(admin_url('emails'));
        }

        if ($this->input->post()) {
            $success = $this->emails_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfuly', _l('email_template')));
            }
            redirect(admin_url('emails/email_template/' . $id));
        }

        $data['available_merge_fields'] = $this->emails_model->get_available_merge_fields();
        $data['template'] = $this->emails_model->get_email_template_by_id($id);

        $data['editor_assets'] = true;
        $data['title'] = _l('edit', _l('email_template'));
        $this->load->view('admin/emails/template', $data);
    }

    /**
     * Test Smtp settings
     */
    public function sent_smtp_test_email()
    {
        if ($this->input->post()) {
            if (!empty($this->input->post('test_email'))) {
                $email = $this->input->post('test_email');
                $subject = get_option('companyname') . ' : SMTP Test de configuration';
                $message = '<p>Ceci est un email de test SMTP de ' . get_option('companyname') . '.</p><p> Si vous avez reçu ce message, cela signifie que vos paramètres SMTP sont définis correctement.</p>';
                $send = $this->emails_model->send_simple_email($email, $subject, $message);
                if ($send) {
                    set_alert('success', _l('it_appears_that_your_smtp_settings_are_configured_correctly_check_your_email_now'));
                } else {
                    set_debug_alert('<h1>' . _l('your_smtp_settings_ar_not_set_correctly_here_is_the_debug_log') . '</h1><br />' . $this->email->print_debugger());
                }
            } else {
                set_alert('warning', _l('empty_email'));
            }
        }
    }
}

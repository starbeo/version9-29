<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //Check if coming-soon
        if (get_option('coming_soon') == 1 && is_date(to_sql_date_1(get_option('coming_soon_date_time_start'), true)) && (date('d/m/Y H:i') >= get_option('coming_soon_date_time_start'))) {
            if (is_date(to_sql_date_1(get_option('coming_soon_date_time_end'), true)) && (date('d/m/Y H:i') < get_option('coming_soon_date_time_end'))) {
                redirect(site_url('coming_soon'));
            } else {
                update_option('coming_soon', 0);
            }
        }
        //Default language
        $language = get_option('active_language');
        $this->lang->load($language . '_lang', $language);
        //Load model
        $this->load->model('Authentication_model');
        //Load library
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->admin();
    }

    public function admin()
    {
        if (is_point_relais_logged_in()) {
            set_alert('danger', 'Déconnectez vous d\'abord de l\'espace point relais');
            redirect(site_url('point_relais'));
        } else if (is_staff_logged_in()) {
            if (get_option('espace_livreur_mobile') == 1 && is_livreur()) {
                redirect(livreur_url('home'));
            } else {
                redirect(site_url('admin'));
            }
        }

        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $compte = 'admin';
                $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), $compte);
                if (is_array($success) && isset($success['memberinactive'])) {
                    set_alert('danger', _l('admin_auth_inactive_account'));
                    redirect(site_url('authentication/admin'));
                } else if (is_array($success) && isset($success['a_relay_point'])) {
                    set_alert('danger', 'Vous êtes agent point relais');
                    redirect(site_url('authentication/point_relais'));
                } else if ($success == false) {
                    set_alert('danger', _l('admin_auth_invalid_email_or_password'));
                    redirect(site_url('authentication/admin'));
                }

                if (get_option('espace_livreur_mobile') == 1 && is_livreur()) {
                    redirect(livreur_url('home'));
                } else {
                    redirect(site_url('admin'));
                }
            }
        }

        $data['title'] = get_option('companyname') . ' - ' . _l('login_to_administration_area');
        $this->load->view('admin/authentication/' . get_option('theme_login_admin') . '/login', $data);
    }

    public function email_exists($email)
    {
        $total_rows = total_rows('tblstaff', array(
            'email' => $email
        ));
        if ($total_rows == 0) {
            $this->form_validation->set_message('email_exists', '%s not found.');
            return false;
        }

        return true;
    }

    public function logout()
    {
        $this->Authentication_model->logout();
        do_action('after_user_logout');
        redirect(site_url('authentication/admin'));
    }

    public function point_relais()
    {
        if (is_staff_logged_in()) {
            set_alert('danger', 'Déconnectez vous d\'abord de l\'espace administration');
            redirect(site_url('admin'));
        } else if (is_point_relais_logged_in()) {
            redirect(site_url('point_relais'));
        }

        if ($this->input->post()) {
            $compte = 'point_relais';
            $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), $compte);

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', 'Compte inactif');
                redirect(site_url('authentication/point_relais'));
            } else if (is_array($success) && isset($success['not_a_relay_point'])) {
                set_alert('danger', 'Vous n\'êtes pas agent point relais');
                redirect(site_url('authentication/admin'));
            } else if ($success == false) {
                set_alert('danger', 'Email ou Mot de passe Invalide');
                redirect(site_url('authentication/point_relais'));
            }
            redirect(site_url('point_relais'));
        }

        $data['title'] = get_option('companyname') . ' - ' . _l('login_to_point_relais_area');
        $this->load->view('point-relais/authentication/' . get_option('theme_login_point_relais') . '/login', $data);
    }

    public function logout_point_relais()
    {
        $this->Authentication_model->logout(false);
        redirect(site_url('authentication/point_relais'));
    }

    public function client()
    {
        if (is_expediteur_logged_in()) {
            redirect(site_url('client'));
        }

        if ($this->input->post()) {
            $compte = 'expediteur';
            $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), $compte);

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', 'Compte inactif');
                redirect(site_url('authentication/client'));
            } else if ($success == false) {
                set_alert('danger', 'Email ou Mot de passe Invalide');
                redirect(site_url('authentication/client'));
            }
            redirect(site_url('client'));
        }

        $data['title'] = get_option('companyname') . ' - ' . _l('login_to_client_area');
        if (get_option('companyalias') == 'power-coursier') {
            $this->load->view('client/authentication/' . get_option('theme_login_client') . '/login', $data);
        } else {
            $this->load->view('client/authentication/' . get_option('theme_login_client') . '/login_default', $data);
        }
    }

    public function expediteur()
    {
        if (is_expediteur_logged_in()) {
            redirect(site_url('expediteurs/colis'));
        }

        if ($this->input->post()) {
            $compte = 'expediteur';
            $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), $compte);

            if (is_array($success) && isset($success['memberinactive'])) {
                set_alert('danger', 'Compte inactif');
                redirect(site_url('expediteurs/login'));
            } else if ($success == false) {
                set_alert('danger', 'Email ou Mot de passe Invalide');
                redirect(site_url('expediteurs/login'));
            }
            redirect(site_url('expediteurs'));
        }
    }

    public function logout_client()
    {
        $this->Authentication_model->logout(false);
        redirect(site_url('authentication/client'));
    }

    public function api()
    {
        //add the header here
        header('Content-Type: application/json');

        $success = false;
        $code = 404;
        $message = 'Problème lors de l\'authentification';
        $redirectUrl = '';
        $userId = '';
        $entrepriseId = '';
        $loggedIn = '';

        if ($this->input->post()) {
            if ($this->input->post('token') && $this->config->item('token_authentication_api') == $this->input->post('token')) {
                if (!empty($this->input->post('email')) && !empty($this->input->post('password'))) {
                    $compte = 'expediteur';
                    $success = $this->Authentication_model->login($this->input->post('email'), $this->input->post('password'), $this->input->post('remember'), $compte);
                    if (is_array($success) && isset($success['memberinactive'])) {
                        $code = 300;
                        $message = 'Compte client inactive';
                    } else if ($success == false) {
                        $code = 400;
                        $message = 'Email ou Mot de passe Invalide';
                    } else {
                        $success = true;
                        $code = 200;
                        $message = 'Authentification avec succès';
                        $redirectUrl = site_url('client');
                        $userId = $this->session->userdata('expediteur_user_id');
                        $entrepriseId = $this->session->userdata('staff_user_id_entreprise');
                        $loggedIn = $this->session->userdata('expediteur_logged_in');
                    }
                } else {
                    $code = 403;
                    $message = 'Paramètres vide';
                }
            } else {
                $code = 500;
                $message = 'Token invalide';
            }
        }

        echo json_encode(array('success' => $success, 'code' => $code, 'message' => $message, 'redirect_url' => $redirectUrl, 'user_id' => $userId, 'entreprise_id' => $entrepriseId, 'logged_in' => $loggedIn));
    }
}

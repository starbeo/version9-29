<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('expediteurs_model');
    }

    /**
     * View public profile. If id passed view profile by staff id else current user
     */
    public function index()
    {
        $data['client'] = $this->expediteurs_model->get(get_expediteur_user_id());
        // Notifications
        $totalNotifications = total_rows('tblnotificationscustomer', array('toclientid' => get_expediteur_user_id()));
        $this->load->model('misc_model');
        $data['total_pages'] = ceil($totalNotifications / $this->misc_model->notifications_limit);
        // Get agent
        $this->load->model('staff_model');
        if ($data['client'] && is_numeric($data['client']->commerciale)) {
            $data['commerciale'] = $this->staff_model->get($data['client']->commerciale);
        }
        if ($data['client'] && is_numeric($data['client']->account_manager)) {
            $data['account_manager'] = $this->staff_model->get($data['client']->account_manager);
        }
        if ($data['client'] && is_numeric($data['client']->livreur)) {
            $data['livreur'] = $this->staff_model->get($data['client']->livreur);
        }

        $data['title'] = _l('profile') . ' - ' . $data['client']->nom;
        $this->load->view('client/profile/myprofile', $data);
    }

    /**
     * Notifications
     */
    public function notifications()
    {
        if ($this->input->post()) {
            $this->load->model('misc_model');
            $clientId = get_expediteur_user_id();
            echo json_encode($this->misc_model->get_all_notifications_client($clientId, $this->input->post('page')));
            die;
        }
    }

    /**
     * Edit profile
     */
    public function edit()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            unset($data['change_logo']);
            $success = $this->expediteurs_model->update($data, get_expediteur_user_id());
            if ($success) {
                set_alert('success', _l('clients_logo_updated'));
            }

            redirect(client_url('profile/edit'));
        }

        $client = $this->expediteurs_model->get(get_expediteur_user_id());
        $data['client'] = $client;
        // Get contrat
        $this->load->model('contrats_model');
        $contract = $this->contrats_model->get_contrat_by_client(get_expediteur_user_id());
        $data['showContrat'] = false;
        if ($contract && $contract->not_visible_to_client == 0 && !empty($contract->fullname) && !empty($contract->address) && !empty($contract->contact) && !empty($contract->frais_livraison_interieur) && !empty($contract->frais_livraison_exterieur) && !empty($contract->date_created_client)) {
            $data['showContrat'] = true;
        }
        $data['title'] = $client->nom;
        $this->load->view('client/profile/profile', $data);
    }

    /**
     * Change password
     */
    public function change_password()
    {
        if ($this->input->post()) {
            $success = $this->expediteurs_model->change_expediteur_password($this->input->post());
            if (is_array($success) && isset($success['old_password_not_match'])) {
                set_alert('warning', 'Your old password dont match');
            } else if ($success == true) {
                set_alert('success', 'Password changed successfuly');
            }
        }

        redirect(client_url('profile/edit'));
    }

    /**
     * Change password
     */
    public function change_settings()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            unset($data['change_settings']);
            $success = $this->expediteurs_model->update_settings($data, get_expediteur_user_id());
            if ($success) {
                set_alert('success', 'Paramètres changé avec succés.');
            }
        }

        redirect(client_url('profile/edit'));
    }

    /**
     * Change password
     */
    public function remove_logo()
    {
        $clientId = get_expediteur_user_id();

        $this->db->where('id', $clientId);
        $this->db->update('tblexpediteurs', array('logo' => NULL));
        if ($this->db->affected_rows() > 0) {
            if (file_exists(CLIENTS_LOGO_FOLDER . $clientId)) {
                delete_dir(CLIENTS_LOGO_FOLDER . $clientId);
            }
        }

        redirect(client_url('profile/edit'));
    }

    /**
     * Get settings client
     */
    public function get_settings_client()
    {
        $success = false;
        if (is_numeric(get_expediteur_user_id())) {
            //Get Infos Client
            $this->load->model('expediteurs_model');
            $expediteur = $this->expediteurs_model->get(get_expediteur_user_id());
            $ouvertureColis = 0;
            $optionFrais = 0;
            if ($expediteur) {
                $success = true;
                $ouvertureColis = $expediteur->ouverture;
                $optionFrais = $expediteur->option_frais;
                $optionFraisAssurance = $expediteur->option_frais_assurance;
            }
        }

        echo json_encode(array('success' => $success, 'ouverture_colis' => $ouvertureColis, 'option_frais' => $optionFrais, 'option_frais_assurance' => $optionFraisAssurance));
    }

    /**
     * Print PDF contract
     */
    public function contrat()
    {
        // Get template contract
        $template = get_option('contrat_template');
        if (empty($template)) {
            redirect(client_url('profile'));
        }

        // Get contract
        $this->load->model('contrats_model');
        $contract = $this->contrats_model->get_contrat_by_client(get_expediteur_user_id());
        if ($contract && $contract->not_visible_to_client == 0 && !empty($contract->fullname) && !empty($contract->address) && !empty($contract->contact) && !empty($contract->frais_livraison_interieur) && !empty($contract->frais_livraison_exterieur) && !empty($contract->date_created_client)) {
            // Parse template
            $logo = '<a href="' . site_url() . '" target="_blank"><img src="' . site_url('uploads/company/' . get_option('companyalias') . '/logo-entete.jpg') . '" style="width: 300px;"></a>';
            $template = str_ireplace('{logo_url}', $logo, $template);
            $template = str_ireplace('{client_fullname}', '<b>' . strtoupper($contract->fullname) . '</b>', $template);
            $template = str_ireplace('{client_address}', $contract->address, $template);
            if (!empty($contract->commercial_register)) {
                $template = str_ireplace('{client_commercial_register}', 'Matriculé au registre commercial de Casablanca sous le N°' . $contract->commercial_register, $template);
            } else {
                $template = str_ireplace('<p style="margin-right:40.25pt">{client_commercial_register}</p>', '', $template);
            }
            $template = str_ireplace('{client_contact}', 'Représentée par Mr/Mme <b>' . $contract->contact . '</b>', $template);
            $template = str_ireplace('{client_frais_livraison_interieur}', '<span style="font-weight: bold; font-size: 19px;">' . $contract->frais_livraison_interieur . '</span>', $template);
            $template = str_ireplace('{client_frais_livraison_exterieur}', '<span style="font-weight: bold; font-size: 19px;">' . $contract->frais_livraison_exterieur . '</span>', $template);
            $template = str_ireplace('{client_date_created}', date(get_current_date_format(), strtotime($contract->date_created_client)), $template);
            $contract->body = $template;

            contract_pdf($contract);
        } else {
            set_alert('warning', _l('contract_is_not_yet_ready'));
            redirect(client_url('profile'));
        }
    }


 public function change_password_home()
    {
        if ($this->input->post()) {
            $success = $this->expediteurs_model->change_expediteur_password_home($this->input->post());
            if (is_array($success) && isset($success['old_password_not_match'])) {
                set_alert('warning', 'Your old password dont match');
            } else if ($success == true) {
                set_alert('success', 'Password changed successfuly');
            }
        }

        redirect(client_url('home'));
    }

}

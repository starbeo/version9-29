<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients_en_attente extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        //Check if module is active
        if (get_permission_module('join_shipper') == 0) {
            redirect(admin_url('home'));
        }
        //Load model
        $this->load->model('clients_en_attente_model');
    }

    /**
     * List all clients en attente
     */
    public function index()
    {
        if (!has_permission('join_shipper', '', 'view')) {
            access_denied('Join Shipper');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblrejoindreexpediteur.societe',
                'tblrejoindreexpediteur.personne_a_contacte',
                'tblrejoindreexpediteur.email',
                'tblrejoindreexpediteur.telephone',
                'tblvilles.name',
                'tblrejoindreexpediteur.datecreated'
            );

            $sIndexColumn = "id";
            $sTable = 'tblrejoindreexpediteur';

            $join = array('LEFT JOIN tblvilles ON tblvilles.id = tblrejoindreexpediteur.ville_id');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, array(), array('tblrejoindreexpediteur.id'), 'tblrejoindreexpediteur.id', '', 'DESC');
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblrejoindreexpediteur.datecreated') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_time_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'eye', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#client-en-attente', 'data-id' => $aRow['id']));
                $options .= icon_btn('admin/clients_en_attente/convert_to_client/' . $aRow['id'], 'reply', 'btn-info');
                $row[] = $options .= icon_btn('admin/clients_en_attente/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('clients_en_attente');
        $this->load->view('admin/clients_en_attente/manage', $data);
    }

    /**
     * Get client en attente
     */
    public function get($id)
    {
        echo json_encode($this->clients_en_attente_model->get($id, ''));
    }

    /**
     * Convert client en attente to client
     */
    public function convert_to_client($id)
    {
        if (has_permission('join_shipper', '', 'edit')) {
            //Load model
            $this->load->model('expediteurs_model');
            //Get client en attente
            $clientEnAttente = $this->clients_en_attente_model->get($id, '');
            if ($clientEnAttente) {
                //Check client if exist with same email
                $exist = $this->expediteurs_model->get('', '', array('email' => $clientEnAttente->email));
                if($exist && count($exist) > 0 && is_numeric($exist[0]['id'])) {
                    //Redirected to client
                    set_alert('success', _l('client_already_exist'));
                    redirect(admin_url('expediteurs/expediteur/' . $exist[0]['id']));
                } else {
                    $data['nom'] = $clientEnAttente->societe;
                    $data['contact'] = $clientEnAttente->personne_a_contacte;
                    $data['email'] = $clientEnAttente->email;
                    $data['telephone'] = $clientEnAttente->telephone;
                    $data['adresse'] = $clientEnAttente->adresse;
                    $data['ville_id'] = $clientEnAttente->ville_id;
                    $data['password'] = randomPassword();
                    $data['active'] = 0;
                    if (!is_null($clientEnAttente->affiliation_code) && !empty($clientEnAttente->affiliation_code)) {
                        $data['affiliation_code'] = $clientEnAttente->affiliation_code;
                    }
                    //Add client en attente to client
                    $clientId = $this->expediteurs_model->add($data, true);
                    if ($clientId) {
                        //Delete client en attente
                        $this->clients_en_attente_model->delete($id);
                        //Redirected to client
                        set_alert('success', _l('converted_successfuly', _l('client_en_attente')));
                        redirect(admin_url('expediteurs/expediteur/' . $clientId));
                    }
                }
            }
        }
        
        set_alert('warning', _l('problem_conversion', _l('client_en_attente')));
        redirect(admin_url('clients_en_attente'));
    }

    /**
     * Delete client en attente
     */
    public function delete($id)
    {
        if (!has_permission('join_shipper', '', 'delete')) {
            access_denied('Join Shipper');
        }

        $response = $this->clients_en_attente_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('client_en_attente')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('client_en_attente_lowercase')));
        }

        redirect(admin_url('clients_en_attente'));
    }
}

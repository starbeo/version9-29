<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contrats extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('contrats_model');

        if (get_permission_module('contrats') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * Get list contrats
     */
    public function index($clientId = false)
    {
        if (!has_permission('contrats', '', 'view') && !has_permission('contrats', '', 'view_own')) {
            access_denied('contrats');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblcontrats.subject',
                'tblexpediteurs.nom',
                'tblcontrats.datestart',
                'tblcontrats.dateend',
                'tblcontrats.addedfrom',
                'tblcontrats.date_created'
            );

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcontrats.client_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblcontrats.addedfrom'
            );

            $where = array();
            if (is_numeric($clientId)) {
                $where = array(' AND client_id = ' . $clientId);
            }
            //Filtre
            if ($this->input->post('f-client') && is_numeric($this->input->post('f-client'))) {
                array_push($where, ' AND tblcontrats.client_id = ' . $this->input->post('f-client'));
            }
            if ($this->input->post('f-date-start') && is_date(to_sql_date($this->input->post('f-date-start')))) {
                array_push($where, ' AND tblcontrats.datestart = "' . to_sql_date($this->input->post('f-date-start')) . '"');
            }
            if ($this->input->post('f-date-end') && is_date(to_sql_date($this->input->post('f-date-end')))) {
                array_push($where, ' AND tblcontrats.dateend = "' . to_sql_date($this->input->post('f-date-end')) . '"');
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblcontrats.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }
            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblcontrats.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblcontrats.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblcontrats.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $sIndexColumn = "id";
            $sTable = 'tblcontrats';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcontrats.id', 'tblcontrats.client_id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcontrats.subject') {
                        $_data = '<a href="' . admin_url('contrats/contrat/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['client_id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblcontrats.datestart' || $aColumns[$i] == 'tblcontrats.dateend' || $aColumns[$i] == 'tblcontrats.date_created') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcontrats.addedfrom') {
                        $utilisateurId = $_data;
                        $_data = staff_profile_image($utilisateurId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $utilisateurId) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('contrats', '', 'edit')) {
                    $options .= icon_btn('admin/contrats/contrat/' . $aRow['id'], 'pencil-square-o');
                }
                $options .= icon_btn('admin/contrats/pdf/' . $aRow['id'], 'file-pdf-o', 'btn-danger');
                if (has_permission('contrats', '', 'delete')) {
                    $options .= icon_btn('admin/contrats/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                }
                $row[] = $options;

                if (!empty($aRow['dateend'])) {
                    $dateEnd = date(get_current_date_format(), strtotime($aRow['dateend']));
                    if ($dateEnd < date('Y-m-d')) {
                        $row['DT_RowClass'] = 'alert-danger';
                    }
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get clients
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();
        // Variables
        $data['idEntreprise'] = $this->session->userdata('staff_user_id_entreprise');
        $data['minusSevenDays'] = date('Y-m-d', strtotime("-7 days"));
        $data['plusSevenDays'] = date('Y-m-d', strtotime("+7 days"));

        $data['title'] = _l('contracts');
        $this->load->view('admin/contrats/manage', $data);
    }

    /**
     * Edit contract or add new contract
     */
    public function contrat($id = '')
    {

        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('contrats', '', 'create')) {
                    access_denied('contrats');
                }
                $id = $this->contrats_model->add($this->input->post());
                if (is_numeric($id)) {
                    set_alert('success', _l('added_successfuly', _l('contract')));
                }
            } else {
                if (!has_permission('contrats', '', 'edit')) {
                    access_denied('contrats');
                }
                $success = $this->contrats_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('contract')));
                }
            }
            redirect(admin_url('contrats/contrat/' . $id));
        }

        if ($id == '') {
            $title = _l('add_new', _l('contract_lowercase'));
        } else {
            $data['contract'] = $this->contrats_model->get($id);
            if (!$data['contract'] || (!has_permission('contracts', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                set_alert('warning', _l('not_found', _l('contract_lowercase')));
                redirect(admin_url('contrats'));
            }

            if (!is_null($data['contract']->datestart)) {
                $data['contract']->datestart = date(get_current_date_format(), strtotime($data['contract']->datestart));
            } else {
                $data['contract']->datestart = '';
            }
            if (!is_null($data['contract']->dateend)) {
                $data['contract']->dateend = date(get_current_date_format(), strtotime($data['contract']->dateend));
            } else {
                $data['contract']->dateend = '';
            }

            $title = _l('edit', _l('contract_lowercase'));
        }

        // Get clients
        $this->load->model('contrats_model');
        $data['clients'] = $this->contrats_model->get_clients_not_have_contract();
        // Get Template
        $data['template'] = get_option('contrat_template');
        // Variables
        $data['defaultDateStart'] = date(get_current_date_format(), strtotime(date('Y-m-d')));

        $data['title'] = $title;
        $this->load->view('admin/contrats/contrat', $data);
    }

    /**
     * Print PDF contract
     */
    public function pdf($id)
    {
        if (!has_permission('contrats', '', 'view') && !has_permission('contrats', '', 'view_own')) {
            access_denied('contrats');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('contrats'));
        }

        // Get template contract
        $template = get_option('contrat_template');
        if (empty($template)) {
            redirect(admin_url('contrats'));
        }

        // Get contract
        $contract = $this->contrats_model->get($id);
        if ($contract && !empty($contract->fullname) && !empty($contract->address) && !empty($contract->contact) && !empty($contract->frais_livraison_interieur) && !empty($contract->frais_livraison_exterieur) && !empty($contract->date_created_client)) {
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
            set_alert('warning', _l('information_related_to_the_contract_must_be_completed_before_downloading_the_pdf'));
            redirect(admin_url('contrats'));
        }
    }

    /**
     * Delete contract
     */
    public function delete($id)
    {

        if (!has_permission('contrats', '', 'delete')) {
            access_denied('contrats');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('contrats'));
        }

        $response = $this->contrats_model->delete($id);
        if ($response) {
            set_alert('success', _l('deleted', _l('contract')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_lowercase')));
        }

        redirect(admin_url('contrats'));
    }
}

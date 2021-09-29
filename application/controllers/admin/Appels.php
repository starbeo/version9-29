<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appels extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('appels_model');

        if (get_permission_module('appels') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all calls delivery men
     */
    public function livreurs()
    {
        $has_permission = has_permission('appels', '', 'view');
        if (!has_permission('appels', '', 'view') && !has_permission('appels', '', 'view_own')) {
            access_denied('Appels');
        }


        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblappelslivreur.livreur_id',
                'tblexpediteurs.nom',
                'tblcolis.code_barre',
                'tblappelslivreur.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblappelslivreur';

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblappelslivreur.colis_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblappelslivreur.livreur_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblappelslivreur.client_id'
            );

            $where = array();
            if (is_livreur()) {
                $livreur_id = get_staff_user_id();
                array_push($where, ' AND tblappelslivreur.livreur_id = ' . $livreur_id);
            }
            //Filtre
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                array_push($where, ' AND tblappelslivreur.livreur_id = ' . $this->input->post('f-livreur'));
            }
            if ($this->input->post('f-client') && is_numeric($this->input->post('f-client'))) {
                array_push($where, ' AND tblappelslivreur.client_id = ' . $this->input->post('f-client'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblappelslivreur.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }

            //Affichage historiques appels coli
            if ($this->input->post('f-coli-id') && !empty($this->input->post('f-coli-id'))) {
                array_push($where, ' AND tblappelslivreur.colis_id = "' . $this->input->post('f-coli-id') . '"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array("CONCAT(tblstaff.firstname, ' ', tblstaff.lastname) as livreur_name", "tblappelslivreur.client_id"));
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

                    switch ($aColumns[$i]) {
                        case 'tblappelslivreur.livreur_id' :
                            $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['livreur_name'] . '</a>';
                            break;
                        case 'tblexpediteurs.nom' :
                            $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['client_id']) . '" target="_blank">' . ucwords($_data) . '</a>';
                            break;
                        case 'tblcolis.code_barre' :
                            $_data = '<b>' . $_data . '</b>';
                            break;
                        case 'tblappelslivreur.date_created' :
                            if (!is_null($_data)) {
                                $_data = date(get_current_date_time_format(), strtotime($_data));
                            } else {
                                $_data = '';
                            }
                            break;
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get Delivery Mens
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        // Get Clients
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();

        $data['title'] = _l('appels_livreurs');
        $this->load->view('admin/appels/livreurs/manage', $data);
    }
}

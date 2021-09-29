<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reclamations extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reclamations_model');
    }

    /**
     * List reclamations
     */
    public function index($etat = false)
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblreclamations.objet',
                'tblreclamations.etat',
                'tblreclamations.date_created',
                'tblreclamations.date_traitement'
            );

            $sIndexColumn = "id";
            $sTable = 'tblreclamations';

            $join = array();
            $where = array('AND tblreclamations.relation_id = ' . get_expediteur_user_id());
            //Get
            if (is_numeric($etat)) {
                array_push($where, ' AND tblreclamations.etat = ' . $this->input->get('etat'));
            }
            // Filtre
            if ($this->input->post('f-etat') && !empty($this->input->post('f-etat'))) {
                if ($this->input->post('f-etat') == 'traite') {
                    $etatFiltre = 1;
                } else {
                    $etatFiltre = 0;
                }
                array_push($where, ' AND tblreclamations.etat = ' . $etatFiltre);
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblreclamations.date_created = "' . to_sql_date($this->input->post('f-date-created')) . '"');
            }
            if ($this->input->post('f-date-traitement') && is_date(to_sql_date($this->input->post('f-date-traitement')))) {
                array_push($where, ' AND tblreclamations.date_traitement LIKE "' . to_sql_date($this->input->post('f-date-traitement')) . '%"');
            }

            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblreclamations.date_created = "' . date('Y-m-d') . '"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblreclamations.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblreclamations.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblreclamations.id'), 'tblreclamations.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tblreclamations.objet') {
                        if ($aRow['tblreclamations.etat'] == 1) {
                            $dataAction = 'show';
                        } else {
                            $dataAction = 'edit';
                        }
                        $_data = '<b class="curp" data-toggle="modal" data-target="#reclamation" data-id="' . $aRow['id'] . '" data-action="' . $dataAction . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblreclamations.etat') {
                        if ($_data == 0) {
                            $_data = '<span class="label label-danger">Non Traité</span>';
                        } else if ($_data == 1) {
                            $_data = '<span class="label label-success">Traité</span>';
                        }
                    } else if ($aColumns[$i] == 'tblreclamations.date_created' || $aColumns[$i] == 'tblreclamations.date_traitement') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                if ($aRow['tblreclamations.etat'] == 1) {
                    $options = icon_btn('javascript:void(0)', 'eye', 'btn-success', array('data-toggle' => 'modal', 'data-target' => '#reclamation', 'data-id' => $aRow['id'], 'data-action' => 'show'));
                } else {
                    $options = icon_btn('javascript:void(0)', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#reclamation', 'data-id' => $aRow['id'], 'data-action' => 'edit'));
                }
                $row[] = $options .= icon_btn('client/reclamations/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get etats
        $data['etats'] = $this->reclamations_model->get_etats();

        $data['title'] = _l('reclamations');
        $this->load->view('client/reclamations/manage', $data);
    }

    /**
     * Get reclamation
     */
    function get_reclamation($id)
    {
        // Get reclamation
        $reclamation = $this->reclamations_model->get($id);
        $result = array();
        if ($reclamation && $reclamation->relation_id == get_expediteur_user_id()) {
            $result['objet'] = $reclamation->objet;
            $result['message'] = $reclamation->message;
            $result['reponse'] = $reclamation->reponse;
        }

        echo json_encode($result);
    }

    /**
     * Edit or add new reclamation
     */
    public function reclamation()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            if ($id == '') {
                $success = $this->reclamations_model->add($data);
                $message = _l('problem_adding', _l('reclamation'));
                if ($success) {
                    $message = _l('added_successfuly', _l('reclamation'));
                }
            } else {
                // Get reclamation
                $reclamation = $this->reclamations_model->get($id);
                $success = false;
                $message = _l('problem_updating', _l('reclamation'));
                if ($reclamation && $reclamation->relation_id == get_expediteur_user_id()) {
                    $success = $this->reclamations_model->update($data, $id);
                    if ($success) {
                        $message = _l('updated_successfuly', _l('reclamation'));
                    }
                }
            }

            echo json_encode(array('success' => $success, 'message' => $message));
        }
    }

    /**
     * Delete reclamation
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            redirect(client_url('reclamations'));
        }

        $response = $this->reclamations_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('reclamation')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('reclamation_lowercase')));
        }

        redirect(client_url('reclamations'));
    }
}

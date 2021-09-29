<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reclamations extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reclamations_model');
    }
    /* List all reclamations expediteur */

    public function list_reclamation_expediteur()
    {
        if (!has_permission('claim_shipper', '', 'view')) {
            access_denied('Claim Shipper');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblexpediteurs.id',
                'objet',
                'etat',
                'tblreclamations.date_created',
                'tblstaff.firstname',
                'tblreclamations.date_traitement'
            );

            $sIndexColumn = "id";
            $sTable = 'tblreclamations';

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblreclamations.relation_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblreclamations.staff_id'
            );
            
            $where = array('AND tblreclamations.relation_id != "NULL" ');
            //Get
            if ($this->input->get('etat')) {
                array_push($where, ' AND tblreclamations.etat = ' . $this->input->get('etat'));
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
            
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblreclamations.id', 'tblexpediteurs.nom', 'tblstaff.lastname', 'tblstaff.staffid'));
            $output = $result['output'];
            $rResult = $result['rResult'];
            
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'tblexpediteurs.id') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['tblexpediteurs.id']) . '">' . $aRow['nom'] . '</a>';
                    } else if ($aColumns[$i] == 'etat') {
                        if ($aRow['etat'] == 0) {
                            $etat = 'Non Traité';
                            $_data = '<span class="label label-danger">' . $etat . '</span>';
                        } else if ($aRow['etat'] == 1) {
                            $etat = 'Traité';
                            $_data = '<span class="label label-success">' . $etat . '</span>';
                        }
                    } else if ($aColumns[$i] == 'tblreclamations.date_created' || $aColumns[$i] == 'tblreclamations.date_traitement') {
                        if (!is_null($_data)) {
                            $_data = date('d/m/Y', strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblstaff.firstname') {
                        if(!is_null($aRow['staffid'])) {
                            $_data = staff_profile_image($aRow['staffid'], array(
                                'staff-profile-image-small'
                            ));
                            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['tblstaff.firstname'] . ' ' . $aRow['lastname'] . '</a>';
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'reply', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#reclamation', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/reclamations/delete_reclamation_expediteur/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('reclamations');
        $this->load->view('admin/reclamations/manage', $data);
    }
    /* Send Answer Claim */

    public function reponse_reclamation($id = '')
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->post()) {
            if ($this->input->post('id') != "") {
                if (!has_permission('claim_shipper', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $data = $this->input->post();
                    $id = $data['id'];
                    unset($data['id']);
                    $success = $this->reclamations_model->update($data, $id);
                    if ($success) {
                        $message = _l('updated_successfuly', _l('reclamation'));
                    }
                    echo json_encode(array('success' => $success, 'message' => $message));
                }
            }
            die;
        }
    }
    /* List all reclamation client */

    public function list_reclamation_client()
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'Codecoli',
                'Nom',
                'Num_Tel',
                'Message'
            );

            $sIndexColumn = "id";
            $sTable = 'tblreclamations';

            $join = array();
            $where = array('AND tblreclamations.relation_id = "client"');

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblreclamations.id'));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'Nom') {
                        $_data = ucwords($_data);
                    } elseif ($aColumns[$i] == 'Message') {
                        $_data = ucfirst($_data);
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'eye', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#reclamation', 'data-id' => $aRow['id']));
                $row[] = $options .= icon_btn('admin/reclamations/delete_reclamation_client/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('als_reclamtions');
        $this->load->view('admin/reclamations/manage', $data);
    }
    /* Delete reclamation expediteur from database */

    public function delete_reclamation_expediteur($id)
    {
        if (!has_permission('claim_shipper', '', 'delete')) {
            access_denied('Claim Shipper');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $response = $this->reclamations_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('reclamation')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('reclamation_lowercase')));
        }

        redirect(admin_url('reclamations/list_reclamation_expediteur'));
    }
    /* Delete reclamation client from database */

    public function delete_reclamation_client($id)
    {
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $response = $this->reclamations_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('reclamation')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('reclamation_lowercase')));
        }

        redirect(admin_url('reclamations/list_reclamation_client'));
    }
    /* Get info rejoindre expediteur */

    function get_info_reclamations($id)
    {
        echo json_encode($this->reclamations_model->get($id));
    }
}

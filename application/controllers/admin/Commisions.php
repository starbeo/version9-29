<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commisions extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('commisions_model');
    }

    /**
     * List all commisions
     */
    public function index($livreurId = false)
    {
        $has_permission = has_permission('commisions', '', 'view');
        if (!has_permission('commisions', '', 'view') && !has_permission('commisions', '', 'view_own')) {
            access_denied('commisions');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblvilles.name', 'tbllivreurcommisions.commision', 'tbllivreurcommisions.datecreated', 'tbllivreurcommisions.addedfrom','tbllivreurcommisions.refuse_commision');

            $sIndexColumn = "id";
            $sTable = 'tbllivreurcommisions';

            $join = array(
                'left join tblvilles ON tblvilles.id = tbllivreurcommisions.city',
                'left join tblstaff ON tblstaff.staffid = tbllivreurcommisions.addedfrom'
            );

            $where = array();
            //If not admin show only own estimates
            if (!$has_permission) {
                array_push($where, 'AND tbllivreurcommisions.addedfrom = "' . get_staff_user_id() . '"');
            }
            if (is_numeric($livreurId)) {
                array_push($where, ' AND tbllivreurcommisions.livreur = ' . $livreurId);
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tbllivreurcommisions.id', 'tbllivreurcommisions.city', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_livreur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];

                    if ($aColumns[$i] == 'tblvilles.name') {
                        $_data = '<b>' . $_data . '</b>';
                        $_data = '<a href="#" data-toggle="modal" data-target="#commision_modal" data-id="' . $aRow['id'] . '" data-city="' . $aRow['city'] . '" data-commision="' . $aRow['tbllivreurcommisions.commision'] . '" data-commisionrefuse="' . $aRow['tbllivreurcommisions.refuse_commision'].'"  >' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tbllivreurcommisions.datecreated') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tbllivreurcommisions.addedfrom') {
                        $staffId = $_data;
                        $_data = staff_profile_image($staffId, array('staff-profile-image-small mright5'));
                        $_data .= '<a href="' . admin_url('staff/member/' . $staffId) . '" target="_blank">' . $aRow['name_livreur'] . '</a>';
                    }

                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#commision_modal', 'data-id' => $aRow['id'], 'data-city' => $aRow['city'], 'data-commision' => $aRow['tbllivreurcommisions.commision'],'data-commisionrefuse' => $aRow['tbllivreurcommisions.refuse_commision']));
                $options .= icon_btn('#', 'remove', 'btn-danger btn-delete-confirm', array('onclick' => 'removeCommision(' . $aRow['id'] . ')'));
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('commisions');
        $this->load->view('admin/commisions/manage', $data);
    }

    /**
     * Add or edit commision
     */
    public function commision()
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_adding', _l('commision'));
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] == '') {
                if (!has_permission('commisions', '', 'create')) {
                    $type = 'danger';
                    $message = _l('access_denied');
                } else {
                    $success = $this->commisions_model->add($data);
                    if (is_array($success) && isset($success['already_exist'])) {
                        $message = _l('commision_already_exist');
                    } else if ($success) {
                        $type = 'success';
                        $message = _l('added_successfuly', _l('commision'));
                    }
                }
            } else {
                if (!has_permission('commisions', '', 'edit')) {
                    $type = 'danger';
                    $message = _l('access_denied');
                } else {
                    $success = $this->commisions_model->update($data);
                    if ($success) {
                        $type = 'success';
                        $message = _l('updated_successfuly', _l('commision'));
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Delete commision
     */
    public function delete($id)
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_deleting', _l('commision_lowercase'));
        if ($this->input->is_ajax_request() && is_numeric($id)) {
            if (!has_permission('commisions', '', 'delete')) {
                $message = _l('access_denied');
                $type = 'danger';
            } else {
                $success = $this->commisions_model->delete($id);
                if ($success) {
                    $type = 'success';
                    $message = _l('deleted', _l('commision'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }
}



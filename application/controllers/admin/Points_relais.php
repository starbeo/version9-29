<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Points_relais extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('points_relais_model');

        if (get_permission_module('points_relais') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * Get list points relais
     */
    public function index()
    {
        if (!has_permission('points_relais', '', 'view') && !has_permission('points_relais', '', 'view_own')) {
            access_denied('points_relais');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblpointsrelaissocietes.name',
                'tblpointsrelais.nom',
                'tblpointsrelais.adresse',
                'tblvilles.name',
                'tblpointsrelais.active',
                'tblpointsrelais.addedfrom',
                'tblpointsrelais.date_created'
            );

            $join = array(
                'LEFT JOIN tblpointsrelaissocietes ON tblpointsrelaissocietes.id = tblpointsrelais.societe_id',
                'LEFT JOIN tblvilles ON tblvilles.id = tblpointsrelais.ville',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblpointsrelais.addedfrom'
            );

            $where = array();

            //Filtre
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                array_push($where, ' AND tblpointsrelais.ville = ' . $this->input->post('f-ville'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblpointsrelais.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }
            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblpointsrelais.datecreated LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblpointsrelais.datecreated, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblpointsrelais.datecreated > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $sIndexColumn = "id";
            $sTable = 'tblpointsrelais';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblpointsrelais.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_staff'));
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

                    if ($aColumns[$i] == 'tblpointsrelais.nom') {
                        $_data = '<a href="' . admin_url('points_relais/point_relai/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblpointsrelais.active') {
                        $checked = '';
                        if ($aRow['tblpointsrelais.active'] == 1) {
                            $checked = 'checked';
                        }
                        $_data = '<input type="checkbox" class="switch-box input-xs" data-size="mini" data-id="' . $aRow['id'] . '" data-switch-url="admin/points_relais/change_point_relai_status" ' . $checked . '>';
                        // For exporting
                        $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
                    } else if ($aColumns[$i] == 'tblpointsrelais.addedfrom') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_staff'] . '</a>';
                    } else if ($aColumns[$i] == 'tblpointsrelais.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('points_relais', '', 'edit')) {
                    $options .= icon_btn('admin/points_relais/point_relai/' . $aRow['id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Point Relai'));
                }
                if (has_permission('points_relais', '', 'delete')) {
                    $options .= icon_btn('admin/points_relais/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm');
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();

        $data['title'] = _l('points_relais');
        $this->load->view('admin/points_relais/manage', $data);
    }

    /**
     * Get point relai
     */
    function get($id)
    {
        $pointRelai = $this->points_relais_model->get($id);
        $data = [];
        $i = 0;
        if ($pointRelai) {
            $data[$i]['nom'] = $pointRelai->nom;
            $data[$i]['adresse'] = $pointRelai->adresse;
            $data[$i]['latitude'] = $pointRelai->latitude;
            $data[$i]['longitude'] = $pointRelai->longitude;
            $data[$i]['ville'] = $pointRelai->ville;
        }

        echo json_encode($data);
    }

    /**
     * Get city point relai
     */
    function get_city($id)
    {
        $pointRelai = $this->points_relais_model->get($id);
        $city = '';
        if ($pointRelai) {
            $city = $pointRelai->ville;
        }

        echo json_encode(array('city' => $city));
    }

    /**
     * Edit or add new point relai 
     */
    public function point_relai($id = '')
    {
        if (!has_permission('points_relais', '', 'view') && !has_permission('points_relais', '', 'view_own')) {
            access_denied('points_relais');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('points_relais', '', 'create')) {
                    access_denied('points_relais');
                }
                $id = $this->points_relais_model->add($this->input->post());
                if (is_numeric($id)) {
                    set_alert('success', _l('added_successfuly', _l('point_relai')));
                    redirect(admin_url('points_relais'));
                }
            } else {
                if (!has_permission('points_relais', '', 'edit')) {
                    access_denied('points_relais');
                }
                $success = $this->points_relais_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('point_relai')));
                }
                redirect(admin_url('points_relais/point_relai/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('point_relai_lowercase'));
        } else {
            $pointRelai = $this->points_relais_model->get($id);
            if (!$pointRelai) {
                set_alert('warning', _l('not_found', _l('point_relai')));
                redirect(admin_url('points_relais'));
            }

            $data['point_relai'] = $pointRelai;
            $title = _l('edit', _l('point_relai_lowercase'));
        }

        // Get cities
        $data['societes'] = $this->points_relais_model->get_societes();
        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        // Get banks
        $this->load->model('banques_model');
        $data['banks'] = $this->banques_model->get();

        $data['title'] = $title;
        $this->load->view('admin/points_relais/point_relai', $data);
    }

    /**
     * Change point relai status / active / inactive
     */
    public function change_point_relai_status($id, $status)
    {
        if (has_permission('points_relais', '', 'edit')) {
            if ($this->input->is_ajax_request()) {
                $this->points_relais_model->change_point_relai_status($id, $status);
            }
        }
    }

    /**
     * Delete point relai
     */
    public function delete($id)
    {
        if (!has_permission('points_relais', '', 'delete')) {
            access_denied('points_relais');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('points_relais'));
        }

        $response = $this->points_relais_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('point_relai_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('point_relai')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('point_relai_lowercase')));
        }

        redirect(admin_url('points_relais'));
    }
}

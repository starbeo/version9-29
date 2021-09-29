<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('status_model');

        if (get_permission_module('status') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all status
     */
    public function index()
    {
        $has_permission = has_permission('status', '', 'view');
        if (!has_permission('status', '', 'view') && !has_permission('status', '', 'view_own')) {
           access_denied('Status');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblstatus.id',
                'tblstatus.code_barre',
                'tblstatus.type',
                'tbllocations.name',
                'tblstatus.sent',
                'tblstatus.id_utilisateur',
                'tblstatus.date_created',
                'tblstatus.date_reporte'
            );

            $sIndexColumn = "id";
            $sTable = 'tblstatus';

            $join = array(
                'LEFT JOIN tbllocations ON tbllocations.id = tblstatus.emplacement_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblstatus.id_utilisateur'
            );

            $where = array();
            //If not admin show only own estimates
            if (!$has_permission) {
                array_push($where, 'AND tblstatus.id_utilisateur = "' . get_staff_user_id() . '"');
            }
            //If is delivery men
            if (is_livreur()) {
                $livreur_id = get_staff_user_id();
                array_push($where, ' AND tblstatus.id_utilisateur = ' . $livreur_id);
            }
            // Performance
            if (!empty(get_option('display_statuses_from_date'))) {
                $dateDisplayStatus = get_option('display_statuses_from_date');
                if (is_date($dateDisplayStatus)) {
                    array_push($where, ' AND tblstatus.date_created >= "' . $dateDisplayStatus . '"');
                }
            }
            //Filtre
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND tblstatus.type = ' . $this->input->post('f-statut'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblstatus.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
            }
            $showOptions = true;
            //Affichage historiques status coli
            if ($this->input->post('f-code-barre') && !empty($this->input->post('f-code-barre'))) {
                $showOptions = false;
                array_push($where, ' AND tblstatus.code_barre = "' . $this->input->post('f-code-barre') . '"');
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblstaff.admin', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblstatus.code_barre') {
                        $_data = '<a href="javascript:void(0)" data-toggle="modal" data-target="#status" data-barcode="' . $aRow['tblstatus.code_barre'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblstatus.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblstatus.sent') {
                        if ($_data == 1) {
                            $label = _l('yes');
                            $colorLabel = 'success';
                        } else {
                            $label = _l('no');
                            $colorLabel = 'danger';
                        }
                        $_data = '<span class="label label-' . $colorLabel . '">' . $label . '</span>';
                    } else if ($aColumns[$i] == 'tblstatus.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblstatus.id_utilisateur') {
                        $_data = render_icon_user_by_type($aRow['admin']) . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_utilisateur'] . '</a>';
                    }
                    else if ($aColumns[$i] == 'tblstatus.date_reporte')
                    {
                        if ($aRow['tblstatus.date_reporte'] =='0000-00-00')
                            $_data = '';
                    }

                    $row[] = $_data;
                }

                if ($showOptions) {
                    $options = '';
                    $options .= icon_btn('#', 'clone', 'btn-info', array('data-toggle' => 'modal', 'data-target' => '#status', 'data-barcode' => $aRow['tblstatus.code_barre']));
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#status', 'data-id' => $aRow['tblstatus.id']));
                    $row[] = $options .= icon_btn('admin/status/delete/' . $aRow['tblstatus.id'], 'remove', 'btn-danger btn-delete-confirm');
                }
               else if($showOptions==false) {
                    $options = '';
                    $options .= icon_btn('#', 'clone', 'btn-info', array('data-toggle' => 'modal', 'data-target' => '#statuspopup', 'data-barcode' => $aRow['tblstatus.code_barre']));
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#statuspopup', 'data-id' => $aRow['tblstatus.id']));
                    $row[] = $options .= icon_btn('admin/status/delete/' . $aRow['tblstatus.id'], 'remove', 'btn-danger btn-delete-confirm');
                }

                $output['aaData'][] = $row;
            }

            //Get Type Status
            $this->load->model('colis_model');
            $data['types'] = $this->colis_model->get_status_colis();
            //Get Locations
            $this->load->model('locations_model');
            $data['locations'] = $this->locations_model->get();
            //Get Motif
            $data['motifs'] = $this->status_model->get_motif_status();
            //Get Statuses Colis
            $this->load->model('statuses_colis_model');
            $data['statuses'] = $this->statuses_colis_model->get();

            echo json_encode($output);
            die();
        }
        //Get Type Status
        $this->load->model('colis_model');
        $data['types'] = $this->colis_model->get_status_colis();
        //Get Locations
        $this->load->model('locations_model');
        $data['locations'] = $this->locations_model->get();
        //Get Motif
        $data['motifs'] = $this->status_model->get_motif_status();
        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $data['statuses'] = $this->statuses_colis_model->get();

        $data['title'] = _l('als_status');
        $this->load->view('admin/status/manage', $data);
    }

    /**
     * Edit or add new status
     */
    public function status($id = '')
    {
        if ($this->input->post()) {
            if ($this->input->post('id') == "") {
                if (!has_permission('status', '', 'create')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $id = $this->status_model->add($this->input->post());
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfuly', _l('als_status'));
                        echo json_encode(array('success' => $success, 'message' => $message));
                    }
                }
            } else {
                if (!has_permission('status', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $data = $this->input->post();
                    $id = $data['id'];
                    unset($data['id']);
                    $success = $this->status_model->update($data, $id);
                    if ($success) {
                        $message = _l('updated_successfuly', _l('als_status'));
                        echo json_encode(array('success' => $success, 'message' => $message));
                    }
                }
            }
        }
    }

    /**
     * Delete status
     */
    public function delete($id)
    {
        if (!has_permission('status', '', 'delete')) {
            access_denied('Status');
        }

        $response = $this->status_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('status_lowercase')));
        }

        redirect(admin_url('status'));
    }

    /**
     * Get info status
     */
    public function get_info_status($id)
    {
        echo json_encode($this->status_model->get($id));
    }

    /**
     * Get telephone colis
     */
    public function get_coli_by_barcode($barcode)
    {
        echo json_encode($this->status_model->get_coli_by_barcode($barcode));
    }

    /**
     * Suivi colis
     */
    public function suivi_colis_ajax()
    {
        $code = $this->input->post('__RequestVerificationToken');
        $status = $this->status_model->suivi_colis($code);
        $row = array();

        foreach ($status as $s) {
            $s['date_created'] = date('d/m/Y', strtotime($s['date_created']));
            array_push($row, array(
                'status' => $s['name'],
                'emp' => $s['emplacement'],
                'date' => $s['date_created']
                )
            );
        }
        echo json_encode($row);
    }

    /**
     * Check if code barre exist
     */
    public function check_code_barre_exist()
    {
        // ID entreprise 
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->db->where('code_barre', $this->input->post('code_barre_verifie'));
                $this->db->where('id_entreprise', $id_E);
                $total_rows = $this->db->count_all_results('tblcolis');

                if ($total_rows > 0) {
                    echo json_encode(true);
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }




}



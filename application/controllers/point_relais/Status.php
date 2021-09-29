<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status extends Point_relais_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('status_model');
    }

    /**
     * List all status
     */
    public function index()
    {
        //Get points relais staff
        $pointsRelaisStaff = get_staff_points_relais();
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblstatus.id',
                'tblstatus.code_barre',
                'tblstatus.type',
                'tbllocations.name',
                'tblstatus.id_utilisateur',
                'tblstatus.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblstatus';

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.code_barre = tblstatus.code_barre',
                'LEFT JOIN tbllocations ON tbllocations.id = tblstatus.emplacement_id',
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblstatus.id_utilisateur'
            );

            $where = array();
            // By point relais
            array_push($where, ' AND tblcolis.point_relai_id IN ' . $pointsRelaisStaff);
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

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, get_entreprise_id(), $where, array('tblstatus.id', 'tblstaff.admin', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_utilisateur'));
            $output = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblstatus.code_barre') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#status" data-barcode="' . $aRow['tblstatus.code_barre'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblstatus.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblstatus.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblstatus.id_utilisateur') {
                        $_data = render_icon_user_by_type($aRow['admin']) . '<b>' . $aRow['name_utilisateur'] . '</b>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('#', 'clone', 'btn-info', array('data-toggle' => 'modal', 'data-target' => '#status', 'data-barcode' => $aRow['tblstatus.code_barre']));
                $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#status', 'data-id' => $aRow['tblstatus.id']));
                $row[] = $options;

                $output['aaData'][] = $row;
            }

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
        $this->load->view('point-relais/status/manage', $data);
    }

    /**
     * Edit or add new status
     */
    public function status($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->input->post('id') == "") {
                $id = $this->status_model->add($data);
                if (is_numeric($id)) {
                    $success = true;
                    $message = _l('added_successfuly', _l('als_status'));
                    echo json_encode(array('success' => $success, 'message' => $message));
                }
            } else {
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
     * Check if code barre exist
     */
    public function check_code_barre_exist()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->db->where('code_barre', $this->input->post('code_barre_verifie'));
                $this->db->where('id_entreprise', get_entreprise_id());
                $totalRows = $this->db->count_all_results('tblcolis');
                if ($totalRows > 0) {
                    echo json_encode(true);
                } else {
                    echo json_encode(false);
                }
            }
        }
    }
}

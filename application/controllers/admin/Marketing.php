<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('marketing_model');

        if (get_permission_module('marketing') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all marketing
     */
    public function index()
    {
        $has_permission = has_permission('marketing', '', 'view');
        if (!has_permission('marketing', '', 'view') && !has_permission('marketing', '', 'view_own')) {
            access_denied('Marketing');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblmarketing.name',
                'tblmarketing.type',
                'tblmarketing.notification_by',
                'tblmarketing.sent',
                'tblmarketing.staff_who_executed',
                'tblmarketing.sending_date',
                'tblmarketing.addedfrom',
                'tblmarketing.date_created'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblmarketing';

            $join = array('left join tblstaff ON tblstaff.staffid = tblmarketing.addedfrom');
            $where = array();
            if (!$has_permission) {
                array_push($where, ' AND tblmarketing.addedfrom = ' . get_staff_user_id());
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblmarketing.id', 'CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as name_staff'));
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

                    if ($aColumns[$i] == 'tblmarketing.name') {
                        $_data = '<a href="' . admin_url('marketing/marketing/' . $aRow['id']) . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblmarketing.type') {
                        if ($_data == 1) {
                            $_data = '<b>' . _l('all_the_clients') . '</b>';
                        } else if ($_data == 2) {
                            $_data = '<b>' . _l('by_client') . '</b>';
                        } else if ($_data == 3) {
                            $_data = '<b>' . _l('by_group') . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblmarketing.notification_by') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'tblmarketing.sent') {
                        if ($_data == 1) {
                            $_data = '<label class="label label-success">' . _l('yes') . '</b>';
                        } else {
                            $_data = '<label class="label label-danger">' . _l('no') . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblmarketing.staff_who_executed' || $aColumns[$i] == 'tblmarketing.addedfrom') {
                        if (!is_null($_data)) {
                            $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $_data) . '" target="_blank">' . $aRow['name_staff'] . '</a>';
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblmarketing.sending_date' || $aColumns[$i] == 'tblmarketing.date_created') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_time_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('marketing', '', 'edit')) {
                    $options .= icon_btn('admin/marketing/marketing/' . $aRow['id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier'));
                }
                if (has_permission('marketing', '', 'edit')) {
                    if ($aRow['tblmarketing.sent'] == 0) {
                        $options .= icon_btn('admin/marketing/start/' . $aRow['id'], 'paper-plane-o', 'btn-success', array('title' => 'Lancer'));
                    } else {
                        $options .= icon_btn('#', 'eye', 'btn-primary', array('data-toggle' => 'modal', 'data-target' => '#historiques', 'data-marketing-id' => $aRow['id']));
                    }
                }
                if (has_permission('marketing', '', 'delete')) {
                    $options .= icon_btn('admin/marketing/delete/' . $aRow['id'], 'remove', 'btn-danger btn-delete-confirm', array('title' => 'Supprimer'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['title'] = _l('marketing');
        $this->load->view('admin/marketing/manage', $data);
    }

    /**
     * Edit or add new marketing
     */
    public function marketing($id = '')
    {
        if (!has_permission('marketing', '', 'view') && !has_permission('marketing', '', 'view_own')) {
            access_denied('Marketing');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if (!is_numeric($id)) {
                if (!has_permission('marketing', '', 'create')) {
                    access_denied('Marketing');
                }
                $id = $this->marketing_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('marketing')));
                    redirect(admin_url('marketing/marketing/' . $id));
                }
            } else {
                if (!has_permission('marketing', '', 'edit')) {
                    access_denied('Marketing');
                }
                $success = $this->marketing_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('marketing')));
                }
                redirect(admin_url('marketing/marketing/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('marketing_lowercase'));
        } else {
            $marketing = $this->marketing_model->get($id);
            if (!$marketing || (!has_permission('marketing', '', 'view') && ($marketing->addedfrom != get_staff_user_id()))) {
                set_alert('warning', _l('not_found', _l('marketing')));
                redirect(admin_url('marketing'));
            }

            $data['marketing'] = $marketing;
            $title = $marketing->name;
        }

        // Get types
        $data['types'] = $this->marketing_model->get_types();
        // Get clients
        $this->load->model('expediteurs_model');
        $data['clients'] = $this->expediteurs_model->get();
        // Get groupes clients
        $this->load->model('groupes_model');
        $data['groupes'] = $this->groupes_model->get();

        $data['title'] = $title;
        $data['editor_assets'] = true;
        $this->load->view('admin/marketing/marketing', $data);
    }

    /**
     * Remove image marketing
     */
    public function remove_image_marketing($id)
    {
        if (!is_numeric($id)) {
            redirect(admin_url('marketing'));
        }

        if (file_exists(MARKETING_ATTACHED_PIECE_FOLDER . $id)) {
            delete_dir(MARKETING_ATTACHED_PIECE_FOLDER . $id);
        }
        $this->db->where('id', $id);
        $this->db->update('tblmarketing', array('image' => NULL));
        if ($this->db->affected_rows() > 0) {
            redirect(admin_url('marketing/marketing/' . $id));
        }
    }

    /**
     * Get all clients
     */
    public function get_all_clients()
    {
        $this->load->model('expediteurs_model');
        echo json_encode($this->expediteurs_model->get('', 1, array(), 'tblexpediteurs.id as id, tblexpediteurs.nom as name'));
    }

    /**
     * Get all clients
     */
    public function get_all_groupes()
    {
        $this->load->model('groupes_model');
        echo json_encode($this->groupes_model->get());
    }

    /**
     * Start marketing
     */
    public function start($id = '')
    {
        if (!has_permission('marketing', '', 'edit')) {
            access_denied('Marketing');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('marketing'));
        }

        $response = $this->marketing_model->start($id);
        if ($response) {
            set_alert('success', _l('launch_successfuly', _l('marketing')));
        } else {
            set_alert('warning', _l('problem_launching', _l('marketing_lowercase')));
        }

        redirect(admin_url('marketing'));
    }

    /**
     * List all marketing
     */
    public function init_historique()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblmarketinghistoriques.send_type',
                'tblmarketinghistoriques.sent',
                'tblexpediteurs.nom',
                'tblmarketinghistoriques.date_created'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblmarketinghistoriques';

            $join = array('LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblmarketinghistoriques.relation_id');
            $where = array();
            if ($this->input->post('f-marketing-id') && is_numeric($this->input->post('f-marketing-id'))) {
                array_push($where, ' AND tblmarketinghistoriques.marketing_id = ' . $this->input->post('f-marketing-id'));
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblmarketinghistoriques.relation_id'));
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

                    if ($aColumns[$i] == 'tblmarketinghistoriques.send_type') {
                        $_data = '<b>' . ucwords($_data) . '</b>';
                    } else if ($aColumns[$i] == 'tblmarketinghistoriques.sent') {
                        if ($_data == 1) {
                            $_data = '<label class="label label-success">' . _l('yes') . '</b>';
                        } else {
                            $_data = '<label class="label label-danger">' . _l('no') . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['relation_id']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'tblmarketinghistoriques.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Delete marketing
     */
    public function delete($id)
    {
        if (!has_permission('marketing', '', 'delete')) {
            access_denied('Marketing');
        }
        if (!$id) {
            redirect(admin_url('marketing'));
        }

        $response = $this->marketing_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('marketing')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('marketing_lowercase')));
        }

        redirect(admin_url('marketing'));
    }
}

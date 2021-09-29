<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supports extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('supports_model');
        
        if(get_permission_module('supports') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* Open also all supports if user access this / supports url */

    public function index($id = '')
    {
        $this->list_supports($id);
    }
    /* List all supports */

    public function list_supports($id = '')
    {
        if (!has_permission('supports', '', 'view') && !has_permission('supports', '', 'view_own')) {
            access_denied('supports');
        }

        // ID entreprise 
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        // if passed from url
        $_custom_view = '';
        if ($this->input->get('custom_view')) {
            $_custom_view = $this->input->get('custom_view');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'id',
                'name',
                '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM tblsupportstaffassignees JOIN tblstaff on tblstaff.staffid = tblsupportstaffassignees.staffid WHERE supportid = tblsupports.id) as members',
                'priority',
                'startdate',
                'duedate',
                'datefinished'
            );

            $join = array();
            
            $where = array();
            if ($this->input->post()) {
                $_where = '';
                if ($this->input->post('finished') == 'finished') {
                    $_where .= ' AND finished = 1 ';
                }
                if ($this->input->post('unfinished') == 'unfinished') {
                    $_where .= ' AND finished = 0 ';
                }
                if ($this->input->post('not_assigned') == 'not_assigned') {
                    $_where .= ' AND tblsupports.id NOT IN (SELECT supportid FROM tblsupportstaffassignees) ';
                }
                if ($this->input->post('due_date_passed') == 'due_date_passed') {
                    $_where .= ' AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL) AND finished = 0 ';
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            //If not admin show only own supports
            if (!has_permission('supports', '', 'view')) {
                $sql = 'AND (tblsupports.id IN (SELECT supportid FROM tblsupportstaffassignees WHERE staffid = ' . get_staff_user_id() . ')  OR addedfrom = ' . get_staff_user_id() . ')';
                array_push($where, $sql);
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblsupports.dateadded LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblsupports.dateadded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblsupports.dateadded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }
            
            $where_entreprise = 'AND tblsupports.id_entreprise=' . $id_E;
            array_push($where, $where_entreprise);

            $sIndexColumn = "id";
            $sTable = 'tblsupports';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('dateadded', 'finished', 'duedate', '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM tblsupportstaffassignees WHERE supportid = tblsupports.id) as assignees_ids'));
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

                    if ($aColumns[$i] == 'name') {
                        $support_name = $_data;
                        $_data = '';
                        if ($aRow['finished'] == 1) {
                            $_data .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('support_unmark_as_complete') . '"></i></a>';
                        } else {
                            $_data .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('support_single_mark_as_complete') . '"></i></a>';
                        }
                        $_data .= '<a href="#" class="main-tasks-table-href-name" style="width: 88%;" onclick="init_support(' . $aRow['id'] . '); return false;">' . $support_name . '</a>';
                    } else if ($i == 2) {
                        $members = explode(',', $_data);
                        $_data = '';
                        $export_members = '';
                        $m = 0;
                        foreach ($members as $member) {
                            if ($member != '') {
                                $members_ids = explode(',', $aRow['assignees_ids']);
                                $member_id = $members_ids[$m];
                                $_data .= '<a href="' . admin_url('profile/' . $member_id) . '">' . staff_profile_image($member_id, array(
                                        'staff-profile-image-small mright5'
                                        ), 'small', array(
                                        'data-toggle' => 'tooltip',
                                        'data-title' => $member
                                    )) . '</a>';
                                // For exporting
                                $export_members .= $member . ', ';
                            }
                            $m++;
                        }
                        if ($export_members != '') {
                            $_data .= '<span class="hide">' . mb_substr($export_members, 0, -2) . '</span>';
                        }
                    } else if ($aColumns[$i] == 'startdate' || $aColumns[$i] == 'duedate') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'datefinished') {
                        $_data = '';
                        if (is_date($_data) && $_data !== '0000-00-00 00:00:00') {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        }
                    } else if ($aColumns[$i] == 'finished') {
                        if ($_data == 1) {
                            $_data = _l('support_table_is_finished_indicator');
                        } else {
                            $_data = _l('support_table_is_not_finished_indicator');
                        }
                    } else if ($aColumns[$i] == 'priority') {
                        if(!is_null($_data)) {
                            $_data = format_priority_support($_data);
                        } else {
                            $_data = '';
                        }
                    }

                    $row[] = $_data;
                }

                if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['finished'] == 0) {
                    $row['DT_RowClass'] = 'text-danger bold';
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['supportid'] = '';
        if (is_numeric($id)) {
            $data['supportid'] = $id;
        }

        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('supports');
        $this->load->view('admin/supports/manage', $data);
    }
    /* Add new support or update existing */

    public function support($id = '')
    {
        if (!has_permission('supports', '', 'edit') && !has_permission('supports', '', 'create')) {
            access_denied('Supports');
        }

        $data = array();
        if ($this->input->post()) {
            if ($id == '') {
                $id = $this->supports_model->add($this->input->post());
                if (is_numeric($id)) {
                    set_alert('success', _l('added_successfuly', _l('support')));
                    redirect(admin_url('supports/support/' . $id));
                }
            } else {
                $success = $this->supports_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('added_successfuly', _l('support')));
                    redirect(admin_url('supports/support/' . $id));
                }
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('support_lowercase'));
        } else {
            $support = $this->supports_model->get($id);

            $support->startdate = date(get_current_date_format(), strtotime($support->startdate));
            $support->duedate = date(get_current_date_format(), strtotime($support->duedate));

            $data['support'] = $support;

            if (!$data['support'] || (!has_permission('supports', '', 'view') && $data['support']->addedfrom != get_staff_user_id())) {
                set_alert('warning', _l('not_found', _l('support_lowercase')));
                redirect(admin_url('supports'));
            }

            $title = _l('edit', _l('support_lowercase'));
        }

        // Get Priorities
        $data['priorities'] = $this->supports_model->get_priorities();

        $data['title'] = $title;
        $data['editor_assets'] = true;
        $this->load->view('admin/supports/support', $data);
    }
    /* Delete support from database */

    public function delete_support($id)
    {
        if (!has_permission('supports', '', 'delete')) {
            access_denied('supports');
        }

        $success = $this->supports_model->delete_support($id);
        $message = _l('problem_deleting', _l('support_lowercase'));
        if ($success) {
            $message = _l('deleted', _l('support'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Mark support as complete / ajax */

    public function mark_complete($id)
    {
        $success = $this->supports_model->mark_complete($id);
        $message = '';
        if ($success) {
            $message = _l('support_marked_as_complete');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Unmark support as complete / ajax */

    public function unmark_complete($id)
    {
        $success = $this->supports_model->unmark_complete($id);
        $message = '';
        if ($success) {
            $message = _l('support_unmarked_as_complete');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
    /* Get support data in a right pane */

    public function get_support_data()
    {
        $supportid = $this->input->post('supportid');
        // Support main data
        $support = $this->supports_model->get($supportid);
        if (!$support) {
            echo 'Support not found';
            die();
        }

        $support->startdate = date(get_current_date_format(), strtotime($support->startdate));
        $support->duedate = date(get_current_date_format(), strtotime($support->duedate));

        $data['support'] = $support;
        $data['id'] = $support->id;
        // Get all comments html
        $data['comments'] = $this->get_support_comments($support->id, true);
        // Get support assignees
        $data['assignees'] = $this->supports_model->get_support_assignees($supportid);

        $this->load->view('admin/supports/view_support_template', $data);
    }

    /**
     * Reload all supports attachments to view
     * @param  mixed $supportid supportid
     * @return json
     */
    public function reload_support_attachments($supportid)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->supports_model->get_support_attachments($supportid));
        }
    }

    /**
     * Remove support attachment
     * @param  mixed $id attachment it
     * @return json
     */
    public function remove_support_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->supports_model->remove_support_attachment($id)
            ));
        }
    }
    /* Check which staff is already in this group and remove from select */

    public function reload_assignees_select($supportid)
    {
        $options = '';
        $this->load->model('staff_model');
        $staff = $this->staff_model->get('', 1);

        foreach ($staff as $assignee) {
            if (total_rows('tblsupportstaffassignees', array(
                    'staffid' => $assignee['staffid'],
                    'supportid' => $supportid
                )) == 0) {
                $options .= '<option value="' . $assignee['staffid'] . '">' . get_staff_full_name($assignee['staffid']) . '</option>';
            }
        }

        echo $options;
    }

    public function init_checklist_items()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $post_data = $this->input->post();
                $data['support_id'] = $post_data['supportid'];
                $data['checklists'] = $this->supports_model->get_checklist_items($post_data['supportid']);
                $this->load->view('admin/supports/checklist_items_template', $data);
            }
        }
    }

    public function update_checklist_order()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->supports_model->update_checklist_order($this->input->post());
            }
        }
    }

    /**
     * Upload support attachment
     */
    public function upload_file()
    {
        if ($this->input->post()) {
            $supportid = $this->input->post('supportid');
            $file = handle_supports_attachments($supportid);
            if ($file) {
                $success = $this->supports_model->add_attachment_to_database($supportid, $file);
                echo json_encode(array(
                    'success' => $success
                ));
            }
        }
    }

    public function add_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                echo json_encode(array('success' => $this->supports_model->add_checklist_item($this->input->post())));
            }
        }
    }
    /* Add new support comment / ajax */

    public function add_support_comment()
    {
        echo json_encode(array(
            'success' => $this->supports_model->add_support_comment($this->input->post())
        ));
    }
    /* Get all supports comments */

    public function get_support_comments($supportid, $return = false)
    {
        $result = $this->supports_model->get_support_comments($supportid);
        $comments = '';
        foreach ($result as $comment) {
            $comments .= '<div class="col-md-12 mbot10" data-commentid="' . $comment['id'] . '">';
            $comments .= '<a href="' . admin_url('profile/' . $comment['commentator']) . '">' . staff_profile_image($comment['commentator'], array(
                    'staff-profile-image-small',
                    'media-object img-circle pull-left mright10'
                )) . '</a>';
            if ($comment['commentator'] == get_staff_user_id() || is_admin()) {
                $comments .= '<span class="pull-right"><a href="#" onclick="remove_support_comment(' . $comment['id'] . '); return false;"><i class="fa fa-trash text-danger"></i></span></a>';
            }
            $comments .= '<div class="media-body">';
            $comments .= '<a href="' . admin_url('profile/' . $comment['commentator']) . '">' . get_staff_full_name($comment['commentator']) . '</a> <br />';
            $comments .= check_for_links($comment['content']) . '<br />';
            $comments .= '<small class="mtop10 text-muted">' . _dt($comment['dateadded']) . '</small>';

            $comments .= '</div>';
            $comments .= '<hr />';
            $comments .= '</div>';
        }

        if ($return == false) {
            echo $comments;
        } else {
            return $comments;
        }
    }
    /* Remove support comment / ajax */

    public function remove_comment($id)
    {
        echo json_encode(array(
            'success' => $this->supports_model->remove_comment($id)
        ));
    }

    public function delete_checklist_item($id)
    {
        $item = $this->supports_model->get_checklist_item($id);

        if ((has_permission('supports', '', 'delete') || $item->addedfrom != get_staff_user_id())) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(array('success' => $this->supports_model->delete_checklist_item($id)));
            }
        }
    }

    public function update_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->supports_model->update_checklist_item($this->input->post('listid'), $this->input->post('description'));
            }
        }
    }

    public function checkbox_action($listid, $value)
    {
        $this->db->where('id', $listid);
        $this->db->update('tblsupportchecklists', array('finished' => $value));
    }
    /* Add support assignees / ajax */

    public function add_support_assignees()
    {
        if (!has_permission('supports', '', 'edit') || !has_permission('supports', '', 'create')) {
            access_denied('supports');
        }

        echo json_encode(array(
            'success' => $this->supports_model->add_support_assignees($this->input->post())
        ));
    }
    /* Remove assignee / ajax */

    public function remove_assignee($id, $supportid)
    {
        if (!has_permission('supports', '', 'edit') && !has_permission('supports', '', 'create')) {
            access_denied('supports');
        }

        $success = $this->supports_model->remove_assignee($id, $supportid);
        $message = '';
        if ($success) {
            $message = _l('support_assignee_removed');
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }
}

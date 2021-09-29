<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('expenses_model');
        
        if(get_permission_module('expenses') == 0) {
            redirect(admin_url('home'));
        }
    }

    public function index($id = '')
    {
        $this->list_expenses($id);
    }

    public function list_expenses($id = '')
    {
        $has_permission = has_permission('expenses', '', 'view');
        if (!has_permission('expenses', '', 'view') && !has_permission('expenses', '', 'view_own')) {
            access_denied('expenses');
        }

        if ($this->input->is_ajax_request()) {

            $aColumns = array(
                'note',
                'category',
                'amount',
                'date',
                'firstname',
                'paymentmode'
            );

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblexpenses.livreurid',
                'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category'
            );

            $where = array();
            if ($this->input->post('custom_view')) {
                $custom_view = $this->input->post('custom_view');
                // is expense category
                if (is_numeric($custom_view)) {
                    array_push($where, 'AND category = ' . $custom_view);
                }
            }

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            //If not admin show only own expenses
            if (!has_permission('expenses', '', 'view')) {
                array_push($where, 'AND tblexpenses.addedfrom ="' . get_staff_user_id() . '"');
            }
            
            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblexpenses.dateadded = "' . date('Y-m-d') . '"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblexpenses.dateadded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblexpenses.dateadded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $sIndexColumn = "id";
            $sTable = 'tblexpenses';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(
                'name',
                'tblexpenses.id',
                'staffid',
                'lastname'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $base_currency_symbol = $this->currencies_model->get_base_currency()->symbol;

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'note') {
                        if (strlen($_data) >= 30) {
                            $_data = substr($_data, 0, 30) . '<span onclick="init_expense(' . $aRow['id'] . ');return false;" data-toggle="tooltip" data-original-title="' . $_data . '" style="color: #0081BB !important"> (Lire la suite)</span>';
                        }
                        $_data = '<a href="#" onclick="init_expense(' . $aRow['id'] . ');return false;">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'category') {
                        $_data = '<b>' . $aRow['name'] . '</b>';
                    } else if ($aColumns[$i] == 'amount') {
                        $_data = format_money($_data, $base_currency_symbol);
                    } else if ($aColumns[$i] == 'date') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'firstname') {
                        if (is_numeric($aRow['staffid'])) {
                            $_data = staff_profile_image($aRow['staffid'], array('staff-profile-image-small'));
                            $_data .= ' <a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                        }
                    } else if ($aColumns[$i] == 'paymentmode') {
                        $_data = '';
                        if ($aRow['paymentmode'] != 0) {
                            $_data = $this->payment_modes_model->get($aRow['paymentmode'])->name;
                        }
                    }
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        $data['expenseid'] = '';
        if (is_numeric($id)) {
            $data['expenseid'] = $id;
        }

        $data['bodyclass'] = 'small-table';
        $data['title'] = _l('expenses');
        $this->load->view('admin/expenses/manage', $data);
    }

    public function expense($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('expenses', '', 'create')) {
                    set_alert('danger', _l('access_denied'));
                    echo json_encode(array(
                        'url' => admin_url('expenses/expense')
                    ));
                    die;
                }
                $id = $this->expenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense')));
                    echo json_encode(array(
                        'url' => admin_url('expenses/list_expenses/' . $id),
                        'expenseid' => $id
                    ));
                    die;
                }
                echo json_encode(array(
                    'url' => admin_url('expenses/expense')
                ));
                die;
            } else {
                if (!has_permission('expenses', '', 'edit')) {
                    set_alert('danger', _l('access_denied'));
                    echo json_encode(array(
                        'url' => admin_url('expenses/expense/' . $id)
                    ));
                    die;
                }
                $success = $this->expenses_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense')));
                }

                echo json_encode(array(
                    'url' => admin_url('expenses/list_expenses/' . $id),
                    'expenseid' => $id
                ));
                die;
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('expense_lowercase'));
        } else {
            $data['expense'] = $this->expenses_model->get($id);

            if (!$data['expense'] || (!has_permission('expenses', '', 'view') && $data['expense']->addedfrom != get_staff_user_id())) {
                set_alert('warning', _l('not_found', _l('expense_lowercase')));
                redirect(admin_url('expenses/list_expenses'));
            }

            $title = _l('edit', _l('expense_lowercase'));
        }

        $this->load->model('staff_model');
        $this->load->model('payment_modes_model');
        $this->load->model('currencies_model');
        $this->load->model('locations_model');

        $data['livreurs'] = $this->staff_model->get_livreurs();
        $data['categories'] = $this->expenses_model->get_category();
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['locations'] = $this->locations_model->get('', true);

        $data['title'] = $title;
        $this->load->view('admin/expenses/expense', $data);
    }

    public function delete($id)
    {
        if (!has_permission('expenses', '', 'delete')) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/list_expenses'));
        }

        $response = $this->expenses_model->delete($id);
        if ($response === true) {
            set_alert('success', _l('deleted', _l('expense')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_lowercase')));
        }
        redirect(admin_url('expenses/list_expenses'));
    }

    public function copy($id)
    {
        if (!has_permission('expenses', '', 'create')) {
            access_denied('expenses');
        }

        $new_expense_id = $this->expenses_model->copy($id);

        if ($new_expense_id) {
            set_alert('success', _l('expense_copy_success'));
            redirect(admin_url('expenses/expense/' . $new_expense_id));
        } else {
            set_alert('warning', _l('expense_copy_fail'));
        }

        redirect(admin_url('expenses/list_expenses/' . $id));
    }

    public function get_expense_data_ajax($id)
    {
        if (!has_permission('expenses', '', 'view') && !has_permission('expenses', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $expense = $this->expenses_model->get($id);

        if (!$expense || (!has_permission('expenses', '', 'view') && $expense->addedfrom != get_staff_user_id())) {
            echo _l('expense_not_found');
            die;
        }

        $data['expense'] = $expense;

        $this->load->view('admin/expenses/expense_preview_template', $data);
    }

    public function categories()
    {
        if (!is_admin()) {
            access_denied('expenses');
        }

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'name',
                'description'
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblexpensescategories';

            $join = array(
                'LEFT JOIN tblentreprise ON tblentreprise.id_entreprise = tblexpensescategories.id_entreprise'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, array(), array(
                'id'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('expenses/category/' . $aRow['id']) . '">' . $_data . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'pencil-square-o', 'btn-default', array('onclick' => 'edit_category(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['name'], 'data-description' => clear_textarea_breaks($aRow['description'])));
                $row[] = $options .= icon_btn('admin/expenses/delete_category/' . $aRow['id'], 'remove', 'btn-danger');

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        $data['title'] = _l('expense_categories');
        $this->load->view('admin/expenses/manage_categories', $data);
    }

    public function category()
    {
        if (!is_admin()) {
            access_denied('expenses');
        }

        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->expenses_model->add_category($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('expense_category')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->expenses_model->update_category($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('expense_category')));
                }
            }
        }
    }

    public function delete_category($id)
    {
        if (!is_admin()) {
            access_denied('expenses');
        }
        if (!$id) {
            redirect(admin_url('expenses/categories'));
        }

        $response = $this->expenses_model->delete_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('expense_category_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('expense_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_category_lowercase')));
        }

        redirect(admin_url('expenses/categories'));
    }

    public function add_expense_attachment($id)
    {
        handle_expense_attachments($id);
    }

    public function delete_expense_attachment($id, $preview = '')
    {
        $success = $this->expenses_model->delete_expense_attachment($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('expense_receipt')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('expense_receipt_lowercase')));
        }

        if ($preview == '') {
            redirect(admin_url('expenses/expense/' . $id));
        } else {
            redirect(admin_url('expenses/list_expenses/' . $id));
        }
    }

    function get_expenses_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->expenses_model->get_expenses_total($this->input->post());

            if ($data['totals']['currency_switcher'] == true) {
                $this->load->model('currencies_model');
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['_currency'] = $data['totals']['currencyid'];
            $this->load->view('admin/expenses/expenses_total_template', $data);
        }
    }

    public function get_list_categories_expense()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->expenses_model->get_category();
            echo json_encode(array('error' => '', 'result' => $data));
        }
    }

    public function add_category_ajax()
    {
        if (!has_permission('expenses', '', 'create')) {
            access_denied('expenses');
        }

        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $name = $this->input->post('name');
                $categoryid = $this->expenses_model->add_category($this->input->post());
                $message = '';
                if ($categoryid) {
                    $success = true;
                    $message = _l('added_successfuly', _l('expense_category'));
                }
                echo json_encode(array('success' => $success, 'message' => $message, 'categoryid' => $categoryid, 'name' => $name));
            }
        }
    }
}

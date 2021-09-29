<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get expense(s)
     * @param  mixed $id Optional expense id
     * @return mixed     object or array
     */
    public function get($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->db->select('*,tblexpensescategories.name as category_name,tblinvoicepaymentsmodes.name as payment_mode_name, tblexpenses.id as expenseid');
        $this->db->from('tblexpenses');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblexpenses.livreurid', 'left');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblexpenses.paymentmode', 'left');
        $this->db->join('tblexpensescategories', 'tblexpensescategories.id = tblexpenses.category');
        $this->db->where('tblexpenses.id_entreprise', $id_E);

        if (is_numeric($id)) {
            $this->db->where('tblexpenses.id', $id);
            return $this->db->get()->row();
        }
        return $this->db->get()->result_array();
    }

    /**
     * Add new expense
     * @param mixed $data All $_POST data
     * @return  mixed
     */
    public function add($data)
    {
        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $this->db->insert('tblexpenses', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_expense_added') . ' [ID : ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update expense
     * @param  mixed $data All $_POST data
     * @param  mixed $id   expense id to update
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;
        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);

        $this->db->where('id', $id);
        $this->db->update('tblexpenses', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity(_l('expense_updated') . ' [ID : ' . $id . ']');
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete expense from database, if used return
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblexpenses');

        if ($this->db->affected_rows() > 0) {
            logActivity(_l('expense_deleted') . ' [' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Copy expense
     * @param  mixed $id expense id to copy from
     * @return mixed
     */
    public function copy($id)
    {
        $expense_fields = $this->db->list_fields('tblexpenses');
        $expense = $this->get($id);
        $new_expense_data = array();
        foreach ($expense_fields as $field) {
            if (isset($expense->$field)) {
                // We dont need the invoiceid field
                if ($field != 'invoiceid' && $field != 'id') {
                    $new_expense_data[$field] = $expense->$field;
                }
            }
        }
        $new_expense_data['addedfrom'] = get_staff_user_id();
        $new_expense_data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert('tblexpenses', $new_expense_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('expense_copied') . ' [ExpenseID' . $id . ', NewExpenseID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Delete Expense attachment
     * @param  mixed $id expense id
     * @return boolean
     */
    public function delete_expense_attachment($id)
    {
        if (delete_dir(EXPENSE_ATTACHMENTS_FOLDER . $id)) {

            $this->db->where('id', $id);
            $this->db->update('tblexpenses', array(
                'attachment' => ''
            ));

            logActivity(_l('expense_receipt_deleted') . ' [ExpenseID' . $id . ']');
            return true;
        }

        return false;
    }
    /* Categories start */

    /**
     * Get expense category
     * @param  mixed $id category id (Optional)
     * @return mixed     object or array
     */
    public function get_category($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblexpensescategories')->row();
        }

        return $this->db->get('tblexpensescategories')->result_array();
    }

    /**
     * Add new expense category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_category($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;

        $data['description'] = nl2br($data['description']);
        $this->db->insert('tblexpensescategories', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity(_l('new_expense_category_added') . ' [ID : ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update expense category
     * @param  mixed $data All $_POST data
     * @param  mixed $id   expense id to update
     * @return boolean
     */
    public function update_category($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $this->db->where('id', $id);
        $this->db->update('tblexpensescategories', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity(_l('expense_category_updated') . ' [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete expense category from database, if used return array with key referenced
     */
    public function delete_category($id)
    {
        if (is_reference_in_table('category', 'tblexpenses', $id)) {
            return array(
                'referenced' => true
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblexpensescategories');

        if ($this->db->affected_rows() > 0) {
            logActivity(_l('expense_category_deleted') . ' [' . $id . ']');
            return true;
        }
        return false;
    }

    public function get_expenses_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM tblexpenses ORDER by year DESC')->result_array();
    }

    public function get_expenses_total($data)
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        $this->load->model('currencies_model');
        $base_currency = $this->currencies_model->get_base_currency()->id;
        $base = true;
        $currency_switcher = false;
        if (isset($data['currency'])) {
            $currencyid = $data['currency'];
            $currency_switcher = true;
        } else if (isset($data['project_id']) && $data['project_id'] != '') {
            $this->load->model('projects_model');
            $currencyid = $this->projects_model->get_currency($data['project_id'])->id;
        } else {
            $currencyid = $base_currency;
            if (total_rows('tblexpenses', array(
                    'currency !=' => $base_currency
                ))) {
                $currency_switcher = true;
            }
        }
        $symbol = $this->currencies_model->get_currency_symbol($currencyid);

        $has_permission_view = has_permission('expenses', '', 'view');
        $_result = array();

        for ($i = 1; $i <= 5; $i++) {
            $this->db->select('amount,tax,invoiceid');
            $this->db->where('currency', $currencyid);
            $this->db->where('id_entreprise', $id_E);

            /* if (isset($data['years']) && count($data['years']) > 0) {
              $this->db->where('YEAR(date) IN (' . implode(', ', $data['years']) . ')');
              }
              if (isset($data['months']) && count($data['months']) > 0) {
              $this->db->where('MONTH(date) IN (' . implode(', ', $data['months']) . ')');
              } */
            if (isset($data['categories']) && count($data['categories']) > 0) {
                $this->db->where('category IN (' . implode(', ', $data['categories']) . ')');
            }
            if (isset($data['project_id']) && $data['project_id'] != '') {
                $this->db->where('project_id', $data['project_id']);
            }

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }
            switch ($i) {
                case 1:
                    $key = 'all';
                    break;
                case 2:
                    $key = 'billable';
                    $this->db->where('billable', 1);
                    break;
                case 3:
                    $key = 'non_billable';
                    $this->db->where('billable', 0);
                    break;
            }
            $all_expenses = $this->db->get('tblexpenses')->result_array();

            $_total_all = array();
            foreach ($all_expenses as $expense) {
                $_total = $expense['amount'];
                if ($expense['tax'] != 0) {
                    $tax = get_tax_by_id($expense['tax']);
                    $_total += ($_total / 100 * $tax->taxrate);
                }
                array_push($_total_all, $_total);
            }
            $_result[$key]['total'] = format_money(array_sum($_total_all), $symbol);
        }

        $_result['currency_switcher'] = $currency_switcher;
        $_result['currencyid'] = $currencyid;

        return $_result;
    }
}

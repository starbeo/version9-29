<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('payments_model');
        
        if(get_permission_module('payments') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* List all invoice paments */

    public function index()
    {
        $has_permission = has_permission('payments', '', 'view');
        if (!has_permission('payments', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('payments');
        }

        $_custom_view = '';
        if ($this->input->get('custom_view')) {
            $_custom_view = $this->input->get('custom_view');
        }

        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'factureinterneid',
                'paymentmode',
                'amount',
                'tblfactureinternepaymentrecords.date'
            );

            $join = array(
                'LEFT JOIN tblfacturesinternes ON tblfacturesinternes.id = tblfactureinternepaymentrecords.factureinterneid',
                'LEFT JOIN tblinvoicepaymentsmodes ON tblinvoicepaymentsmodes.id = tblfactureinternepaymentrecords.paymentmode'
            );

            $where = array();
            if ($this->input->post('custom_view')) {
                $custom_view = $this->input->post('custom_view');
                if ($custom_view == 'today') {
                    array_push($where, 'AND DATE(tblfactureinternepaymentrecords.date) = "' . date('Y-m-d') . '"');
                }
            }

            //If not admin show only own estimates
            if (!$has_permission) {
                array_push($where, 'AND factureinterneid IN (SELECT id FROM tblfacturesinternes WHERE id_utilisateur=' . get_staff_user_id() . ')');
            }
            
            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblfactureinternepaymentrecords.daterecorded = "' . date('Y-m-d') . '"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblfactureinternepaymentrecords.daterecorded, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblfactureinternepaymentrecords.daterecorded > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $sIndexColumn = "id";
            $sTable = 'tblfactureinternepaymentrecords';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array(
                'tblfactureinternepaymentrecords.id as paymentrecordid',
                'tblfactureinternepaymentrecords.date',
                'tblfacturesinternes.nom',
                'tblinvoicepaymentsmodes.name',
                'tblinvoicepaymentsmodes.id as paymentmodeid'
            ));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();

                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                    } else if ($aColumns[$i] == 'tblfactureinternepaymentrecords.date') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'factureinterneid') {
                        $_data = '<a href="' . admin_url('factures_internes/index/' . $aRow[$aColumns[$i]]) . '">' . $aRow['nom'] . '</a>';
                    } else if ($aColumns[$i] == 'amount') {
                        $_data = '<p class="pright30" style="    text-align: right;">' . format_money($_data) . ' Dhs</p>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('admin/payments/payment/' . $aRow['paymentrecordid'], 'pencil-square-o');
                if (has_permission('payments', '', 'delete')) {
                    $options .= icon_btn('admin/payments/delete/' . $aRow['paymentrecordid'], 'remove', 'btn-danger');
                }

                $row[] = $options;
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }

        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('payments');
        $this->load->view('admin/payments/manage', $data);
    }
    /* Update payment data */

    public function payment($id = '')
    {
        if (!has_permission('payments', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('payments');
        }
        if (!$id) {
            redirect(admin_url('payments'));
        }

        if ($this->input->post()) {
            if (!has_permission('payments', '', 'edit')) {
                access_denied('Update Payment');
            }
            $success = $this->payments_model->update($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfuly', _l('payment')));
            }
            redirect(admin_url('payments/payment/' . $id));
        }

        $data['payment'] = $this->payments_model->get($id);

        if (!$data['payment'] || (!has_permission('payments', '', 'view') && $data['payment']->addedfrom != get_staff_user_id())) {
            set_alert('warning', _l('not_found', _l('payment')));
            redirect(admin_url('payments'));
        }
        //Get Facture Interne
        $this->load->model('factures_internes_model');
        $data['invoice'] = $this->factures_internes_model->get($data['payment']->factureinterneid);
        //Get Payment Modes
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get();

        $data['title'] = _l('edit', _l('payment_lowercase'));
        $this->load->view('admin/payments/payment', $data);
    }

    /**
     * Generate payment pdf
     * @param  mixed $id Payment id
     */
    public function pdf($id)
    {

        if (!has_permission('payments', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('View Payment');
        }

        $payment = $this->payments_model->get($id);
        $this->load->model('factures_internes_model');
        $payment->invoice_data = $this->factures_internes_model->get($payment->factureinterneid);
        $paymentpdf = payment_pdf($payment);
        $paymentpdf->Output(_l('payment') . '-' . $payment->paymentid . '.pdf', 'D');
    }
    /* Delete payment */

    public function delete($id)
    {
        if (!has_permission('payments', '', 'delete')) {
            access_denied('Delete Payment');
        }
        if (!$id) {
            redirect(admin_url('payments'));
        }

        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }

        redirect(admin_url('payments'));
    }
}

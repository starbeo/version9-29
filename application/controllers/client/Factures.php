<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures extends Client_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('factures_model');
    }

    /**
     * List all factures
     */
    public function index($id = false, $status = false, $type = false)
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblfactures.nom',
                'tblfactures.total_crbt',
                'tblfactures.total_frais',
                'tblfactures.total_net',
                '(SELECT count(id) FROM tblcolisfacture WHERE facture_id = tblfactures.id) as nbr_colis',
                'tblfactures.type',
                'tblfactures.status',
                'tblfactures.date_created'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactures';

            $join = array();
            $where = array('AND tblfactures.id_expediteur = ' . get_expediteur_user_id());
            if (is_numeric($status)) {
                array_push($where, 'AND tblfactures.status = ' . $status);
            }
            if (is_numeric($type)) {
                array_push($where, 'AND tblfactures.type = ' . $type);
            }

            //Filtre
            if ($this->input->post('f-type') && is_numeric($this->input->post('f-type'))) {
                array_push($where, ' AND tblfactures.type = ' . $this->input->post('f-type'));
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND tblfactures.status = ' . $this->input->post('f-statut'));
            }
            if ($this->input->post('f-date-created-start') && is_date(to_sql_date($this->input->post('f-date-created-start')))) {
                array_push($where, ' AND tblfactures.date_created >= "' . to_sql_date($this->input->post('f-date-created-start')) . ' 00:00:00"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblfactures.date_created <= "' . to_sql_date($this->input->post('f-date-created-end')) . ' 23:59:59"');
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblfactures.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblfactures.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblfactures.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblfactures.id'), 'tblfactures.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = '<a href="#" onclick="init_facture(' . $aRow['id'] . '); return false;">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblfactures.total_crbt' || $aColumns[$i] == 'tblfactures.total_frais' || $aColumns[$i] == 'tblfactures.total_net') {
                        $_data = '<p class="pright30" style="text-align: right;"><span class="label label-default inline-block">' . format_money($_data) . '</span></p>';
                    } else if ($aColumns[$i] == 'nbr_colis') {
                        if ($_data == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $_data . '</span></p>';
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        $_data = date(get_current_date_format(), strtotime($_data));
                    }

                    $row[] = $_data;
                }

                $options = '';
                $options .= icon_btn('javascript:void(0)', 'eye', 'btn-info', array('onclick' => 'init_facture(' . $aRow['id'] . ')'));
                $options .= icon_btn('client/factures/pdf/' . $aRow['id'], 'file-pdf-o', 'btn-danger');

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        //check owned by client then do next
        if (is_numeric($id)) {
            if (owns_data("tblfactures", get_expediteur_user_id(), '', 'id_expediteur') == 1) {
                $data['invoiceid'] = $id;
            }
        }

        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $data['statuses'] = array(array('id' => 1, 'name' => _l('invoices_unpaid')), array('id' => 2, 'name' => _l('invoices_paid')));
        //Types
        $data['types'] = array(array('id' => '2', 'name' => _l('delivred')), array('id' => '3', 'name' => _l('returned')));

        $data['title'] = _l('invoices');
        $this->load->view('client/factures/manage', $data);
    }

    /**
     * Preview facture
     */
    public function preview($id)
    {
        if (is_numeric($id)) {
            $this->index($id);
        }

        redirect(client_url('factures'));
    }

    /**
     * Facture livrer
     */
    public function livrer($type = false)
    {
        if ($type == 2) {
            $this->index(false, false, $type);
        } else {
            redirect(client_url('factures'));
        }
    }

    /**
     * Facture Retourner
     */
    public function retourner($type = false)
    {
        if ($type == 3) {
            $this->index(false, false, $type);
        } else {
            redirect(client_url('factures'));
        }
    }

    /**
     * Get invoice data used when user click on invoice number in a datatable left side
     */
    public function get_facture_data_ajax($id)
    {
        if (!is_numeric($id)) {
            die('Aucune facture trouvée');
        }

        // Get invoice
        $facture = $this->factures_model->get($id);
        // Check invoice
        if (!$facture || ($facture && $facture->id_expediteur != get_expediteur_user_id())) {
            echo _l('invoice_not_found');
            die;
        }
        $facture->date_created = date(get_current_date_format(), strtotime($facture->date_created));

        $data['invoice'] = $facture;
        $this->load->view('client/factures/facture_preview_template', $data);
    }

    /**
     * Print PDF
     */
    public function pdf($id)
    {
        if (!is_numeric($id)) {
            redirect(client_url('factures'));
        }

        // Get invoice
        $facture = $this->factures_model->get($id);
        // Check invoice
        if (!$facture || ($facture && $facture->id_expediteur != get_expediteur_user_id())) {
            set_alert('danger', _l('access_denied'));
            redirect(client_url('factures'));
        }

        if (count($facture->items) == 0) {
            set_alert('warning', _l('invoice_does_not_contain_any_colis'));
            redirect(client_url('factures'));
        } else {
            $facture->nom_expediteur = $facture->client->nom;
            $facture->frais = $facture->client->frais_retourne;
            facture_pdf($facture);
        }
    }
}

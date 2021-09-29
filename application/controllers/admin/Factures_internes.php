<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures_internes extends Admin_controller
{

    public $pdf_zip;

    function __construct()
    {
        parent::__construct();
        $this->load->model('factures_internes_model');

        if (get_permission_module('factures_internes') == 0) {
            redirect(admin_url('home'));
        }
    }
    /* List all factures internes */

    public function index($id = false)
    {
        $has_permission = has_permission('factures_internes', '', 'view');
        if (!has_permission('factures_internes', '', 'view') && !has_permission('factures_internes', '', 'view_own')) {
            access_denied('Factures Internes');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblfacturesinternes.id',
                'tblfacturesinternes.nom',
                'tblfacturesinternes.total_received',
                'tblfacturesinternes.total',
                'tblfacturesinternes.rest',
                'lastname',
                'tblfacturesinternes.date_created',
                'firstname'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfacturesinternes';

            $join = array(
                'LEFT JOIN tblstaff ON tblstaff.staffid = tblfacturesinternes.id_utilisateur'
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblfacturesinternes.id_utilisateur = "' . get_staff_user_id() . '"');
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblfacturesinternes.date_created LIKE "' . date('Y-m-d') . '%"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblfacturesinternes.date_created, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblfacturesinternes.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('staffid', 'tblfacturesinternes.total_frais', 'tblfacturesinternes.total_refuse', 'tblfacturesinternes.total_parrainage', 'tblfacturesinternes.total_remise'));
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

                    if ($aColumns[$i] == 'tblfacturesinternes.nom') {
                        $_data = '<a href="#" onclick="init_facture_interne(' . $aRow['tblfacturesinternes.id'] . '); return false;">' . $aRow['tblfacturesinternes.nom'] . '</a>';
                    } else if ($aColumns[$i] == 'tblfacturesinternes.total_received') {
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($_data) . ' Dhs</p>';
                    } else if ($aColumns[$i] == 'tblfacturesinternes.total') {
                        $totalNet = $_data - $aRow['total_frais'] - $aRow['total_refuse'] + $aRow['total_parrainage'] + $aRow['total_remise'];
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($totalNet) . ' Dhs</p>';
                    } else if ($aColumns[$i] == 'tblfacturesinternes.rest') {
                        $label = '';
                        $color = '';
                        if ($_data == 0) {
                            $label = 'label-success';
                            $color = '#449d44';
                        } elseif ($_data > 0) {
                            $label = 'label-info';
                            $color = '#03a9f4';
                        } elseif ($_data < 0) {
                            $label = 'label-danger';
                            $color = 'red';
                        }
                        $_data = '<p style="text-align: right;" class="label ' . $label . '">' . format_money($_data) . ' Dhs</p>';
                    } else if ($aColumns[$i] == 'lastname') {
                        $_data = '<p style="text-align: center;">' . total_rows('tblfactureinterneitems', array('facture_interne_id' => $aRow['tblfacturesinternes.id'])) . '</p>';
                    } else if ($aColumns[$i] == 'tblfacturesinternes.date_created') {
                        $_data = date('d/m/Y', strtotime($_data));
                    } else if ($aColumns[$i] == 'firstname') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['firstname'] . ' ' . $aRow['lastname'] . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('factures_internes', '', 'edit')) {
                    $options .= icon_btn('admin/factures_internes/facture/' . $aRow['tblfacturesinternes.id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Facture Interne'));
                }
                if (has_permission('factures_internes', '', 'download')) {
                    $options .= icon_btn('admin/factures_internes/pdf/' . $aRow['tblfacturesinternes.id'], 'file-pdf-o', 'btn-danger', array('title' => 'Imprimer PDF Facture Interne'));
                    $options .= icon_btn('admin/factures_internes/pdf/' . $aRow['tblfacturesinternes.id'] . '/detailles', 'file-pdf-o', 'btn-danger', array('title' => 'Imprimer PDF Virements Clients'));
                    $options .= icon_btn('admin/factures_internes/excel/' . $aRow['tblfacturesinternes.id'], 'file-excel-o', 'btn-success', array('title' => 'Imprimer Excel Virements Clients'));
                    $options .= icon_btn('admin/factures_internes/zip_factures_facture_interne/' . $aRow['tblfacturesinternes.id'], 'download', 'btn-info', array('title' => 'Télécharger le dossier complet de cette Facture Interne'));
                }
                if (has_permission('factures_internes', '', 'delete')) {
                    $options .= icon_btn('admin/factures_internes/delete/' . $aRow['tblfacturesinternes.id'], 'remove', 'btn-danger btn-delete-confirm', array('title' => 'Supprimer Facture Interne'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //check if is admin or owned by user then do next
        if (is_numeric($id)) {
            if (owns_data("tblfacturesinternes", $id, '', 'id_utilisateur') == 1 OR is_admin() == 1) {
                $data['facture_interne_id'] = $id;
            }
        }

        $data['title'] = _l('factures_internes');
        $this->load->view('admin/factures_internes/manage', $data);
    }
    /* Edit or add new facture */

    public function facture($id = '')
    {
        if (!has_permission('factures_internes', '', 'view') && !has_permission('factures_internes', '', 'view_own')) {
            access_denied('Factures Internes');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('factures_internes', '', 'create')) {
                    access_denied('Facture Interne');
                }
                $id = $this->factures_internes_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('facture_interne')));
                }
            } else {
                if (!has_permission('factures_internes', '', 'edit')) {
                    access_denied('Facture Interne');
                }
                $success = $this->factures_internes_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('facture_interne')));
                }
            }
            redirect(admin_url('factures_internes/facture/' . $id));
        }

        if ($id == '') {
            //$title = _l('add_new',_l('facture_interne_lowercase'));
            //$data['class1'] = 'col-md-6';
            $last_facture_interne = $this->factures_internes_model->get_last_facture_interne();
            if (is_null($last_facture_interne) || (!is_null($last_facture_interne) && total_rows('tblfactureinterneitems', array('facture_interne_id' => $last_facture_interne->id))) > 0) {
                $new_id = $this->factures_internes_model->add();
                redirect(admin_url('factures_internes/facture/' . $new_id));
            } else if (!is_null($last_facture_interne) && total_rows('tblfactureinterneitems', array('facture_interne_id' => $last_facture_interne->id)) == 0) {
                redirect(admin_url('factures_internes/facture/' . $last_facture_interne->id));
            }
        } else {
            $facture = $this->factures_internes_model->get($id);
            if (!$facture) {
                set_alert('warning', _l('not_found', _l('facture_interne')));
                redirect(admin_url('factures_internes'));
            }

            $data['facture'] = $facture;
            $title = $facture->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        $data['title'] = $title;
        $this->load->view('admin/factures_internes/facture', $data);
    }
    /* Get all facture interne data used when user click on facture interne number in a datatable left side */

    public function get_facture_interne_data_ajax($id)
    {
        if (!has_permission('factures_internes', '', 'view') && !has_permission('factures_internes', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }
        if (!$id) {
            die('Aucune facture interne trouvée');
        }

        $facture_interne = $this->factures_internes_model->get($id);
        if (!$facture_interne || (!has_permission('factures_internes', '', 'view') && $facture_interne->id_utilisateur != get_staff_user_id())) {
            echo _l('invoice_not_found');
            die;
        }
        $facture_interne->date_created = date(get_current_date_format(), strtotime($facture_interne->date_created));

        $data['invoice'] = $facture_interne;
        $this->load->view('admin/factures_internes/facture_preview_template', $data);
    }

    public function add_facture_to_facture_interne()
    {
        $factureInterneId = $this->input->post('facture_interne_id');
        $factureId = $this->input->post('facture_id');
        if (!has_permission('factures_internes', '', 'create') || !is_numeric($factureId) || !is_numeric($factureInterneId)) {
            echo json_encode(array('success' => false, 'type' => 'danger', 'message' => _l('access_denied')));
        } else {
            $success = $this->factures_internes_model->add_facture_to_facture_interne($factureInterneId, $factureId);
            if (is_array($success)) {
                $message = _l('added_successfuly', _l('facture_to_facture_interne'));
                echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total_crbt' => $success['total_crbt'], 'total_frais' => $success['total_frais'], 'total_refuse' => $success['total_refuse'], 'total_parrainage' => $success['total_parrainage'], 'total_remise' => $success['total_remise'], 'total_net' => $success['total_net']));
            }
        }
    }

    public function remove_facture_to_facture_interne()
    {
        $id = $this->input->post('id');
        if (!has_permission('factures_internes', '', 'edit') || !is_numeric($id)) {
            echo json_encode(array('success' => false, 'type' => 'danger', 'message' => _l('access_denied')));
        } else {
            $success = $this->factures_internes_model->remove_facture_to_facture_interne($id);
            if (is_array($success)) {
                $message = _l('deleted', _l('facture_to_facture_interne'));
                echo json_encode(array('success' => true, 'type' => 'success', 'message' => $message, 'total_crbt' => $success['total_crbt'], 'total_frais' => $success['total_frais'], 'total_refuse' => $success['total_refuse'], 'total_parrainage' => $success['total_parrainage'], 'total_remise' => $success['total_remise'], 'total_net' => $success['total_net']));
            }
        }
    }
    /* Delete facture interne from database */

    public function delete($id)
    {
        if (!has_permission('factures_internes', '', 'delete')) {
            access_denied('Facture Interne');
        }

        $response = $this->factures_internes_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('facture_interne')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('facture_interne_lowercase')));
        }

        redirect(admin_url('factures_internes'));
    }

    public function init_items_facture_interne()
    {
        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblfactures.sent',
                'tblfactures.id',
                'tblfactures.nom',
                'tblfactures.total_net',
                'tblfactures.type',
                'tblfactures.status',
                'tblfactures.date_created',
                'tblfactures.id_expediteur'
            );

            $where = array();
            array_push($where, 'AND type = 2');
            array_push($where, 'AND status = 1');
            array_push($where, 'AND tblfactures.id NOT IN (SELECT facture_id FROM tblfactureinterneitems WHERE facture_id = tblfactures.id)');

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblfactures.id_expediteur',);

            $sIndexColumn = "id";
            $sTable = 'tblfactures';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.nom as expediteur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                        $_data = $aRow[$aColumns[$i]];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblfactures.id') {
                        $_data = icon_btn('#', 'plus', 'btn-success facture_added', array('data-id' => $_data));


                    } else if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = ucfirst($_data);
                    }
                    else if ($aColumns[$i] == 'tblfactures.sent') {
                        $_data = '<div class="form-group"><div class="checkbox checkbox-primary" data-toggle="tooltip" title=""><input id="product_checked_' . $aRow['tblfactures.id'] . '" class="product_checked" type="checkbox" value="' .  $aRow['tblfactures.id'] . '"><label for=""></label></div></div>';

                    }
                    else if ($aColumns[$i] == 'tblfactures.total_net') {
                        $_data = number_format($_data, 2, '.', ',');
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_facture_type($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblfactures.id_expediteur') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $_data) . '" target="_blank">' . $aRow['expediteur'] . '</a>';
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    public function init_historique_items_facture_interne($facture_id)
    {
        if ($this->input->is_ajax_request()) {
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array(
                'tblfactures.nom',
                'tblfactures.total_net',
                'tblfactures.type',
                'tblfactures.status',
                'tblfactures.date_created',
                'tblfactures.id_expediteur'
            );

            $where = array();
            array_push($where, ' AND tblfactureinterneitems.facture_interne_id = "' . $facture_id . '"');

            $join = array(
                'LEFT JOIN tblfactures ON tblfactures.id = tblfactureinterneitems.facture_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblfactures.id_expediteur'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactureinterneitems';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblfactures.id', 'tblexpediteurs.nom as expediteur', 'tblfactureinterneitems.id as facture_factureinterne_id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                        $_data = $aRow[$aColumns[$i]];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = ucfirst($_data);
                    } else if ($aColumns[$i] == 'tblfactures.total_net') {
                        $_data = number_format($_data, 2, '.', ',');
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_facture_type($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblfactures.id_expediteur') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $_data) . '" target="_blank">' . $aRow['expediteur'] . '</a>';
                    }

                    $row[] = $_data;
                }

                $options = icon_btn('#', 'remove', 'btn-danger facture_remove', array('data-item-id' => $aRow['facture_factureinterne_id']));

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }

    /**
     * Imprimer PDF
     * @param int $id
     */
    public function pdf($id, $type = '')
    {
        if (!has_permission('factures_internes', '', 'download')) {
            access_denied('Facture Interne');
        }
        if (!$id) {
            redirect(admin_url('factures_internes'));
        }

        $facture_interne = $this->factures_internes_model->get($id);
        if (count($facture_interne->items) == 0) {
            set_alert('warning', _l('facture_interne_does_not_contain_any_facture'));
            redirect(admin_url('factures_internes'));
        } else {
            facture_interne_pdf($facture_interne, true, $type);
        }
    }

    /**
     * Imprimer Excel
     * @param int $id
     */
    public function excel($id)
    {
        if (!has_permission('factures_internes', '', 'download')) {
            access_denied('Facture Interne');
        }
        if (!$id) {
            redirect(admin_url('factures_internes'));
        }

        $facture_interne = $this->factures_internes_model->get($id);
        if (count($facture_interne->items) == 0) {
            set_alert('warning', _l('facture_interne_does_not_contain_any_facture'));
            redirect(admin_url('factures_internes'));
        } else {
            facture_interne_excel($facture_interne);
        }
    }
    /* Zip all factures facture interne */

    public function zip_factures_facture_interne($id)
    {
        if (!has_permission('factures_internes', '', 'download')) {
            access_denied('Facture Interne');
        }
        if (!$id) {
            redirect(admin_url('factures_internes'));
        }

        if (!has_permission('factures_internes', '', 'view') && !has_permission('factures_internes', '', 'view_own')) {
            access_denied('Zip Factures  "Facture Interne"');
        }

        $facture_interne = $this->factures_internes_model->get($id);
        if ($facture_interne) {
            if (count($facture_interne->items) == 0) {
                set_alert('warning', _l('facture_interne_zip_no_data_found', _l('facture_interne')));
                redirect(admin_url('factures_internes/facture/' . $id));
            }

            $zip_file_name = $facture_interne->nom;
            $this->load->helper('file');
            if (!is_really_writable(TEMP_FOLDER)) {
                show_error('/temp folder is not writable. You need to change the permissions to 777');
            }
            //If folder exists, delete the folder
            $dir = TEMP_FOLDER . $zip_file_name;
            if (is_dir($dir)) {
                delete_dir($dir);
            }
            mkdir($dir, 0777);

            $this->load->model('factures_model');
            $this->load->model('expediteurs_model');
            include(APPPATH . 'third_party/MPDF57/mpdf.php');
            foreach ($facture_interne->items as $item) {
                $invoice = $this->factures_model->get($item['factureid']);
                $expediteur = $this->expediteurs_model->get($invoice->id_expediteur);
                $invoice->nom_expediteur = $expediteur->nom;
                $invoice->frais = $expediteur->frais_retourne;
                $this->pdf_zip = facture_factureinterne_pdf($invoice);
                $file_name = $dir . '/' . $invoice->nom;
                $this->pdf_zip->Output($file_name . '.pdf', 'F');
            }

            $this->load->library('zip');
            // Read the facture interne
            $this->zip->read_dir($dir, false);
            // Delete the temp directory for the facture interne
            delete_dir($dir);
            //Download Zip
            $this->zip->download($zip_file_name . '.zip');
            $this->zip->clear_data();
        }
    }
    /* Record new facture interne payment view */

    public function record_facture_interne_payment_ajax($id)
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get();
        $data['invoice'] = $this->factures_internes_model->get($id);
        $data['payments'] = $this->payments_model->get_facture_interne_payments($id);
        $this->load->view('admin/factures_internes/record_payment_template', $data);
    }
    /* This is where facture interne payment record $_POST data is send */

    public function record_payment()
    {
        if (!has_permission('payments', '', 'create')) {
            access_denied('Record Payment');
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $do_not_redirect = $data['do_not_redirect'];
            $factureinterneid = $data['factureinterneid'];
            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($data, '');
            if ($id) {
                set_alert('success', _l('facture_interne_payment_recorded'));
            } else {
                set_alert('danger', _l('facture_interne_payment_record_failed'));
            }

            if (is_numeric($id) && $do_not_redirect == "on") {
                redirect(admin_url('factures_internes/index/' . $factureinterneid));
            } else {
                redirect(admin_url('payments/payment/' . $id));
            }
        }
    }
    /* Delete facture interne payment */

    public function delete_payment($id, $factureinterneid)
    {
        if (!has_permission('payments', '', 'delete')) {
            access_denied('payments');
        }

        if (!$id) {
            redirect(admin_url('payments'));
        }

        $this->load->model('payments_model');
        $response = $this->payments_model->delete($id);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }

        redirect(admin_url('factures_internes/index/' . $factureinterneid));
    }
}



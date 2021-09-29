<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('factures_model');

        if (get_permission_module('invoices') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all factures
     */
    public function index($id = false, $status = false, $type = false,$has_permission)
    {
    // $has_permission =has_fac_permission();
        if (!has_fac_permission()) {
            access_denied('Factures ');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblfactures.id',
                'tblfactures.nom',
                'tblexpediteurs.nom',
                'tblfactures.total_crbt',
                'tblfactures.total_frais',
                'tblfactures.total_net',
                'tblfactures.commentaire',
                'tblfactures.type',
                'tblfactures.status',
                'tblfactures.date_created',
                'tblstaff.firstname'
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactures';

            $join = array(
                'Left join tblexpediteurs ON tblexpediteurs.id = tblfactures.id_expediteur ',
                'Left join tblstaff ON tblstaff.staffid = tblfactures.id_utilisateur '
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblfactures.id_utilisateur = ' . get_staff_user_id());
            }
            if (is_numeric($status)) {
                array_push($where, 'AND tblfactures.status = ' . $status);
            }
            if (is_numeric($type)) {
                array_push($where, 'AND tblfactures.type = ' . $type);
            }

            //Filtre
            if ($this->input->post('f-clients') && is_numeric($this->input->post('f-clients'))) {
                array_push($where, ' AND tblfactures.id_expediteur = ' . $this->input->post('f-clients'));
            }
            if ($this->input->post('f-statut') && is_numeric($this->input->post('f-statut'))) {
                array_push($where, ' AND tblfactures.status = ' . $this->input->post('f-statut'));
            }
            if ($this->input->post('f-utilisateur') && is_numeric($this->input->post('f-utilisateur'))) {
                array_push($where, ' AND tblfactures.id_utilisateur = ' . $this->input->post('f-utilisateur'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblfactures.date_created LIKE "' . to_sql_date($this->input->post('f-date-created')) . '%"');
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

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.id as expediteur_id', 'tblfactures.commentaire', 'tblstaff.staffid', 'tblstaff.lastname', 'num_factureinterne', 'tblfactures.id_demande', '(SELECT count(id) FROM tblcolisfacture WHERE facture_id = tblfactures.id) as nbr_colis'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {

                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                        $_data = $aRow[$aColumns[$i]];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblfactures.nom') {
                        $_data = render_btn_copy('column-name-facture-' . $key, 'invoice_name') . '<a id="column-name-facture-' . $key . '" href="#" onclick="init_facture(' . $aRow['tblfactures.id'] . '); return false;">' . $aRow['tblfactures.nom'] . '</a>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['expediteur_id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblfactures.total_crbt' || $aColumns[$i] == 'tblfactures.total_frais' || $aColumns[$i] == 'tblfactures.total_net') {
                        $_data = '<p class="pright30" style="text-align: right;"><span class="label label-default inline-block">' . format_money($_data) . '</span></p>';
                    } else if ($aColumns[$i] == 'tblfactures.commentaire') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    } else if ($aColumns[$i] == 'tblfactures.type') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblfactures.status') {
                        $_data = format_facture_status($_data);
                    } else if ($aColumns[$i] == 'tblfactures.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'tblstaff.firstname') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/profile/' . $aRow['staffid']) . '">' . $_data . ' ' . $aRow['lastname'] . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                if (is_null($aRow['num_factureinterne'])) {
                    if (has_permission('invoices', '', 'edit')) {
                        $options .= icon_btn('admin/factures/facture/' . $aRow['tblfactures.id'] . '/' . $aRow['tblfactures.type'], 'pencil-square-o');
                    }
                } else {
                    $options .= '<a href="' . admin_url('factures_internes/facture/' . $aRow['num_factureinterne']) . '" class="btn btn-default btn-icon">FCT-INT-' . $aRow['num_factureinterne'] . '</a>';
                }
                if (has_permission('invoices', '', 'edit')) {
                    $options .= icon_btn('#', 'comments-o', 'btn-info', array('data-toggle' => 'modal', 'data-target' => '#commentaire_modal', 'data-id' => $aRow['tblfactures.id'], 'data-comment' => $aRow['commentaire']));
                }
                if (has_permission('invoices', '', 'download')) {
                    $options .= icon_btn('admin/factures/pdf/' . $aRow['tblfactures.id'], 'file-pdf-o', 'btn-danger');
                }
                if (!is_null($aRow['id_demande'])) {
                    $options .= '<a href="' . admin_url('demandes/preview/' . $aRow['id_demande']) . '" class="btn btn-primary btn-icon" target="_blank">DMD-' . $aRow['id_demande'] . '</a>';
                }
                if (has_permission('invoices', '', 'delete') && is_null($aRow['num_factureinterne'])) {
                    $options .= icon_btn('admin/factures/delete/' . $aRow['tblfactures.id'], 'remove', 'btn-danger btn-delete-confirm');
                }

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
        //check if is admin or owned by user then do next
        if (is_numeric($id)) {
            if (owns_data("tblfactures", $id, '', 'id_utilisateur') == 1 OR is_admin()->admin == 1) {
                $data['invoiceid'] = $id;
            }
        }

        //Get Clients
        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        //Get Statuses Colis
        $this->load->model('statuses_colis_model');
        $data['statuses'] = array(array('id' => 1, 'name' => _l('invoices_unpaid')), array('id' => 2, 'name' => _l('invoices_paid')));
        //Get Utilisateurs
        $this->load->model('staff_model');
        $data['staff'] = $this->staff_model->get('', 1, 'staffid != 1');
        //Types
        $data['types'] = array(array('id' => '2', 'name' => 'Livré'), array('id' => '3', 'name' => 'Retourné'));
        //Get types livraison
        $this->load->model('colis_model');
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        //Get Type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = _l('als_factures');
        $this->load->view('admin/factures/manage', $data);
    }

    /**
     * View facture
     */
    public function aprecu_facture($id = false)
    {
        $this->index($id);
    }

    /**
     * Facture livrer
     */
    public function livrer($type = false)
    {
      $has_permission = has_permission('invoices', '', 'view');
      if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
          access_denied('Invoices');
      }
        if ($type == 2) {
            $this->index(false, false, $type,$has_permission);
        } else {
            redirect(admin_url('factures'));
        }
    }

    /**
     * Facture Retourner
     */
    public function retourner($type = false)
    {
        $has_permission = has_permission('factures_ret', '', 'view');
        if (!has_permission('factures_ret', '', 'view') && !has_permission('factures_ret', '', 'view_own')) {
            access_denied('Factures Retourne');
        }
        if ($type == 3) {
            $this->index(false, false, $type,$has_permission);
        } else {
            redirect(admin_url('factures'));
        }
    }

    /**
     * Get clients batch
     */
    public function get_clients_batch()
    {
        $params = $this->input->post();
        $type = $params['type'];
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $typeMessage = 'warning';
        $message = '';
        $clients = array();
        if (is_numeric($type)) {
            if ($startDate && is_date(to_sql_date($startDate)) && $endDate && is_date(to_sql_date($endDate))) {
                $this->load->model('expediteurs_model');
                if ($type == 2) {
                    $clients = $this->expediteurs_model->get('', '', 'id IN (select id_expediteur from tblcolis where etat_id = 2 AND (status_id = 2 OR status_reel = 9) AND (date_livraison BETWEEN "' . to_sql_date($startDate) . '" AND "' . to_sql_date($endDate) . '") AND num_facture IS NULL AND num_etatcolislivrer IS NOT NULL)');
                } else if ($type == 3) {
                    $clients = $this->expediteurs_model->get('', '', 'id IN (select id_expediteur from tblcolis where ((status_id = 3 AND etat_id = 1) OR (status_reel = 9 AND etat_id = 3) OR (status_reel = 13 AND etat_id = 1)) AND (date_ramassage BETWEEN "' . to_sql_date($startDate) . '" AND "' . to_sql_date($endDate) . '") AND num_facture IS NULL)');
                }

                $typeMessage = 'success';
                $message = 'Vous avez une liste de ' . count($clients) . ' clients';
            } else {
                $message = 'Date invalide';
            }
        } else {
            $message = 'Type invalide';
        }

        echo json_encode(array('type' => $typeMessage, 'message' => $message, 'clients' => $clients));
    }

    /**
     * Factures batch
     */
    public function batch()
    {
        $params = $this->input->post();
        $listClients = array();
        if (isset($params['customers'])) {
            $listClients = $params['customers'];
        }

        $typeLivraison = $params['type_livraison'];
        $type = $params['type'];
        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $typeMessage = 'warning';
        $message = '';
        $messageSuccess = '';
        $messageErrors = '';
        $idsCustomersToRemove = array();

        if (!$this->input->is_ajax_request() || !has_permission('invoices', '', 'create')) {
            $message = _l('access_denied');
        } else {
            if (is_array($listClients) && count($listClients) > 0) {
                if (is_numeric($type)) {
                    if ($startDate && is_date(to_sql_date($startDate)) && $endDate && is_date(to_sql_date($endDate))) {
                        $this->load->model('expediteurs_model');
                        $this->load->model('colis_model');
                        $this->load->model('factures_model');
                        foreach ($listClients as $clientId) {
                            //Get expediteur
                            $expediteur = $this->expediteurs_model->get($clientId);
                            $clientName = '';
                            if ($expediteur) {
                                $clientName = $expediteur->nom;
                            }
                            //Data Invoice
                            $dataInvoice = array();
                            //Get colis by client
                            $whereListColis = 'id_expediteur = ' . $clientId;
                            if ($type == 2) {
                                $labelType = 'success';
                                if (!empty($typeLivraison)) {
                                    $whereListColis .= ' AND type_livraison = "' . $typeLivraison . '"';
                                }
                                $whereListColis .= " AND etat_id = 2 AND (status_id = 2 OR status_reel = 9) AND (date_livraison BETWEEN '" . to_sql_date($startDate) . "' AND '" . to_sql_date($endDate) . "')";
                            } else {
                                $labelType = 'danger';
                                $whereListColis .= " AND ((status_id = 3 AND etat_id = 1) OR (status_reel = 9 AND etat_id = 3) OR (status_reel = 13 AND etat_id = 1)) AND (date_ramassage BETWEEN '" . to_sql_date($startDate) . "' AND '" . to_sql_date($endDate) . "')";
                            }
                            $listColis = $this->colis_model->get('', $whereListColis);
                            if (is_array($listColis) && count($listColis) > 0) {
                                $nbrBoucle = 1;
                                $countListColis = count($listColis);
                                $limitTotalColisAddedToInvoice = get_option('limit_total_colis_added_to_invoice');
                                if ($countListColis > $limitTotalColisAddedToInvoice) {
                                    $nbrBoucle = $countListColis / $limitTotalColisAddedToInvoice;
                                    $nbrBoucle = ceil($nbrBoucle);
                                }
                                for ($i = 1; $i <= $nbrBoucle; $i++) {
                                    $dataInvoice['checked_products'] = array();
                                    foreach ($listColis as $key => $colis) {
                                        if ($key < $limitTotalColisAddedToInvoice && is_numeric($colis['id'])) {
                                            array_push($dataInvoice['checked_products'], $colis['id']);
                                            unset($listColis[$key]);
                                        }
                                    }
                                    $listColis = array_values($listColis);
                                    //Get invoice
                                    $whereListInvoices = 'status = 1 AND date_created LIKE "' . date('Y-m-d') . '%" AND id_expediteur = ' . $clientId;
                                    if ($type == 2) {
                                        $whereListInvoices .= ' AND type = 2 AND num_factureinterne IS NULL ';
                                    } else {
                                        $whereListInvoices .= ' AND type = 3 ';
                                    }
                                    $invoice = $this->factures_model->get('', $whereListInvoices, 'tblfactures.date_created DESC', 1);
                                    $resteTotalColis = 0;
                                    $checkResteTotalColis = false;
                                    if ($invoice && count($invoice) > 0) {
                                        $resteTotalColis = $limitTotalColisAddedToInvoice - total_rows('tblcolisfacture', array('facture_id' => $invoice[0]['id']));
                                        if ($resteTotalColis >= count($dataInvoice['checked_products'])) {
                                            $checkResteTotalColis = true;
                                        }
                                    }
                                    $invoiceExist = false;
                                    if ($invoice && count($invoice) > 0 && $checkResteTotalColis == true) {
                                        $invoiceExist = true;
                                        $invoiceId = $invoice[0]['id'];
                                        $remiseType = '';
                                        if (!is_null($invoice[0]['remise_type'])) {
                                            $remiseType = $invoice[0]['remise_type'];
                                        }
                                        $dataInvoice['remise_type'] = $remiseType;
                                        $remise = 0;
                                        if (!is_null($invoice[0]['remise'])) {
                                            $remise = $invoice[0]['remise'];
                                        }
                                        $dataInvoice['remise'] = $remise;
                                        $success = $this->factures_model->update($dataInvoice, $invoiceId);
                                        if ($success == true) {
                                            $id = $invoiceId;
                                        }
                                    } else {
                                        $dataInvoice['id_expediteur'] = $clientId;
                                        $dataInvoice['type'] = $type;
                                        $dataInvoice['sent'] = 0;
                                        $dataInvoice['remise_type'] = '';
                                        $dataInvoice['remise'] = 0;
                                        $dataInvoice['total_line'] = 0;
                                        $id = $this->factures_model->add($dataInvoice);
                                    }

                                    if (is_numeric($id)) {
                                        //Add id customer to remove to list customers selected
                                        array_push($idsCustomersToRemove, $clientId);
                                        if ($invoiceExist == true) {
                                            $messageSuccess .= '<p class="no-margin">- ' . _l('customer_invoice_updated_successfuly', '<b>' . $expediteur->nom . '</b>') . '<a href="' . admin_url('factures/facture/' . $id . '/' . $type) . '" class="curp" target="_blank"><label class="label label-' . $labelType . ' lineh30 mright5">FCT-' . date('dmY') . '-' . $id . ' </label></a>' . '</p>';
                                        } else {
                                            $messageSuccess .= '<p class="no-margin">- ' . _l('customer_invoice_added_successfuly', '<b>' . $expediteur->nom . '</b>') . '<a href="' . admin_url('factures/facture/' . $id . '/' . $type) . '" class="curp" target="_blank"><label class="label label-' . $labelType . ' lineh30 mright5">FCT-' . date('dmY') . '-' . $id . ' </label></a>' . '</p>';
                                        }
                                    } else {
                                        if ($invoiceExist == true) {
                                            $messageErrors .= '<p class="no-margin">- ' . _l('customer_invoice_problem_updating', '<b class="cF00">' . $clientName . '</b>') . '</p>';
                                        } else {
                                            $messageErrors .= '<p class="no-margin">- ' . _l('customer_invoice_problem_adding', '<b class="cF00">' . $clientName . '</b>') . '</p>';
                                        }
                                    }
                                }
                            } else {
                                $messageErrors .= '<p class="no-margin">- ' . _l('no_colis_for_this_client_between_this_period', '<b class="cF00">' . $clientName . '</b>') . '</p>';
                            }
                        }
                    } else {
                        $message = _l('invalid_date');
                    }
                } else {
                    $message = _l('choose_a_type');
                }
            } else {
                $message = _l('add_at_least_one_customer');
            }
        }

        echo json_encode(array('type' => $typeMessage, 'message' => $message, 'messageSuccess' => $messageSuccess, 'messageErrors' => $messageErrors, 'idsCustomers' => $idsCustomersToRemove));
    }

    /**
     * Edit or add new facture
     */
    public function facture($id = false, $type = false)
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('Invoices');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if (!is_numeric($id)) {
                if (!has_permission('invoices', '', 'create')) {
                    access_denied('Invoices');
                }
                $id = $this->factures_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('facture')));
                    if($type == 3)
                    {
                        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
                            access_denied('Invoices');
                        }
                        redirect(admin_url('factures/facture/' . $id . '/' . $type));
                    }

                } else {
                    set_alert('warning', _l('cant_added_invoice'));
                }
            } else {
                if (!has_permission('invoices', '', 'edit')) {
                    access_denied('Invoices');
                }
                $success = $this->factures_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('facture')));
                }
                redirect(admin_url('factures/facture/' . $id . '/' . $type));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('facture_lowercase'));
            $data['class1'] = 'col-md-6';
        } else {
            $facture = $this->factures_model->get($id);
            if (!$facture || (!has_permission('invoices', '', 'view') && $facture->id_utilisateur != get_staff_user_id())) {
                set_alert('warning', _l('not_found', _l('facture')));
                redirect(admin_url('factures'));
            }

            $data['facture'] = $facture;
            $title = $facture->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        $data['types'] = array(array('id' => '2', 'name' => 'Livré'), array('id' => '3', 'name' => 'Retourné'));

        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = $title;
        $this->load->view('admin/factures/facture', $data);
    }

    /**
     * Add comment
     */
    public function commentaire($id = '')
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if ($data['id'] != '') {
                if (!has_permission('invoices', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $success = $this->factures_model->update_comment($data);
                    $message = '';
                    if ($success == true) {
                        $message = _l('updated_successfuly', _l('invoice_comment'));
                    }
                    echo json_encode(array('success' => $success, 'message' => $message));
                }
            }
        }
    }

    /**
     * Add additionnal line in facture
     */
    public function add_additionnal_line($id = '')
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();

            $success = false;
            $message = _l('problem_adding', _l('additionnal_line'));
            if ($data['id'] != '') {
                if (!has_permission('invoices', '', 'edit')) {
                    $success = 'access_denied';
                    $message = _l('access_denied');
                    echo json_encode(array('success' => $success, 'message' => $message));
                } else {
                    $success = $this->factures_model->add_additionnal_line($data);
                    if ($success == true) {
                        $message = _l('added_successfuly', _l('additionnal_line'));
                    }
                }
            }

            echo json_encode(array('success' => $success, 'message' => $message));
        }
    }

    /**
     * Get infos facture
     */
    public function get_info_factures($id)
    {
        echo json_encode($this->factures_model->get($id));
    }

    /**
     * Get all invoice data used when user click on invoice number in a datatable left side
     */
    public function get_facture_data_ajax($id)
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }
        if (!$id) {
            die('Aucune facture trouvée');
        }

        $invoice = $this->factures_model->get($id);

        if (!$invoice || (!has_permission('invoices', '', 'view') && $invoice->id_utilisateur != get_staff_user_id())) {
            echo _l('invoice_not_found');
            die;
        }
        $invoice->date_created = date(get_current_date_format(), strtotime($invoice->date_created));

        $data['invoice'] = $invoice;
        $this->load->view('admin/factures/facture_preview_template', $data);
    }

    /**
     * Send invoice to email
     */
    public function send_to_email($id, $email)
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('invoices');
        }

        $success = $this->factures_model->sent_invoice_to_client($id, $email);
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }

        redirect(admin_url('factures/aprecu_facture/' . $id));
    }

    /**
     * Print PDF
     */
    public function pdf($id)
    {
        if (!has_permission('invoices', '', 'download')) {
            access_denied('Invoices');
        }
        if (!$id) {
            redirect(admin_url('factures'));
        }

        $invoice = $this->factures_model->get($id);
        $this->load->model('expediteurs_model');
        $expediteur = $this->expediteurs_model->get($invoice->id_expediteur);
        if (count($invoice->items) == 0) {
            set_alert('warning', _l('invoice_does_not_contain_any_colis'));
            redirect(admin_url('factures'));
        } else {
            $invoice->nom_expediteur = $expediteur->nom;
            $invoice->frais = $expediteur->frais_retourne;
            facture_pdf($invoice);
        }
    }

    /**
     * Delete facture
     */
    public function delete($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('Invoices');
        }

        $response = $this->factures_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('facture')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('facture_lowercase')));
        }

        redirect(admin_url('factures'));
    }

    /**
     * Init colis facture
     */
    public function init_colis_facture($expediteur_id, $status_id)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'nom_complet',
                'crbt',
                'date_ramassage',
                'date_livraison',
                'etat_id',
                'status_reel',
                'frais',
            );

            $where = array();
            array_push($where, ' AND id_expediteur = ' . $expediteur_id);
            if ($status_id == 2) {
                array_push($where, ' AND ((status_id = ' . $status_id . ' OR status_reel = 9) AND etat_id = 2)');
            } elseif ($status_id == 3) {
                array_push($where, ' AND ((status_id = ' . $status_id . ' AND etat_id = 1) OR (status_reel = 10 AND etat_id = 1) OR (status_reel = 13 AND etat_id = 1) OR (status_reel = 13 AND etat_id = 3))');
            }

            $join = array();

            $i = 0;
            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id', 'id_expediteur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.id') {
                        $_data = '<div class="form-group"><div class="checkbox checkbox-primary" data-toggle="tooltip" title=""><input id="product_checked_' . $_data . '" class="product_checked" type="checkbox" value="' . $_data . '"><label for=""></label></div></div>';
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['id'] . '" data-expediteurid="' . $aRow['id_expediteur'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'nom_complet') {
                        $_data = ucfirst($_data);
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_livraison') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt') {
                        $_data = number_format($_data, 2, ',', ' ');
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
     * Init historique colis facture
     */
    public function init_historique_colis_facture($facture_id, $status_id)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolisfacture.id as colisfacture_id',   'num_commande',
                'code_barre',
                'nom_complet',
                'crbt',
                'date_ramassage',
                'date_livraison',
                'etat_id',
                'status_reel',
                'frais',
            );

            $where = array();
            array_push($where, ' AND tblcolisfacture.facture_id = ' . $facture_id);
            if (is_numeric($status_id) && ($status_id = 2 || $status_id = 3)) {
                array_push($where, ' AND tblcolis.etat_id = 3');
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblcolisfacture.colis_id',
                'LEFT JOIN tbletatcolis ON tbletatcolis.id = tblcolis.etat_id',
                'LEFT JOIN tblstatuscolis ON tblstatuscolis.id = tblcolis.status_reel',
            );

            // ID entreprise 
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolisfacture';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id', 'id_expediteur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], ' as ') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], ' as ')];
                        $aColumns[$i] = strafter($aColumns[$i], ' as ');
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'colisfacture_id') {
                        $_data = icon_btn('admin/factures/delete_colis_facture/' . $_data, 'remove', 'btn-danger');
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<a id="column-historique-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['id'] . '" data-expediteurid="' . $aRow['id_expediteur'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'nom_complet') {
                        $_data = ucfirst($_data);
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_livraison') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt') {
                        $_data = number_format($_data, 2, ',', ' ');
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
     * Delete colis to list colis facture
     */
    public function delete_colis_facture($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('Invoices');
        }
        $response = $this->factures_model->delete_colis_facture($id);
        if (is_numeric($response)) {
            $id = $response;
            set_alert('success', _l('deleted_colis_facture'));
        } else {
            set_alert('warning', _l('problem_deleting', _l('colis_facture_lowercase')));
        }

        redirect(admin_url('factures/facture/' . $id));
    }

    /**
     * Export
     */
    public function export()
    {
        if (!has_permission('invoices', '', 'export')) {
            access_denied('Invoices');
        }

        //Get all factures
        $factures = $this->factures_model->get_facture_export();

        $columnHeader = "Nom Facture" . "\t" . "Date Creation" . "\t" . "Client" . "\t" . "Type" . "\t" . "Nombre Colis" . "\t" . "Total Prix Colis" . "\t" . "Total Frais Colis" . "\t" . "Total NET" . "\t";

        $setData = '';
        foreach ($factures as $key => $f) {
            $rowData = '';
            foreach ($f as $key => $value) {
                $value = '"' . $value . '"' . "\t";
                $rowData .= $value;
            }
            $setData .= trim($rowData) . "\n";
        }
        //header('Content-Type: application/msexcel; charset=UTF-8');
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=Factures-" . date("d-m-Y") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo ucwords($columnHeader) . "\n" . $setData . "\n";
    }



    public function init_colis_factur($expediteur_id, $status_id)
    {

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'nom_complet',
                'crbt',
                'date_ramassage',
                'date_livraison',
                'etat_id',
                'status_reel',
                'frais',
            );

            $where = array();
            //  array_push($where, ' AND id_expediteur = ' . $expediteur_id)
            //if ($status_id == 2) {
            //array_push($where, ' AND ((status_id = ' . $status_id . ' OR status_reel = 9) AND etat_id = 2)');
            //  }

            $join = array();

            $i = 0;
            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.id', 'id_expediteur'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if ($aColumns[$i] == 'tblcolis.id') {
                        $_data = '<div class="form-group"><div class="checkbox checkbox-primary" data-toggle="tooltip" title=""><input id="product_checked_' . $_data . '" class="product_checked" type="checkbox" value="' . $_data . '"><label for=""></label></div></div>';
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['id'] . '" data-expediteurid="' . $aRow['id_expediteur'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'nom_complet') {
                        $_data = ucfirst($_data);
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_livraison') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt') {
                        $_data = number_format($_data, 2, ',', ' ');
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }



    }

}


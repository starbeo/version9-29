
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commission_livreur extends Admin_controller
{

    private $not_importable_colis_fields = array('id', 'etat_id', 'status_id', 'date_creation', 'commentaire', 'id_expediteur', 'colis_id', 'id_entreprise');

    function __construct()
    {

        parent::__construct();
        $this->load->model('colis_model');
        $this->load->model('demandes_model');
        $this->load->model('factures_etl_model');

        if (get_permission_module('colis') == 0) {
            redirect(admin_url('home'));
        }
    }


    public function index($id = false, $status = false, $type = false)
    {
        $has_permission = has_permission('invoices', '', 'view');
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            access_denied('Invoices');
        }
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblfactures_etl.id_entreprise',
                'tblfactures_etl.id',
                'tblfactures_etl.nom',
                'CONCAT(a.firstname, " ", a.lastname) as fullname_livreur',
                'tblfactures_etl.totalnbr_livre',
                'tblfactures_etl.total_frais',
                'tblfactures_etl.totalnbr_refuse',
                'tblfactures_etl.total_refuse',
                'tblfactures_etl.statu',
                'tblfactures_etl.date_created',
                'CONCAT(c.firstname, " ", c.lastname) as fullname_staff',
                'tblfactures_etl.total_manque',
                'tblfactures_etl.id_livreur',
            );

            $sIndexColumn = "id";
            $sTable = 'tblfactures_etl';

            $join = array(
                'LEFT JOIN tblstaff as a ON a.staffid = tblfactures_etl.id_livreur',
                'LEFT JOIN tblstaff as c ON c.staffid = tblfactures_etl.id_utilisateur',
            );

            $where = array();


            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblfactures_etl.id_livreur', 'tblfactures_etl.id_utilisateur',));
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
                    if ($aColumns[$i] == 'tblfactures_etl.nom') {
              
 $_data = render_btn_copy('column-name-facture-' . $key, 'invoice_name') . '<a id="column-name-facture-' . $key . '" href="commission_livreur/facture/1/'. $aRow['tblfactures_etl.id'].'" >' . $aRow['tblfactures_etl.nom'] . '</a>';

                    }

                    if ($aColumns[$i] == 'tblfactures_etl.id') {
                        $_data = '<div class="checkbox checkbox-primary"><input id="checkbox-etat-' . $_data . '" value="' . $_data . '" name="ids[]" class="checkbox-etat" type="checkbox" /><label></label></div>';
                    }
                    else if ($aColumns[$i] == 'fullname_livreur') {
                        $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $aRow['tblfactures_etl.id_livreur']) . '" target="_blank">' . $_data . '</a>';

                    }
                    else if ($aColumns[$i] == 'tblfactures_etl.statu') {
                        $_data = format_facture_status($_data);
                    }
                    else if ($aColumns[$i] == 'tblfactures_etl.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    }
                    else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $aRow['id_utilisateur']) . '">' . $_data . '</a>';

                    }else if ($aColumns[$i] =='tblfactures_etl.id_livreur')
                    {
                        if (has_permission('invoices', '', 'download')) {
                            $_data = icon_btn('admin/commission_livreur/pdf/' . $aRow['tblfactures_etl.id'], 'file-pdf-o', 'btn-danger');
                        }
                        if (has_permission('invoices', '', 'delete') ) {
                            $_data .= icon_btn('admin/commission_livreur/delete/' . $aRow['tblfactures_etl.id'], 'remove', 'btn-danger btn-delete-confirm');
                        }
                        if (has_permission('invoices', '', 'edit')) {
                            $_data .= icon_btn('admin/commission_livreur/facture/' . $aRow['tblfactures_etl.id'] . '/2' , 'pencil-square-o');
                        }

                    }
                    else if ($aColumns[$i] =='tblfactures_etl.totalnbr_livre' || $aColumns[$i] =='tblfactures_etl.total_manque' ||$aColumns[$i] =='tblfactures_etl.total_frais' )
                    {
                        $classLabel = 'default';
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $_data . '</span></p>';

                    }
                    else if ($aColumns[$i] =='tblfactures_etl.totalnbr_refuse' || $aColumns[$i] =='tblfactures_etl.total_refuse')
                    {
                        $classLabel = 'danger';
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $_data . '</span></p>';

                    }


                    $row[] = $_data;
                }





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
        $this->load->view('admin/commission_livreur/manage', $data);
    }

    public function aprecu_facture($id = false)
    {
        $this->index($id);
    }

    public function historiques_coli_info()
    {
        $has_permission = has_permission('bon_livraison', '', 'view');
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array();
            array_push($aColumns, 'tblcolis.id', 'tblcolis.code_barre', 'tblcolis.num_commande', 'tblexpediteurs.nom','tblcolis.telephone','DATE_FORMAT(date_ramassage, "%d/%m/%Y")', 'status_reel', 'etat_id','tblcolis.ville', 'crbt','tblvilles.name');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $join = array(
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur'
            );
            $where = array();

            //Filtre
            if ($this->input->post('f-coli-id') && is_numeric($this->input->post('f-coli-id'))) {
                array_push($where, ' AND tblcolis.id = ' . $this->input->post('f-coli-id'));
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolis.anc_crbt'), '', '', '', false);
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
                    if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    }
                    else if ($aColumns[$i] == 'tblcolis.ville') {
                        $_data = $aRow['tblvilles.name'];
                    }

                    $row[] = $_data;
                }
                $options = '';

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }


    }

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
                $id = $this->factures_etl_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('facture')));
                    redirect(admin_url('commission_livreur/facture/' . $id . '/' . 2));
                } else {
                    set_alert('warning', _l('cant_added_invoice'));
                }
            } else {
                if (!has_permission('invoices', '', 'edit')) {
                    access_denied('Invoices');
                }
                $success = $this->factures_etl_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('facture')));
                }
                redirect(admin_url('commission_livreur/facture/' . $id . '/' . $type));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('facture_lowercase'));
            $data['class1'] = 'col-md-6';
        } else {
            $facture = $this->factures_etl_model->get($id);
            if (!$facture || (!has_permission('invoices', '', 'view') && $facture->id_utilisateur != get_staff_user_id())) {
                set_alert('warning', _l('not_found', _l('facture')));
                redirect(admin_url('factures'));
            }

            $data['facture'] = $facture;
            $title = $facture->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        $this->load->model('staff_model');
        $data['expediteurs'] = $this->staff_model->get_livreurs_fu();
        $data['types'] = array(array('id' => '2', 'name' => 'Livré'), array('id' => '3', 'name' => 'Retourné'));

        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = $title;
        $this->load->view('admin/commission_livreur/facture', $data);
    }

    public function init_colis_facture($expediteur_id, $status_id)
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tbletatcolislivre.id',
                'nom',
                'total',
                'commision',
                'manque',

                'date_created',
                'id_livreur',
                'justif',
                'etat'
            );


            $join = array(
                'LEFT JOIN tblstaff as a ON a.staffid = tbletatcolislivre.id_livreur',
                'LEFT JOIN tblstaff as c ON c.staffid = tbletatcolislivre.user_point_relais'
            );
            $where = array();
         //   array_push($where, ' AND id_livreur = ' . $expediteur_id.' AND facture_etl = 0 AND etat = 1');
            //if ($status_id == 2) {
            //array_push($where, ' AND ((status_id = ' . $status_id . ' OR status_reel = 9) AND etat_id = 2)');
            //  }
            array_push($where, ' AND id_livreur = ' . $expediteur_id.' AND tbletatcolislivre.facture_etl =0    AND tbletatcolislivre.date_created >="' . to_sql_date('01/02/2021') . '"');

            array_push($where, ' AND (SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id ) !=0 ');

            //Additional columns
            $additionalColumns = array(
                '(SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis',
                'tbletatcolislivre.id_utilisateur',
                '(SELECT COUNT(id) FROM tbllivreurversements WHERE etat_colis_livre_id = tbletatcolislivre.id) as nbr_versements',
                'tbletatcolislivre.type_livraison',
                'tbletatcolislivre.user_point_relais',
                'CONCAT(c.firstname, " ", c.lastname) as fullname_poit_relais',
            );
            $i = 0;
            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tbletatcolislivre';
            $result = data_tables_init12($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where,$additionalColumns);
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
                    if ($aColumns[$i] == 'tbletatcolislivre.id') {

                        $_data = '<div class="form-group"><div class="checkbox checkbox-primary" data-toggle="tooltip" title=""><input id="product_checked_' . $_data . '" class="product_checked" type="checkbox" value="' . $_data . '"><label for=""></label></div></div>';

                    }else if ($aColumns[$i] == 'nom') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'nom') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id="' . $aRow['tbletatcolislivre.id'] . '" data-expediteurid="' . $aRow['id_livreur'] . '">' . $_data . '</a>';
                    }
                    else if ($aColumns[$i] == 'fullname_livreur') {
                        if ($aRow['type_livraison'] == 'a_domicile') {
                            $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $aRow['tbletatcolislivre.id_livreur']) . '" target="_blank">' . $_data . '</a>';
                        } else {
                            $_data = render_icon_university() . '<a href="' . admin_url('staff/member/' . $aRow['user_point_relais']) . '" target="_blank">' . $aRow['fullname_poit_relais'] . '</a>';
                        }
                    }
                    else if ($aColumns[$i] == 'id_livreur') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    }
                    else if ($aColumns[$i] == 'tbletatcolislivre.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    }

                    else if ($aColumns[$i] == 'justif') {
                        $dt  =  $aRow['tbletatcolislivre.id'];
                        $this->load->model('etat_colis_livrer_model');
                        $dor   = $this->etat_colis_livrer_model->getcolisrefuse($dt,2);
                        $_data =$dor;
                    }
                    else if ($aColumns[$i] == 'etat') {
                        $dt  =  $aRow['tbletatcolislivre.id'];
                        $this->load->model('etat_colis_livrer_model');
                        $dor   = $this->etat_colis_livrer_model->getcolisrefuse($dt,9);
                        $_data =$dor;
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }
    public function get_facture_data_ajax($id)
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('invoices', '', 'view_own')) {
            echo _l('access_denied');
            die;
        }
        if (!$id) {
            die('Aucune facture trouvée');
        }

        $invoice = $this->factures_etl_model->get($id);

        if (!$invoice || (!has_permission('invoices', '', 'view') && $invoice->id_utilisateur != get_staff_user_id())) {
            echo _l('invoice_not_found');
            die;
        }
        $invoice->date_created = date(get_current_date_format(), strtotime($invoice->date_created));

        $data['invoice'] = $invoice;
        $this->load->view('admin/factures/facture_preview_template', $data);
    }
    public function init_historique_colis_facture($facture_id, $status_id)
    {
        if ($this->input->is_ajax_request()) {
              $aColumns = array(
                'tbletatfactures.id',
                'tbletatcolislivre.nom',
                'tbletatcolislivre.total',
                'tbletatcolislivre.date_created',
                'tbletatcolislivre.id_livreur',
                'tbletatcolislivre.justif',
                'tbletatcolislivre.commision',
                'tbletatcolislivre.etat',
                                'tbletatcolislivre.refuse_commision'

            );
            $where = array();
            array_push($where, ' AND tbletatfactures.etatfacture_id = ' . $facture_id);

            //    if (is_numeric($status_id) && ($status_id = 2 || $status_id = 3)) {
            //     array_push($where, ' AND tblcolis.etat_id = 3');
            //  }

            $join = array(
                'LEFT JOIN tbletatcolislivre ON tbletatcolislivre.id = tbletatfactures.etat_id',

            );

            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tbletatfactures';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tbletatcolislivre.id','(SELECT COUNT(id) FROM tbletatcolislivreitems WHERE etat_id = tbletatcolislivre.id) as nbr_colis',));
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
                    if ($aColumns[$i] == 'tbletatfactures.id') {
                        $_data = icon_btn('admin/commission_livreur/delete_colis_facture/' . $_data, 'remove', 'btn-danger');
                    }
                    else if ($aColumns[$i] == 'tbletatcolislivre.justif') {
                        $dt  =  $aRow['id'];
                        $this->load->model('etat_colis_livrer_model');
                        $dor   =  $this->etat_colis_livrer_model->getcolisrefuse($dt,2);
                        $_data =$dor;
                    }
                    else if ($aColumns[$i] == 'tbletatcolislivre.etat') {
                        $dt  =  $aRow['id'];
                        $this->load->model('etat_colis_livrer_model');
                        $dor   =  $this->etat_colis_livrer_model->getcolisrefuse($dt,9);
                        $_data =$dor;
                    }
        else if ($aColumns[$i] == 'tbletatcolislivre.id_livreur') {
                        if ($aRow['nbr_colis'] == 0) {
                            $classLabel = 'danger';
                        } else {
                            $classLabel = 'default';
                        }
                        $_data = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $aRow['nbr_colis'] . '</span></p>';
                    }





                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }
    }
    public function delete_colis_facture($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('Invoices');
        }
        $response = $this->factures_etl_model->delete_colis_facture($id);
        if (is_numeric($response)) {
            $id = $response;
            set_alert('success', _l('deleted_colis_facture'));
        } else {
            set_alert('warning', _l('problem_deleting', _l('colis_facture_lowercase')));
        }

        redirect(admin_url('commission_livreur/facture/' . $id));
    }

    public function pdf10($id)
    {
        if (!has_permission('invoices', '', 'download')) {
            access_denied('Invoices');
        }
        if (!$id) {
            redirect(admin_url('commission_livreur'));
        }

        $invoice = $this->factures_etl_model->get_for_pdf($id);
         $this->load->model('etat_colis_livrer_model');
        $invoice->items = $this->etat_colis_livrer_model->getbyetlid($id);
        $this->load->model('staff_model');
        $staff = $this->staff_model->get($invoice->id_livreur);
        if (count($invoice->items) == 0) {
            set_alert('warning', _l('invoice_does_not_contain_any_colis'));
            redirect(admin_url('commission_livreur'));
        } else {
            $invoice->nom_expediteur = $staff->firstname ." ". $staff->lastname;
            $invoice->frais = "";
            facture_cl_pdf($invoice);
        }
    }

    public function pdf3($id)
    {
        if (!has_permission('invoices', '', 'download')) {
            access_denied('Invoices');
        }
        if (!$id) {
            redirect(admin_url('commission_livreur'));
        }

                        $this->load->model('etat_colis_livrer_model');
        $invoice = $this->etat_colis_livrer_model->getbyetlid($id);
        $this->load->model('staff_model');
        $staff = $this->staff_model->get($invoice->id_livreur);
        if (count($invoice->items) == 0) {
            set_alert('warning', _l('invoice_does_not_contain_any_colis'));
            redirect(admin_url('commission_livreur'));
        } else {
            $invoice->nom_expediteur = $staff->firstname ."". $staff->lastname;
            $invoice->frais = "";
            facture_cl_pdf($invoice);
        }
    }

    public function pdf($id)
    {
        if (!has_permission('invoices', '', 'download')) {
            access_denied('Invoices');
        }
        if (!$id) {
            redirect(admin_url('commission_livreur'));
        }

        $invoice = $this->factures_etl_model->get_for_pdf($id);
        $this->load->model('staff_model');
        $staff = $this->staff_model->get($invoice->id_livreur);
        if (count($invoice->items) == 0) {
            set_alert('warning', _l('invoice_does_not_contain_any_colis'));
            redirect(admin_url('commission_livreur'));
        } else {
            $invoice->nom_expediteur = $staff->firstname ."". $staff->lastname;
            $invoice->frais = "";
            facture_cl_pdf($invoice);
        }
    }
    public function delete($id)
    {
        if (!has_permission('invoices', '', 'delete')) {
            access_denied('Invoices');
        }

        $response = $this->factures_etl_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('facture')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('facture_lowercase')));
        }

        redirect(admin_url('commission_livreur'));
    }
    public function valider()
    {
        $this->index(2);
    }

    public function change_status($etat = '')
    {
        $ids='';
        $success = false;
        $type = 'warning';
        $message = _l('problem_updating', _l('state'));
        if ($this->input->post()) {
            if (!has_permission('etat_colis_livrer', '', 'edit')) {
                $type = 'danger';
                $message = _l('access_denied');
            } else {
              //  $ids =
             //   $ids =  substr($this->input->post('data'), 1, -1);
                $ids = json_decode($this->input->post('data1'));

                $doo = $this->input->post('data1');
                foreach ($ids as $id) {
                    if (is_numeric($id)) {
                        $result = $this->factures_etl_model->change_etat($id, $etat);
                        if ($result) {
                            $success = true;
                            $type = 'success';
                            $message = _l('updated_successfuly', _l('state'));
                        }
                    }
                }
            }

        }
  //      $ids = json_encode($ids);
        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));

    }

}


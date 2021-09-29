<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis_en_attente extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_en_attente_model');

        if (get_permission_module('colis_en_attente') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all colis en attente
     */
    public function index($barcode = '')
    {
        if (!has_permission('colis_en_attente', '', 'view')) {
            access_denied('Colis en attente');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblcolisenattente.id', 'tblcolisenattente.code_barre', 'tblcolisenattente.num_commande', 'tblexpediteurs.nom');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tblcolisenattente.type_livraison');
            }
            array_push($aColumns, 'tblcolisenattente.telephone', 'tblcolisenattente.date_creation', 'tblcolisenattente.status_id', 'tblvilles.name', 'tblcolisenattente.crbt');

            $sIndexColumn = "id";
            $sTable = 'tblcolisenattente';

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolisenattente.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville'
            );

            $where = array();
            if (!empty($barcode)) {
                array_push($where, 'AND tblcolisenattente.code_barre = "' . $barcode . '"');
            } else {
                array_push($where, 'AND tblcolisenattente.colis_id IS NULL');
            }

            if ($this->input->post('custom_view')) {
                $view = $this->input->post('custom_view');
                $_where = '';
                $where = array();
                if (!empty($view)) {
                    if ($view == 'converted') {
                        $_where = 'AND tblcolisenattente.colis_id  IS NOT NULL';
                    } else if ($view == 'not_converted') {
                        $_where = 'AND tblcolisenattente.colis_id IS NULL';
                    } else if ($view == 'all') {
                        $where = array();
                    }
                }

                if ($_where != '') {
                    array_push($where, $_where);
                }
            }

            //Get
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'day':
                        array_push($where, ' AND tblcolisenattente.date_creation = "' . date('Y-m-d') . '"');
                        break;
                    case 'week':
                        array_push($where, ' AND WEEK(tblcolisenattente.date_creation, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND tblcolisenattente.date_creation > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolisenattente.id_expediteur', 'tblcolisenattente.colis_id'));
            $output = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $key => $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'tblcolisenattente.code_barre') {
                        if (is_null($aRow['colis_id'])) {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<a id="column-barcode-' . $key . '" href="#" data-toggle="modal" data-target="#colis" data-id=' . $aRow['tblcolisenattente.id'] . ' data-expediteurid=' . $aRow['id_expediteur'] . '>' . $_data . '</a>';
                        } else {
                            $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                        }
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['id_expediteur']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblcolisenattente.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.status_id') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.date_creation') {
                        if ($_data != NULL) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolisenattente.crbt') {
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($_data) . '</p>';
                    }

                    $row[] = $_data;
                }

                $options = '';
                if (is_null($aRow['colis_id'])) {
                    $options .= icon_btn('#', 'reply', 'btn-info', array('data-toggle' => 'modal', 'data-target' => '#colis', 'data-id' => $aRow['tblcolisenattente.id'], 'data-expediteurid' => $aRow['id_expediteur'], 'title' => 'Convertir en colis'));
                } else {
                    $href = 'colis';
                    $options .= '<a href="' . admin_url($href) . '" class="btn btn-success btn-icon">' . $aRow['colis_id'] . '</a>';
                }
                $options .= icon_btn('admin/colis_en_attente/delete/' . $aRow['tblcolisenattente.id'], 'remove', 'btn-danger btn-delete-confirm', array('title' => 'Supprimer colis en attente'));

                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get clients
        $this->load->model('expediteurs_model');
        $data['expediteurs'] = $this->expediteurs_model->get();
        //Get quartiers
        $this->load->model('quartiers_model');
        $data['quartiers'] = $this->quartiers_model->get();
        //Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get();
        //Get types livraison
        $this->load->model('colis_model');
        $data['types_livraison'] = $this->colis_model->get_types_livraison();
        // Check if option show point relai is actived
        $data['points_relais'] = array();
        if (get_permission_module('points_relais') == 1) {
            // Get points relais
            $this->load->model('points_relais_model');
            $data['points_relais'] = $this->points_relais_model->get('', 1, array(), 'tblpointsrelais.id, CONCAT(CONCAT(tblvilles.name, " - ", tblpointsrelais.nom), " - ", tblpointsrelais.adresse) as nom');
        }

        $data['title'] = _l('als_colis_en_attente');
        $this->load->view('admin/colis_enattente/manage', $data);
    }

    /**
     * Get info colis en attente
     */
    function get_info_colis_en_attente($id)
    {
        echo json_encode($this->colis_en_attente_model->get_info_colis($id));
    }

    /**
     * Delete colis en attente from database
     */
    public function delete($id)
    {
        if (!has_permission('colis_en_attente', '', 'delete')) {
            access_denied('Colis en attente');
        }

        $response = $this->colis_en_attente_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('colis_en_attente')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('colis_en_attente_lowercase')));
        }

        redirect(admin_url('colis_en_attente'));
    }

    /**
     * Export colis en attente from database by date
     */
    public function export_colis_en_attente_by_date()
    {
        if (!has_permission('colis_en_attente', '', 'view')) {
            access_denied('Colis en attente');
        }

        if ($this->input->get()) {
            $params = $this->input->get();
            $params['start'] = to_sql_date($params['start']);
            $params['end'] = to_sql_date($params['end']);
            if (is_date($params['start']) && is_date($params['end'])) {
                $colisEnAttente = $this->colis_en_attente_model->get_colis_en_attente_by_date($params['start'], $params['end']);
                if ($colisEnAttente && count($colisEnAttente) > 0) {
                    $columnHeader = "Code d'envoi" . "\t" . "Numéro de commande" . "\t" . "Client" . "\t" . "Téléphone" . "\t" . "Date Creation" . "\t" . "Statut" . "\t" . "Ville" . "\t" . "CRBT" . "\t";
                    $columnHeader = mb_convert_encoding($columnHeader, 'utf-16LE', 'utf-8');

                    $setData = '';
                    foreach ($colisEnAttente as $key => $c) {
                        $rowData = '';
                        foreach ($c as $key => $value) {
                            $value = mb_convert_encoding($value, 'utf-16LE', 'utf-8');
                            $rowData .= '"' . $value . '"' . "\t";
                        }
                        $setData .= trim($rowData) . "\n";
                    }
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=export-colis-en-attente " . date("-d-m-Y") . ".xls");
                    header("Pragma: no-cache");
                    header("Expires: 0");

                    echo ucwords($columnHeader) . "\n" . $setData . "\n";
                }
            }
        }
    }

    /**
     * Delete colis en attente from database by date
     */
    public function delete_colis_en_attente_by_date()
    {
        if (!has_permission('colis_en_attente', '', 'delete')) {
            access_denied('Colis en attente');
        }

        $success = false;
        $message = _l('problem_deleting', _l('colis_en_attente_by_date'));
        if ($this->input->post()) {
            $params = $this->input->post();
            $params['start'] = to_sql_date($params['start']);
            $params['end'] = to_sql_date($params['end']);
            if (is_date($params['start']) && is_date($params['end'])) {
                $success = $this->colis_en_attente_model->delete_colis_en_attente_by_date($params['start'], $params['end']);
                if ($success) {
                    $message = _l('deleted', _l('colis_en_attente_by_date'));
                }
            }
        }

        echo json_encode(array('success' => $success, 'message' => $message));
    }
}

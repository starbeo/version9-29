<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colis_en_attente extends Client_controller
{

    private $not_importable_colis_en_attente_fields = array('id', 'etat_id', 'status_id', 'date_creation', 'commentaire', 'id_expediteur', 'colis_id', 'id_entreprise');

    function __construct()
    {
        parent::__construct();
        $this->load->model('colis_en_attente_model');
    }

    /**
     * List all colis en attente
     */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblcolisenattente.code_barre', 'tblcolisenattente.num_commande');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tblcolisenattente.type_livraison');
            }
            array_push($aColumns, 'tblcolisenattente.nom_complet', 'tblcolisenattente.telephone', 'tblcolisenattente.crbt', 'tblvilles.name', 'tblcolisenattente.date_creation', 'tblcolisenattente.status_id');

            $sIndexColumn = "id";
            $sTable = 'tblcolisenattente';

            $join = array(
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolisenattente.ville'
            );

            $where = array('AND tblcolisenattente.id_expediteur = ' . get_expediteur_user_id() . ' AND tblcolisenattente.colis_id IS NULL');
            // Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tblcolisenattente.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblcolisenattente.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-ville') && is_numeric($this->input->post('f-ville'))) {
                array_push($where, ' AND tblcolisenattente.ville = ' . $this->input->post('f-ville'));
            }
            if ($this->input->post('f-date-created-strat') && is_date(to_sql_date($this->input->post('f-date-created-strat')))) {
                array_push($where, ' AND tblcolisenattente.date_creation >= "' . to_sql_date($this->input->post('f-date-created-strat')) . '"');
            }
            if ($this->input->post('f-date-created-end') && is_date(to_sql_date($this->input->post('f-date-created-end')))) {
                array_push($where, ' AND tblcolisenattente.date_creation <= "' . to_sql_date($this->input->post('f-date-created-end')) . '"');
            }
            // By periode
            if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
                $periode = $this->input->get('periode');
                switch ($periode) {
                    case 'all':
                        array_push($where, ' AND colis_id IS NULL');
                        break;
                    case 'day':
                        array_push($where, ' AND colis_id IS NULL AND tblcolisenattente.date_creation = "' . date('Y-m-d') . '"');
                        break;
                    case 'week':
                        array_push($where, ' AND colis_id IS NULL AND WEEK(tblcolisenattente.date_creation, 1) = WEEK(CURDATE(), 1)');
                        break;
                    case 'month':
                        array_push($where, ' AND colis_id IS NULL AND tblcolisenattente.date_creation > DATE_SUB(now(), INTERVAL 1 MONTH)');
                        break;
                }
            }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblcolisenattente.id', 'tblcolisenattente.num_bonlivraison'), 'tblcolisenattente.id', '', 'DESC');
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

                    if ($aColumns[$i] == 'tblcolisenattente.code_barre') {
                        $_data = '<a href="#" data-toggle="modal" data-target="#colis-en-attente" data-id="' . $aRow['id'] . '" data-barcode="' . $aRow['tblcolisenattente.code_barre'] . '">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblcolisenattente.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.nom_complet') {
                        $_data = ucwords($_data);
                    } else if ($aColumns[$i] == 'tblcolisenattente.crbt') {
                        $_data = '<p class="pright30" style="text-align: right;">' . format_money($_data) . '</p>';
                    } else if ($aColumns[$i] == 'tblcolisenattente.date_creation') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'tblcolisenattente.status_id') {
                        $_data = format_status_colis($_data);
                    }

                    $row[] = $_data;
                }

                $options = '';
                if(is_null($aRow['num_bonlivraison'])) {
                    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('data-toggle' => 'modal', 'data-target' => '#colis-en-attente', 'data-id' => $aRow['id'], 'data-barcode' => $aRow['tblcolisenattente.code_barre']));
                } else {
                    $options .= icon_btn('client/bons_livraison/bon/' . $aRow['num_bonlivraison'], '', 'btn-info mbot5', array('target' => '_blank', 'data-toggle' => 'tooltip', 'title' => _l('_see', _l('bon_livraison'))), false, 'BL-' . $aRow['num_bonlivraison']);
                }
                $row[] = $options;
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        // Get cities
        $this->load->model('villes_model');
        $data['cities'] = $this->villes_model->get('', 1);
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

        $data['title'] = _l('colis_en_attente');
        $this->load->view('client/colis-en-attente/manage', $data);
    }

    /**
     * Add or Edit colis en attente
     */
    public function coli()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $data['id'];
            unset($data['id']);
            if ($id == "") {
                $id = $this->colis_en_attente_model->add($data);
                $message = _l('problem_adding', _l('colis_en_attente'));
                $success = true;
                if (is_numeric($id)) {
                    $message = _l('added_successfuly', _l('colis_en_attente'));
                }
            } else {
                $success = $this->colis_en_attente_model->update($data, $id);
                $message = _l('problem_updating', _l('colis_en_attente'));
                if ($success) {
                    $message = _l('updated_successfuly', _l('colis_en_attente'));
                }
            }

            echo json_encode(array('success' => $success, 'message' => $message));
        }
    }

    /**
     * Get infos colis en attente
     */
    public function get_colis_en_attente($id)
    {
        echo json_encode($this->colis_en_attente_model->get($id));
    }

    /**
     * Check if numero commande already exists for this colis
     */
    public function check_num_commande_exists()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                // First we need to check if the refernce is the same
                $colisId = $this->input->post('colis_id');
                if ($colisId != 'undefined') {
                    $this->db->where('id', $colisId);
                    $currentColisEnAttente = $this->db->get('tblcolisenattente')->row();
                    if ($currentColisEnAttente && $currentColisEnAttente->num_commande == $this->input->post('num_commande')) {
                        echo json_encode(true);
                        die();
                    }
                }

                $numCommandeExistsInTableColis = total_rows('tblcolis', array('num_commande' => $this->input->post('num_commande')));
                $numCommandeExistsInTableColisEnAttente = total_rows('tblcolisenattente', array('num_commande' => $this->input->post('num_commande')));
                if ($numCommandeExistsInTableColisEnAttente > 0 || $numCommandeExistsInTableColis > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
                die();
            }
        }
    }

    /**
     * Check if telephone has +212
     */
    public function check_telephone()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $telephone = $this->input->post('telephone');
                if (strlen($telephone) == 10) {
                    if (!preg_match("/^[0-9]{10}$/", $telephone)) {
                        echo json_encode(false);
                    } else {
                        echo json_encode(true);
                    }
                } else {
                    echo json_encode(false);
                }
                die();
            }
        }
    }

    /**
     * Get quartiers by city id
     */
    public function get_quartiers_by_villeid($villeId)
    {
        $this->load->model('quartiers_model');
        echo json_encode($this->quartiers_model->get('', array('ville_id' => $villeId)));
    }

    /**
     * Import
     */
    public function import()
    {
        require_once(APPPATH . 'third_party/Excel_reader/php-excel-reader/excel_reader2.php');
        require_once(APPPATH . 'third_party/Excel_reader/SpreadsheetReader.php');
        
        //Load model
        $this->load->model('expediteurs_model');
        //Variable declaration
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $errors = array();
        $data['btnimport'] = false;
        $total_imported = 0;
        if ($this->input->post()) {
            $path = $_FILES['file_xls']['name'];

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext =='xlsx'  || $ext =='xls' )
            {
                if (isset($_FILES['file_xls']['name']) && $_FILES['file_xls']['name'] != '') {
                    // Get the temp file path
                    $tmpFilePath = $_FILES['file_xls']['tmp_name'];
                    // Make sure we have a filepath
                    if (!empty($tmpFilePath) && $tmpFilePath != '') {
                        // Setup our new file path
                        $newFilePath = TEMP_FOLDER . $_FILES['file_xls']['name'];
                        if (!file_exists(TEMP_FOLDER)) {
                            mkdir(TEMP_FOLDER, 777);
                        }
                        if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                            //Get rows sheet excel
                            try {
                                $data_xls = new SpreadsheetReader($newFilePath);
                            } catch (Exception $e) {
                                die('Erreur lors du chargement du fichier "' . pathinfo($newFilePath, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                            };

                            $totalSheet = count($data_xls->sheets());

                            $rows = array();
                            // For Loop for all sheets 
                            for ($i = 0; $i < $totalSheet; $i++) {
                                $data_xls->ChangeSheet($i);
                                $cpt = 0;
                                $nbr = 0;
                                foreach ($data_xls as $Row) {
                                    if ($cpt !== 0) {
                                        $destinataire = isset($Row[0]) ? $Row[0] : '';
                                        $adresse = isset($Row[1]) ? $Row[1] : '';
                                        $telephone = isset($Row[2]) ? $Row[2] : '';
                                        $ville = isset($Row[3]) ? $Row[3] : '';
                                        $crbt = isset($Row[4]) ? $Row[4] : '';
                                        $num_commande = isset($Row[5]) ? $Row[5] : '';

                                        $rows[$nbr][0] = $destinataire;
                                        $rows[$nbr][1] = $adresse;
                                        $rows[$nbr][2] = $telephone;
                                        $rows[$nbr][3] = $ville;
                                        $rows[$nbr][4] = $crbt;
                                        $rows[$nbr][5] = $num_commande;
                                    }
                                    $cpt++;
                                    $nbr++;
                                }
                            }

                            if (count($rows) < 1) {
                                set_alert("warning", "Pas assez de lignes pour l'importation");
                                redirect(client_url('colis_en_attente/import'));
                            }

                            $db_temp_fields = array('nom_complet', 'adresse', 'telephone', 'ville', 'crbt', 'num_commande');
                            $db_fields = array();
                            foreach ($db_temp_fields as $field) {
                                if (in_array($field, $this->not_importable_colis_en_attente_fields)) {
                                    continue;
                                }
                                $db_fields[] = $field;
                            }

                            //Variable declaration
                            $cpt = 0;
                            $erreur_global = false;
                            $alias = get_option('alias_barcode');
                            $clientid = get_expediteur_user_id();
                            //Get client
                            $client = $this->expediteurs_model->get($clientid);
                            //Check if ouverture colis client is active
                            $ouvertureColis = 0;
                            if($client && is_numeric($client->ouverture) && $client->ouverture == 1) {
                                $ouvertureColis = $client->ouverture;
                            }
                            foreach ($rows as $key => $row) {
                                //Do for db fields
                                $insert = array();
                                for ($i = 0; $i < count($db_fields); $i++) {
                                    $erreur = false;

                                    if (!isset($row[$i])) {
                                        continue;
                                    }

                                    if ($row[$i] !== '') {
                                        if ($db_fields[$i] == 'num_commande') {
                                            $numCommandeExistsInTableColis = total_rows('tblcolis', array('num_commande' => $row[$i], 'id_entreprise' => $id_E));
                                            $numCommandeExistsInTableColisEnAttente = total_rows('tblcolisenattente', array('num_commande' => $row[$i], 'id_entreprise' => $id_E));
                                            //Dont insert duplicate num commande
                                            if ($numCommandeExistsInTableColis > 0 || $numCommandeExistsInTableColisEnAttente > 0) {
                                                $erreur = true;
                                            }
                                        }

                                        if ($db_fields[$i] == 'telephone') {
                                            $row[$i] = correctionPhoneNumber($row[$i]);
                                            if (strlen($row[$i]) == 10) {
                                                if (!preg_match("/^[0-9]{10}$/", $row[$i])) {
                                                    $erreur = true;
                                                }
                                            } else {
                                                $erreur = true;
                                            }
                                        }

                                        if ($db_fields[$i] == 'crbt') {
                                            if (!is_numeric($row[$i])) {
                                                $erreur = true;
                                            }
                                        }

                                        if ($db_fields[$i] == 'ville') {
                                            $this->db->where('id_entreprise', $id_E);
                                            $this->db->where('name', $row[$i]);
                                            $ville = $this->db->get('tblvilles')->row();
                                            if (is_null($ville)) {
                                                $erreur = true;
                                            } else {
                                                $row[$i] = $ville->id;
                                            }
                                        }

                                        $insert[$db_fields[$i]] = $row[$i];
                                    } else {
                                        $erreur = true;
                                        $row[$i] = 'Colonne vide';
                                    }

                                    if ($erreur == true) {
                                        if (!isset($errors[$cpt]['ligne'])) {
                                            $errors[$cpt]['ligne'] = $key + 1;
                                        }
                                        if ($db_fields[$i] == 'nom_complet') {
                                            $errors[$cpt]['name'] = $row[$i];
                                        }
                                        if ($db_fields[$i] == 'adresse') {
                                            $errors[$cpt]['address'] = $row[$i];
                                        }
                                        if ($db_fields[$i] == 'telephone') {
                                            $errors[$cpt]['phone'] = $row[$i];
                                        }
                                        if ($db_fields[$i] == 'ville') {
                                            $errors[$cpt]['ville'] = $row[$i];
                                        }
                                        if ($db_fields[$i] == 'crbt') {
                                            $errors[$cpt]['crbt'] = $row[$i];
                                        }
                                        if ($db_fields[$i] == 'num_commande') {
                                            $errors[$cpt]['num_commande'] = $row[$i];
                                        }
                                        $erreur_global = true;
                                    }
                                }

                                if ($erreur_global == true) {
                                    $cpt++;
                                }

                                if ($this->input->post('import')) {
                                    if (count($insert) > 0 && count($errors) == 0) {
                                        $total_imported++;
                                        //Data
                                        if (isset($insert['num_commande']) && !empty($insert['num_commande']) && _startsWith(strtoupper($insert['num_commande']), 'TA') && endsWith(strtoupper($insert['num_commande']), 'MA')) {
                                            $insert['code_barre'] = $insert['num_commande'];
                                        } else {
                                            $insert['code_barre'] = $alias . $clientid . 'MA' . get_nbr_coli_by_expediteur($clientid);
                                        }
                                        $insert['id_entreprise'] = $id_E;
                                        $insert['date_creation'] = date('Y-m-d');
                                        $insert['etat_id'] = 1;
                                        $insert['status_id'] = 12;
                                        $insert['id_expediteur'] = $clientid;
                                        $insert['ouverture'] = $ouvertureColis;
                                        $insert['importer'] = 1;

                                        $this->db->insert('tblcolisenattente', $insert);
                                    }
                                    $import_result = true;
                                }

                                if (count($errors) == 0) {
                                    $data['btnimport'] = true;
                                }
                            }
                        }
                    } else {
                        set_alert('warning', _l('import_upload_failed'));
                    }
                }
            }

            else
            {

                redirect(client_url('colis_en_attente/import'));


            }
        }

        $data['errors'] = $errors;
        if (isset($import_result) && $import_result == true) {
            //Delete File
            unlink($newFilePath);
            set_alert('success', _l('import_total_imported', $total_imported));
        }

        $data['not_importable'] = $this->not_importable_colis_en_attente_fields;
        $data['colis_db_fields'] = array('nom_complet', 'adresse', 'telephone', 'ville', 'crbt', 'num_commande');
        $data['title'] = _l('import');
        $this->load->view('client/colis-en-attente/import', $data);
    }
}

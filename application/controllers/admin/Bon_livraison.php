<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bon_livraison extends Admin_controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bon_livraison_model');

        if (get_permission_module('bon_livraison') == 0) {
            redirect(admin_url('home'));
        }
    }

    /**
     * List all bon livraison
     */
    public function index($type = false, $status = false)
    {
        $has_permission = has_permission('bon_livraison', '', 'view');
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->is_ajax_request()) {
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $aColumns = array('tblbonlivraison.id', 'tblbonlivraison.nom');
            // Check if option show point relai is actived
            if (get_permission_module('points_relais') == 1) {
                array_push($aColumns, 'tblbonlivraison.type_livraison');
            }
            array_push($aColumns, 'tblbonlivraison.type', 'tblbonlivraison.status', 'tblbonlivraison.id_livreur', 'CONCAT(a.firstname, " ", a.lastname) as fullname_livreur', 'tblbonlivraison.date_created', 'CONCAT(b.firstname, " ", b.lastname) as fullname_staff');

            $sIndexColumn = "id";
            $sTable = 'tblbonlivraison';

            $join = array(
                'LEFT JOIN tblstaff as a ON a.staffid = tblbonlivraison.id_livreur',
                'LEFT JOIN tblstaff as b ON b.staffid = tblbonlivraison.id_utilisateur',
                'LEFT JOIN tblpointsrelais ON tblpointsrelais.id = tblbonlivraison.point_relai_id',
                'LEFT JOIN tblpointsrelaissocietes ON tblpointsrelaissocietes.id = tblpointsrelais.societe_id'
            );

            $where = array();
            if (!$has_permission) {
                array_push($where, 'AND tblbonlivraison.id_utilisateur = ' . get_staff_user_id());
            }
            if (is_numeric($type)) {
                array_push($where, 'AND tblbonlivraison.type = ' . $type);
            }
            if (is_numeric($status)) {
                array_push($where, 'AND tblbonlivraison.status = ' . $status);
            }
            if (is_livreur()) {
                $livreur_id = get_staff_user_id();
                array_push($where, ' AND tblbonlivraison.id_livreur = ' . $livreur_id);
            }

            //Filtre
            if ($this->input->post('f-type-livraison') && !empty($this->input->post('f-type-livraison'))) {
                array_push($where, ' AND tblbonlivraison.type_livraison = "' . $this->input->post('f-type-livraison') . '"');
            }
            if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                array_push($where, ' AND tblbonlivraison.id_livreur = ' . $this->input->post('f-livreur'));
            }
            if ($this->input->post('f-point-relai') && is_numeric($this->input->post('f-point-relai'))) {
                array_push($where, ' AND tblbonlivraison.point_relai_id = ' . $this->input->post('f-point-relai'));
            }
            if ($this->input->post('f-utilisateur') && is_numeric($this->input->post('f-utilisateur'))) {
                array_push($where, ' AND tblbonlivraison.id_utilisateur = ' . $this->input->post('f-utilisateur'));
            }
            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                array_push($where, ' AND tblbonlivraison.date_created >="' . to_sql_date($this->input->post('f-date-created')) . '"');
            }

            if ($this->input->post('f-date-end') && is_date(to_sql_date($this->input->post('f-date-end')))) {
                array_push($where, ' AND tblbonlivraison.date_created <= "' . to_sql_date($this->input->post('f-date-end')) . '"');
                array_push($where, ' OR tblbonlivraison.date_created LIKE "' . to_sql_date($this->input->post('f-date-end')) . '%"');


            }
            //Get
          //  if ($this->input->get('periode') && !empty($this->input->get('periode'))) {
             //   $periode = $this->input->get('periode');
             //   switch ($periode) {
              //      case 'day':
                  //      array_push($where, ' AND tblbonlivraison.date_created LIKE "' . date('Y-m-d') . '%"');
              //          break;
                  //  case 'week':
                 //       array_push($where, ' AND WEEK(tblbonlivraison.date_created, 1) = WEEK(CURDATE(), 1)');
                  //      break;
                  // case 'month':
                   //     array_push($where, ' AND tblbonlivraison.date_created > DATE_SUB(now(), INTERVAL 1 MONTH)');
                   //     break;
                //}
           // }

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('(SELECT count(id) FROM tblcolisbonlivraison WHERE bonlivraison_id = tblbonlivraison.id) as nbr_colis', 'tblbonlivraison.id_utilisateur', 'tblbonlivraison.type_livraison', 'tblpointsrelaissocietes.name as societe_point_relai', 'tblpointsrelais.nom as point_relai', 'tblbonlivraison.point_relai_id'));
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

                    if ($aColumns[$i] == 'tblbonlivraison.id') {
                        if ($aRow['tblbonlivraison.status'] == 1) {
                            $disabled = '';
                        } else {
                            $disabled = 'disabled';
                        }
                        $_data = '<div class="checkbox checkbox-primary"><input id="checkbox-bon-livraison-' . $_data . '" value="' . $_data . '" name="ids[]" class = "checkbox-etat" type="checkbox" ' . $disabled . ' /><label></label></div>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.nom') {
                        $_data = render_btn_copy('column-name-bl-' . $key, 'name_of_delivery_note') . '<a id="column-name-bl-' . $key . '" href="' . admin_url('bon_livraison/bon/' . $aRow['tblbonlivraison.id']) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblbonlivraison.type_livraison') {
                        $_data = format_type_livraison_colis($_data);
                    } else if ($aColumns[$i] == 'tblbonlivraison.type') {
                        if ($_data == 1) {
                            $_data = '<span class="label label-info">' . _l('delivery_note_type_output') . '</span>';
                        } else if ($_data == 2) {
                            $_data = '<span class="label label-danger">' . _l('delivery_note_type_returned') . '</span>';
                        }
                    } else if ($aColumns[$i] == 'tblbonlivraison.status') {
                        $_data = format_status_bon_livraison($_data);
                    } else if ($aColumns[$i] == 'tblbonlivraison.id_livreur') {
                        $_data = render_nombre_colis($aRow['nbr_colis']);
                    } else if ($aColumns[$i] == 'fullname_livreur') {
                        if ($aRow['type_livraison'] == 'a_domicile') {
                            $_data = render_icon_motorcycle() . '<a href="' . admin_url('staff/member/' . $aRow['tblbonlivraison.id_livreur']) . '" target="_blank">' . $_data . '</a>';
                        } else {
                            $_data = render_icon_university() . '<a href="' . admin_url('points_relais/point_relai/' . $aRow['point_relai_id']) . '" target="_blank">' . $aRow['societe_point_relai'] . ' : ' . $aRow['point_relai'] . '</a>';
                        }
                    } else if ($aColumns[$i] == 'tblbonlivraison.date_created') {
                        $_data = date(get_current_date_time_format(), strtotime($_data));
                    } else if ($aColumns[$i] == 'fullname_staff') {
                        $_data = render_icon_user() . '<a href="' . admin_url('staff/member/' . $aRow['id_utilisateur']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }

                $options = '';
                if (has_permission('bon_livraison', '', 'edit')) {
                    $options .= icon_btn('admin/bon_livraison/bon/' . $aRow['tblbonlivraison.id'], 'pencil-square-o', 'btn-default', array('title' => 'Modifier Bon Livraison'));
                }
                if (has_permission('bon_livraison', '', 'download') && $aRow['nbr_colis'] > 0) {
                    $options .= icon_btn('admin/bon_livraison/pdf/' . $aRow['tblbonlivraison.id'], 'file-pdf-o', 'btn-success', array('title' => 'Imprimer PDF Bon Livraison'));
                    $options .= icon_btn('admin/bon_livraison/etiquette/' . $aRow['tblbonlivraison.id'], 'file-image-o', 'btn-info', array('title' => 'Imprimer Etiquette'));
                    $options .= icon_btn('#', 'eye', 'btn-primary mbot5', array('data-toggle' => 'modal', 'data-target' => '#bonlivraison', 'data-bonlivraison-id' => $aRow['tblbonlivraison.id']));

                }
                if (has_permission('bon_livraison', '', 'delete')) {
                    $options .= icon_btn('admin/bon_livraison/delete/' . $aRow['tblbonlivraison.id'], 'remove', 'btn-danger btn-delete-confirm', array('title' => 'Supprimer Bon Livraison'));
                }
                $row[] = $options;

                $output['aaData'][] = $row;
            }

            echo json_encode($output);
            die();
        }

        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get staff
        $data['staff'] = $this->staff_model->get('', 1, 'staffid != 1');
        //Get Type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }
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

        $data['types'] = $this->bon_livraison_model->get_types();

        $data['title'] = _l('delivery_note');
        $this->load->view('admin/bon_livraison/manage', $data);
    }

    /**
     * Bon de livraison sortie
     */
    public function sortie()
    {
        $this->index(1);
    }

    /**
     * Bon de livraison Retourner
     */
    public function retourner()
    {
        $this->index(2);
    }

    /**
     * Bon de livraison non confirmer
     */
    public function non_confirmer()
    {
        $this->index(false, 1);
    }

    /**
     * Bon de livraison confirmer
     */
    public function confirmer()
    {
        $this->index(false, 2);
    }

    /**
     * Edit or add new bon
     */
    public function bon($id = false, $type = false)
    {
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if (!is_numeric($id)) {
                if (!has_permission('bon_livraison', '', 'create')) {
                    access_denied('Bon livraison');
                }
                $id = $this->bon_livraison_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('delivery_note')));
                    redirect(admin_url('bon_livraison/bon/' . $id));
                }
            } else {
                if (!has_permission('bon_livraison', '', 'edit')) {
                    access_denied('Bon livraison');
                }
                $success = $this->bon_livraison_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('delivery_note')));
                }
                redirect(admin_url('bon_livraison/bon/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('delivery_note_lowercase'));
            $data['class1'] = 'col-md-6';
            $data['class2'] = '';
        } else {
            $bon_livraison = $this->bon_livraison_model->get($id);
            if (!$bon_livraison || (!has_permission('bon_livraison', '', 'view') && ($bon_livraison->id_utilisateur != get_staff_user_id()))) {
                set_alert('warning', _l('not_found', _l('delivery_note')));
                redirect(admin_url('bon_livraison'));
            }

            $data['bon_livraison'] = $bon_livraison;
            $title = $bon_livraison->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get types
        $data['types'] = $this->bon_livraison_model->get_types();
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
        //Get type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = $title;
        $this->load->view('admin/bon_livraison/bon', $data);
    }

    /**
     * Change status
     */
    public function change_status($status = '')
    {
        $success = false;
        $type = 'warning';
        $message = _l('problem_updating', _l('status'));
        if (has_permission('bon_livraison', '', 'edit') && $this->input->post() && is_numeric($status) && $status != 0) {
            $ids = $this->input->post('ids');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if (is_numeric($id)) {
                        $result = $this->bon_livraison_model->change_status($id, $status);
                        if (is_array($result) && $result['bon_livraison_confirmer'] == true) {
                            $success = false;
                            $type = 'warning';
                            $message = _l('delivery_note_already_confirmed');
                        } else if ($result) {
                            $success = true;
                            $type = 'success';
                            $message = _l('updated_successfuly', _l('status'));
                        }
                    }
                }
            }
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Get id coli
     */
    public function get_id_coli()
    {
        $barcode = $this->input->post('barcode');
        $this->load->model('colis_model');
        $coli = $this->colis_model->get_id_coli_by_barcode($barcode);

        $success = false;
        $message = 'Problème lors de la récupération de l\'id de la coli';
        $id = '';
        if ($coli && is_numeric($coli->id)) {
            $success = true;
            $message = 'Id récupérer avec succées.';
            $id = $coli->id;
        }

        echo json_encode(array('success' => $success, 'message' => $message, 'id' => $id));
    }

    /**
     * Add colis to bon ivraison
     */
    public function add_colis_to_bon_livraison()
    {
        $success = false;
        $type = 'warning';
        if (has_permission('bon_livraison', '', 'create')) {
            $colisId = $this->input->post('colis_id');
            $bonlivraisonId = $this->input->post('bonlivraison_id');
            $this->load->model('colis_model');
            $coli =  $this->colis_model->get($colisId);
            $coli_city = $coli->ville;
            $coli_br = $coli->code_barre;
            $this->db->where('tblvilles.id', $coli_city);
            $res = $this->db->get('tblvilles')->row();
            $coli_city = $res->name;
            if (is_numeric($colisId) && is_numeric($bonlivraisonId)) {
                $result = $this->bon_livraison_model->add_colis_to_bon_livraison($bonlivraisonId, $colisId);
                if (is_numeric($result)) {
                    $success = true;
                    $type = 'success';
                    $coli_city ='';
                    $message = _l('added_successfuly', _l('colis_delivery_note_lowercase'));
                } else if (is_array($result) && array_key_exists('bon_livraison_confirmer', $result)) {
                    $message = _l('delivery_note_already_confirmed');
                } else if (is_array($result) && array_key_exists('colis_already_exists_in_the_delivery_note', $result)) {
                    $message = _l('colis_already_exists_in_the_delivery_note');
                } else if (is_array($result) && array_key_exists('colis_already_delivered_or_returned', $result)) {
                    $message = _l('colis_already_delivered_or_returned');
                } else {
                    $message = _l('problem_adding', _l('colis_delivery_note_lowercase'));
                }
            }
        } else {
            $message = _l('access_denied');
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message,'city'=>$coli_city, 'coli_br' =>$coli_br));
    }

    /**
     * Remove colis to bon ivraison
     */
    public function remove_colis_to_bon_livraison()
    {
        $success = false;
        $type = 'warning';
        if (has_permission('bon_livraison', '', 'edit')) {
            $colisBonLivraisonId = $this->input->post('colisbonlivraison_id');
            if (is_numeric($colisBonLivraisonId)) {
                $success = $this->bon_livraison_model->remove_colis_to_bon_livraison($colisBonLivraisonId);
                if ($success == true) {
                    $type = 'success';
                    $message = _l('deleted', _l('colis_delivery_note_lowercase'));
                } else {
                    $message = _l('problem_deleting', _l('colis_delivery_note_lowercase'));
                }
            }
        } else {
            $message = _l('access_denied');
        }

        echo json_encode(array('success' => $success, 'type' => $type, 'message' => $message));
    }

    /**
     * Print PDF
     */
    public function pdf($id)
    {
        if (!has_permission('bon_livraison', '', 'download')) {
            access_denied('Bon livraison');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('bon_livraison'));
        }

        //Récupération du bon de livraison
        $bonLivraison = $this->bon_livraison_model->get($id);
        //Récupération des colis du bon de livraison
        $bonLivraison->items = $this->bon_livraison_model->get_items_bon_livraison($id);
        //Vérification si le bon de livraison contient des colis
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(admin_url('bon_livraison'));
        } else {
            if ($bonLivraison->type_livraison == 'a_domicile') {
                //Get infos livreur
                $this->load->model('staff_model');
                $livreur = $this->staff_model->get($bonLivraison->id_livreur);
                $bonLivraison->livreur = $livreur;
            } else {
                //Get infos point relai
                $this->load->model('points_relais_model');
                $point_relai = $this->points_relais_model->get($bonLivraison->point_relai_id);
                $bonLivraison->point_relai = $point_relai;
            }
            if ($bonLivraison->type == 1) {
                $bonLivraison->type = _l('output');
            } else if ($bonLivraison->type == 2) {
                $bonLivraison->type = _l('returned');
            }
            bon_livraison_pdf($bonLivraison);
        }
    }

    /**
     * Print etiquette
     */
    public function etiquette($id)
    {
        if (!has_permission('bon_livraison', '', 'download')) {
            access_denied('Bon livraison');
        }
        if (!is_numeric($id)) {
            redirect(admin_url('bon_livraison'));
        }

        //Récupération du bon de livraison
        $bonLivraison = $this->bon_livraison_model->get($id);
        //Récupération des colis du bon de livraison
        $bonLivraison->items = $this->bon_livraison_model->get_items_bon_livraison($id);
        //Vérification si le bon de livraison contient des colis
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(admin_url('bon_livraison'));
        } else {
            //Vérification si le bon de livraison est confirmé
            if ($bonLivraison->status == 2) {
                etiquette_bon_livraison_pdf($bonLivraison);
            } else {
                set_alert('warning', _l('you_must_confirm_the_delivery_note_first'));
                redirect(admin_url('bon_livraison'));
            }
        }
    }

    /**
     * Print PDF client
     */
    public function pdf_client($id)
    {
        if (!is_numeric($id)) {
            redirect(admin_url('expediteurs'));
        }

        //Récupération des infos du bon de livraison
        $this->load->model('bon_livraison_customer_model');
        $bonLivraison = $this->bon_livraison_customer_model->get($id);
        $bonLivraison->items = $this->bon_livraison_customer_model->get_items_bon_livraison($id);
        //Récupération des infos du client
        $this->load->model('expediteurs_model');
        $expediteur = $this->expediteurs_model->get($bonLivraison->id_expediteur);
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(admin_url('expediteurs/expediteur/' . $expediteur->id));
        } else {
            //Données pour le bon de livraison
            $bonLivraison->nom_expediteur = $expediteur->nom;
            $bonLivraison->telephone_expediteur = $expediteur->telephone;
            $bonLivraison->contact_expediteur = $expediteur->contact;
            //Génération pdf bon de livraison
            bon_livraison_customer_pdf($bonLivraison);
        }
    }

    /**
     * Print PDF client
     */
    public function etiquette_client($id)
    {
        if (!is_numeric($id)) {
            redirect(admin_url('expediteurs'));
        }

        //Récupération des infos du bon de livraison
        $this->load->model('bon_livraison_customer_model');
        $bonLivraison = $this->bon_livraison_customer_model->get($id);
        $bonLivraison->items = $this->bon_livraison_customer_model->get_items_bon_livraison($id);
        //Récupération des infos du client
        $this->load->model('expediteurs_model');
        $expediteur = $this->expediteurs_model->get($bonLivraison->id_expediteur);
        if (count($bonLivraison->items) == 0) {
            set_alert('warning', _l('delivery_note_does_not_contain_any_colis'));
            redirect(admin_url('expediteurs/expediteur/' . $expediteur->id));
        } else {
            //Données pour le bon de livraison
            $bonLivraison->client = $expediteur;
            //Génération etiquette bon de livraison
            etiquette_bon_livraison_customer_pdf($bonLivraison);
        }
    }

    /**
     * Delete bon livraison
     */
    public function delete($id)
    {
        if (!has_permission('bon_livraison', '', 'delete')) {
            access_denied('Bon livraison');
        }

        $response = $this->bon_livraison_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('delivery_note_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('delivery_note')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('delivery_note_lowercase')));
        }

        redirect(admin_url('bon_livraison'));
    }

    /**
     * Init colis bon livraison
     */
    public function init_colis_bon_livraison()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolis.id',
                'code_barre',
                'tblexpediteurs.nom',
                'tblvilles.name',
                'crbt',
                'frais',
                'date_ramassage',
                'etat_id',
                'status_reel'
            );

            $where = array();
            if ($this->input->post('type_livraison') && !empty($this->input->post('type_livraison'))) {
                array_push($where, ' AND tblcolis.type_livraison = "' . $this->input->post('type_livraison') . '"');
            }
            if ($this->input->post('id_livreur') && is_numeric($this->input->post('id_livreur')) && get_option('show_colis_displayed_in_the_delivery_note_by_livreur') == 1) {
                array_push($where, ' AND tblcolis.livreur = ' . $this->input->post('id_livreur'));
            }
            if ($this->input->post('point_relai_id') && is_numeric($this->input->post('point_relai_id'))) {
                array_push($where, ' AND tblcolis.point_relai_id = ' . $this->input->post('point_relai_id'));
            }
            if ($this->input->post('type') && is_numeric($this->input->post('type')) && $this->input->post('type') == 1) {
                //array_push($where, 'AND tblcolis.id NOT IN (SELECT colis_id FROM tblcolisbonlivraison WHERE colis_id = tblcolis.id)');
                //array_push($where, 'AND num_bonlivraison is NULL');
            }

            if ($this->input->post('type') && is_numeric($this->input->post('type')) && $this->input->post('type') == 1) {
                if (!empty(get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output'))) {
                    array_push($where, 'AND tblcolis.status_reel IN (' . get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output') . ')');
                } else {
                    array_push($where, 'AND tblcolis.status_reel NOT IN (2, 3, 12)');
                }
            } else if ($this->input->post('type') && is_numeric($this->input->post('type')) && $this->input->post('type') == 2) {
                if (!empty(get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned'))) {
                    array_push($where, 'AND tblcolis.status_reel IN (' . get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned') . ')');
                } else {
                    array_push($where, 'AND tblcolis.status_reel NOT IN (2, 3, 12)');
                }
            }

            $join = array(
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            $i = 0;
            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolis';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.id as client_id','tblcolis.ville'));
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

                    if ($aColumns[$i] == 'tblcolis.id') {
                        $liv = $this->input->post('id_livreur');
                    $arry= get_deli_cities($liv);
                      if (in_array($aRow['ville'],$arry)  )
                      {
                          $_data = icon_btn('#', 'plus', 'btn-success colis_added', array('data-id' => $_data));

                      }else {
                          $_data ='';
                      }

                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-barcode-' . $key, 'code_barre') . '<b id="column-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['client_id']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
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
     * Init historique colis bon livraison
     */
    public function init_historique_colis_bon_livraison()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolisbonlivraison.id as colisbonlivraison_id',
                'code_barre',
                'tblexpediteurs.nom',
                'tblvilles.name',
                'crbt',
                'frais',
                'date_ramassage',
                'etat_id',
                'status_reel'
            );

            $where = array();
            if ($this->input->post('bon_livraison_id') && is_numeric($this->input->post('bon_livraison_id'))) {
                array_push($where, ' AND tblcolisbonlivraison.bonlivraison_id = ' . $this->input->post('bon_livraison_id'));
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblcolisbonlivraison.colis_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolisbonlivraison';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.id as client_id'));
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

                    if ($aColumns[$i] == 'colisbonlivraison_id') {
                        $_data = icon_btn('#', 'remove', 'btn-danger colis_remove', array('data-colisbonlivraison-id' => $_data));
                    } else if ($aColumns[$i] == 'code_barre') {
                        $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<b id="column-historique-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['client_id']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $_data = format_etat_colis($_data);
                    } else if ($aColumns[$i] == 'status_reel') {
                        $_data = format_status_colis($_data);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data = date(get_current_date_format(), strtotime($_data));
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
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


    public function init_historique_colis_bon_livraison_popup()
    {
        if ($this->input->is_ajax_request()) {
            $aColumns = array(
                'tblcolisbonlivraison.id as colisbonlivraison_id',
                'code_barre',
                'tblexpediteurs.nom',
                'etat_id',
                'crbt',
                'frais',
                'date_ramassage',
                'tblvilles.name',
                'status_reel'
            );

            $where = array();
            if ($this->input->post('bon_livraison_id') && is_numeric($this->input->post('bon_livraison_id'))) {
                array_push($where, ' AND tblcolisbonlivraison.bonlivraison_id = ' . $this->input->post('bon_livraison_id'));
            }

            $join = array(
                'LEFT JOIN tblcolis ON tblcolis.id = tblcolisbonlivraison.colis_id',
                'LEFT JOIN tblexpediteurs ON tblexpediteurs.id = tblcolis.id_expediteur',
                'LEFT JOIN tblvilles ON tblvilles.id = tblcolis.ville'
            );

            // ID entreprise
            $id_E = $this->session->userdata('staff_user_id_entreprise');

            $sIndexColumn = "id";
            $sTable = 'tblcolisbonlivraison';
            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $id_E, $where, array('tblexpediteurs.id as client_id'));
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

                    if ($aColumns[$i] == 'colisbonlivraison_id') {
                        $_data = '';
                    } else if ($aColumns[$i] == 'code_barre') {
                       // $id_bl  =  $this->input->post('bon_livraison_id');
                      //  $this->load->model('bon_livraison_model');
                      //  $_data   =$this->bon_livraison_model->get_bl_name($id_bl);

                     $_data = render_btn_copy('column-historique-barcode-' . $key, 'code_barre') . '<b id="column-historique-barcode-' . $key . '">' . $_data . '</b>';
                    } else if ($aColumns[$i] == 'tblexpediteurs.nom') {
                        $_data = '<a href="' . admin_url('expediteurs/expediteur/' . $aRow['client_id']) . '">' . ucwords($_data) . '</a>';
                    } else if ($aColumns[$i] == 'etat_id') {
                        $code_barre  =  $aRow['code_barre'] ;
                        $this->load->model('bon_livraison_model');
                        $smsstau   =  $this->bon_livraison_model->get_colis_sms($code_barre);
                        if ($smsstau == 1) {
                            $label = _l('yes');
                            $colorLabel = 'success';
                        } else {
                            $label = _l('no');
                            $colorLabel = 'danger';
                        }
                        $_data = '<span class="label label-' . $colorLabel . '">' . $label . '</span>';

                    } else if ($aColumns[$i] == 'status_reel') {
                        $id_bl  =  $this->input->post('bon_livraison_id');
                        $this->load->model('bon_livraison_model');
                        $_data   =$this->bon_livraison_model->get_bl_comment($id_bl);
                    } else if ($aColumns[$i] == 'date_ramassage') {
                        if (!is_null($_data)) {
                            $_data ='<div style="display: none"></div>';
                        } else {
                            $_data = '';
                        }
                    } else if ($aColumns[$i] == 'crbt' || $aColumns[$i] == 'frais') {
                        $_data = '<div style="display: none"></div>';
                    }
                    else if ( $aColumns[$i] == 'tblvilles.name') {
                        $_data = '<div style="display: none"></div>';
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;

            }

            echo json_encode($output);
            die();
        }
    }


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

    public function bopopup($id = false, $type = false)
    {
        if (!has_permission('bon_livraison', '', 'view') && !has_permission('bon_livraison', '', 'view_own')) {
            access_denied('Bon livraison');
        }

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if (!is_numeric($id)) {
                if (!has_permission('bon_livraison', '', 'create')) {
                    access_denied('Bon livraison');
                }
                $id = $this->bon_livraison_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('delivery_note')));
                    redirect(admin_url('bon_livraison/bopopup/' . $id));
                }
            } else {
                if (!has_permission('bon_livraison', '', 'edit')) {
                    access_denied('Bon livraison');
                }
                $success = $this->bon_livraison_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('delivery_note')));
                }
                redirect(admin_url('bon_livraison/bopopup/' . $id));
            }
        }

        if (!is_numeric($id)) {
            $title = _l('add_new', _l('delivery_note_lowercase'));
            $data['class1'] = 'col-md-6';
            $data['class2'] = '';
        } else {
            $bon_livraison = $this->bon_livraison_model->get($id);
            if (!$bon_livraison || (!has_permission('bon_livraison', '', 'view') && ($bon_livraison->id_utilisateur != get_staff_user_id()))) {
                set_alert('warning', _l('not_found', _l('delivery_note')));
                redirect(admin_url('bon_livraison'));
            }

            $data['bon_livraison'] = $bon_livraison;
            $title = $bon_livraison->nom;
            $data['class1'] = 'col-md-3';
            $data['class2'] = 'col-md-9';
        }

        //Get livreurs
        $this->load->model('staff_model');
        $data['livreurs'] = $this->staff_model->get_livreurs();
        //Get types
        $data['types'] = $this->bon_livraison_model->get_types();
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
        //Get type
        $data['type'] = '';
        if (is_numeric($type)) {
            $data['type'] = $type;
        }

        $data['title'] = $title;
        $this->load->view('admin/bon_livraison/bonup', $data);
    }


    public function export()
    {
        ini_set('memory_limit', '1024M');
        //Get all colis
        $colis = $this->bon_livraison_model->get_colis_export();
        $columnHeader = "CODE ENVOI" ."\t" . "\t" . "CLIENTS" . "\t" . "LIVREUR" . "\t" . "TELEPHONE" . "\t" . "CRBT" . "\t" . "STATUS" . "\t" . "VILLE" . "\t" . "DATE RAMASSAGE" . "\t" . "DATE LIVRAISON" . "\t" . "FRAIS" . "\t" . "ETAT COLIS" . "\t" . "BonLivraison" . "\t";

        $setData = '';
        foreach ($colis as $key => $c) {
            $rowData = '';
            foreach ($c as $key => $value) {
                $value = mb_convert_encoding($value, 'utf-16LE', 'utf-8');
                $rowData .= '"' . $value . '"' . "\t";
            }
            $setData .= trim($rowData) . "\n";
        }
        header('Content-type: text/html; charset=UTF-8');
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=MyColis-export" . date("-d-m-Y") . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo ucwords($columnHeader) . "\n" . $setData . "\n";
    }



    public function export_by_filter()
    {
        if ($this->input->post()) {
            // Filtre
            $where = ' 1 = 1 ';
            // $where .= ' AND tbldemandes.department = ' . 4;

            //Filtre

         if ($this->input->post('f-utilisateur') && !empty($this->input->post('f-utilisateur'))) {
                $where .= ' AND tblbonlivraison.id_utilisateur = "' . $this->input->post('f-utilisateur') . '"';
         }
           if ($this->input->post('f-livreur') && is_numeric($this->input->post('f-livreur'))) {
                $where .= ' AND tblbonlivraison.id_livreur = ' . $this->input->post('f-livreur');
         }
           // if ($this->input->post('f-client') && is_numeric($this->input->post('f-client'))) {
           //     $where .= ' AND tbldemandes.client_id = ' . $this->input->post('f-client');
           // }
           // if ($this->input->post('f-departement') && is_numeric($this->input->post('f-departement'))) {
           //     $where .= ' AND tbldemandes.department = ' . $this->input->post('f-departement');
            //}
          //  if ($this->input->post('f-priority') && is_numeric($this->input->post('f-priority'))) {
           //     $where .= ' AND tbldemandes.priorite = ' . $this->input->post('f-priority');
           // }
           // if ($this->input->post('f-status') && is_numeric($this->input->post('f-status'))) {
            //   $where .=' AND tbldemandes.status = ' . $this->input->post('f-status');
         //   }

            if ($this->input->post('f-date-created') && is_date(to_sql_date($this->input->post('f-date-created')))) {
                $where  .= ' AND tblbonlivraison.date_created >= "' . to_sql_date($this->input->post('f-date-created')) . '"';
            }


            if ($this->input->post('f-date-end') && is_date(to_sql_date($this->input->post('f-date-end')))) {
                $where  .= ' AND tblbonlivraison.date_created <= "' . to_sql_date($this->input->post('f-date-end')) . '"';
                $where  .= ' OR tblbonlivraison.date_created LIKE "' . to_sql_date($this->input->post('f-date-end')) . '%"';


            }

            //Get list demands
            $demandes = $this->bon_livraison_model->export_demandes($where);
            if (count($demandes) > 0) {
                //Generate excel
                $filename = 'Liste Demandes ' . date(get_current_date_format(), strtotime(date('Y-m-d')));
                export_bl_excel($filename, $demandes);
            } else {
                set_alert('warning', _l('empty_result'));
            }
        }

        redirect(admin_url('demandes'));
    }


    public function checkville ()
    {

         $success = false;
        $coli_id = $this->input->post('coli_id');
        if ($coli_id && !empty($coli_id)) {
            $this->load->model('colis_model');
             $coli =  $this->colis_model->get($coli_id);
             $idliv =  $coli->livreur;
             $coli_city = $coli->ville;
            $cities = get_deli_cities($idliv);   //$this->bon_livraison_model->checklivruer($coli_id);
            if (in_array($coli_city,$cities))
            {
                $success = true;
            } else {
                $this->db->where('tblvilles.id', $coli_city);
                $res = $this->db->get('tblvilles')->row();
                $coli_city = $res->name;
                $success = false;
            }
        }

        echo json_encode(array('success' => $success,'citie' => $coli_city));
    }

}


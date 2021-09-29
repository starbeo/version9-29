<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
define('CRON', true);

class Crontime_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Run
     */
    public function run()
    {
        //$this->check_colis_with_num_etat_colis_livre_is_not_null_and_etat_id_is_not_payed();
        $this->check_colis_with_num_facture_is_not_null_and_etat_id_is_not_facture();
        $this->colis_without_num_bonlivraison_and_colis_id_in_tblcolisbonlivraison();
        $this->colis_without_num_facture_and_colis_id_in_tblcolisfacture();
        $this->colis_without_num_etat_colis_livre_and_colis_id_in_tbletatcolislivreitems();
        $this->check_invoices_with_name_invoice_is_empty();
        //$this->make_backup_db();
    }

    /**
     * Log activity
     */
    public function log_activity($name_cron = '', $description = '', $dateStart = NULL, $dateEnd = NULL)
    {
        $log = array(
            'name_cron' => $name_cron,
            'description' => $description,
            'date_created' => date('Y-m-d H:i:s'),
            'date_start' => $dateStart,
            'date_end' => $dateEnd
        );

        $this->db->insert('tblcronactivitylog', $log);
    }
    
    /**
     * Check Colis With Numéro Etat Colis Livré is not NULL And Etat_id is not payed
     */
    public function check_colis_with_num_etat_colis_livre_is_not_null_and_etat_id_is_not_payed()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->where('num_etatcolislivrer IS NOT NULL');
        $this->db->where('etat_id =', 1);
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        //Update
        $cpt = 0;
        if (count($colis) > 0) {
            //Update Etat id Colis
            $this->db->where('num_etatcolislivrer IS NOT NULL');
            $this->db->where('etat_id =', 1);
            $this->db->update('tblcolis', array('etat_id' => 2));
            if ($this->db->affected_rows() > 0) {
                $cpt = count($colis);
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Vérification Etat Id Colis pour chaque Colis si la coli à un numéro de état colis livré', $cpt . ' colis réglé', $dateStart, $dateEnd);
    }
    
    /**
     * Check Colis With Numéro Facture is not NULL And Etat_id is not facturé
     */
    public function check_colis_with_num_facture_is_not_null_and_etat_id_is_not_facture()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->where('num_facture IS NOT NULL');
        $this->db->where('etat_id !=', 3);
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        //Update
        $cpt = 0;
        if (count($colis) > 0) {
            //Update Etat id Colis
            $this->db->where('num_facture IS NOT NULL');
            $this->db->where('etat_id !=', 3);
            $this->db->update('tblcolis', array('etat_id' => 3));
            if ($this->db->affected_rows() > 0) {
                $cpt = count($colis);
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Vérification Etat Id Colis pour chaque Colis si la coli à un numéro de facture', $cpt . ' colis réglé', $dateStart, $dateEnd);
    }
    
    /**
     * Get Colis Without Numéro Bon de livraison And Colis_id IN tblcolisbonlivraison
     */
    public function colis_without_num_bonlivraison_and_colis_id_in_tblcolisbonlivraison()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->where('num_bonlivraison IS NULL');
        $this->db->where('id IN (SELECT colis_id FROM `tblcolisbonlivraison` WHERE colis_id = tblcolis.id)');
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        $cpt = 0;
        foreach ($colis as $key => $c) {
            //Get Colis Bon Livraison
            $this->db->where('colis_id', $c['id']);
            $colisbonlivraison = $this->db->get('tblcolisbonlivraison')->result_array();
            if ($colisbonlivraison) {
                if (count($colisbonlivraison) == 1) {
                    if (is_numeric($colisbonlivraison[0]['bonlivraison_id'])) {
                        //Update Numéro Bon de livraison in tblcolis to NULL
                        $this->db->where('id', $colisbonlivraison[0]['colis_id']);
                        $this->db->update('tblcolis', array('num_bonlivraison' => $colisbonlivraison[0]['bonlivraison_id']));
                        if ($this->db->affected_rows() > 0) {
                            $cpt++;
                        }
                    }
                } else if (count($colisbonlivraison) > 1) {
                    for ($i = count($colisbonlivraison) - 1; $i >= 0; $i--) {
                        if ($i == (count($colisbonlivraison) - 1)) {
                            if (is_numeric($colisbonlivraison[$i]['bonlivraison_id'])) {
                                //Update Numéro Bon de livraison in tblcolis to NULL
                                $this->db->where('id', $colisbonlivraison[$i]['colis_id']);
                                $this->db->update('tblcolis', array('num_bonlivraison' => $colisbonlivraison[$i]['bonlivraison_id']));
                                if ($this->db->affected_rows() > 0) {
                                    $cpt++;
                                }
                            }
                        }
                    }
                }
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Vérification Numéro Bon de livraison pour chaque Colis', $cpt . ' colis réglé', $dateStart, $dateEnd);
    }

    /**
     * Get Colis Without Numéro Facture And Colis_id IN tblcolisfacture
     */
    public function colis_without_num_facture_and_colis_id_in_tblcolisfacture()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->where('num_facture IS NULL');
        $this->db->where('id IN (SELECT colis_id FROM `tblcolisfacture` WHERE colis_id = tblcolis.id)');
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        $cpt = 0;
        foreach ($colis as $key => $c) {
            //Get Colis Facture
            $this->db->where('colis_id', $c['id']);
            $colisfacture = $this->db->get('tblcolisfacture')->result_array();
            if ($colisfacture) {
                //Delete colis facture
                $this->db->where('colis_id', $c['id']);
                $this->db->delete('tblcolisfacture');
                
                if (count($colisfacture) == 1) {
                    if (is_numeric($colisfacture[0]['facture_id'])) {
                        //Update Numéro Facture in tblcolis to NULL
                        $this->db->where('id', $colisfacture[0]['colis_id']);
                        $this->db->update('tblcolis', array('num_facture' => $colisfacture[0]['facture_id']));
                        if ($this->db->affected_rows() > 0) {
                            $cpt++;
                        }
                    }
                } else if (count($colisfacture) > 1) {
                    for ($i = count($colisfacture) - 1; $i >= 0; $i--) {
                        if ($i == (count($colisfacture) - 1)) {
                            if (is_numeric($colisfacture[0]['facture_id'])) {
                                //Update Numéro Facture in tblcolis to NULL
                                $this->db->where('id', $colisfacture[$i]['colis_id']);
                                $this->db->update('tblcolis', array('num_facture' => $colisfacture[$i]['facture_id']));
                                if ($this->db->affected_rows() > 0) {
                                    $cpt++;
                                }
                            }
                        }
                    }
                }
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Vérification Numéro Facture pour chaque Colis', $cpt . ' colis réglé', $dateStart, $dateEnd);
    }

    /**
     * Get Colis Without Numéro Etat Colis Livré And Colis_id IN tbletatcolislivreitems
     */
    public function colis_without_num_etat_colis_livre_and_colis_id_in_tbletatcolislivreitems()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->where('num_etatcolislivrer IS NULL');
        $this->db->where('id IN (SELECT colis_id FROM `tbletatcolislivreitems` WHERE colis_id = tblcolis.id)');
        $this->db->order_by('id', 'DESC');
        $colis = $this->db->get('tblcolis')->result_array();

        $cpt = 0;
        foreach ($colis as $key => $c) {
            //Get Colis Etat Colis Livré
            $this->db->where('colis_id', $c['id']);
            $colisEtatColisLivre = $this->db->get('tbletatcolislivreitems')->result_array();
            if ($colisEtatColisLivre) {
                if (count($colisEtatColisLivre) == 1) {
                    if (is_numeric($colisEtatColisLivre[0]['etat_id'])) {
                        //Update Numéro Facture in tblcolis to NULL
                        $this->db->where('id', $colisEtatColisLivre[0]['colis_id']);
                        $this->db->update('tblcolis', array('num_etatcolislivrer' => $colisEtatColisLivre[0]['etat_id']));
                        if ($this->db->affected_rows() > 0) {
                            $cpt++;
                        }
                    }
                } else if (count($colisEtatColisLivre) > 1) {
                    for ($i = count($colisEtatColisLivre) - 1; $i >= 0; $i--) {
                        if ($i == (count($colisEtatColisLivre) - 1)) {
                            if (is_numeric($colisEtatColisLivre[0]['etat_id'])) {
                                //Update Numéro Facture in tblcolis to NULL
                                $this->db->where('id', $colisEtatColisLivre[$i]['colis_id']);
                                $this->db->update('tblcolis', array('num_etatcolislivrer' => $colisEtatColisLivre[$i]['etat_id']));
                                if ($this->db->affected_rows() > 0) {
                                    $cpt++;
                                }
                            }
                        }
                    }
                }
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Vérification Numéro Etat Colis Livré pour chaque Colis', $cpt . ' colis réglé', $dateStart, $dateEnd);
    }

    /**
     * Get Etat Colis Livré Where Total Différent de la somme des CRBT des colis de cette Etat Colis Livré
     */
    public function etat_colis_livre_where_total_different_de_la_somme_des_crbt_des_colis_de_cette_etat_colis_livre()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get colis
        $this->db->order_by('tbletatcolislivre.id', 'DESC');
        $this->db->limit(200);
        $etaColisLivre = $this->db->get('tbletatcolislivre')->result_array();

        $cpt = 0;
        foreach ($etaColisLivre as $key => $ecl) {
            //Get Items Etat Colis Livré
            $this->db->select('CAST(sum(tblcolis.crbt) AS SIGNED) as sum_crbt_items');
            $this->db->from('tbletatcolislivreitems');
            $this->db->join('tblcolis', 'tblcolis.id = tbletatcolislivreitems.colis_id');
            $this->db->where('tbletatcolislivreitems.etat_id', $ecl['id']);
            $sumItemsEtatColisLivre = $this->db->get()->row();
            if ($sumItemsEtatColisLivre) {
                //Update Total Etat Colis Livre
                $this->db->set('total', $sumItemsEtatColisLivre->sum_crbt_items, FALSE);
                $this->db->set('manque', 'total_received -' . $sumItemsEtatColisLivre->sum_crbt_items, FALSE);
                $this->db->where('id', $ecl['id']);
                $this->db->update('tbletatcolislivre');
                if ($this->db->affected_rows() > 0) {
                    $cpt++;
                }
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Réglage Total Etats Colis Livré : ', $cpt . ' états réglé', $dateStart, $dateEnd);
    }

    /**
     * Get Factures Livré Where Total Différent du total des CRBT, FRAIS, REFUSE, REMISE des colis de cette Facture Livré
     * 
     */
    public function factures_livre_where_total_different_du_total_des_crbt_frais_refuse_remise_des_colis_de_cette_facture_livre()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get invoices
        $this->db->where('tblfactures.type', 2);
        $this->db->order_by('tblfactures.id', 'DESC');
        $this->db->limit(200);
        $facturesLivrer = $this->db->get('tblfactures')->result_array();

        $cpt = 0;
        foreach ($facturesLivrer as $fct) {
            //Get Items Factures Livré
            $this->db->select('tblcolis.*');
            $this->db->from('tblcolisfacture');
            $this->db->join('tblcolis', 'tblcolis.id = tblcolisfacture.colis_id');
            $this->db->where('tblcolisfacture.facture_id', $fct['id']);
            $itemsFacturesLivre = $this->db->get()->result_array();
            if (count($itemsFacturesLivre) > 0) {
                $totalCrbt = 0;
                $totalFrais = 0;
                $totalRefuse = 0;
                foreach ($itemsFacturesLivre as $item) {
                    if ($item['status_reel'] == 9) {
                        $totalRefuse += $item['frais'];
                    } else {
                        $totalCrbt += $item['crbt'];
                        $totalFrais += $item['frais'];
                    }
                }

                //Get total parrainage
                $totalParrainage = $fct['total_parrainage'];
                //Calcule Discount
                $totalRemise = 0;
                if (isset($fct['remise']) && $fct['remise'] > 0) {
                    if ($fct['remise_type'] == 'fixed_amount') {
                        $totalRemise += $fct['remise'];
                    } else {
                        $totalRemise += $totalRefuse * ($fct['remise'] / 100);
                    }
                }
                //Calcule TOTAL net
                $totalNet = $totalCrbt - $totalFrais - $totalRefuse + $totalParrainage + $totalRemise;
                //Update Facture
                $this->db->where('id', $fct['id']);
                $this->db->update('tblfactures', array('total_crbt' => $totalCrbt, 'total_frais' => $totalFrais, 'total_refuse' => $totalRefuse, 'total_parrainage' => $totalParrainage, 'total_remise' => $totalRemise, 'total_net' => $totalNet));
                if ($this->db->affected_rows() > 0) {
                    //Nombre Facture Modifié
                    $cpt++;
                }
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Réglage Total Facture Livré : ', $cpt . ' factures réglé', $dateStart, $dateEnd);
    }

    /**
     * Check Invoices With Name Invoice is empty
     */
    public function check_invoices_with_name_invoice_is_empty()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');

        //Get invoices
        $this->db->where('nom', '');
        $this->db->order_by('id', 'DESC');
        $invoices = $this->db->get('tblfactures')->result_array();

        $cpt = 0;
        //Update
        foreach ($invoices as $invoice) {
            $this->db->where('id', $invoice['id']);
            $this->db->update('tblfactures', array('nom' => 'FCT-' . date('dmY', strtotime($invoice['date_created'])) . '-' . $invoice['id']));
            if ($this->db->affected_rows() > 0) {
                $cpt++;
            }
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Modification Nom Facture s\'il est vide', $cpt . ' factures modifié', $dateStart, $dateEnd);
    }

    /**
     * Cloturer les demandes répondu par le service clientèle et le délai de 72 heures a été dépassé
     */
    public function cloturer_demande()
    {
        //Date début cron
        $dateStart = date('Y-m-d H:i:s');
        //Chargement du model
        $this->load->model('demandes_model');
        //Déclaration variable
        $cpt = 0;
        //Calcule de la date du système moins 3 jours
        $date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 3 days'));
        //Récupération des demandes répondu par l'administration
        $demandes = $this->demandes_model->get('', 'status = 2 AND datecreated <= "' . $date . '"');
        //Traitement
        foreach ($demandes as $demande) {
            //Cloturer la demande
            $success = $this->demandes_model->cloturer($demande['id']);
            if ($success === true) {
                $cpt++;
            }
        }
        //Date fin cron
        $dateEnd = date('Y-m-d H:i:s');

        //Ajouter journale d'activité cron
        $this->log_activity('Fermeture des demandes qui ont été répondu par le service des demandes après un delai de 3 jours', $cpt . ' demandes cloturées', $dateStart, $dateEnd);
    }
    
    /**
     * Définir les notifications : Rappel des demandes en cours
     */
    public function ajouter_notification_staff_des_demandes_en_cours()
    {
        //Date début cron
        $dateStart = date('Y-m-d H:i:s');
        //Chargement du model
        $this->load->model('demandes_model');
        //Déclaration variable
        $cpt = 0;
        //Récupération des demandes en cours
        $demandes = $this->demandes_model->get('', 'status = 1');
        //Traitement
        foreach ($demandes as $demande) {
            //Ajouter une notification à l'utilisateur de ce departement
            if (is_numeric($demande['department'])) {
                //Chargement du model
                $this->load->model('staff_model');
                //Récupération des utilisateurs avec le meme departement
                $utilisateurs = $this->staff_model->get('', '', 'admin != 0 AND department = ' . $demande['department']);
                //Traitement
                foreach ($utilisateurs as $utilisateur) {
                    //Ajouter notification
                    if (is_numeric($utilisateur['staffid']) && total_rows('tblnotifications', 'date LIKE "' . date('Y-m-d') . '%" AND touserid = ' . $utilisateur['staffid'] . ' AND fromcompany = ' . $demande['client_id'] . ' AND link = "' . admin_url('demandes/preview/' . $demande['id'] . '"')) == 0) {
                        $dataNotification['description'] = "Nouvelle demande crée [Nom: " . $demande['name'] . "]";
                        $dataNotification['fromcompany'] = $demande['client_id'];
                        $dataNotification['touserid'] = $utilisateur['staffid'];
                        $dataNotification['link'] = admin_url('demandes/preview/' . $demande['id']);
                        add_notification($dataNotification);
                        $cpt++;
                    }
                }
            }
        }
        //Date fin cron
        $dateEnd = date('Y-m-d H:i:s');

        //Ajouter journale d'activité cron
        $this->log_activity('Ajout notification des demandes en cours pour les utilisateurs avec le meme département de la demande', $cpt . ' notifications crées', $dateStart, $dateEnd);
    }
    
    /**
     * Backup db
     */
    public function make_backup_db()
    {
        //Date start
        $dateStart = date('Y-m-d H:i:s');
        //Load dbutil
        $this->load->dbutil();

        $prefs = array(
            'tables' => array('tblcolis'),
            'format' => 'zip',
            'filename' => date("Y-m-d-H-i-s") . '_backup.sql'
        );

        $backup = $this->dbutil->backup($prefs);
        $backup_name = 'database_backup_' . date("Y-m-d") . '.zip';
        $backup_name = unique_filename(BACKUPS_FOLDER, $backup_name);
        $save = BACKUPS_FOLDER . $backup_name;

        //Load helper
        $this->load->helper('file');
        $description = 'Non Enregistrer';
        if (write_file($save, $backup)) {
            $description = 'Enregistrer';
            //Delete Last Backup
            $date_last_backup = date('Y-m-d', strtotime(date('Y-m-d') . ' -2 day'));
            $last_backup_name = 'database_backup_' . $date_last_backup . '.zip';
            $backup_file_name = BACKUPS_FOLDER . $last_backup_name;
            if (file_exists($backup_file_name)) {
                unlink($backup_file_name);
            }
            //Send email
            $this->load->model('emails_model');
            $this->emails_model->add_attachment(array(
                'attachment' => base_url() . 'backups/' . $backup_name,
                'filename' => $backup_name,
                'type' => 'application/zip'
            ));

            $send = $this->emails_model->send_simple_email('ahmed.mouda08@gmail.com', 'Base de données', 'Bonjour');
            if ($send) {
                echo 'Yes';
                exit;
            }
            echo 'No';
            exit;
        }

        //Date end
        $dateEnd = date('Y-m-d H:i:s');

        //Add Log Activity
        $this->log_activity('Archivage Base de donnée', $description, $dateStart, $dateEnd);
    }

    /**
     * Generate excel colis
     */
    public function generate_excel_colis()
    {
        //Load model
        $this->load->model('colis_model');
        $this->load->model('emails_model');
        //Params
        $dateDebut = date("Y-m-d", mktime(0, 0, 0, date("m") - 3, 1, date("Y")));
        $dateFin = date("Y-m-d");
        //Get list colis
        $where = ' tblcolis.date_ramassage >= "' . $dateDebut . '"';
        $where .= ' AND tblcolis.date_ramassage <= "' . $dateFin . '"';
        $colis = $this->colis_model->export_colis($where);
        if (count($colis) > 0) {
            $titre = 'Liste Colis ';
            //Generate excel
            $filename = $titre . date(get_current_date_format(), strtotime(date('Y-m-d')));
            export_colis_excel($filename, $colis, false, true);
            $fileGenerateExcelColis = TEMP_FOLDER . date('d-m-Y') . '/' . $filename;
            //Send via mail ahmed.mouda08@gmail.com
//            $this->emails_model->add_attachment(array(
//                'attachment' => $fileGenerateExcelColis,
//                'filename' => $filename . '.xls',
//                'type' => 'application/xls'
//            ));
//            $this->emails_model->send_simple_email('hamza.elkhaddar@gmail.com', $filename, '');
            //Delete Last excel
            $dateLastGenerateExcelColis = date('d-m-Y', strtotime(date('Y-m-d') . ' -1 day'));
            $lastDirGenerateExcelColisName = TEMP_FOLDER . $dateLastGenerateExcelColis;
            if (is_dir($lastDirGenerateExcelColisName)) {
                delete_dir($lastDirGenerateExcelColisName);
            }
        }
    }

    /**
     * Add colis to cash plus
     */
    public function add_colis_to_cash_plus()
    {
        if (get_permission_module('points_relais') == 1) {
            //Date start
            $dateStart = date('Y-m-d H:i:s');
            //load model
            $this->load->model('colis_model');
            $this->load->model('villes_model');
            $this->load->model('points_relais_model');
            //load Librairie
            $this->load->library("Nusoap");

            //Get colis type livraison "Point relai" des bons de livraison confirmé
            $this->db->where('sent', 0);
            $colis = $this->db->get('tblcoliscashplus')->result_array();
            
            $nbrColisAdded = 0;
            if (count($colis) > 0) {
                foreach ($colis as $coli) {
                    //Get coli
                    $coli = $this->colis_model->get($coli['colis_id']);
                    if ($coli && $coli->type_livraison == 'point_relai' && is_numeric($coli->point_relai_id)) {
                        //Get city
                        $city = $this->villes_model->get($coli->ville);
                        $cityName = '';
                        if ($city) {
                            $cityName = $city->name;
                        }
                        //Get point relai
                        $pointRelai = $this->points_relais_model->get($coli->point_relai_id);
                        $pointRelaiName = '';
                        if ($pointRelai) {
                            $pointRelaiName = $pointRelai->nom;
                        }

                        $data = array(
                            "SOCIETE" => "",
                            "NOM" => $coli->nom_complet,
                            "PRENOM" => "",
                            "ADR1" => $coli->adresse,
                            "ADR2" => "",
                            "CP" => "",
                            "VILLE" => $cityName,
                            "PAYS" => "Maroc",
                            "CODECLI" => "",
                            "INFO_SUP" => "",
                            "NUMCOLISTRANSP" => $coli->code_barre,
                            "ORIGINE" => "",
                            "DATESOUHAITEE" => "",
                            "DATEENLSOUHAITEE" => "",
                            "HEUREENLRDV" => "",
                            "PERIODE" => "",
                            "URGENCE" => "1",
                            "NUMADR" => "FC70DBE23E3875B8DB3E",
                            "COMMENT" => "",
                            "VALEURCOLIS" => "",
                            "POIDS" => "",
                            "VALCONTREREMBOURSEMENT" => $coli->crbt,
                            "DIMENSION" => "",
                            "HEURERDV" => "",
                            "SUPPORT" => "",
                            "AGENCE" => $pointRelaiName);
                        $json = "[" . json_encode($data) . "]";

                        $options = array('fonction' => 'createCM');
                        $params = array("prm1" => $json, "prm2" => "");
                        
                        if (!empty($options)) {
                            $result = $this->nusoap->soapCashPlus($options, $params);
                            if ($result || is_null($result)) {
                                //Check coli if is added
                                $options = array('fonction' => 'getColisStatus');
                                $params = array("prm1" => $coli->code_barre, "prm2" => "");

                                $sent = 0;
                                $description = 'Problème lors de l\'ajout du coli au système Cash Plus';
                                if (!empty($options)) {
                                    $results = $this->nusoap->soapCashPlus($options, $params);
                                    if (is_array($results) && count($results) > 0) {
                                        $sent = 1;
                                        $description = 'Colis ajouté au système Cash Plus avec succès';
                                        $nbrColisAdded++;
                                        foreach ($results as $key => $result) {
                                            if ($key == 0) {
                                                //Add status
                                                $dataInsertStatut['code_barre'] = $coli->code_barre;
                                                $dataInsertStatut['type'] = 100;
                                                $dataInsertStatut['date_created'] = date('Y-m-d H:i:s');
                                                $dataInsertStatut['emplacement_id'] = 10;
                                                $dataInsertStatut['id_utilisateur'] = 0;
                                                $dataInsertStatut['id_entreprise'] = 0;
                                                $this->db->insert('tblstatus', $dataInsertStatut);
                                            }
                                        }
                                    }
                                }
                                //Update
                                $dataUpdate['sent'] = $sent;
                                $dataUpdate['description'] = $description;
                                $this->db->where('colis_id', $coli->id);
                                $this->db->update('tblcoliscashplus', $dataUpdate);
                            }
                        }
                    }
                }
            }

            //Date end
            $dateEnd = date('Y-m-d H:i:s');

            //Add Log Activity
            $this->log_activity('Ajout des colis au système Cash Plus', $nbrColisAdded . ' colis ajouté', $dateStart, $dateEnd);
        }
    }

    /**
     * Get status colis cash plus
     */
    public function get_status_colis_cash_plus()
    {
        /*if (get_permission_module('points_relais') == 1) {
            //Date start
            $dateStart = date('Y-m-d H:i:s');
            //load model
            $this->load->model('status_model');
            //load Librairie
            $this->load->library("Nusoap");

            //Get all status coli
            $options = array('fonction' => 'getColisStatusM');
            $params = array("prm1" => date('Ymd'), "prm2" => "");

            $nbrStatusAdded = 0;
            $description = 'Problème lors de la récupération des status des colis à partir du système Cash Plus';
            if (!empty($options)) {
                $results = $this->nusoap->soapCashPlus($options, $params);
                if (is_array($results) && count($results) > 0) {
                    $description = 'Status récupérer du système Cash Plus avec succès';
                    $allStatusCashPlus = array(0 => 100, 15 => 101, 30 => 102, 40 => 2, 50 => 9, 70 => 13);
                    $locationStatusCashPlus = array(0 => 10, 15 => 10, 30 => 10, 40 => 6, 50 => 9, 70 => 9);
                    foreach ($results as $result) {
                        //Get last status coli
                        $this->db->where('tblstatus.code_barre', $result['numtransp']);
                        $this->db->order_by('tblstatus.id', 'desc');
                        $this->db->limit('1');
                        $lastStatut = $this->db->get('tblstatus')->row();
                        if ($lastStatut) {
                            $statusReel = $allStatusCashPlus[$result['status']];
                            if ($lastStatut->type != $statusReel) {
                                //Add status
                                $dataInsertStatut['code_barre'] = $result['numtransp'];
                                $dataInsertStatut['type'] = $statusReel;
                                $dataInsertStatut['date_created'] = date('Y-m-d H:i:s');
                                $dataInsertStatut['emplacement_id'] = $locationStatusCashPlus[$result['status']];
                                $dataInsertStatut['id_utilisateur'] = 0;
                                $dataInsertStatut['id_entreprise'] = 0;
                                $this->db->insert('tblstatus', $dataInsertStatut);
                                $insert_id = $this->db->insert_id();
                                if ($insert_id) {
                                    $dateLivraison = NULL;
                                    $dateRetour = NULL;
                                    $statusId = 1;
                                    if($statusReel == 2) {
                                        $statusId = $statusReel;
                                        $dateLivraison = date('Y-m-d');
                                    } else if($statusReel == 3) {
                                        $statusId = $statusReel;
                                        $dateRetour = date('Y-m-d');
                                    }
                                    //Update status colis
                                    $this->db->where('code_barre', $result['numtransp']);
                                    $this->db->update('tblcolis', array(
                                        'status_reel' => $statusReel,
                                        'status_id' => $statusId,
                                        'etat_id' => 1,
                                        'date_livraison' => $dateLivraison,
                                        'date_retour' => $dateRetour,
                                        'num_facture' => NULL
                                    ));
                
                                    $nbrStatusAdded++;
                                }
                            }
                        }
                    }
                    //Add Log Activity
                    $dataInsertLogActivity0['name'] = 'Récupération des status du système Cash Plus';
                    $dataInsertLogActivity0['description'] = $description;
                    $dataInsertLogActivity0['id_entreprise'] = 0;
                    $this->db->insert('tblactivitylogcronstatuscashplus', $dataInsertLogActivity0);
                }
            }

            //Add Log Activity
            $dataInsertLogActivity1['name'] = 'Récupération des status du système Cash Plus';
            $dataInsertLogActivity1['description'] = $nbrStatusAdded . ' status ajouté';
            $dataInsertLogActivity1['id_entreprise'] = 0;
            $this->db->insert('tblactivitylogcronstatuscashplus', $dataInsertLogActivity1);

            //Date end
            $dateEnd = date('Y-m-d H:i:s');

            //Add Log Activity
            $this->log_activity('Récupération des status du système Cash Plus', $nbrStatusAdded . ' statuts ajouté', $dateStart, $dateEnd);
        }*/
    }
}

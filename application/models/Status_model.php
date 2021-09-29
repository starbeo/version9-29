
      <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Status_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $order_by = 'ASC', $limit = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        if ($id_E != 0) {
            $this->db->where('id_entreprise', $id_E);
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if ($id != '') {
            $this->db->where('id', $id);
            return $this->db->get('tblstatus')->row();
        }

        $this->db->order_by('id', $order_by);

        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }

        return $this->db->get('tblstatus')->result_array();
    }

    public function get_coli_by_barcode($barcode = '')
    {
        if (!empty($barcode)) {
            $this->db->select('tblcolis.*, tblstaff.phonenumber as telephone_livreur');
            $this->db->from('tblcolis');
            $this->db->join('tblstaff', 'tblstaff.staffid = tblcolis.livreur', 'left');
            $this->db->where('code_barre', $barcode);
            return $this->db->get()->row();
        }
    }

    public function get_motif_status($id = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $this->db->where('id_entreprise', $id_E);
        $this->db->where('motif', 1);
        $this->db->order_by('tblstatuscolis.name', 'asc');
        return $this->db->get('tblstatuscolis')->result_array();
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update status sent Active/Inactive
     */
    public function update_sent_status($id, $sent)
    {
        $this->db->where('id', $id);
        $this->db->update('tblstatus', array('sent' => $sent));
        if ($this->db->affected_rows() > 0) {
            logActivity('SMS Envoyé [StatusID: ' . $id . ' Sent(Active/Inactive): ' . $sent . ']');
            return true;
        }

        return false;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new status
     */
    public function add($data)
    {
        //Check if coli id exist
        if (isset($data['coli_id']) && is_numeric($data['coli_id'])) {
            $coliId = $data['coli_id'];
        }
        //Check if client id exist
        if (isset($data['clientid']) && is_numeric($data['clientid'])) {
            $toclientid = $data['clientid'];
            unset($data['clientid']);
        }
        //Check if telephone exist
        if (isset($data['telephone']) && !empty($data['telephone'])) {
            $telephone = $data['telephone'];
            unset($data['telephone']);
        }
        //Check if crbt exist
        $crbt = '';
        if (isset($data['crbt']) && !empty($data['crbt'])) {
            $crbt = $data['crbt'];
            unset($data['crbt']);
        }

        //Champs manuelle
        $data['date_created'] = date('Y-m-d H:i:s');
        $data['id_utilisateur'] = get_staff_user_id();
        $data['id_entreprise'] = get_entreprise_id();
        //Vérification du champ code barre
        $data['code_barre'] = $data['code_barre_verifie'];
        unset($data['code_barre_verifie']);
        //Vérification du champ date reporte
        if (!isset($data['date_reporte']) || empty($data['date_reporte'])) {
            unset($data['date_reporte']);
        }
        //Vérification du champ motif
        if (!isset($data['motif']) || empty($data['motif'])) {
            $data['motif'] = 0;
        }
        //Annulation des champs
        unset($data['id']);
        unset($data['coli_id']);
        //Ajout du statut
        $this->db->insert('tblstatus', $data);
        $insertId = $this->db->insert_id();
        if ($insertId) {
            //Activity log add status
            logActivity('Nouveau Status Ajouté [Code Barre: ' . $data['code_barre'] . ', ID: ' . $insertId . ']');

            $cpt = 0;
            $status = $data['type'];
            if ($status == 2) {
                $cpt = 1;
                $etat = 1;
                $dateLivraison = date('Y-m-d');
                $dateRetour = NULL;
            } else if ($status == 3) {
                $cpt = 1;
                $etat = 1;
                $dateLivraison = NULL;
                $dateRetour = date('Y-m-d');
            } else {
                $etat = 1;
            }

            if ($cpt == 1) {
                //Update status colis
                $this->db->where('code_barre', $data['code_barre']);
                $this->db->update('tblcolis', array(
                    'status_reel' => $status,
                    'status_id' => $status,
                    'etat_id' => $etat,
                    'date_livraison' => $dateLivraison,
                    'date_retour' => $dateRetour,
                   // 'num_facture' => NULL
                ));
            } else {
                //Update status colis par defaut
                $this->db->where('code_barre', $data['code_barre']);
                $this->db->update('tblcolis', array(
                    'status_reel' => $status,
                    'status_id' => 1,
                    'etat_id' => $etat,
                    'date_livraison' => NULL,
                    'date_retour' => NULL,
                   // 'num_facture' => NULL
                ));
            }

            //Récupération du numéro du client s'il n'existe pas
            if (!isset($toclientid)) {
                $toclientid = get_client_id($data['code_barre']);
            }

            // Send SMS
            if (is_numeric($status) && !empty($telephone)) {
                $this->load->model('sms_model');
                $sms = $this->sms_model->get("", 1, "automatic_sending = 'Automatique' AND status_id = " . $status);
                if (is_array($sms) && count($sms) > 0) {
                    $messageSms = $sms[0]['message'];
                    if (!empty($messageSms) && !empty($data['code_barre'])) {
                        $messageSms = $this->sms_model->parse_message($messageSms, $toclientid, false, $data['code_barre']);
                        $send = send_sms_to_recipient($telephone, $messageSms);
                        if ($send) {
                            $sent = 1;
                            //Update sent sms status
                            $this->db->where('id', $insertId);
                            $this->db->update('tblstatus', array('sent' => $sent));
                            //Add log activity
                            logActivity('Sms envoyé [Téléphone : ' . $telephone . ', Code Barre: ' . $data['code_barre'] . ', ID Status: ' . $insertId . ', Status : ' . $status . ']');
                        } else {
                            $sent = 0;
                            //Add log activity
                            logActivity('Sms non envoyé [Téléphone : ' . $telephone . ', Code Barre: ' . $data['code_barre'] . ', ID Status: ' . $insertId . ', Status : ' . $status . ']');
                        }
                        //Add sms log activity
                        logActivitySms($data['code_barre'], $status, $messageSms, $sent);
                    }
                }
            }

            //Si le statut est refusé mettre le prix de la coli refusé dans le champ anc_crbt et mettre la valeur 0 dans le champ crbt 
            if ($status == 9) {
                //Récupération du numéro de la coli s'il n'existe pas
                if (!isset($coliId) || !is_numeric($coliId)) {
                    $this->db->where('code_barre', $data['code_barre']);
                    $colis = $this->db->get('tblcolis')->row();
                    if ($colis) {
                        $coliId = $colis->id;
                        if (empty($crbt)) {
                            $crbt = $colis->crbt;
                        }
                    }
                }

     $this->db->where('code_barre', $data['code_barre']);
                $colis = $this->db->get('tblcolis')->row();
                if ($colis) {
                    $coliId = $colis->id;
                    if (empty($crbt)) {
                        $crbt =$colis->crbt;

                    }
                    $frais = $colis->frais;
                }


                $fraisRefuseClient = get_option('frais_colis_refuse_par_defaut');
                if (is_numeric($toclientid)) {
                    $fraisRefuseClient = get_client_frais_refuse($toclientid);
                }
                $this->db->where('id', $coliId);
                $this->db->update('tblcolis', array('crbt' => 0, 'anc_crbt' => $crbt, 'frais' => $fraisRefuseClient,'anc_frais'=>$frais));
            }

            if (is_numeric($toclientid)) {
                //Ajout d'une notification au client
                $_data['code_barre'] = $data['code_barre'];
                $_data['description'] = 'Nouveau Status Ajouté [Code Barre: <b>' . $data['code_barre'] . '</b>, Status : <b style="color: ' . get_status_color($status) . '">' . get_status_name($status) . '</b>';
                //Check if data motif is not empty or if data date report is not empty
                if (isset($data['motif']) && !empty($data['motif'])) {
                    $_data['description'] .= ', Motif : ' . get_status_name($data['motif']);
                } else if (isset($data['date_reporte']) && !empty($data['date_reporte'])) {
                    $_data['description'] .= ', Date reportation : ' . date('d/m/Y', strtotime($data['date_reporte']));
                }
                $_data['description'] .= ']';
                $_data['toclientid'] = $toclientid;
                $_data['link'] = site_url('expediteurs/colis/false/' . $data['code_barre']);
                add_notification_customer($_data);
            }

            return $insertId;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update status to database
     */
    public function update($data, $id)
    {
        //Check if coli id exist
        if (isset($data['coli_id']) && is_numeric($data['coli_id'])) {
            $coliId = $data['coli_id'];
            unset($data['coli_id']);
        }
        //Check if client id exist
        if (isset($data['clientid']) && is_numeric($data['clientid'])) {
            $toclientid = $data['clientid'];
            unset($data['clientid']);
        }
        //Check if telephone exist
        if (isset($data['telephone']) && !empty($data['telephone'])) {
            $telephone = $data['telephone'];
            unset($data['telephone']);
        }
        //Check if crbt exist
        $crbt = '';
        if (isset($data['crbt']) && !empty($data['crbt'])) {
            $crbt = $data['crbt'];
            unset($data['crbt']);
        }

        //Vérification du champ code barre
        $data['code_barre'] = $data['code_barre_verifie'];
        unset($data['code_barre_verifie']);
        //Vérification du champ date reporte
        if (!isset($data['date_reporte']) || empty($data['date_reporte'])) {
            unset($data['date_reporte']);
        }
        //Vérification du champ motif
        if (!isset($data['motif']) || empty($data['motif'])) {
            $data['motif'] = 0;
        }
        //Modification du statut
        $this->db->where('id', $id);
        $this->db->update('tblstatus', $data);
        if ($this->db->affected_rows() > 0) {
            //Activity log add status
            logActivity('Status Modifié [Code Barre: ' . $data['code_barre'] . ', ID: ' . $id . ']');

            $cpt = 0;
            $status = $data['type'];
            if ($status == 2) {
                $cpt = 1;
                $etat = 1;
                $dateLivraison = date('Y-m-d');
                $dateRetour = NULL;
            } else if ($status == 3) {
                $cpt = 1;
                $etat = 1;
                $dateLivraison = NULL;
                $dateRetour = date('Y-m-d');
            } else {
                $etat = 1;
            }

            if ($cpt == 1) {
                //Update status colis
                $this->db->where('code_barre', $data['code_barre']);
                $this->db->update('tblcolis', array(
                    'status_reel' => $status,
                    'status_id' => $status,
                    'etat_id' => $etat,
                    'date_livraison' => $dateLivraison,
                    'date_retour' => $dateRetour,
                   // 'num_facture' => NULL
                ));
            } else {
                //Update status colis par defaut
                $this->db->where('code_barre', $data['code_barre']);
                $this->db->update('tblcolis', array(
                    'status_reel' => $status,
                    'status_id' => 1,
                    'etat_id' => $etat,
                    'date_livraison' => NULL,
                    'date_retour' => NULL,
                    //'num_facture' => NULL
                ));
            }

            //Récupération du numéro du client s'il n'existe pas
            if (!isset($toclientid)) {
                $toclientid = get_client_id($data['code_barre']);
            }

            // Send SMS
            if (is_numeric($status) && !empty($telephone)) {
                $this->load->model('sms_model');
                $sms = $this->sms_model->get("", 1, "automatic_sending = 'Automatique' AND status_id = " . $status);
                if (is_array($sms) && count($sms) > 0) {
                    $messageSms = $sms[0]['message'];
                    if (!empty($messageSms) && !empty($data['code_barre'])) {
                        $messageSms = $this->sms_model->parse_message($messageSms, $toclientid, false, $data['code_barre']);
                        $send = send_sms_to_recipient($telephone, $messageSms);
                        if ($send) {
                            $sent = 1;
                            //Update sent sms status
                            $this->db->where('id', $id);
                            $this->db->update('tblstatus', array('sent' => $sent));
                            //Add log activity
                            logActivity('Sms envoyé [Téléphone : ' . $telephone . ', Code Barre: ' . $data['code_barre'] . ', ID Status: ' . $id . ', Status : ' . $status . ']');
                        } else {
                            $sent = 0;
                            //Add log activity
                            logActivity('Sms non envoyé [Téléphone : ' . $telephone . ', Code Barre: ' . $data['code_barre'] . ', ID Status: ' . $id . ', Status : ' . $status . ']');
                        }
                        //Add sms log activity
                        logActivitySms($data['code_barre'], $status, $messageSms, $sent);
                    }
                }
            }

            //Si le statut est refusé mettre le prix de la coli refusé dans le champ anc_crbt et mettre la valeur 0 dans le champ crbt 
            if ($status == 9) {
                //Récupération du numéro de la coli s'il n'existe pas
                if (!isset($coliId) || !is_numeric($coliId)) {
                    $this->db->where('code_barre', $data['code_barre']);
                    $colis = $this->db->get('tblcolis')->row();
                    if ($colis) {
                        $coliId = $colis->id;
                        if (empty($crbt)) {
                            $crbt = $colis->crbt;
                        }
                    }
                }



         $this->db->where('code_barre', $data['code_barre']);
                $colis = $this->db->get('tblcolis')->row();
                if ($colis) {
                    $coliId = $colis->id;
                    if (empty($crbt)) {
                        $crbt =$colis->crbt;

                    }
                    $frais = $colis->frais;
                }



                $fraisRefuseClient = get_option('frais_colis_refuse_par_defaut');
                if (is_numeric($toclientid)) {
                    $fraisRefuseClient = get_client_frais_refuse($toclientid);
                }
                $this->db->where('id', $coliId);
                $this->db->update('tblcolis', array('crbt' => 0, 'anc_crbt' => $crbt, 'frais' => $fraisRefuseClient,'anc_frais'=>$frais));
            }

            if (is_numeric($toclientid)) {
                //Ajout d'une notification au client
                $_data['code_barre'] = $data['code_barre'];
                $_data['description'] = 'Nouveau Status Ajouté [Code Barre: <b>' . $data['code_barre'] . '</b>, Status : <b style="color: ' . get_status_color($status) . '">' . get_status_name($status) . '</b>';
                //Check if data motif is not empty or if data date report is not empty
                if (isset($data['motif']) && !empty($data['motif'])) {
                    $_data['description'] .= ', Motif : ' . get_status_name($data['motif']);
                } else if (isset($data['date_reporte']) && !empty($data['date_reporte'])) {
                    $_data['description'] .= ', Date reportation : ' . date('d/m/Y', strtotime($data['date_reporte']));
                }
                $_data['description'] .= ']';
                $_data['toclientid'] = $toclientid;
                $_data['link'] = site_url('expediteurs/colis/' . $data['code_barre']);
                add_notification_customer($_data);
            }

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete status from database, if used return array with key referenced
     */
    public function delete($id)
    {
        $lastStatus = $this->get($id);

        $this->db->where('id', $id);
        $this->db->delete('tblstatus');
        if ($this->db->affected_rows() > 0) {
            if ($lastStatus) {
                $beforeLastStatus = $this->get('', 'code_barre = "' . $lastStatus->code_barre . '"', 'DESC', 1);
                if ($beforeLastStatus) {
                    $statusId = $beforeLastStatus[0]['type'];
                    if (!is_numeric($statusId)) {
                        $statusId = 5;
                        //Ajouter statut "Ramassé" par defaut
                        $dataStatus = [];
                        $dataStatus['code_barre_verifie'] = $lastStatus->code_barre;
                        $dataStatus['type'] = $statusId;
                        $dataStatus['emplacement_id'] = 9;
                        $this->load->model('status_model');
                        $this->add($dataStatus);
                    }

                    //Modification du statut et l'etat de la coli
                    $this->db->where('code_barre', $lastStatus->code_barre);
                    $this->db->update('tblcolis', array(
                        'etat_id' => 1,
                        'status_id' => 1,
                        'status_reel' => $statusId,
                        'date_livraison' => NULL,
                        'date_retour' => NULL
                        )
                    );
                }
            }

            logActivity(_l('status') . _l('deleted') . ' [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    public function suivi_colis($barcode)
    {
        $this->db->select('tblstatuscolis.name, tblstatuscolis.color, tbllocations.name as emplacement, tblstatus.date_created');
        $this->db->from('tblstatus');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblstatus.type', 'left');
        $this->db->join('tbllocations', 'tbllocations.id = tblstatus.emplacement_id', 'left');
        $this->db->where('tblstatus.code_barre', $barcode);
        $this->db->order_by('tblstatus.id', 'asc');
        return $this->db->get()->result_array();
    }
}



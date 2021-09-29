<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $orderBy = '', $limit = '')
    {
        $this->db->select('tblfactures.*, tblstatuscolis.name');
        $this->db->from('tblfactures');
        $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblfactures.type', 'left');

        if ($id != '') {
            $this->db->where('tblfactures.id', $id);
            $facture = $this->db->get()->row();
            if ($facture) {
                $facture->items = $this->get_items_facture($id);
                $this->load->model('expediteurs_model');
                $facture->client = $this->expediteurs_model->get($facture->id_expediteur);
            }
            return $facture;
        }

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        if (!empty($orderBy)) {
            $this->db->order_by($orderBy);
        }

        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result_array();
    }

    public function get_items_facture($id = '')
    {
        $this->db->select('tblcolis.num_commande,tblcolis.code_barre, tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.status_id, tblcolis.status_reel, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_reel) as status_reel_name, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_id) as status, tblcolis.etat_id, (SELECT name FROM tbletatcolis WHERE id = tblcolis.etat_id) as etat, tblcolis.crbt,  tblcolis.anc_crbt, tblvilles.name as ville_name, tblcolis.frais, (SELECT tblstatuscolis.name FROM tblstatuscolis Left join tblstatus on tblstatus.motif = tblstatuscolis.id WHERE tblstatus.motif = tblstatuscolis.id AND tblcolis.code_barre = tblstatus.code_barre ORDER BY tblstatus.id DESC LIMIT 1) as motif');
        $this->db->from('tblcolisfacture');
        $this->db->join('tblcolis', 'tblcolis.id = tblcolisfacture.colis_id', 'left');
        $this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->where('facture_id', $id);
        $this->db->order_by('tblcolis.status_id', 'desc');
        return $this->db->get()->result_array();
    }

    public function get_factures_by_clientid($where = array())
    {
        $this->db->select('id, nom as name');

        if (is_array($where)) {
            if (sizeof($where) > 0) {
                $this->db->where($where);
            }
        } else if (strlen($where) > 0) {
            $this->db->where($where);
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get('tblfactures')->result_array();
    }

    public function get_facture_export()
    {
        $query = 'SELECT tblfactures.nom as "Nom_facture", tblfactures.date_created, tblexpediteurs.nom as "client", 
                (case tblfactures.type when 2 then "Livre" when 3 then "Retourne" end ) as  "type_facture",
                (select count(tblcolis.id) FROM tblcolisfacture, tblcolis where tblcolisfacture.facture_id = tblfactures.id and tblcolis.id = tblcolisfacture.colis_id group by tblfactures.id) as "Nombre_colis",
                (select sum(tblcolis.crbt) FROM tblcolisfacture, tblcolis where tblcolisfacture.facture_id = tblfactures.id and tblcolis.id = tblcolisfacture.colis_id group by tblfactures.id) as "Crbt",
                (select sum(tblcolis.frais) FROM tblcolisfacture, tblcolis where tblcolisfacture.facture_id = tblfactures.id and tblcolis.id = tblcolisfacture.colis_id group by tblfactures.id) as "Frais",
                (select sum(tblcolis.crbt-tblcolis.frais) FROM tblcolisfacture, tblcolis where tblcolisfacture.facture_id=tblfactures.id and tblcolis.id=tblcolisfacture.colis_id group by tblfactures.id) as "Total_net"
                FROM tblfactures,tblexpediteurs 
                WHERE tblfactures.id_expediteur=tblexpediteurs.id 
                ORDER BY tblfactures.id desc LIMIT 200';
        return $this->db->query($query)->result_array();
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new facture
     */
    public function add($data)
    {
        //Load Model
        $this->load->model('expediteurs_model');

        $colis = array();
        if (isset($data['checked_products'])) {
            $colis = $data['checked_products'];
            unset($data['checked_products']);
        }

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        //Insertion de l'ID de l'utilisateur
        $data['id_utilisateur'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');
        //Generate status
        if ($data['type'] == 2) {
            $data['status'] = 1;
        } else {
            $data['status'] = 2;
        }
        //Add delivery men to invoice returned
        if ($data['type'] == 3 && is_numeric($data['id_expediteur'])) {
            $expediteur = $this->expediteurs_model->get($data['id_expediteur']);
            if ($expediteur && is_numeric($expediteur->livreur)) {
                $data['id_livreur'] = $expediteur->livreur;
            }
        }

        $insert_id = null;
        if (count($colis) > 0) {
            $this->db->insert('tblfactures', $data);
            $insert_id = $this->db->insert_id();
        }

        if (isset($insert_id)) {
            //Add List New Colis To facture
            if (count($colis) > 0) {
                $affected_rows = 0;
                $totalColisParrainage = 0;
                $totalCrbt = 0;
                $totalFrais = 0;
                $totalRefuse = 0;
                $this->load->model('colis_model');
                foreach ($colis as $c) {
                    //check if colis exists in list colis bon livraison
                    $exists = total_rows('tblcolisfacture', array('colis_id' => $c, 'id_entreprise' => $data['id_entreprise']));
                    //Get Coli
                    $coli = $this->colis_model->get($c);
                    if ($exists == 0 || ($coli && ($coli->status_reel == 9 || $coli->status_reel == 13))) {
                        //Add colis to list colis facture
                        $this->db->insert('tblcolisfacture', array(
                            'facture_id' => $insert_id,
                            'colis_id' => $c,
                            'date_created' => $data['date_created'],
                            'id_utilisateur' => $data['id_utilisateur'],
                            'id_entreprise' => $data['id_entreprise']
                            )
                        );
                        if ($this->db->affected_rows() > 0) {
                            //Get Coli
                            $coli = $this->colis_model->get($c);
                            if ($data['type'] == 2) {
                                if ($coli && is_numeric($coli->crbt) && is_numeric($coli->frais)) {
                                    if ($coli->status_reel == 9) {
                                        $totalRefuse += $coli->frais;
                                    } else {
                                        $totalCrbt += $coli->crbt;
                                        $totalFrais += $coli->frais;
                                    }
                                }
                                //Update colis
                                $this->db->where('id', $c);
                                $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture' => $insert_id));
                                //Increment colis parrainage
                                $totalColisParrainage++;
                            } else if ($data['type'] == 3) {
                                //Add status colis
                                $this->db->insert('tblstatus', array(
                                    'code_barre' => $coli->code_barre,
                                    'type' => 3,
                                    'date_created' => date('Y-m-d H:i:s'),
                                    'emplacement_id' => 6,
                                    'id_utilisateur' => $data['id_utilisateur'],
                                    'id_entreprise' => $data['id_entreprise']
                                ));
                                //Update colis
                                $this->db->where('id', $c);
                                $nmfacture = $this->db->get('tblcolis')->row()->num_facture;
                                if (!is_null($nmfacture)) {
                                    $this->db->where('id', $c);
                                    $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture_re' => $insert_id, 'status_id' => 3, 'status_reel' => 3, 'date_livraison' => NULL, 'date_retour' => date('Y-m-d')));
                            } else {
                                    $this->db->where('id', $c);
                                    $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture' => $insert_id, 'status_id' => 3, 'status_reel' => 3, 'date_livraison' => NULL, 'date_retour' => date('Y-m-d')));

                                }
                            }
                            $affected_rows++;
                        }
                    }
                }

                $codeAffiliation = '';
                //Stock total colis parrainage
                if (is_numeric($data['id_expediteur']) && $totalColisParrainage > 0) {
                    $client = $this->expediteurs_model->get($data['id_expediteur']);
                    if ($client) {
                        if (!is_null($client->affiliation_code) && !empty($client->affiliation_code)) {
                            $codeAffiliation = $client->affiliation_code;
                            $clientParent = $this->expediteurs_model->get_by_affiliation_code($codeAffiliation);
                            if ($clientParent) {
                                // Update total parrainage client
                                $this->db->set('total_colis_parrainage', 'total_colis_parrainage + ' . $totalColisParrainage, FALSE);
                                $this->db->where('id', $clientParent->id);
                                $this->db->update('tblexpediteurs');
                            }
                        }
                    }
                }

                //Get total parrainage
                $totalParrainage = 0;
                $fraisParrainage = get_option('frais_parrainage');
                if (!empty($codeAffiliation) && is_numeric($fraisParrainage) && $fraisParrainage > 0) {
                    $client = $this->expediteurs_model->get($data['id_expediteur']);
                    if ($client) {
                        $totalParrainage += $fraisParrainage * $client->total_colis_parrainage;
                        // Update total parrainage client
                        $this->db->set('total_colis_parrainage', 0);
                        $this->db->where('id', $client->id);
                        $this->db->update('tblexpediteurs');
                    }
                }

                //Calcule Total Net
                $totalNet = $totalCrbt - $totalFrais - $totalRefuse + $totalParrainage;
                //Update Facture
                $nom = 'FCT-' . date('dmY') . '-' . $insert_id;
                $this->db->where('id', $insert_id);
                $this->db->update('tblfactures', array('nom' => $nom, 'total_crbt' => $totalCrbt, 'total_frais' => $totalFrais, 'total_refuse' => $totalRefuse, 'total_parrainage' => $totalParrainage, 'total_net' => $totalNet));

                if ($affected_rows > 0) {
                    logActivity('Nouveau Liste Colis Facture Ajouté [FACTURE :' . $nom . ', ID: ' . $insert_id . ']');
                }
            }

            logActivity('Nouveau Facture Ajouté [' . $nom . ', ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update facture to database
     */
    public function update($data, $id)
    {
        //Get Facture
        $facture = $this->get($id);

        $affected_rows_1 = 0;
        unset($data['isedit']);

        $colis = array();
        if (isset($data['checked_products'])) {
            $colis = $data['checked_products'];
            unset($data['checked_products']);
        }

        if (isset($data['remise']) && $data['remise'] > 0) {
            $remiseType = $data['remise_type'];
            $remise = $data['remise'];
        }

        $this->db->where('id', $id);
        $this->db->update('tblfactures', $data);
        if ($this->db->affected_rows() > 0) {
            $affected_rows_1++;
            logActivity('Facture modifié [Nom: ' . $data['nom'] . ', ID: ' . $id . ']');
        }

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        //Insertion de l'ID de l'utilisateur
        $data['id_utilisateur'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');

        //Add List New Colis To facture
        $affected_rows_2 = 0;
        $totalColisParrainage = 0;
        $totalCrbt = $facture->total_crbt;
        $totalFrais = $facture->total_frais;
        $totalRefuse = $facture->total_refuse;
        $totalParrainage = $facture->total_parrainage;
        $this->load->model('colis_model');
        foreach ($colis as $c) {
            //check if colis exists in list colis bon livraison
            $exists = total_rows('tblcolisfacture', array('colis_id' => $c, 'id_entreprise' => $data['id_entreprise']));
            //Get Coli
            $coli = $this->colis_model->get($c);
            if ($exists == 0 || ($coli && ($coli->status_reel == 9 || $coli->status_reel == 13))) {
                //Add colis to list colis facture
                $this->db->insert('tblcolisfacture', array(
                    'facture_id' => $id,
                    'colis_id' => $c,
                    'date_created' => $data['date_created'],
                    'id_utilisateur' => $data['id_utilisateur'],
                    'id_entreprise' => $data['id_entreprise']
                    )
                );
                if ($this->db->affected_rows() > 0) {
                    if ($facture->type == 2) {
                        if ($coli && is_numeric($coli->crbt) && is_numeric($coli->frais)) {
                            if ($coli->status_reel == 9) {
                                $totalRefuse += $coli->frais;
                            } else {
                                $totalCrbt += $coli->crbt;
                                $totalFrais += $coli->frais;
                            }
                        }
                        //Update colis
                        $this->db->where('id', $c);
                        $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture' => $id));
                        //Increment colis parrainage
                        $totalColisParrainage++;
                    } else if ($data['type'] == 3) {
                        //Add status colis
                        $this->db->insert('tblstatus', array(
                            'code_barre' => $coli->code_barre,
                            'type' => 3,
                            'date_created' => date('Y-m-d H:i:s'),
                            'emplacement_id' => 6,
                            'id_utilisateur' => $data['id_utilisateur'],
                            'id_entreprise' => $data['id_entreprise']
                        ));
                        //Update colis
                        $this->db->where('id', $c);
                        $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture' => $id, 'status_id' => 3, 'status_reel' => 3));
                    }
                    //Update Total (crbt, frais, reste) facture interne
                    if ($facture) {
                        if (is_numeric($facture->num_factureinterne)) {
                            $this->update_total_facture_interne($facture->num_factureinterne, $c);
                        }
                    }
                    $affected_rows_2++;
                }
            }
        }
        
        //Stock total colis parrainage
        if (is_numeric($data['id_expediteur']) && $totalColisParrainage > 0) {
            $client = $this->expediteurs_model->get($data['id_expediteur']);
            if ($client) {
                if (!is_null($client->affiliation_code) && !empty($client->affiliation_code)) {
                    $clientParent = $this->expediteurs_model->get_by_affiliation_code($client->affiliation_code);
                    if ($clientParent) {
                        // Update total parrainage client
                        $this->db->set('total_colis_parrainage', 'total_colis_parrainage + ' . $totalColisParrainage, FALSE);
                        $this->db->where('id', $clientParent->id);
                        $this->db->update('tblexpediteurs');
                    }
                }
            }
        }
                
        //Calcule TOTAL net
        $totalNet = $totalCrbt - $totalFrais - $totalRefuse + $totalParrainage;
        //Calcule Discount
        $totalRemise = 0;
        if (isset($remise) && $remise > 0) {
            if ($remiseType == 'fixed_amount') {
                $totalRemise += $remise;
            } else {
                $totalRemise += $totalRefuse * ($remise / 100);
            }
        }
        $totalNet += $totalRemise;
        //Update Facture
        $this->db->where('id', $id);
        $this->db->update('tblfactures', array('total_crbt' => $totalCrbt, 'total_frais' => $totalFrais, 'total_refuse' => $totalRefuse, 'total_remise' => $totalRemise, 'total_net' => $totalNet));

        if ($affected_rows_2 > 0) {
            logActivity('Nouveau Liste Colis Facture Ajouté [Nom Facture :' . $data['nom'] . ', ID: ' . $id . ']');
        }

        if ($affected_rows_1 > 0 || $affected_rows_2 > 0) {
            return true;
        }

        return false;
    }

    public function update_comment($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tblfactures', array('commentaire' => $data['commentaire']));
        if ($this->db->affected_rows() > 0) {
            logActivity('Commentaire facture Modifié [ID:' . $data['id'] . ']');
            return true;
        }
        return false;
    }

    public function add_additionnal_line($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->set('description_line', $data['description_line']);
        $this->db->set('total_line', $data['total_line'], FALSE);
        $this->db->set('total_net', 'total_net - ' . $data['total_line'], FALSE);
        $this->db->update('tblfactures');
        if ($this->db->affected_rows() > 0) {
            logActivity('Ajout d\'une ligne supplémentaire dans la facture [ID:' . $data['id'] . ']');
            return true;
        }
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete facture from database, if used return array with key referenced
     */
    public function delete($id)
    {
        do_action('before_facture_deleted', $id);
        //Get Facture
        $facture = $this->get($id);

        //Delete facture
        $this->db->where('id', $id);
        $this->db->delete('tblfactures');
        if ($this->db->affected_rows() > 0) {
            //Delete colis facture
            $this->db->where('facture_id', $id);
            $this->db->delete('tblcolisfacture');
            //Update colis affeted to this facture
            if ($facture) {
                //Update numero facture to colis
                if ($facture->type == 2) {
                    $etat_id = 2;
                } else {
                    $etat_id = 1;
                }
                $this->db->where('num_facture', $id);
                $this->db->update('tblcolis', array('num_facture' => null, 'etat_id' => $etat_id));
            }

            logActivity('Facture Supprimé [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete colis facture from database, if used return array with key referenced
     */
    public function delete_colis_facture($id)
    {
        do_action('before_colis_facture_deleted', $id);
        //Get ID Colis & ID Facture
        $this->db->where('id', $id);
        $colis_facture = $this->db->get('tblcolisfacture')->row();
        $colis_id = $colis_facture->colis_id;
        $facture_id = $colis_facture->facture_id;

        //Delete colis facture
        $this->db->where('id', $id);
        $this->db->delete('tblcolisfacture');
        if ($this->db->affected_rows() > 0) {
            //Get Facture by ID Facture
            $facture = $this->get($facture_id);
            if ($facture) {
                //Get Colis by ID Colis
                $this->db->where('id', $colis_id);
                $colis = $this->db->get('tblcolis')->row();

                if ($facture->type == 2) {
                    //Update Colis
                    if ($colis->status_id == 2 || $colis->status_reel == 9) {
                        $this->db->where('id', $colis_id);
                        $this->db->update('tblcolis', array('etat_id' => 2, 'num_facture' => NULL));
                    }
                    //Update Facture
                    if ($colis->status_reel == 9) {
                        $totalRefuse = $facture->total_refuse - $colis->frais;
                        //Calcule TOTAL net
                        $totalNet = $facture->total_crbt - $facture->total_frais - $totalRefuse;
                        //Calcule Discount
                        if (isset($facture->remise) && $facture->remise > 0) {
                            if ($facture->remise_type == 'fixed_amount') {
                                $totalNet += $facture->remise;
                            } else {
                                $totalNet += $totalRefuse * ($facture->remise / 100);
                            }
                        }
                        //Update Total Facture
                        $this->db->where('id', $facture_id);
                        $this->db->update('tblfactures', array('total_refuse' => $totalRefuse, 'total_net' => $totalNet));
                    } else {
                        $totalCrbt = $facture->total_crbt - $colis->crbt;
                        $totalFrais = $facture->total_frais - $colis->frais;
                        //Calcule TOTAL net
                        $totalNet = $totalCrbt - $totalFrais - $facture->total_refuse;
                        //Calcule Discount
                        if (isset($facture->remise) && $facture->remise > 0) {
                            if ($facture->remise_type == 'fixed_amount') {
                                $totalNet += $facture->remise;
                            } else {
                                $totalNet += $facture->total_refuse * ($facture->remise / 100);
                            }
                        }
                        //Update Total Facture
                        $this->db->where('id', $facture_id);
                        $this->db->update('tblfactures', array('total_crbt' => $totalCrbt, 'total_frais' => $totalFrais, 'total_net' => $totalNet));
                    }

                    //Stock total colis parrainage
                    if (is_numeric($facture->id_expediteur)) {
                        $client = $this->expediteurs_model->get($facture->id_expediteur);
                        if ($client) {
                            if (!is_null($client->affiliation_code) && !empty($client->affiliation_code)) {
                                $clientParent = $this->expediteurs_model->get_by_affiliation_code($client->affiliation_code);
                                if ($clientParent) {
                                    // Update total parrainage client
                                    $this->db->set('total_colis_parrainage', 'total_colis_parrainage - 1', FALSE);
                                    $this->db->where('id', $clientParent->id);
                                    $this->db->update('tblexpediteurs');
                                }
                            }
                        }
                    }
                } else if ($facture->type == 3) {
                    //Update Colis
                    $statusColisBeforeStatusReturned = get_status_colis_before_status_returned($colis->code_barre);
                    if ($colis->status_id == 3 && is_numeric($statusColisBeforeStatusReturned)) {
                        if (remove_last_status_returned_affected_to_colis($colis->code_barre)) {
                            $this->db->where('id', $colis_id);
                            $this->db->update('tblcolis', array('status_id' => 1, 'status_reel' => $statusColisBeforeStatusReturned, 'etat_id' => 3, 'num_facture' => NULL));
                        } else {
                            $this->db->where('id', $colis_id);
                            $this->db->update('tblcolis', array('etat_id' => 1, 'num_facture' => NULL));
                        }
                    } else if ($colis->status_id == 3) {
                        $this->db->where('id', $colis_id);
                        $this->db->update('tblcolis', array('etat_id' => 1, 'num_facture' => NULL));
                    }
                }
                //Update Total Facture Interne (crbt, frais, reste) facture interne
                if (is_numeric($facture->num_factureinterne)) {
                    $this->update_total_facture_interne($facture->num_factureinterne, $colis_id, false);
                }
            }

            logActivity('Colis Facture Supprimé [ID: ' . $id . ']');
            return $facture_id;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Update Total facture interne from database
     */
    public function update_total_facture_interne($facture_interne_id, $colis_id, $add = true)
    {
        //Get Colis
        $this->load->model('colis_model');
        $colis = $this->colis_model->get($colis_id);
        if ($colis) {
            //CRBT & Frais
            $crbt = $colis->crbt;
            $frais = $colis->frais;
            //Add Total CRBT & Total Frais
            $this->db->where('id', $facture_interne_id);
            if ($add == true) {
                $this->db->set('total', 'total+' . $crbt, FALSE);
                $this->db->set('total_frais', 'total_frais+' . $frais, FALSE);
                $this->db->set('rest', 'total_received-(total-total_frais)', FALSE);
            } else {
                $this->db->set('total', 'total-' . $crbt, FALSE);
                $this->db->set('total_frais', 'total_frais-' . $frais, FALSE);
                $this->db->set('rest', 'total_received-(total-total_frais)', FALSE);
            }
            $this->db->update('tblfacturesinternes');
        }
    }

    /**
     * Sent invoice to client
     * @param  integer  $id invoiceid
     * @param  string  $email email
     * @param  mixed  $attachpdf attach pdf
     * @return boolean
     * */
    public function sent_invoice_to_client($id, $email, $attachpdf = true)
    {
        if (is_numeric($id) && !empty($email)) {
            $this->load->model('emails_model');
            // Get invoice
            $invoice = $this->get($id);
            // Get PDF invoice
            $mpdf = facture_pdf($invoice, false);
            $attach = $mpdf->Output($invoice->nom . '.pdf', 'S');
            if ($attachpdf) {
                $this->emails_model->add_attachment(array(
                    'attachment' => $attach,
                    'filename' => $invoice->nom . '.pdf',
                    'type' => 'application/pdf'
                ));
            }

            $send = $this->emails_model->send_email_template('send-invoice-to-customer', $email, $invoice->id_expediteur, $id);
            if ($send) {
                $this->set_invoice_sent($id);
                logActivity('Email envoyé au [Email:' . $email . ']');
                return true;
            }
        }

        return false;
    }

    /**
     * Set invoice to sent when email is successfuly sended to client
     * @param mixed $id invoiceid
     * @return  boolean
     */
    public function set_invoice_sent($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblfactures', array(
            'sent' => 1,
            'datesend' => date('Y-m-d H:i:s')
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity(get_staff_full_name() . _l('sent_invoice_to_client') . ' [ID: ' . $id . ']');
            return true;
        }

        return false;
    }
}



<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Factures_etl_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get($id = '', $where = array(), $orderBy = '', $limit = '')
    {
        $this->db->select('tblfactures_etl.*');
        $this->db->from('tblfactures_etl');
    //    $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblfactures.type', 'left');

        if ($id != '') {
            $this->db->where('tblfactures_etl.id', $id);
            $facture = $this->db->get()->row();
            if ($facture) {
                $facture->items = $this->get_items_facture($id);
              //  $this->load->model('expediteurs_model');
                //$facture->client = $this->expediteurs_model->get($facture->id_expediteur);
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
   //     $this->db->select('tblcolis.num_commande,tblcolis.code_barre, tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.status_id, tblcolis.status_reel, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_reel) as status_reel_name, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_id) as status, tblcolis.etat_id, (SELECT name FROM tbletatcolis WHERE id = tblcolis.etat_id) as etat, tblcolis.crbt,  tblcolis.anc_crbt, tblvilles.name as ville_name, tblcolis.frais, (SELECT tblstatuscolis.name FROM tblstatuscolis Left join tblstatus on tblstatus.motif = tblstatuscolis.id WHERE tblstatus.motif = tblstatuscolis.id AND tblcolis.code_barre = tblstatus.code_barre ORDER BY tblstatus.id DESC LIMIT 1) as motif');
        $this->db->from('tbletatcolislivre');
       // $this->db->join('tblcolis', 'tblcolis.id = tblcolisfacture.colis_id', 'left');
        //$this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->where('facture_etl', $id);
        //$this->db->order_by('tblcolis.status_id', 'desc');
        return $this->db->get()->result_array();
    }


    public function get_items_facture11($id = '')
    {
   //     $this->db->select('tblcolis.num_commande,tblcolis.code_barre, tblcolis.date_ramassage, tblcolis.date_livraison, tblcolis.status_id, tblcolis.status_reel, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_reel) as status_reel_name, (SELECT name FROM tblstatuscolis WHERE id = tblcolis.status_id) as status, tblcolis.etat_id, (SELECT name FROM tbletatcolis WHERE id = tblcolis.etat_id) as etat, tblcolis.crbt,  tblcolis.anc_crbt, tblvilles.name as ville_name, tblcolis.frais, (SELECT tblstatuscolis.name FROM tblstatuscolis Left join tblstatus on tblstatus.motif = tblstatuscolis.id WHERE tblstatus.motif = tblstatuscolis.id AND tblcolis.code_barre = tblstatus.code_barre ORDER BY tblstatus.id DESC LIMIT 1) as motif');
        $this->db->from('tbletatfactures');
       // $this->db->join('tblcolis', 'tblcolis.id = tblcolisfacture.colis_id', 'left');
        //$this->db->join('tblvilles', 'tblvilles.id = tblcolis.ville', 'left');
        $this->db->where('etatfacture_id', $id);
        //$this->db->order_by('tblcolis.status_id', 'desc');
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

    public function add($data)
    {
        //Load Model
        $this->load->model('etat_colis_livrer_model');

        $etls = array();
        if (isset($data['checked_products'])) {
            $etls = $data['checked_products'];
            unset($data['checked_products']);
        }

        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');
        $data['id_entreprise'] = $id_E;
        //Insertion de l'ID de l'utilisateur
        $data['id_utilisateur'] = get_staff_user_id();
        $data['date_created'] = date('Y-m-d H:i:s');
        //Generate status
        $data['statu'] = 1;

        if (isset($data['id_expediteur'])) {
           $data['id_livreur']= $data['id_expediteur'];
            unset($data['id_expediteur']);
        }

        unset($data['type']);

        //Add delivery men to invoice returned
       // $data['id_livreur'] = $this->etat_colis_livrer_model->get_id_livreur_by_et_id($colis);


        $insert_id = null;
        if (count($etls) > 0) {
            $this->db->insert('tblfactures_etl', $data);
           $insert_id = $this->db->insert_id();
           // echo json_encode($colis);
        }

        if (isset($insert_id)) {
            //Add List New Colis To facture
            if (count($etls) > 0) {
                $affected_rows = 0;
                $totalColisParrainage = 0;
                $totalManque = 0;
                $totalCrbt = 0;
                $totalFrais = 0;
                $totalRefuse = 0;
                $totalcolisrefuse = 0;
                $totalcolislivre = 0;

                $this->load->model('etat_colis_livrer_model');
                foreach ($etls as $c) {
                    //check if etl exists in list colis bon livraison
                    $exists = total_rows('tbletatfactures', array('etat_id' => $c, 'id_entreprise' => $data['id_entreprise']));
                    //Get etl
                    $etl = $this->etat_colis_livrer_model->get($c);
                    if ($exists == 0 ) {
                        //Add etl to list etl facture
                        $this->db->insert('tbletatfactures', array(
                                'etatfacture_id' => $insert_id,
                                'etat_id' => $c,
                                'date_created' => $data['date_created'],
                                'id_utilisateur' => $data['id_utilisateur'],
                                'id_entreprise' => $data['id_entreprise']
                            )
                        );
                        if ($this->db->affected_rows() > 0) {
                            //Get Coli
                            $etl = $this->etat_colis_livrer_model->get($c);

                                if ($etl && is_numeric($etl->refuse_commision) && is_numeric($etl->commision)) {

                                        $totalRefuse += $etl->refuse_commision;
                                        $totalCrbt += $etl->commision;
                                        $totalManque += $etl->manque;
                                      //  $totalFrais += $etl ->total;
                                        $this->load->model('etat_colis_livrer_model');
                                        $totalcolislivre   +=  $this->etat_colis_livrer_model->getcolisrefuse($etl->id,2);
                                        $totalcolisrefuse +=  $this->etat_colis_livrer_model->getcolisrefuse($etl->id,9);


                                }
                                //Update colis
                            $this->db->where('id', $c);
                            $this->db->update('tbletatcolislivre', array('facture_etl' => $insert_id));
                              //  $this->db->where('id', $c);
                             //   $this->db->update('tblcolis', array('etat_id' => 3, 'num_facture' => $insert_id));
                                //Increment colis parrainage
                                $totalColisParrainage++;
                            }

                            $affected_rows++;
                        }
                    }



          $totalFrais = $totalManque +$totalCrbt +$totalRefuse ;

                //Calcule Total Net
               // $totalNet = $totalCrbt + $totalRefuse ;
                //Update Facture
                $nom = 'CML-' . date('dmY') . '-' . $insert_id;
                $this->db->where('id', $insert_id);
                $this->db->update('tblfactures_etl', array('nom' => $nom, 'total_frais' => $totalCrbt, 'total_line' => $totalFrais, 'total_refuse' => $totalRefuse,"total_manque" => $totalManque,"totalnbr_livre" =>$totalcolislivre,'totalnbr_refuse'=>$totalcolisrefuse));

             //   if ($affected_rows > 0) {
                 //   logActivity('Nouveau Liste Colis Facture Ajouté [FACTURE :' . $nom . ', ID: ' . $insert_id . ']');
              //  }
            }

          //  logActivity('Nouveau Facture Ajouté [' . $nom . ', ID: ' . $insert_id . ']');
            return $insert_id;
        }



        return false;
    }

    public function update($data, $id)
    {
        //Get Facture
        $facture = $this->get($id);

        $affected_rows_1 = 0;
        unset($data['isedit']);

        $etls = array();
        if (isset($data['checked_products'])) {
            $etls = $data['checked_products'];
            unset($data['checked_products']);
        }

        unset($data['type']);
        if (isset($data['id_expediteur'])) {
            $data['id_livreur']= $data['id_expediteur'];
            unset($data['id_expediteur']);
        }
        $this->db->where('id', $id);
        $this->db->update('tblfactures_etl', $data);
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

        $totalRefuse = $facture->total_refuse;
        $totalCrbt = $facture->total_frais;
        $totalManque = $facture->total_manque;
        $totalFrais = $facture->total_line;
        $totalcolisrefuse =$facture->totalnbr_refuse ;
        $totalcolislivre =$facture->totalnbr_livre ;

        $this->load->model('etat_colis_livrer_model');
        foreach ($etls as $c) {
            //check if etl exists in list colis bon livraison
            $exists = total_rows('tbletatfactures', array('etat_id' => $c, 'id_entreprise' => $data['id_entreprise']));
            //Get Coli
            $etl = $this->etat_colis_livrer_model->get($c);
            if ($exists == 0 ) {
                //Add colis to list colis facture
                $this->db->insert('tbletatfactures', array(
                        'etatfacture_id' => $id,
                        'etat_id' => $c,
                        'date_created' => $data['date_created'],
                        'id_utilisateur' => $data['id_utilisateur'],
                        'id_entreprise' => $data['id_entreprise']
                    )
                );
                if ($this->db->affected_rows() > 0) {

                    $totalRefuse += $etl->refuse_commision;
                    $totalCrbt += $etl->commision;
                    $totalManque += $etl->manque;
                    //$totalFrais += $etl ->total;

                    $this->load->model('etat_colis_livrer_model');
                    $totalcolislivre   +=  $this->etat_colis_livrer_model->getcolisrefuse($etl->id,2);
                    $totalcolisrefuse +=  $this->etat_colis_livrer_model->getcolisrefuse($etl->id,9);

                    $this->db->where('id', $c);
                    $this->db->update('tbletatcolislivre', array('facture_etl' => $id));
                    $affected_rows_2++;
                }
            }
        }


        $totalFrais = $totalManque +$totalCrbt +$totalRefuse ;

        //Update Facture
        $this->db->where('id', $id);
        $this->db->update('tblfactures_etl',  array( 'total_frais' => $totalCrbt, 'total_line' => $totalFrais, 'total_refuse' => $totalRefuse,"total_manque" => $totalManque,"totalnbr_livre" =>$totalcolislivre,'totalnbr_refuse'=>$totalcolisrefuse));

        if ($affected_rows_2 > 0) {
            logActivity('Nouveau Liste Colis Facture Ajouté [Nom Facture :' . $data['nom'] . ', ID: ' . $id . ']');
        }

        if ($affected_rows_1 > 0 || $affected_rows_2 > 0) {
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
        $this->db->delete('tblfactures_etl');
        if ($this->db->affected_rows() > 0) {
            //Delete colis facture
            $this->db->where('etatfacture_id', $id);
            $this->db->delete('tbletatfactures');


            $this->db->where('facture_etl', $id);
            $this->db->update('tbletatcolislivre', array('facture_etl' => 0));
            return true;
        }

        return false;
    }


  public function delete_colis_facture($id)
    {
        do_action('before_colis_facture_deleted', $id);
        //Get ID Colis & ID Facture
        $this->db->where('id', $id);
        $colis_facture = $this->db->get('tbletatfactures')->row();
        $colis_id = $colis_facture->etat_id;
        $facture_id = $colis_facture->etatfacture_id;

        //Delete etat facture
        $this->db->where('id', $id);
        $this->db->delete('tbletatfactures');
        if ($this->db->affected_rows() > 0) {
            //Get Facture by ID Facture
            $facture = $this->get($facture_id);
            if ($facture) {
                //Get Colis by ID Colis
                $this->db->where('id', $colis_id);
                $colis = $this->db->get('tbletatcolislivre')->row();
                $this->db->update('tbletatcolislivre', array( 'facture_etl' => 0));

                //Update Facture

                $totalRefuse = $facture->total_refuse - $colis->refuse_commision;
                $totalFrais = $facture->total_frais - $colis->commision;
                $totalManque = $facture->total_manque - $colis->manque;
      //      $totalLine = $facture->total_line - $colis->total;
                $totalLine = $totalManque +$totalFrais +$totalRefuse ;

                $this->load->model('etat_colis_livrer_model');

                $totalcolisrefuse =$facture->totalnbr_refuse -  $this->etat_colis_livrer_model->getcolisrefuse($facture->etat_id,2);;
                $totalcolislivre =$facture->totalnbr_livre - $this->etat_colis_livrer_model->getcolisrefuse($facture->etat_id,9)  ;
                //Update Total Facture
                $this->db->where('id', $facture_id);
                $this->db->update('tblfactures_etl', array('total_frais' => $totalFrais, 'total_line' => $totalLine, 'total_refuse' => $totalRefuse,"total_manque" => $totalManque,"totalnbr_livre" =>$totalcolislivre,'totalnbr_refuse'=>$totalcolisrefuse));


            }


            return $facture_id;
        }

        return false;
    }


    public function get_for_pdf($id = '', $where = array(), $orderBy = '', $limit = '')
    {
        $this->db->select('tblfactures_etl.*');
        $this->db->from('tblfactures_etl');
      //  $this->db->join('tblstatuscolis', 'tblstatuscolis.id = tblfactures.type', 'left');

        if ($id != '') {
            $this->db->where('tblfactures_etl.id', $id);
            $facture = $this->db->get()->row();
            if ($facture) {
                $facture->items = $this->get_items_facture($id);
                $this->load->model('staff_model');
                $facture->client = $this->staff_model->get($facture->id_livreur);
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

    public function change_etat($id, $etat)
    {
        $this->db->where('id', $id);
        $this->db->update('tblfactures_etl', array('statu' => $etat));
        if ($this->db->affected_rows() > 0) {
          //  logActivity('Etat colis livrer STATUT Changé [EtatColisLivrerID: ' . $id . ' Statut(Non Regle / Regle): ' . $status . ']');
            return true;
        }

        return false;
    }


}


<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->helper('download');
    }

    public function file($folder_indicator, $attachmentid = '')
    {
        //Insertion de l'ID de l'entreprise
        $id_E = $this->session->userdata('staff_user_id_entreprise');

        if ($folder_indicator == 'expense') {

            if (!is_staff_logged_in()) {
                die();
            }

            $this->db->where('id', $attachmentid);
            $expense = $this->db->get('tblexpenses')->row();
            $path = EXPENSE_ATTACHMENTS_FOLDER . $expense->id . '/' . $expense->attachment;
            $name = $expense->attachment;
        } else if ($folder_indicator == 'demande') {
            if (!is_staff_logged_in()) {
                die();
            }

            $this->db->where('id', $attachmentid);
            $demande = $this->db->get('tbldemandes')->row();
            $path = DEMANDES_ATTACHED_PIECE_FOLDER . $demande->id . '/' . $demande->attached_piece;
            $name = $demande->attached_piece;
        } else if ($folder_indicator == 'marketing') {
            if (!is_staff_logged_in()) {
                die();
            }

            $this->db->where('id', $attachmentid);
            $marketing = $this->db->get('tblmarketing')->row();
            $path = MARKETING_ATTACHED_PIECE_FOLDER . $marketing->id . '/' . $marketing->image;
            $name = $marketing->image;
        } else if ($folder_indicator == 'slider') {
            if (!is_staff_logged_in()) {
                die();
            }

            $this->db->where('id', $attachmentid);
            $slider = $this->db->get('tblsliders')->row();
            $path = SLIDERS_FILE_FOLDER . $slider->id . '/' . $slider->file;
            $name = $slider->file;
        } else if ($folder_indicator == 'db_backup') {
            if (!is_admin()) {
                die('Access forbidden');
            }
            $path = BACKUPS_FOLDER . $attachmentid;
            $name = $attachmentid;
        } else if ($folder_indicator == 'import_colis') {
            $path = TEMP_FOLDER . 'colis/exemple_importation.xlsx';
            $name = 'Fichier Exemple Importation Colis';
        } else {
            die('folder not specified');
        }

        $data = file_get_contents($path);
        force_download($name, $data);
    }

    public function media($folder, $filename)
    {
        if (is_logged_in()) {
            $path = MEDIA_FOLDER . $folder . '/' . $filename;
            $data = file_get_contents($path);
            force_download($filename, $data);
        }
    }
}

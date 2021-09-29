<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crontime extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('crontime_model');
    }

    public function urls()
    {
        $baseUrlCron = 'wget -q ' . base_url() . 'crontime';
        echo $baseUrlCron;
        echo '<br>';
        echo $baseUrlCron . '/cloturer_demande';
        echo '<br>';
        echo $baseUrlCron . '/ajouter_notification_staff_des_demandes_en_cours';
        echo '<br>';
        echo $baseUrlCron . '/cron_total_etat_colis_livre';
        echo '<br>';
        echo $baseUrlCron . '/cron_total_factures_livre';
        echo '<br>';
        echo $baseUrlCron . '/cron_add_colis_to_cash_plus';
        echo '<br>';
        echo $baseUrlCron . '/cron_get_status_colis_cash_plus';
    }

    public function index()
    {
        $this->crontime_model->run();
    }

    public function cloturer_demande()
    {
        $this->crontime_model->cloturer_demande();
    }

    public function ajouter_notification_staff_des_demandes_en_cours()
    {
        $this->crontime_model->ajouter_notification_staff_des_demandes_en_cours();
    }
    
    public function cron_make_backup_db()
    {
        $this->crontime_model->make_backup_db();
    }

    public function cron_generate_excel_colis()
    {
        $this->crontime_model->generate_excel_colis();
    }

    public function cron_total_etat_colis_livre()
    {
        $this->crontime_model->etat_colis_livre_where_total_different_de_la_somme_des_crbt_des_colis_de_cette_etat_colis_livre();
    }

    public function cron_total_factures_livre()
    {
        $this->crontime_model->factures_livre_where_total_different_du_total_des_crbt_frais_refuse_remise_des_colis_de_cette_facture_livre();
    }

    public function cron_add_colis_to_cash_plus()
    {
        $this->crontime_model->add_colis_to_cash_plus();
    }

    public function cron_get_status_colis_cash_plus()
    {
        $this->crontime_model->get_status_colis_cash_plus();
    }
}

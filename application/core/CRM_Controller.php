<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CRM_Controller extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //Check if coming-soon
        if (get_option('coming_soon') == 1 && is_date(to_sql_date_1(get_option('coming_soon_date_time_start'), true)) && (date('d/m/Y H:i') >= get_option('coming_soon_date_time_start'))) {
            if (is_date(to_sql_date_1(get_option('coming_soon_date_time_end'), true)) && (date('d/m/Y H:i') < get_option('coming_soon_date_time_end'))) {
                redirect(site_url('coming_soon'));
            } else {
                update_option('coming_soon', 0);
            }
        }

        $this->check_installation();
        if ($this->config->item('installed') == true) {
            $this->db->reconnect();
            $timezone = get_option('default_timezone');
            date_default_timezone_set($timezone);
        }
    }

    private function check_installation()
    {
        if ($this->uri->segment(1) !== 'install') {
            $this->load->config('migration');
            if ($this->config->item('installed') == false && $this->config->item('migration_enabled') == false) {
                redirect(site_url('install/make'));
            } else {
                if (is_dir(APPPATH . 'controllers/install')) {
                    echo '<h3>Delete the install folder from application/controllers/install</h3>';
                    die;
                }
            }
        }
    }
}

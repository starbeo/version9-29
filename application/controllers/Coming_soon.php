<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coming_soon extends CI_controller
{

    function __construct()
    {
        parent::__construct();
        //Check if coming-soon
        if (get_option('coming_soon') == 1 && is_date(to_sql_date_1(get_option('coming_soon_date_time_start'), true)) && (date('d/m/Y H:i') >= get_option('coming_soon_date_time_start'))) {
            if (is_date(to_sql_date_1(get_option('coming_soon_date_time_end'), true)) && (date('d/m/Y H:i') >= get_option('coming_soon_date_time_end'))) {
                update_option('coming_soon', 0);
                if (is_expediteur_logged_in()) {
                    redirect(client_url());
                } else if (is_logged_in()) {
                    redirect(admin_url());
                } else {
                    redirect(base_url());
                }
            }
        }
    }

    /**
     * Coming soon
     */
    public function index()
    {
        //Default language
        $language = get_option('active_language');
        $this->lang->load($language . '_lang', $language);
        
        //Get date
        $data['date_start'] = get_option('coming_soon_date_time_start');
        $data['date_end'] = get_option('coming_soon_date_time_end');
        //Get message
        $data['message'] = get_option('coming_soon_message');
        
        $data['company_name'] = get_option('companyname');
        $data['title'] = $data['company_name'] . ' - ' . _l('in_construction');
        $this->load->view('coming-soon/index', $data);
    }
}

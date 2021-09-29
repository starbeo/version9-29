<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expediteurs extends CI_controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect(client_url());
    }

    public function login()
    {
        redirect(client_url());
    }
}

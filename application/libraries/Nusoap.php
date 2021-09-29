<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Nusoap
{

    // Instance CI
    private $_instance;

    function __construct()
    {
        $this->_instance = &get_instance();
        //Load lib Nusoap
        require_once APPPATH . 'third_party/nusoap/lib/nusoap.php';
    }

    function soapCashPlus($options, $params)
    {
        $apiUrl = $this->_instance->config->item('url_api_cash_plus');
        $apiUsername = $this->_instance->config->item('username_api_cash_plus');
        $apiPassword = $this->_instance->config->item('password_api_cash_plus');
        $societe = $this->_instance->config->item('societe_api_cash_plus');
        if (!empty($apiUrl) && !empty($apiUsername) && !empty($apiPassword) && !empty($societe) && !empty($options) && !empty($params)) {
            $clientWS = new SoapClient($apiUrl);
            $auth = array("Login" => $apiUsername, "Password" => $apiPassword, "Fonction" => $options['fonction'], "Societe" => $societe);
            $header = new SoapHeader("http://bunddl.org/", "AuthHeader", $auth, false);
            $clientWS->__setSoapHeaders($header);
            $return = $clientWS->magicalio($params);
            $resultat = $return->magicalioResult;
            if (!empty($resultat)) {
                return json_decode($resultat, true);
            } else {
                return true;
            }

            return false;
        }
    }
}

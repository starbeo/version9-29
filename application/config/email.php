<?php
$CI = & get_instance();
if ($CI->config->item('installed') == true) {
    $config['useragent'] = "CodeIgniter";
    $config['mailpath'] = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
    $config['wordwrap'] = TRUE;
    $config['mailtype'] = 'html';
    $config['charset'] = get_option('smtp_email_charset');
    $config['newline'] = "\r\n";
    //Protocol
    $protocol = '';
    if($CI->input->server('HTTP_HOST') && !empty($CI->input->server('HTTP_HOST')) && $CI->input->server('HTTP_HOST') == 'localhost') {
        //On Local
        $protocol = 'tls';
    } else {
        //On Server
        $protocol = 'smtp';
    }
    $config['protocol']  = $protocol; 
    $config['smtp_host'] = get_option('smtp_host');
    $config['smtp_port'] = get_option('smtp_port');
    $config['smtp_timeout'] = '30';
    $config['smtp_user'] = get_option('smtp_email');
    $config['smtp_pass'] = get_option('smtp_password');
}

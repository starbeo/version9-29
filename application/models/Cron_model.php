<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('memory_limit', '-1');
define('CRON', true);

class Cron_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function run($manualy = false)
    {
        if ($manualy == true) {
            //logActivity('Cron Invoked Manually');
        }

        $this->make_backup_db();
    }

    public function make_backup_db($manual = false)
    {
        if ((get_option('auto_backup_enabled') == "1" && time() > (get_option('last_auto_backup') + get_option('auto_backup_every') * 12 * 60 * 60)) || $manual == true) {

            $this->load->dbutil();

            $prefs = array(
                'format' => 'zip',
                'filename' => date("Y-m-d-H-i-s") . '_backup.sql'
            );

            $backup = $this->dbutil->backup($prefs);
            $backup_name = 'database_backup_' . date("Y-m-d-H-i-s") . '.zip';
            $backup_name = unique_filename(BACKUPS_FOLDER, $backup_name);
            $save = BACKUPS_FOLDER . $backup_name;
            $this->load->helper('file');
            if (write_file($save, $backup)) {
                echo "[CRON] Database Backup";
                if ($manual == false) {
                    //logActivity('[CRON] Database Backup [' . $backup_name . ']', null, true);
                    update_option('last_auto_backup', time());
                } else {
                    //logActivity('Database Backup [' . $backup_name . ']');
                }
                //Send Backup
                //$this->sent_backup_to_admin($save);

                return true;
            }
        }

        return false;
    }

    /**
     * Sent backup to admin
     * @param  mixed  $backup
     * @return boolean
     * */
    public function sent_backup_to_admin($url = '')
    {
        $this->load->library('email');

        $fromemail = 'ahmed.mouda08@gmail.com';
        //$fromemail = get_option('smtp_email');
        $fromname = get_option('companyname');
        $subject = 'Backup Data Base : ' . date('d/m/Y h:i:s');
        $message = '<p><b>Bonjour,</b></p><br><br><p>Voilà le backup de la base de donnée d\'aujourd\'hui</p><br><br><p>Cldt,</p>';
        $attach = chunk_split(base64_encode(file_get_contents($url)));

        $attachment[] = array(
            'attachment' => $attach,
            'filename' => $subject . '.zip',
            'type' => 'application/zip'
        );

        $this->email->initialize();
        $this->email->clear(TRUE);
        $this->email->from($fromemail, $fromname);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->to('ahmed.mouda08@gmail.com');


        if (count($attachment) > 0) {
            foreach ($attachment as $attach) {
                $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
            }
        }

        if ($this->email->send()) {
            //logActivity('Backup envoyé.');
            return true;
        }

        return false;
    }

    /**
     * Sent email to admin
     * @param  mixed  $backup
     * @return boolean
     * */
    public function sent_email_to_admin($url = '')
    {
        if (get_option('last_recurring_expenses_cron') !== date("Y-m-d")) {
            $this->load->library('email');

            $fromemail = get_option('smtp_email');
            $fromname = 'EasyTrack';
            $subject = 'Nouveau client ' . date('d/m/Y h:i:s');
            $message = '<p><b>Bonjour,</b></p><br><p>Voilà l\'url du nouveau client : ' . base_url() . '</p><br><p>Cldt,</p>';

            $this->email->initialize();
            $this->email->clear(TRUE);
            $this->email->from($fromemail, $fromname);
            $this->email->subject($subject);
            $this->email->message($message);
            $this->email->to('ahmed.mouda08@gmail.com');

            if ($this->email->send()) {
                //Update last sent email
                update_option('last_recurring_expenses_cron', date("Y-m-d"));

                return true;
            }
        }

        return false;
    }
}

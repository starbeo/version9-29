<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Authentication_model extends CRM_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('user_autologin');
        $this->autologin();
    }

    /**
     * @param  string Email address for login
     * @param  string User Password
     * @param  boolean Set cookies for user if remember me is checked
     * @param  boolean Is Staff Or Client
     * @return boolean if not redirect url found, if found redirect to the url
     */
    function login($email, $password, $remember, $compte)
    {

        

        if ((!empty($email)) AND ( !empty($password))) {

            if ($compte == 'admin') {
                $table = 'tblstaff';
                $_id = 'staffid';
            } else if ($compte == 'point_relais') {
                $table = 'tblstaff';
                $_id = 'staffid';
            } else if ($compte == 'expediteur') {
                $table = 'tblexpediteurs';
                $_id = 'id';
            }

            $this->db->where('email', $email);
            $user = $this->db->get($table)->row();
          
            if ($user) {
                if ($compte == 'point_relais' && $user->admin != 4) {
                    logActivity('Tentative de connexion échouée vous n\'êtes pas agent point relais [Email:' . $email . ', Compte:' . $compte . ', IP:' .
$this->input->ip_address() . ']');
                    return array('not_a_relay_point' => true);
                } else if ($compte == 'admin' && $user->admin == 4) {
                    logActivity('Tentative de connexion échouée vous êtes agent point relais [Email:' . $email . ', Compte:' . $compte . ', IP:' .
$this->input->ip_address() . ']');
                    return array('a_relay_point' => true);
                }
                // Email is okey lets check the password now
                $this->load->helper('phpass');
                $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
                if (!$hasher->CheckPassword($password, $user->password)) {
                    // Password failed, return
                    return false;
                }
            } else {
                logActivity('Tentative de connexion échouée [Email:' . $email . ', Compte:' . $compte . ', IP:' . $this->input->ip_address() . ']');
                return false;
            }

            if ($user->active == 0) {
                logActivity('Utilisateur inactif essayé de se connecter [Email:' . $email . ', Compte:' . $compte . ', IP:' . $this->input->ip_address() .
']');
                return array(
                    'memberinactive' => true
                );
            }

            if ($compte == 'admin') {
                do_action('before_staff_login', array(
                    'email' => $email,
                    'userid' => $user->$_id
                ));
                $user_data = array(
                    'staff_user_id' => $user->$_id,
                    'staff_user_id_entreprise' => $user->id_entreprise,
                    'staff_logged_in' => true
                );
                //Update online
                update_online_staff($user->$_id);
            } else if ($compte == 'point_relais') {
                do_action('before_point_relais_login', array(
                    'email' => $email,
                    'userid' => $user->$_id
                ));
                $user_data = array(
                    'point_relais_user_id' => $user->$_id,
                    'point_relais_user_id_entreprise' => $user->id_entreprise,
                    'point_relais_logged_in' => true
                );
            } else if ($compte == 'expediteur') {
                do_action('before_expediteur_login', array(
                    'email' => $email,
                    'userid' => $user->$_id
                ));
                $user_data = array(
                    'expediteur_user_id' => $user->$_id,
                    'staff_user_id_entreprise' => $user->id_entreprise,
                    'expediteur_logged_in' => true
                );
                // Add insert to table number of authentication
                add_number_of_authentication(array('clientid' => $user->$_id, 'address_ip' => $this->input->ip_address()));
                //Update online
                update_online_client($user->$_id);
            }

            //check id entreprise
            $id_E = $user_data['staff_user_id_entreprise'];
            $this->db->where('id_entreprise', $id_E);
            $entreprise = $this->db->get('tblentreprise')->row();

            if ($entreprise->id_entreprise !== $id_E) {
                return false;
            }

            $this->session->set_userdata($user_data);

            if ($compte == 'admin') {
                $staff = true;
            } else if ($compte == 'point_relais') {
                $staff = true;
            } else if ($compte == 'expediteur') {
                $staff = false;
            }

            if ($remember) {
                $this->create_autologin($user->$_id, $staff);
            }

            //$this->update_login_info($user->$_id, $staff);
            if ($this->session->has_userdata('red_url')) {
                $red_url = $this->session->userdata('red_url');
                $this->session->unset_userdata('red_url');
                redirect(site_url($red_url));
            }
            logActivity('Connexion Ok  [Email:' . $email . ', Compte:' . $compte . ', IP:' . $this->input->ip_address() . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  boolean If Client or Staff
     * @return none
     */
    function logout($staff = true)
    {
        $this->delete_autologin($staff);
        if (is_expediteur_logged_in()) {
            //Update online
            update_online_client(get_expediteur_user_id(), 0);
            do_action('before_expediteur_logout', get_expediteur_user_id());
            $this->session->unset_userdata(array(
                'expediteur_user_id' => '',
                'expediteur_logged_in' => ''
            ));
        } else {
            //Update online
            update_online_staff(get_staff_user_id(), 0);
            do_action('before_staff_logout', get_staff_user_id());
            $this->session->unset_userdata(array(
                'staff_user_id' => '',
                'staff_logged_in' => ''
            ));
        }

        $this->session->sess_destroy();
    }

    /**
     * @param  integer ID to create autologin
     * @param  boolean Is Client or Staff
     * @return boolean
     */
    private function create_autologin($user_id, $staff)
    {
        $this->load->helper('cookie');
        $key = substr(md5(uniqid(rand() . get_cookie($this->config->item('sess_cookie_name')))), 0, 16);

        $this->user_autologin->delete($user_id, $key, $staff);

        if ($this->user_autologin->set($user_id, md5($key), $staff)) {
            set_cookie(array(
                'name' => 'autologin',
                'value' => serialize(array(
                    'user_id' => $user_id,
                    'key' => $key
                )),
                'expire' => 60 * 60 * 24 * 31 * 2 // 2 months
            ));
            return true;
        }
        return false;
    }

    /**
     * @param  boolean Is Client or Staff
     * @return none
     */
    private function delete_autologin($staff)
    {
        $this->load->helper('cookie');
        if ($cookie = get_cookie('autologin', true)) {
            $data = unserialize($cookie);
            $this->user_autologin->delete($data['user_id'], md5($data['key']), $staff);
            delete_cookie('autologin', 'aal');
        }
    }

    /**
     * @return boolean
     * Check if autologin found
     */
    public function autologin()
    {
        if (!is_logged_in()) {

            $this->load->helper('cookie');
            if ($cookie = get_cookie('autologin', true)) {

                $data = unserialize($cookie);

                if (isset($data['key']) AND isset($data['user_id'])) {

                    if (!is_null($user = $this->user_autologin->get($data['user_id'], md5($data['key'])))) {
                        // Login user
                        if ($user->staff == 1) {
                            $user_data = array(
                                'staff_user_id' => $user->id,
                                'staff_logged_in' => true
                            );
                        } else {
                            $user_data = array(
                                'client_user_id' => $user->id,
                                'client_logged_in' => true
                            );
                        }

                        $this->session->set_userdata($user_data);
                        // Renew users cookie to prevent it from expiring
                        set_cookie(array(
                            'name' => 'autologin',
                            'value' => $cookie,
                            'expire' => 60 * 60 * 24 * 31 * 2 // 2 months
                        ));

                        //$this->update_login_info($user->id, $user->staff);
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

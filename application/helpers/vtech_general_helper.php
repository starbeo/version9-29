<?php
header('Content-Type: text/html; charset=utf-8');

/**
 * Update the config variable to installed / used in update and install
 * @since  Version 1.0.2
 * @param  string $config_path config path
 * @return boolean
 */
function update_config_installed()
{
    $CI = & get_instance();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');

    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $config_file = str_replace("\$config['installed'] = false;", "\$config['installed'] = true;", $config_file);
    $config_file = str_replace("\$config['base_url'] = '';", "\$config['base_url'] = '" . site_url() . "';", $config_file);

    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return FALSE;
    }

    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);

    @chmod($config_path, FILE_READ_MODE);
    return TRUE;
}

/**
 * Available date formats
 * @return array
 */
function get_available_date_formats()
{
    $date_formats = array(
        'Y-m-d|yyyy-mm-dd' => 'yyyy-mm-dd',
        'm/d/Y|mm/dd/yyyy' => 'mm/dd/yyyy',
        'd/m/Y|dd/mm/yyyy' => 'dd/mm/yyyy',
        'Y/m/d|yyyy/mm/dd' => 'yyyy/mm/dd',
        'd.m.Y|dd.mm.yyyy' => 'dd.mm.yyyy'
    );

    return do_action('get_available_date_formats', $date_formats);
}

/**
 * Get current date format from options
 * @return string
 */
function get_current_date_format()
{
    $format = get_option('dateformat');
    $format = explode('|', $format);
    return $format[0];
}

/**
 * Get current date format from options
 * @return string
 */
function get_current_date_time_format()
{
    $format = get_option('dateformat');
    $format = explode('|', $format);
    return $format[0] . ' H:i:s';
}

/**
 * Check if current user is admin
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_admin($staffid = '')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }
    $CI = & get_instance();
    $CI->db->select('admin');
    $CI->db->where('admin', 1);
    $CI->db->where('staffid', $_staffid);
    return $CI->db->get('tblstaff')->row();
}

/**
 * Check if current user is livreur
 * @param  mixed $staffid
 * @return boolean if user is not livreur
 */
function is_livreur($staffid = '')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }

    $CI = & get_instance();
    $CI->db->select('1');
    $CI->db->where('role', 1);
    $CI->db->where('staffid', $_staffid);
    return $CI->db->get('tblstaff')->row();
}

/**
 * Check if current user is point relais
 * @param  mixed $staffid
 * @return boolean if user is not point relais
 */
function is_point_relais($staffId = '')
{
    $_staffId = get_staff_user_id();
    if (is_numeric($staffId)) {
        $_staffId = $staffId;
    }

    $CI = & get_instance();
    $CI->db->select('1');
    $CI->db->where('admin', 4);
    $CI->db->where('staffid', $_staffId);
    return $CI->db->get('tblstaff')->row();
}

/**
 * Get City livreur
 * @param  mixed $staffid
 * @return boolean if user is not livreur
 */
function get_city_livreur($staffid = '')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }
    $CI = & get_instance();
    $CI->db->select('city');
    $CI->db->where('staffid', $_staffid);
    return $CI->db->get('tblstaff')->row()->city;
}

/**
 * Get Telephone livreur
 * @param  mixed $staffid
 * @return boolean if user is not livreur
 */
function get_telephone_livreur($livreurId = '')
{
    if (!is_numeric($livreurId)) {
        return false;
    }

    $CI = & get_instance();
    $CI->db->select('phonenumber');
    $CI->db->where('staffid', $livreurId);
    return $CI->db->get('tblstaff')->row()->phonenumber;
}

/**
 * Check if current user is owns data
 * @param  mixed $staffid
 * @param  mixed $dataid
 * @param  string $table
 * @return boolean if user owns data
 */
function owns_data($table, $dataid, $staffid = '', $table_col = '', $table_index = 'id')
{
    $_staffid = get_staff_user_id();
    if (is_numeric($staffid)) {
        $_staffid = $staffid;
    }
    $CI = & get_instance();
    $CI->db->select('1');
    $CI->db->where($table_index, $dataid);
    $CI->db->where($table_col, $_staffid);
    $result = $CI->db->get($table)->row();
    if (!is_null($result)) {
        return 1;
    }

    return 0;
}

/**
 * Is user logged in
 * @return boolean
 */
function is_logged_in()
{
    $CI = & get_instance();
    if (!$CI->session->has_userdata('client_logged_in') && !$CI->session->has_userdata('staff_logged_in')) {
        return false;
    }
    return true;
}

/**
 * Is point relais logged in
 * @return boolean
 */
function is_point_relais_logged_in()
{
    $CI = & get_instance();
    if ($CI->session->has_userdata('point_relais_logged_in') && $CI->session->get_userdata('point_relais_logged_in') != false) {
        return true;
    }
    return false;
}

/**
 * Is client logged in
 * @return boolean
 */
function is_client_logged_in()
{
    $CI = & get_instance();
    if ($CI->session->has_userdata('client_logged_in') && $CI->session->get_userdata('client_logged_in') != false) {
        return true;
    }
    return false;
}

/**
 * Is client logged in
 * @return boolean
 */
function is_expediteur_logged_in()
{
    $CI = & get_instance();
    if ($CI->session->has_userdata('expediteur_logged_in') && $CI->session->get_userdata('expediteur_logged_in') != false) {
        return true;
    }
    return false;
}

/**
 * Is staff logged in
 * @return boolean
 */
function is_staff_logged_in()
{
    $CI = & get_instance();
    if ($CI->session->has_userdata('staff_logged_in')) {
        return true;
    }
    return false;
}

/**
 * Return logged staff User ID from session
 * @return mixed
 */
function get_staff_user_id()
{
    $CI = & get_instance();
    if($CI->session->has_userdata('staff_logged_in')) {
        return $CI->session->userdata('staff_user_id');
    } else if($CI->session->has_userdata('point_relais_logged_in')) {
        return $CI->session->userdata('point_relais_user_id');
    }
    
    return false;
}

/**
 * Get staff points relais
 * @return array
 */
function get_staff_points_relais($resultArray = false, $staffPointRelais = '')
{
    $CI = & get_instance();
    
    if(!is_numeric($staffPointRelais)) {
        $staffPointRelais = get_staff_user_id();
    }
    
    $CI->db->where('staff_id', $staffPointRelais);
    $pointsRelaisStaff = $CI->db->get('tblpointrelaisstaff')->result_array();
    
    if($resultArray) {
        $pointsRelaisArray = array();
        foreach ($pointsRelaisStaff as $pointRelais) {
            array_push($pointsRelaisArray, $pointRelais['point_relais_id']);
        }
    
        return $pointsRelaisArray;
    }
    
    $pointsRelais = '(1000000000000000000000000';
    foreach ($pointsRelaisStaff as $pointRelais) {
        $pointsRelais .= ', ' . $pointRelais['point_relais_id'];
    }
    $pointsRelais .= ')';
    
    return $pointsRelais;
}

/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_client_user_id()
{
    $CI = & get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }
    return $CI->session->userdata('client_user_id');
}

/**
 * Return logged client User ID from session
 * @return mixed
 */
function get_expediteur_user_id()
{
    $CI = & get_instance();
    if (!$CI->session->has_userdata('expediteur_logged_in')) {
        return false;
    }
    return $CI->session->userdata('expediteur_user_id');
}

/**
 * Get admin url
 * @param string url to append (Optional)
 * @return string admin url
 */
function admin_url($url = '')
{
    if (empty($url)) {
        return site_url(ADMIN_URL) . '/';
    } else {
        return site_url(ADMIN_URL . '/' . $url);
    }
}

/**
 * Get point relais url
 * @param string url to append (Optional)
 * @return string point relais url
 */
function point_relais_url($url = '')
{
    if (empty($url)) {
        return site_url(POINT_RELAIS_URL) . '/';
    } else {
        return site_url(POINT_RELAIS_URL . '/' . $url);
    }
}

/**
 * Get client url
 * @param string url to append (Optional)
 * @return string client url
 */
function client_url($url = '')
{
    if (empty($url)) {
        return site_url(CLIENT_URL) . '/';
    } else {
        return site_url(CLIENT_URL . '/' . $url);
    }
}

/**
 * Get livreur url
 * @param string url to append (Optional)
 * @return string livreur url
 */
function livreur_url($url = '')
{
    if (empty($url)) {
        return site_url(LIVREUR_URL) . '/';
    } else {
        return site_url(LIVREUR_URL . '/' . $url);
    }
}

/**
 * Get url logo pdf 
 * @param string url to append (Optional)
 * @return string admin url
 */
function logo_pdf_url()
{
    return base_url('', 'http') . 'uploads/company/' . get_option('companyalias') . '/logo-entete.jpg';
}

/**
 * Get url logo pdf 
 * @param string url to append (Optional)
 * @return string admin url
 */
function base_url_logo_client()
{
    return base_url('', 'http') . 'uploads/clients/logo/';
}

/**
 * Outputs language string based on passed line
 * @since  Version 1.0.1
 * @param  string $line  language line string
 * @param  string $label sprint_f label
 * @return string        formated language
 */
function _l($line, $label = '')
{
    $CI = & get_instance();
    $_line = sprintf($CI->lang->line($line), $label);
    if ($_line !== '') {
        return $_line;
    }
    // dont change this line
    return 'translate_not_found_' . $line;
}

/**
 * Set session alert / flashdata
 * @param string $type    Alert type
 * @param string $message Alert message
 */
function set_alert($type, $message)
{
    $CI = & get_instance();
    $CI->session->set_flashdata('message-' . $type, $message);
}

/**
 * Redirect to blank page
 * @param  string $message Alert message
 * @param  string $alert   Alert type
 */
function blank_page($message = '', $alert = 'danger')
{
    set_alert($alert, $message);
    redirect(admin_url('not_found'));
}

/**
 * Set debug message - message wont be hidden in X seconds from javascript
 * @since  Version 1.0.1
 * @param string $message debug message
 */
function set_debug_alert($message)
{
    $CI = & get_instance();
    $CI->session->set_flashdata('debug', $message);
}

/**
 * Format date to selected dateformat
 * @param  date $date Valid date
 * @return date/string
 */
function _d($date)
{
    if (!is_date($date)) {
        return $date;
    }

    $format = get_current_date_format();

    if (strpos($date, ' ') === true) {
        $_date = new DateTime($date);
        $_date = $_date->format($format . ' H:i:s');
        if (is_date($_date)) {
            return $_date;
        }
        return $date;
    }
    $_date = new DateTime($date);
    $_date = $_date->format($format);

    if (is_date($_date)) {
        return $_date;
    }

    return $date;
}

/**
 * Format datetime to selected datetime format
 * @param  datetime $date datetime date
 * @return datetime/string
 */
function _dt($date)
{
    if (!is_date($date)) {
        return $date;
    }

    $_date = new DateTime($date);
    $_date = $_date->format(get_current_date_time_format());
    if (is_date($_date)) {
        return $_date;
    }
    return $date;
}

/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date($date)
{
    if ($date == '') {
        return;
    }
    return DateTime::createFromFormat(get_current_date_format(), $date)->format('Y-m-d');
}

/**
 * Convert string to sql date based on current date format from options
 * @param  string $date date string
 * @return mixed
 */
function to_sql_date_1($date, $datetime = false)
{
    if ($date == '') {
        return NULL;
    }

    $to_date = 'Y-m-d';
    $from_format = get_current_date_format(true);

    $hook_data['date'] = $date;
    $hook_data['from_format'] = $from_format;
    $hook_data['datetime'] = $datetime;

    $hook_data = do_action('before_sql_date_format', $hook_data);

    $date = $hook_data['date'];
    $from_format = $hook_data['from_format'];

    if ($datetime == false) {
        return date_format(date_create_from_format($from_format, $date), $to_date);
    } else {
        if (strpos($date, ' ') === false) {
            $date .= ' 00:00:00';
        } else {
            $_temp = explode(' ', $date);
            $time = explode(':', $_temp[1]);
            if (count($time) == 2) {
                $date .= ':00';
            }
        }

        if ($from_format == 'd/m/Y') {
            $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
        } else if ($from_format == 'm/d/Y') {
            $date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
        } else if ($from_format == 'm.d.Y') {
            $date = preg_replace('#(\d{2}).(\d{2}).(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
        } else if ($from_format == 'm-d-Y') {
            $date = preg_replace('#(\d{2})-(\d{2})-(\d{4})\s(.*)#', '$3-$1-$2 $4', $date);
        }

        $d = strftime('%Y-%m-%d %H:%M:%S', strtotime($date));
        return do_action('to_sql_date_formatted', $d);
    }
}

/**
 * Check if passed string is valid date
 * @param  string  $date
 * @return boolean
 */
function is_date($date)
{
    return (bool) strtotime($date);
}

/**
 * Get weekdays as array
 * @return array
 */
function get_weekdays()
{
    return array(
        _l('wd_monday'),
        _l('wd_tuesday'),
        _l('wd_wednesday'),
        _l('wd_thursday'),
        _l('wd_friday'),
        _l('wd_saturday'),
        _l('wd_sunday')
    );
}

/**
 * Format datetime to time ago with specific hours mins and seconds
 * @param  datetime $lastreply
 * @param  string $from      Optional
 * @return mixed
 */
function time_ago_specific($date, $from = "now")
{
    $datetime = strtotime("now");
    $date2 = strtotime("" . $date);
    $holdtotsec = $datetime - $date2;
    $holdtotmin = ($datetime - $date2) / 60;
    $holdtothr = ($datetime - $date2) / 3600;
    $holdtotday = intval(($datetime - $date2) / 86400);
    $str = '';
    if (0 < $holdtotday) {
        $str .= $holdtotday . "d ";
    }

    $holdhr = intval($holdtothr - $holdtotday * 24);
    $str .= $holdhr . "h ";
    $holdmr = intval($holdtotmin - ($holdhr * 60 + $holdtotday * 1440));
    $str .= $holdmr . "m";
    return $str;
}

/**
 * Short Time ago function
 * @param  datetime $time_ago
 * @return mixed
 */
function time_ago($time_ago)
{
    $time_ago = strtotime($time_ago);
    $cur_time = time();
    $time_elapsed = $cur_time - $time_ago;
    $seconds = $time_elapsed;
    $minutes = round($time_elapsed / 60);
    $hours = round($time_elapsed / 3600);
    $days = round($time_elapsed / 86400);
    $weeks = round($time_elapsed / 604800);
    $months = round($time_elapsed / 2600640);
    $years = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return _l('time_ago_just_now');
    }
    //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return _l('time_ago_minute');
        } else {
            return _l('time_ago_minutes', $minutes);
        }
    }
    //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return _l('time_ago_hour');
        } else {
            return _l('time_ago_hours', $hours);
        }
    }
    //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return _l('time_ago_yesterday');
        } else {
            return _l('time_ago_days', $days);
        }
    }
    //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return _l('time_ago_week');
        } else {
            return _l('time_ago_weeks', $weeks);
        }
    }
    //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return _l('time_ago_month');
        } else {
            return _l('time_ago_months', $months);
        }
    }
    //Years
    else {
        if ($years == 1) {
            return _l('time_ago_year');
        } else {
            return _l('time_ago_years', $years);
        }
    }
}
/**
 * String starts with
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
if (!function_exists('_startsWith')) {

    function _startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }
}
/**
 * String ends with
 * @param  string $haystack
 * @param  string $needle
 * @return boolean
 */
if (!function_exists('endsWith')) {

    function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
}

/**
 * List folder on a specific path
 * @param  stirng $path
 * @return array
 */
function list_folders($path)
{
    $folders = array();
    foreach (new DirectoryIterator($path) as $file) {

        if ($file->isDot())
            continue;

        if ($file->isDir()) {
            array_push($folders, $file->getFilename());
        }
    }
    return $folders;
}

/**
 * List files in a specific folder
 * @param  string $dir directory to list files
 * @return array
 */
function list_files($dir)
{
    $ignored = array(
        '.',
        '..',
        '.svn',
        '.htaccess',
        'index.html'
    );

    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored))
            continue;
        $files[$file] = filectime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : array();
}
/**
 * Convert bytes of files to readable seize
 * @param  string $path file path
 * @return mixed
 *
  function bytesToSize($path)
  {
  $bytes = sprintf('%u', filesize($path));

  if ($bytes > 0) {
  $unit  = intval(log($bytes, 1024));
  $units = array(
  'B',
  'KB',
  'MB',
  'GB'
  );

  if (array_key_exists($unit, $units) === true) {
  return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
  }
  }

  return $bytes;
  } */

/**
 * Convert bytes of files to readable seize
 * @param  string $path file path
 * @param  string $filesize file path
 * @return mixed
 */
function bytesToSize($path, $filesize = '')
{

    if (!is_numeric($filesize)) {
        $bytes = sprintf('%u', filesize($path));
    } else {
        $bytes = $filesize;
    }
    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = array(
            'B',
            'KB',
            'MB',
            'GB'
        );
        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }
    return $bytes;
}

/**
 * Get string after specific charcter/word
 * @param  string $string    string from where to get
 * @param  substring $substring search for
 * @return string
 */
function strafter($string, $substring)
{
    $pos = strpos($string, $substring);
    if ($pos === false)
        return $string;
    else
        return (substr($string, $pos + strlen($substring)));
}

/**
 * Get string before specific charcter/word
 * @param  string $string    string from where to get
 * @param  substring $substring search for
 * @return string
 */
function strbefore($string, $substring)
{
    $pos = strpos($string, $substring);
    if ($pos === false)
        return $string;
    else
        return (substr($string, 0, $pos));
}

/**
 * Redirect to access danied page and log activity
 * @param  string $permission If permission based to check where user tried to acces
 */
function access_denied($permission = '')
{
    set_alert('danger', _l('access_denied'));
    logActivity(_l('msg_access_denied') . ' [' . $permission . ']');
    redirect(admin_url('access_denied'));
}

/**
 * Replace Last Occurence of a String in a String
 * @since  Version 1.0.1
 * @param  string $search  string to be replaced
 * @param  string $replace replace with
 * @param  string $subject [the string to search
 * @return string
 */
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

/**
 * Copy directory and all contents
 * @since  Version 1.0.2
 * @param  string  $source      string
 * @param  string  $dest        destionation
 * @param  integer $permissions folder permissions
 * @return boolean
 */
function xcopy($source, $dest, $permissions = 0755)
{
    // Check for symlinks
    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }

    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }

    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest, $permissions);
    }

    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep copy directories
        xcopy("$source/$entry", "$dest/$entry", $permissions);
    }

    // Clean up
    $dir->close();
    return true;
}

/**
 * Delete directory
 * @param  string $dirPath dir
 * @return boolean
 */
function delete_dir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }

    return false;
}

/**
 * Is internet connection open
 * @param  string  $domain
 * @return boolean
 */
function is_connected($domain = 'www.google.com')
{
    $connected = @fsockopen($domain, 80);
    //website, port  (try 80 or 443)
    if ($connected) {
        $is_conn = true; //action when connected
        fclose($connected);
    } else {
        $is_conn = false; //action in connection failure
    }
    return $is_conn;
}

/**
 * Is file image
 * @param  string  $path file path
 * @return boolean
 */
function is_image($path)
{
    $image = @getimagesize($path);
    $image_type = $image[2];

    if (in_array($image_type, array(
            IMAGETYPE_GIF,
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_BMP
        ))) {
        return true;
    }
    return false;
}

/**
 * Get file extension by filename
 * @param  string $file_name file name
 * @return mixed
 */
function get_file_extension($file_name)
{
    return substr(strrchr($file_name, '.'), 1);
}

/**
 * Unique filename based on folder
 * @since  Version 1.0.1
 * @param  string $dir      directory to compare
 * @param  string $filename filename
 * @return string           the unique filename
 */
function unique_filename($dir, $filename)
{

    // Separate the filename into a name and extension.
    $info = pathinfo($filename);
    $ext = !empty($info['extension']) ? '.' . $info['extension'] : '';
    $filename = sanitize_file_name($filename);

    $number = '';

    // Change '.ext' to lower case.
    if ($ext && strtolower($ext) != $ext) {
        $ext2 = strtolower($ext);
        $filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);

        // Check for both lower and upper case extension or image sub-sizes may be overwritten.
        while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext"
                ), "-$new_number$ext", $filename);
            $filename2 = str_replace(array(
                "-$number$ext2",
                "$number$ext2"
                ), "-$new_number$ext2", $filename2);
            $number = $new_number;
        }
        return $filename2;
    }

    while (file_exists($dir . "/$filename")) {
        if ('' == "$number$ext") {
            $filename = "$filename-" . ++$number;
        } else {
            $filename = str_replace(array(
                "-$number$ext",
                "$number$ext"
                ), "-" . ++$number . $ext, $filename);
        }
    }
    return $filename;
}

/**
 * Sanitize file name
 * @param  string $filename filename
 * @return mixed
 */
function sanitize_file_name($filename)
{

    $special_chars = array(
        "?",
        "[",
        "]",
        "/",
        "\\",
        "=",
        "<",
        ">",
        ":",
        ";",
        ",",
        "'",
        "\"",
        "&",
        "$",
        "#",
        "*",
        "(",
        ")",
        "|",
        "~",
        "`",
        "!",
        "{",
        "}",
        "%",
        "+",
        chr(0)
    );

    $filename = str_replace($special_chars, '', $filename);
    $filename = str_replace(array(
        '%20',
        '+'
        ), '-', $filename);
    $filename = preg_replace('/[\r\n\t -]+/', '-', $filename);
    $filename = trim($filename, '.-_');

    // Split the filename into a base and extension[s]
    $parts = explode('.', $filename);

    // Return if only one extension
    if (count($parts) <= 2) {
        return $filename;
    }

    // Process multiple extensions
    $filename = array_shift($parts);
    $extension = array_pop($parts);
    /*
     * Loop over any intermediate extensions. Postfix them with a trailing underscore
     * if they are a 2 - 5 character long alpha string not in the extension whitelist.
     */
    foreach ((array) $parts as $part) {
        $filename .= '.' . $part;

        if (preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)) {
            $allowed = false;
            $ext_preg = '!^(' . $ext_preg . ')$!i';
            if (preg_match($ext_preg, $part)) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed)
            $filename .= '_';
    }

    $filename .= '.' . $extension;
    return $filename;
}

/**
 * Get system favourite colors
 * @return array
 */
function get_system_favourite_colors()
{
    // dont delete any of these colors are used all over the system
    $colors = array(
        '#28B8DA',
        '#c53da9',
        '#757575',
        '#8e24aa',
        '#d81b60',
        '#0288d1',
        '#7cb342',
        '#fb8c00',
    );

    $colors = do_action('get_kan_ban_colors', $colors);
    return $colors;
}

/**
 * Slug function
 * @param  string $str
 * @param  array  $options Additional Options
 * @return mixed
 */
function slug_it($str, $options = array())
{
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());

    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(
            '
            /\b(ѓ)\b/i' => 'gj',
            '/\b(ч)\b/i' => 'ch',
            '/\b(ш)\b/i' => 'sh',
            '/\b(љ)\b/i' => 'lj'
        ),
        'transliterate' => true
    );

    // Merge options
    $options = array_merge($defaults, $options);

    $char_map = array(
        // Latin
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Å' => 'A',
        'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ð' => 'D',
        'Ñ' => 'N',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ő' => 'O',
        'Ø' => 'O',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ű' => 'U',
        'Ý' => 'Y',
        'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ð' => 'd',
        'ñ' => 'n',
        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ő' => 'o',
        'ø' => 'o',
        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ű' => 'u',
        'ý' => 'y',
        'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A',
        'Β' => 'B',
        'Γ' => 'G',
        'Δ' => 'D',
        'Ε' => 'E',
        'Ζ' => 'Z',
        'Η' => 'H',
        'Θ' => '8',
        'Ι' => 'I',
        'Κ' => 'K',
        'Λ' => 'L',
        'Μ' => 'M',
        'Ν' => 'N',
        'Ξ' => '3',
        'Ο' => 'O',
        'Π' => 'P',
        'Ρ' => 'R',
        'Σ' => 'S',
        'Τ' => 'T',
        'Υ' => 'Y',
        'Φ' => 'F',
        'Χ' => 'X',
        'Ψ' => 'PS',
        'Ω' => 'W',
        'Ά' => 'A',
        'Έ' => 'E',
        'Ί' => 'I',
        'Ό' => 'O',
        'Ύ' => 'Y',
        'Ή' => 'H',
        'Ώ' => 'W',
        'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a',
        'β' => 'b',
        'γ' => 'g',
        'δ' => 'd',
        'ε' => 'e',
        'ζ' => 'z',
        'η' => 'h',
        'θ' => '8',
        'ι' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        'ν' => 'n',
        'ξ' => '3',
        'ο' => 'o',
        'π' => 'p',
        'ρ' => 'r',
        'σ' => 's',
        'τ' => 't',
        'υ' => 'y',
        'φ' => 'f',
        'χ' => 'x',
        'ψ' => 'ps',
        'ω' => 'w',
        'ά' => 'a',
        'έ' => 'e',
        'ί' => 'i',
        'ό' => 'o',
        'ύ' => 'y',
        'ή' => 'h',
        'ώ' => 'w',
        'ς' => 's',
        'ϊ' => 'i',
        'ΰ' => 'y',
        'ϋ' => 'y',
        'ΐ' => 'i',
        // Turkish
        'Ş' => 'S',
        'İ' => 'I',
        'Ç' => 'C',
        'Ü' => 'U',
        'Ö' => 'O',
        'Ğ' => 'G',
        'ş' => 's',
        'ı' => 'i',
        'ç' => 'c',
        'ü' => 'u',
        'ö' => 'o',
        'ğ' => 'g',
        // Russian
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'J',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'H',
        'Ц' => 'C',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Sh',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'E',
        'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'j',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'e',
        'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye',
        'І' => 'I',
        'Ї' => 'Yi',
        'Ґ' => 'G',
        'є' => 'ye',
        'і' => 'i',
        'ї' => 'yi',
        'ґ' => 'g',
        // Czech
        'Č' => 'C',
        'Ď' => 'D',
        'Ě' => 'E',
        'Ň' => 'N',
        'Ř' => 'R',
        'Š' => 'S',
        'Ť' => 'T',
        'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c',
        'ď' => 'd',
        'ě' => 'e',
        'ň' => 'n',
        'ř' => 'r',
        'š' => 's',
        'ť' => 't',
        'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A',
        'Ć' => 'C',
        'Ę' => 'e',
        'Ł' => 'L',
        'Ń' => 'N',
        'Ó' => 'o',
        'Ś' => 'S',
        'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a',
        'ć' => 'c',
        'ę' => 'e',
        'ł' => 'l',
        'ń' => 'n',
        'ó' => 'o',
        'ś' => 's',
        'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A',
        'Č' => 'C',
        'Ē' => 'E',
        'Ģ' => 'G',
        'Ī' => 'i',
        'Ķ' => 'k',
        'Ļ' => 'L',
        'Ņ' => 'N',
        'Š' => 'S',
        'Ū' => 'u',
        'Ž' => 'Z',
        'ā' => 'a',
        'č' => 'c',
        'ē' => 'e',
        'ģ' => 'g',
        'ī' => 'i',
        'ķ' => 'k',
        'ļ' => 'l',
        'ņ' => 'n',
        'š' => 's',
        'ū' => 'u',
        'ž' => 'z'
    );

    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }

    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);

    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

/**
 * Get timezones list
 * @return array timezones
 */
function get_timezones_list()
{
    return $timezones = array(
        'Pacific/Midway' => "(GMT-11:00) Midway Island",
        'US/Samoa' => "(GMT-11:00) Samoa",
        'US/Hawaii' => "(GMT-10:00) Hawaii",
        'US/Alaska' => "(GMT-09:00) Alaska",
        'US/Pacific' => "(GMT-08:00) Pacific Time (US &amp; Canada)",
        'America/Tijuana' => "(GMT-08:00) Tijuana",
        'US/Arizona' => "(GMT-07:00) Arizona",
        'US/Mountain' => "(GMT-07:00) Mountain Time (US &amp; Canada)",
        'America/Chihuahua' => "(GMT-07:00) Chihuahua",
        'America/Mazatlan' => "(GMT-07:00) Mazatlan",
        'America/Mexico_City' => "(GMT-06:00) Mexico City",
        'America/Monterrey' => "(GMT-06:00) Monterrey",
        'Canada/Saskatchewan' => "(GMT-06:00) Saskatchewan",
        'US/Central' => "(GMT-06:00) Central Time (US &amp; Canada)",
        'US/Eastern' => "(GMT-05:00) Eastern Time (US &amp; Canada)",
        'US/East-Indiana' => "(GMT-05:00) Indiana (East)",
        'America/Bogota' => "(GMT-05:00) Bogota",
        'America/Lima' => "(GMT-05:00) Lima",
        'America/Caracas' => "(GMT-04:30) Caracas",
        'Canada/Atlantic' => "(GMT-04:00) Atlantic Time (Canada)",
        'America/La_Paz' => "(GMT-04:00) La Paz",
        'America/Santiago' => "(GMT-04:00) Santiago",
        'Canada/Newfoundland' => "(GMT-03:30) Newfoundland",
        'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
        'Greenland' => "(GMT-03:00) Greenland",
        'Atlantic/Stanley' => "(GMT-02:00) Stanley",
        'Atlantic/Azores' => "(GMT-01:00) Azores",
        'Atlantic/Cape_Verde' => "(GMT-01:00) Cape Verde Is.",
        'Africa/Casablanca' => "(GMT) Casablanca",
        'Europe/Dublin' => "(GMT) Dublin",
        'Europe/Lisbon' => "(GMT) Lisbon",
        'Europe/London' => "(GMT) London",
        'Africa/Monrovia' => "(GMT) Monrovia",
        'Europe/Amsterdam' => "(GMT+01:00) Amsterdam",
        'Europe/Belgrade' => "(GMT+01:00) Belgrade",
        'Europe/Berlin' => "(GMT+01:00) Berlin",
        'Europe/Bratislava' => "(GMT+01:00) Bratislava",
        'Europe/Brussels' => "(GMT+01:00) Brussels",
        'Europe/Budapest' => "(GMT+01:00) Budapest",
        'Europe/Copenhagen' => "(GMT+01:00) Copenhagen",
        'Europe/Ljubljana' => "(GMT+01:00) Ljubljana",
        'Europe/Madrid' => "(GMT+01:00) Madrid",
        'Europe/Paris' => "(GMT+01:00) Paris",
        'Europe/Prague' => "(GMT+01:00) Prague",
        'Europe/Rome' => "(GMT+01:00) Rome",
        'Europe/Sarajevo' => "(GMT+01:00) Sarajevo",
        'Europe/Skopje' => "(GMT+01:00) Skopje",
        'Europe/Stockholm' => "(GMT+01:00) Stockholm",
        'Europe/Vienna' => "(GMT+01:00) Vienna",
        'Europe/Warsaw' => "(GMT+01:00) Warsaw",
        'Europe/Zagreb' => "(GMT+01:00) Zagreb",
        'Europe/Athens' => "(GMT+02:00) Athens",
        'Europe/Bucharest' => "(GMT+02:00) Bucharest",
        'Africa/Cairo' => "(GMT+02:00) Cairo",
        'Africa/Harare' => "(GMT+02:00) Harare",
        'Europe/Helsinki' => "(GMT+02:00) Helsinki",
        'Europe/Istanbul' => "(GMT+02:00) Istanbul",
        'Asia/Jerusalem' => "(GMT+02:00) Jerusalem",
        'Europe/Kiev' => "(GMT+02:00) Kyiv",
        'Europe/Minsk' => "(GMT+02:00) Minsk",
        'Europe/Riga' => "(GMT+02:00) Riga",
        'Europe/Sofia' => "(GMT+02:00) Sofia",
        'Europe/Tallinn' => "(GMT+02:00) Tallinn",
        'Europe/Vilnius' => "(GMT+02:00) Vilnius",
        'Asia/Baghdad' => "(GMT+03:00) Baghdad",
        'Asia/Kuwait' => "(GMT+03:00) Kuwait",
        'Africa/Nairobi' => "(GMT+03:00) Nairobi",
        'Asia/Riyadh' => "(GMT+03:00) Riyadh",
        'Europe/Moscow' => "(GMT+03:00) Moscow",
        'Asia/Tehran' => "(GMT+03:30) Tehran",
        'Asia/Baku' => "(GMT+04:00) Baku",
        'Europe/Volgograd' => "(GMT+04:00) Volgograd",
        'Asia/Muscat' => "(GMT+04:00) Muscat",
        'Asia/Tbilisi' => "(GMT+04:00) Tbilisi",
        'Asia/Yerevan' => "(GMT+04:00) Yerevan",
        'Asia/Kabul' => "(GMT+04:30) Kabul",
        'Asia/Karachi' => "(GMT+05:00) Karachi",
        'Asia/Tashkent' => "(GMT+05:00) Tashkent",
        'Asia/Kolkata' => "(GMT+05:30) Kolkata",
        'Asia/Kathmandu' => "(GMT+05:45) Kathmandu",
        'Asia/Yekaterinburg' => "(GMT+06:00) Ekaterinburg",
        'Asia/Almaty' => "(GMT+06:00) Almaty",
        'Asia/Dhaka' => "(GMT+06:00) Dhaka",
        'Asia/Novosibirsk' => "(GMT+07:00) Novosibirsk",
        'Asia/Bangkok' => "(GMT+07:00) Bangkok",
        'Asia/Jakarta' => "(GMT+07:00) Jakarta",
        'Asia/Krasnoyarsk' => "(GMT+08:00) Krasnoyarsk",
        'Asia/Chongqing' => "(GMT+08:00) Chongqing",
        'Asia/Hong_Kong' => "(GMT+08:00) Hong Kong",
        'Asia/Kuala_Lumpur' => "(GMT+08:00) Kuala Lumpur",
        'Australia/Perth' => "(GMT+08:00) Perth",
        'Asia/Singapore' => "(GMT+08:00) Singapore",
        'Asia/Taipei' => "(GMT+08:00) Taipei",
        'Asia/Ulaanbaatar' => "(GMT+08:00) Ulaan Bataar",
        'Asia/Urumqi' => "(GMT+08:00) Urumqi",
        'Asia/Irkutsk' => "(GMT+09:00) Irkutsk",
        'Asia/Seoul' => "(GMT+09:00) Seoul",
        'Asia/Tokyo' => "(GMT+09:00) Tokyo",
        'Australia/Adelaide' => "(GMT+09:30) Adelaide",
        'Australia/Darwin' => "(GMT+09:30) Darwin",
        'Asia/Yakutsk' => "(GMT+10:00) Yakutsk",
        'Australia/Brisbane' => "(GMT+10:00) Brisbane",
        'Australia/Canberra' => "(GMT+10:00) Canberra",
        'Pacific/Guam' => "(GMT+10:00) Guam",
        'Australia/Hobart' => "(GMT+10:00) Hobart",
        'Australia/Melbourne' => "(GMT+10:00) Melbourne",
        'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
        'Australia/Sydney' => "(GMT+10:00) Sydney",
        'Asia/Vladivostok' => "(GMT+11:00) Vladivostok",
        'Asia/Magadan' => "(GMT+12:00) Magadan",
        'Pacific/Auckland' => "(GMT+12:00) Auckland",
        'Pacific/Fiji' => "(GMT+12:00) Fiji"
    );
}

function get_contact_user_id()
{
    $CI = & get_instance();
    if (!$CI->session->has_userdata('client_logged_in')) {
        return false;
    }
    return $CI->session->userdata('contact_user_id');
}

/**
 * Check if user is staff member
 * In the staff profile there is option to check IS NOT STAFF MEMBER eq like contractor
 * Some features are disabled when user is not staff member
 * @param  string  $id staff id
 * @return boolean
 */
function is_staff_member($id = '')
{
    $CI = & get_instance();
    $staffid = $id;
    if ($staffid == '') {
        $staffid = get_staff_user_id();
    }
    $CI->db->select('1')->from('tblstaff')->where('staffid', $staffid)->where('is_not_staff', 0);
    $row = $CI->db->get()->row();
    if ($row) {
        return true;
    }
    return false;
}

/**
 * Check if staff user has permission
 * @param  string  $permission permission shortname
 * @param  mixed  $staffid if you want to check for particular staff
 * @return boolean
 */
function has_permission($permission, $staffid = '', $can = '')
{
    $_permission = $permission;
    $CI = & get_instance();
    // check for passed is_admin function
    if (function_exists($permission) && is_callable($permission)) {
        return call_user_func($permission, $staffid);
    }
    if (is_admin($staffid)) {
        return true;
    }
    /* if ($permission == 'colis_en_attente' || $permission == 'quartiers_livreur' || $permission == 'bon_livraison' || $permission == 'quartiers') {
      return true;
      } */

    $_userid = get_staff_user_id();
    if ($staffid != '') {
        $_userid = $staffid;
    }

    if ($can == '') {
        return false;
    }

    $CI->db->select('permissionid');
    $CI->db->where('shortname', $permission);
    $permission = $CI->db->get('tblpermissions')->row();
    if (!$permission) {
        return false;
    }
    $CI->db->select('1');
    $CI->db->from('tblstaffpermissions');
    $CI->db->where('permissionid', $permission->permissionid);
    $CI->db->where('staffid', $_userid);
    $CI->db->where('can_' . $can, 1);
    $perm = $CI->db->get()->row();
    if ($perm) {
        return true;
    }

    return false;
}


function has_fac_permission()
{
    $has_permission = false;
    if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
        $has_permission =  'invoices';
    }
    if (has_permission('factures_ret', '', 'view') && has_permission('factures_ret', '', 'view_own')) {
    $has_permission =  'factures_ret';


    }

    return $has_permission;
}
/**
 * All permissions available in the app with conditions
 * @return array
 */
function get_permission_conditions()
{
    return array(
        'chat' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'staff' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'points_relais' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'quartiers_livreur' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'appels' => array(
            'view' => true,
            'view_own' => true,
            'edit' => false,
            'create' => false,
            'delete' => false,
            'download' => false,
            'export' => false
        ),
        'join_shipper' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'shipper' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'contrats' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'colis_en_attente' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => false,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'colis' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => true
        ),
        'import_colis' => array(
            'view' => false,
            'view_own' => false,
            'edit' => false,
            'create' => true,
            'delete' => false,
            'download' => false,
            'export' => false
        ),
        'status' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'demandes' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'bon_livraison' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => true
        ),
        'etat_colis_livrer' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => true
        ),
        'versements' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'invoices' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => true
        ),
        'factures_ret' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => true
        ),
        'factures_internes' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => true
        ),
        'payments' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => false
        ),
        'marketing' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        ),
        'expenses' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => false
        ),
        'supports' => array(
            'view' => true,
            'view_own' => true,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => true,
            'export' => false
        ),
        'claim_shipper' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => false,
            'delete' => true,
            'download' => true,
            'export' => false
        ),
        'quartiers' => array(
            'view' => true,
            'view_own' => false,
            'edit' => true,
            'create' => true,
            'delete' => true,
            'download' => false,
            'export' => false
        )

    );
}

function get_month_french()
{
    $month_french = array(
        'January' => 'Janvier',
        'February' => 'Février',
        'March' => 'Mars',
        'April' => 'Avril',
        'May' => 'Mai',
        'June' => 'Juin',
        'August' => 'Août',
        'September' => 'Septembre',
        'October' => 'Octobre',
        'November' => 'Novembre',
        'December' => 'Decembre'
    );

    return $month_french;
}

function get_days_french()
{
    $days_french = array(
        'Monday' => 'Lundi',
        'Tuesday' => 'Mardi',
        'Wednesday' => 'Mercredi',
        'Thursday' => 'Jeudi',
        'Friday' => 'Vendredi',
        'Saturday' => 'Samedi',
        'Sunday' => 'Dimanche'
    );

    return $days_french;
}

/**
 * Format money with 2 decimal based on symbol
 * @param  mixed $total
 * @param  string $symbol Money symbol
 * @return string
 */
function format_money($total, $symbol = '')
{
    if (!is_numeric($total) && $total != 0) {
        return false;
    }

    $decimal_separator = get_option('decimal_separator');
    $thousand_separator = get_option('thousand_separator');
    $_formated = number_format($total, 2, $decimal_separator, $thousand_separator) . ' ' . $symbol;

    return $_formated;
}

/**
 * Format facture type
 * @param  integer  $type
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_facture_type($type, $classes = '', $label = true)
{
    if ($type == 1) {
        $type = _l('facture_status_return');
        $label_class = 'danger';
    } else if ($type == 2) {
        $type = _l('facture_status_deliver');
        $label_class = 'success';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $type . '</span>';
    } else {
        return $type;
    }
}

/**
 * Format facture status
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_facture_status($status, $classes = '', $label = true)
{
    if ($status == 1) {
        $status = _l('facture_interne_status_non_regle');
        $label_class = 'danger';
    } else if ($status == 2) {
        $status = _l('facture_interne_status_regle');
        $label_class = 'success';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $status . '</span>';
    } else {
        return $status;
    }
}

/**
 * Format facture sent
 * @param  integer  $status
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_facture_send($sent, $datesend, $classes = '', $label = true)
{
    $_tooltip = '';
    $label_class = '';
    if ($sent == 1) {
        $_tooltip = _l('invoice_already_send_to_client_tooltip', time_ago($datesend));
        $label_class = 'info';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block mtop20">' . $_tooltip . '</span>';
    } else {
        return '';
    }
}

/**
 * Format colis etat
 * @param  integer  $colisid
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_etat_colis($etat_id = '', $colisid = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Etat Id if is not numeric
    if (!is_numeric($etat_id) && is_numeric($colisid)) {
        $CI->db->where('tblcolis.id', $colisid);
        $colis = $CI->db->get('tblcolis')->row();
        if ($colis) {
            $etat_id = $colis->etat_id;
        }
    }

    if (is_numeric($etat_id)) {
        $CI->db->where('tbletatcolis.id', $etat_id);
        $etat = $CI->db->get('tbletatcolis')->row();
        if ($etat) {
            $etat_name = $etat->name;
        }
    }

    if ($etat_id == 1) {
        $label_class = 'danger';
    } else if ($etat_id == 2) {
        $label_class = 'success';
    } else if ($etat_id == 3) {
        $label_class = 'info';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $etat_name . '</span>';
    } else {
        return $etat_id;
    }
}

/**
 * Format colis status
 * @param  integer  $colisid
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_colis($status_id = '', $colisid = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($status_id) && is_numeric($colisid)) {
        $CI->db->where('tblcolis.id', $colisid);
        $colis = $CI->db->get('tblcolis')->row();
        if ($colis) {
            $status_id = $colis->status_id;
        }
    }

    $statusName = '';
    $statusColor = '';
    if (is_numeric($status_id)) {
        $CI->db->where('tblstatuscolis.id', $status_id);
        $status = $CI->db->get('tblstatuscolis')->row();
        if ($status) {
            $statusName = $status->name;
            $statusColor = $status->color;
        }
    }
    
    if(empty($statusColor)) {
        $statusColor = '#777777';
    }

    if ($label == true) {
        return '<span class="label ' . $classes . ' inline-block" style="background-color: ' . $statusColor . ' !important;">' . $statusName . '</span>';
    } else {
        return $status_id;
    }
}

/**
 * Format type livraison colis
 * @param  string  $type
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_type_livraison_colis($type = '', $classes = '', $label = true)
{
    $CI = & get_instance();

    $typeName = '';
    if (!empty($type)) {
        if ($type == 'a_domicile') {
            $labelClass = 'default';
            $typeName = _l('a_domicile');
        } else if ($type == 'point_relai') {
            $labelClass = 'info';
            $typeName = _l('point_relais');
        } else {
            $labelClass = 'danger';
        }
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $typeName . '</span>';
    } else {
        return $type;
    }
}

/**
 * Format support priority 
 * @param  integer  $supportid
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_priority_support($priorityId = '', $supportid = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Etat Id if is not numeric
    if (!is_numeric($priorityId) && is_numeric($supportid)) {
        $CI->db->where('tblsupports.id', $supportid);
        $support = $CI->db->get('tblsupports')->row();
        if ($support) {
            $priorityId = $support->priority_id;
        }
    }

    $priorityName = '';
    if (is_numeric($priorityId)) {
        $CI->db->where('tblsupportpriorities.id', $priorityId);
        $priority = $CI->db->get('tblsupportpriorities')->row();
        if ($priority) {
            $priorityName = $priority->name;
        }
    }

    if ($priorityId == 1) {
        $label_class = 'default';
    } else if ($priorityId == 2) {
        $label_class = 'info';
    } else if ($priorityId == 3) {
        $label_class = 'warning';
    } else {
        $label_class = 'danger';
    }

    if ($label == true) {
        return '<span class="label label-' . $label_class . ' ' . $classes . ' inline-block">' . $priorityName . '</span>';
    } else {
        return $priorityId;
    }
}

/**
 * Check if current user is online
 * @param  mixed $staffid
 * @return boolean if user is not admin
 */
function is_online($id = '')
{
    $_id = get_staff_user_id();
    if (is_numeric($id)) {
        $_id = $id;
    }
    $CI = & get_instance();
    $CI->db->select('online');
    $CI->db->where('online', 1);
    $CI->db->where('staffid', $_id);
    return $CI->db->get('tblstaff')->row();
}

function icon_online($id)
{
    $icon = '';
    if ($id == 0) {
        $icon = '<div class="icon_offline pull-right" data-toggle="tooltip" title="' . _l('offline') . '"></div>';
    } else if ($id == 1) {
        $icon = '<div class="icon_online pull-right" data-toggle="tooltip" title="' . _l('online') . '"></div>';
    } else if ($id == 2) {
        $icon = '<div class="icon_occuped pull-right" data-toggle="tooltip" title="' . _l('occuped') . '"></div>';
    }

    return $icon;
}

function icon_online_conversation($status, $last_time_connected = '')
{
    $icon = '';
    if (!empty($last_time_connected)) {
        if (date('Y-m-d', strtotime($last_time_connected)) == date('Y-m-d', strtotime(date('Y-m-d')))) {
            $last_time_connected = 'aujourd\'hui à ' . date('H:i', strtotime($last_time_connected));
        } else if (date('Y-m-d', strtotime($last_time_connected)) == date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'))) {
            $last_time_connected = 'hier à ' . date('H:i', strtotime($last_time_connected));
        } else {
            $last_time_connected = 'le ' . date('d/m/Y', strtotime($last_time_connected)) . ' à ' . date('H:i', strtotime($last_time_connected));
        }
        $last_time_connected = '<span style="color: #000;">(vu ' . $last_time_connected . ')</span>';
    }
    if ($status == 0) {
        $icon = '<p class="icon-offline-conversation no-margin"><i class="fa fa-times-circle" data-toggle="tooltip" title="' . _l('offline') . '"></i> ' . _l('offline') . '</p>';
    } else if ($status == 1) {
        $icon = '<p class="icon-online-conversation no-margin"><i class="fa fa-check-circle" data-toggle="tooltip" title="' . _l('online') . '"></i> ' . _l('online') . '</p>';
    } else if ($status == 2) {
        $icon = '<p class="icon-occuped-conversation no-margin"><i class="fa fa-times-circle" data-toggle="tooltip" title="' . _l('occuped') . '"></i> ' . _l('occuped') . ' ' . $last_time_connected . '</p>';
    }

    return $icon;
}

function date_compare($a, $b)
{
    $t1 = strtotime($a['last_message_created_at']);
    $t2 = strtotime($b['last_message_created_at']);
    return $t2 - $t1;
}

/**
 * Format versements status
 * @param  integer  $versementId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_versement($statusId = '', $versementId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($versementId)) {
        $CI->db->where('tbllivreurversements.id', $versementId);
        $versement = $CI->db->get('tbllivreurversements')->row();
        if ($versement) {
            $statusId = $versement->status_id;
        }
    }

    $statusName = '';
    $labelClass = '';
    if ($statusId == 1) {
        $labelClass = 'danger';
        $statusName = _l('status_non_regle');
    } else if ($statusId == 2) {
        $labelClass = 'success';
        $statusName = _l('status_regle');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $statusName . '</span>';
    } else {
        return $statusId;
    }
}

/**
 * Format versements status
 * @param  integer  $versementId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_etat_colis_livrer($statusId = '', $etatColisLivrerId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($etatColisLivrerId)) {
        $CI->db->where('tbletatcolislivre.id', $etatColisLivrerId);
        $etatColisLivrer = $CI->db->get('tbletatcolislivre')->row();
        if ($etatColisLivrer) {
            $statusId = $etatColisLivrer->status;
        }
    }

    $statusName = '';
    $labelClass = '';
    if ($statusId == 1) {
        $labelClass = 'info';
        $statusName = _l('waiting');
    } else if ($statusId == 2) {
        $labelClass = 'success';
        $statusName = _l('validate');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $statusName . '</span>';
    } else {
        return $statusId;
    }
}

/**
 * Format versements status
 * @param  integer  $versementId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_etat_etat_colis_livrer($etatId = '', $etatColisLivrerId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($etatId) && is_numeric($etatColisLivrerId)) {
        $CI->db->where('tbletatcolislivre.id', $etatColisLivrerId);
        $etatColisLivrer = $CI->db->get('tbletatcolislivre')->row();
        if ($etatColisLivrer) {
            $etatId = $etatColisLivrer->etat;
        }
    }

    $statusName = '';
    $labelClass = '';
    if ($etatId == 1) {
        $labelClass = 'danger';
        $statusName = _l('status_non_regle');
    } else if ($etatId == 2) {
        $labelClass = 'success';
        $statusName = _l('status_regle');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $statusName . '</span>';
    } else {
        return $etatId;
    }
}

/**
 * Format bon livraison status
 * @param  integer  $bonLivraisonId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_bon_livraison($statusId = '', $bonLivraisonId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($bonLivraisonId)) {
        $CI->db->where('tblbonlivraison.id', $etatColisLivrerId);
        $bonLivraison = $CI->db->get('tblbonlivraison')->row();
        if ($bonLivraison) {
            $statusId = $bonLivraison->status;
        }
    }

    $statusName = '';
    $labelClass = '';
    if ($statusId == 1) {
        $labelClass = 'danger';
        $statusName = _l('status_not_confirmed');
    } else if ($statusId == 2) {
        $labelClass = 'success';
        $statusName = _l('status_confirmed');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $statusName . '</span>';
    } else {
        return $statusId;
    }
}

function format_status_bl_export($statusId = '', $bonLivraisonId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($bonLivraisonId)) {
        $CI->db->where('tblbonlivraison.id', $etatColisLivrerId);
        $bonLivraison = $CI->db->get('tblbonlivraison')->row();
        if ($bonLivraison) {
            $statusId = $bonLivraison->status;
        }
    }

    $statusName = '';
    $labelClass = '';
    if ($statusId == 1) {
        $labelClass = 'danger';
        $statusName = _l('status_not_confirmed');
    } else if ($statusId == 2) {
        $labelClass = 'success';
        $statusName = _l('status_confirmed');
    }

    if ($label == true) {
        return  $statusName ;
    } else {
        return $statusId;
    }
}

/**
 * Format type demande
 * @param  integer  $type
 * @param  integer  $demandeId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_type_demande($type = '', $demandeId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Priorite Id if is not numeric
    if (empty($type) && is_numeric($demandeId)) {
        $CI->db->where('tbldemandes.id', $demandeId);
        $demande = $CI->db->get('tbldemandes')->row();
        if ($demande) {
            $type = $demande->type;
        }
    }

    if ($type == 'demande') {
        $labelClass = 'info';
        $labelName = _l('request');
    } else {
        $labelClass = 'danger';
        $labelName = _l('reclamation');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $labelName . '</span>';
    } else {
        return $type;
    }
}

/**
 * Format priorite demande
 * @param  integer  $prioriteId
 * @param  integer  $demandeId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_priorite_demande($prioriteId = '', $demandeId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Priorite Id if is not numeric
    if (!is_numeric($prioriteId) && is_numeric($demandeId)) {
        $CI->db->where('tbldemandes.id', $demandeId);
        $demande = $CI->db->get('tbldemandes')->row();
        if ($demande) {
            $prioriteId = $demande->priorite;
        }
    }

    if ($prioriteId == 1) {
        $labelClass = 'info';
        $labelName = _l('low');
    } else if ($prioriteId == 2) {
        $labelClass = 'warning';
        $labelName = _l('average');
    } else if ($prioriteId == 3) {
        $labelClass = 'danger';
        $labelName = _l('high');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $labelName . '</span>';
    } else {
        return $prioriteId;
    }
}
function priorite_demande($prioriteId = '', $demandeId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Priorite Id if is not numeric
    if (!is_numeric($prioriteId) && is_numeric($demandeId)) {
        $CI->db->where('tbldemandes.id', $demandeId);
        $demande = $CI->db->get('tbldemandes')->row();
        if ($demande) {
            $prioriteId = $demande->priorite;
        }
    }

    if ($prioriteId == 1) {
        $labelClass = 'info';
        $labelName = _l('low');
    } else if ($prioriteId == 2) {
        $labelClass = 'warning';
        $labelName = _l('average');
    } else if ($prioriteId == 3) {
        $labelClass = 'danger';
        $labelName = _l('high');
    }

    if ($label == true) {
        return $labelName ;
    } else {
        return $prioriteId;
    }
}

/**
 * Format status demande
 * @param  integer  $statusId
 * @param  integer  $demandeId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_demande($statusId = '', $demandeId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($demandeId)) {
        $CI->db->where('tbldemandes.id', $demandeId);
        $demande = $CI->db->get('tbldemandes')->row();
        if ($demande) {
            $statusId = $demande->status;
        }
    }

    if ($statusId == 1) {
        $labelClass = 'warning';
        $labelName = _l('in_progress');
    } else if ($statusId == 2) {
        $labelClass = 'info';
        $labelName = _l('answered');
    } else if ($statusId == 3) {
        $labelClass = 'info';
        $labelName = _l('answered_per_customer');
    } else if ($statusId == 4) {
        $labelClass = 'success';
        $labelName = _l('fencing');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $labelName . '</span>';
    } else {
        return $statusId;
    }
}

function status_demande($statusId = '', $demandeId = '', $classes = '', $label = true)
{
    $CI = & get_instance();
    //Get Status Id if is not numeric
    if (!is_numeric($statusId) && is_numeric($demandeId)) {
        $CI->db->where('tbldemandes.id', $demandeId);
        $demande = $CI->db->get('tbldemandes')->row();
        if ($demande) {
            $statusId = $demande->status;
        }
    }

    if ($statusId == 1) {
        $labelClass = 'warning';
        $labelName = _l('in_progress');
    } else if ($statusId == 2) {
        $labelClass = 'info';
        $labelName = _l('answered');
    } else if ($statusId == 3) {
        $labelClass = 'info';
        $labelName = _l('answered_per_customer');
    } else if ($statusId == 4) {
        $labelClass = 'success';
        $labelName = _l('fencing');
    }

    if ($label == true) {
        return  $labelName ;
    } else {
        return $statusId;
    }
}

/**
 * Format departement
 * @param  integer  $departementId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_departement($departementId = '', $classes = '')
{
    $CI = & get_instance();
    //Get departement
    if (is_numeric($departementId)) {
        $CI->db->where('tbldepartements.id', $departementId);
        $departement = $CI->db->get('tbldepartements')->row();
        if ($departement) {
            $departementName = $departement->name;
            $departementColor = $departement->color;

            return '<span class="label ' . $classes . ' inline-block" style="height: 22px; border-color: ' . $departementColor . '; background-color: ' . $departementColor . ';">' . $departementName . '</span>';
        }
    } else {
        return $prioriteId;
    }
}

/**
 * Get Version
 */
function version_sources()
{
    $CI = &get_instance();
    //Version
    return $CI->config->item('version_source');
}

/**
 * Random Password
 */
function randomPassword()
{
    $alphabet = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $password[] = $alphabet[$n];
    }
    return implode($password);
}

/**
 * Correction phone number
 */
function correctionPhoneNumber($phoneNumber)
{
    if (!empty($phoneNumber)) {
        $phoneNumber0 = preg_replace("/\s/", "", $phoneNumber);
        $phoneNumber1 = str_replace("-", "", $phoneNumber0);
        $phoneNumber2 = str_replace("'", "", $phoneNumber1);
        $phoneNumber3 = str_replace("+2120", "0", $phoneNumber2);
        $phoneNumber4 = str_replace("2120", "0", $phoneNumber3);
        $phoneNumber5 = str_replace("+212", "0", $phoneNumber4);
        $phoneNumber6 = str_replace("212", "0", $phoneNumber5);
        if (strlen($phoneNumber6) == 9) {
            $phoneNumber6 = '0' . $phoneNumber6;
        }

        if (strlen($phoneNumber6) == 10 && preg_match("/^[0-9]{10}$/", $phoneNumber6)) {
            return $phoneNumber6;
        }
    }

    return $phoneNumber;
}

/**
 * Generate pagination
 */
function generate_pagination($url, $totalRows, $nbrParPage)
{
    $CI = &get_instance();
    // load Pagination library
    $CI->load->library('pagination');
    $tagOpen = '<li class="page-item">';
    $tagClose = '</li>';
    //Config Pagination
    $conf['base_url'] = $url;
    $conf['total_rows'] = $totalRows;
    $conf['per_page'] = $nbrParPage;
    $conf['num_links'] = 1;
    $conf['use_page_numbers'] = TRUE;
    $conf['full_tag_open'] = '';
    $conf['full_tag_close'] = '';
    //$conf['display_pages'] = FALSE;
    $conf['first_link'] = _l('dt_paginate_first');
    $conf['first_tag_open'] = $tagOpen;
    $conf['first_tag_close'] = $tagClose;
    $conf['prev_link'] = '<<';
    $conf['prev_tag_open'] = $tagOpen;
    $conf['prev_tag_close'] = $tagClose;
    $conf['next_link'] = '>>';
    $conf['next_tag_open'] = $tagOpen;
    $conf['next_tag_close'] = $tagClose;
    $conf['last_link'] = _l('dt_paginate_last');
    $conf['last_tag_open'] = $tagOpen;
    $conf['last_tag_close'] = $tagClose;
    $conf['num_tag_open'] = $tagOpen;
    $conf['num_tag_close'] = $tagClose;
    $conf['cur_tag_open'] = '<li class="page-item current-num-pagination">';
    $conf['cur_tag_close'] = $tagClose;
    $CI->pagination->initialize($conf);
    $pagination = $CI->pagination->create_links();

    return $pagination;
}

/**
 * Format access apis status
 * @param  integer  $bonLivraisonId
 * @param  string  $classes additional classes
 * @param  boolean $label   To include in html label or not
 * @return mixed
 */
function format_status_access_apis($statusId = '', $classes = '', $label = true)
{
    $CI = & get_instance();

    $statusName = '';
    $labelClass = '';
    if ($statusId == 1) {
        $labelClass = 'info';
        $statusName = _l('requested');
    } else if ($statusId == 2) {
        $labelClass = 'success';
        $statusName = _l('validate');
    } else if ($statusId == 3) {
        $labelClass = 'danger';
        $statusName = _l('blocked');
    }

    if ($label == true) {
        return '<span class="label label-' . $labelClass . ' ' . $classes . ' inline-block">' . $statusName . '</span>';
    } else {
        return $statusId;
    }
}

/**
 * Random Token Api Client
 */
function randomTokenApiClient()
{
    $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklnopqrstuvwxyz@$&";
    $token = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i <= 25; $i++) {
        $n = rand(0, $alphaLength);
        $token[] = $alphabet[$n];
    }
    return implode($token);
}

/**
 * Return entreprise ID from session
 * @return mixed
 */
function get_entreprise_id()
{
    $CI = & get_instance();
    if($CI->session->has_userdata('staff_user_id_entreprise')) {
        return $CI->session->userdata('staff_user_id_entreprise');
    } else if($CI->session->has_userdata('point_relais_user_id_entreprise')) {
        return $CI->session->userdata('point_relais_user_id_entreprise');
    }
    
    return NULL;
}

<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vtech_base
{

    private $options = array();
    // Instance CI
    private $_instance;

    function __construct()
    {
        $this->_instance = &get_instance();

        if ($this->_instance->config->item('installed') == true) {
            $options = $this->_instance->db->get('tbloptions')->result_array();
            foreach ($options as $option) {
                $this->options[$option['name']] = $option['value'];
            }
        }
    }

    public function get_options()
    {
        return $this->options;
    }

    public function get_option($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        
        return '';
    }

    /**
     * Function that will parse table data from the tables folder for amin area
     * @param  string $table  table filename
     * @param  array  $params additional params
     * @return void
     */
    public function get_table_data($id_E, $table, $params = array())
    {
        $hook_data = do_action('before_render_table_data', array(
            'table' => $table,
            'params' => $params
        ));
        foreach ($hook_data['params'] as $key => $val) {
            $$key = $val;
        }

        $table = $hook_data['table'];
        if (file_exists(VIEWPATH . 'admin/tables/my_' . $table . '.php')) {
            include_once(VIEWPATH . 'admin/tables/my_' . $table . '.php');
        } else {
            include_once(VIEWPATH . 'admin/tables/' . $table . '.php');
        }
        echo json_encode($output);
        die;
    }

}

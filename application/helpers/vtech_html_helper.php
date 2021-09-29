<?php
/**
 * Init admin head
 * @param  boolean $aside should include aside
 */
function init_head($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/head');
    $CI->load->view('admin/includes/header');
    $CI->load->view('admin/includes/customizer-sidebar');
    if ($aside == true) {
        $CI->load->view('admin/includes/aside');
    }

}function init_head_bonpop($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/head');

}


/**
 * Init point relais footer/tails
 */
function init_tail()
{
    $CI =& get_instance();
    $CI->load->view('admin/includes/scripts');
}
/**
 * Init point relais head
 * @param  boolean $aside should include aside
 */
function init_head_point_relais($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('point-relais/includes/head');
    $CI->load->view('point-relais/includes/header');
    if ($aside == true) {
        $CI->load->view('point-relais/includes/aside');
    }

}
/**
 * Init point relais footer/tails
 */
function init_tail_point_relais()
{
    $CI =& get_instance();
    $CI->load->view('point-relais/includes/scripts');
}
/**
 * Init client head
 * @param  boolean $aside should include aside
 */
function init_head_client($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('client/includes/head');
    $CI->load->view('client/includes/header');
    if ($aside == true) {
        $CI->load->view('client/includes/aside');
    }

}
/**
 * Init client footer/tails
 */
function init_tail_client()
{
    $CI =& get_instance();
    $CI->load->view('client/includes/scripts');
}
/**
 * Init livreur head
 * @param  boolean $aside should include aside
 */
function init_head_livreur($aside = true)
{
    $CI =& get_instance();
    $CI->load->view('livreur/includes/head');
    $CI->load->view('livreur/includes/header');
}
/**
 * Init livreur footer/tails
 */
function init_tail_livreur()
{
    $CI =& get_instance();
    $CI->load->view('livreur/includes/scripts');
}

/**
 * Remove <br /> html tags from string to show in textarea with new linke
 * @param  string $text
 * @return string formated text
 */
function clear_textarea_breaks($text)
{
    $_text  = '';
    $_text  = $text;
    $breaks = array(
        "<br />",
        "<br>",
        "<br/>"
    );
    $_text  = str_ireplace($breaks, "", $_text);
    $_text  = trim($_text);
    return $_text;
}
/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
function render_yes_no($name, $value, $label, $tooltip = '')
{
    if ($tooltip != '') {
        $tooltip = ' data-toggle="tooltip" title="' . _l($tooltip) . '"';
    }
    
    if($value == 1) {
        $checkedYes = 'checked';
        $checkedNo = '';
    } else {
        $checkedYes = '';
        $checkedNo = 'checked';
    }
    $input = '
        <div class="form-group" ' . $tooltip . '>
            <label for="' . $name . '" class="control-label mbot10 clearfix">' . _l($label) . '</label>
            <div class="radio radio-primary radio-inline">
                <input type="radio" id="radio-yes" name="' . $name . '" value="1" ' . $checkedYes . '>
                <label>' . _l('settings_yes') . '</label>
            </div>
            <div class="radio radio-primary radio-inline">
                <input type="radio" id="radio-no" name="' . $name . '" value="0" ' . $checkedNo . '>
                <label>' . _l('settings_no') . '</label>
            </div>
        </div>';
    
    return $input;
}
/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $option_value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
function render_yes_no_option($option_value, $label, $tooltip = '')
{
    ob_start();
    if ($tooltip != '') {
        $tooltip = ' data-toggle="tooltip" title="' . _l($tooltip) . '"';
    }
?>
        <div class="form-group"<?php
    echo $tooltip;
?>>
            <label for="<?php
    echo $option_value;
?>" class="control-label clearfix"><?php
    echo _l($label);
?></label>
            <div class="radio radio-primary radio-inline">
                <input type="radio" name="settings[<?php
    echo $option_value;
?>]" value="1" <?php
    if (get_option($option_value) == '1') {
        echo 'checked';
    }
?>>
                <label><?php
    echo _l('settings_yes');
?></label>
            </div>
            <div class="radio radio-primary radio-inline">
                <input type="radio" name="settings[<?php
    echo $option_value;
?>]" value="0" <?php
    if (get_option($option_value) == '0') {
        echo 'checked';
    }
?>>
                <label><?php
    echo _l('settings_no');
?></label>
            </div>
        </div>
        <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo $settings;
}

function get_relation_data($type, $rel_id = '')
{
    $CI =& get_instance();
    $data = array();
    if ($type == 'customer' || $type == 'customers') {
        $CI->load->model('clients_model');
        $data = $CI->clients_model->get($rel_id, 1);
    } else if ($type == 'invoice') {
        $CI->load->model('invoices_model');
        $data = $CI->invoices_model->get($rel_id);
    } else if ($type == 'estimate') {
        $CI->load->model('estimates_model');
        $data = $CI->estimates_model->get($rel_id);
    } else if ($type == 'contract' || $type == 'contracts') {
        $CI->load->model('contracts_model');
        $data = $CI->contracts_model->get($rel_id);
    } else if ($type == 'expense' || $type == 'expenses') {
        $CI->load->model('expenses_model');
        $data = $CI->expenses_model->get($rel_id);
    } else if ($type == 'lead' || $type == 'leads') {
        $CI->load->model('leads_model');
        $data = $CI->leads_model->get($rel_id);
    } else if ($type == 'proposal') {
        $CI->load->model('proposals_model');
        $data = $CI->proposals_model->get($rel_id);
    } else if ($type == 'tasks') {
        $CI->load->model('tasks_model');
        $data = $CI->tasks_model->get($rel_id);
    } else if ($type == 'staff') {
        $CI->load->model('staff_model');
        $data = $CI->staff_model->get($rel_id);
    } else if ($type == 'project') {
        $CI->load->model('projects_model');
        $data = $CI->projects_model->get($rel_id, array(''));
    }

    return $data;
}

function get_relation_values($relation, $type)
{
    if ($relation == '') {
        return;
    }
    $name = '';
    $id   = '';
    $link = '';
    if ($type == 'customer' || $type == 'customers') {
        if (is_array($relation)) {
            $id   = $relation['userid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->userid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('clients/client/' . $id);
    } else if ($type == 'invoice') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = format_invoice_number($id);
        } else {
            $id   = $relation->id;
            $name = format_invoice_number($id);
        }
        $link = admin_url('invoices/list_invoices/' . $id);
    } else if ($type == 'estimate') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = format_estimate_number($id);
        } else {
            $id   = $relation->id;
            $name = format_estimate_number($id);
        }
        $link = admin_url('estimates/list_estimates/' . $id);
    } else if ($type == 'contract' || $type == 'contracts') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['subject'];
        } else {
            $id   = $relation->id;
            $name = $relation->subject;
        }
        $link = admin_url('contracts/contract/' . $id);
    } else if ($type == 'ticket') {
        if (is_array($relation)) {
            $id   = $relation['ticketid'];
            $name = '#' . $relation['ticketid'];
        } else {
            $id   = $relation->ticketid;
            $name = '#' . $relation->ticketid;
        }
        $link = admin_url('tickets/ticket/' . $id);
    } else if ($type == 'expense' || $type == 'expenses') {
        if (is_array($relation)) {
            $id   = $relation['expenseid'];
            $name = $relation['category_name'] . ' - ' . _format_number($relation['amount']);

        } else {
            $id   = $relation->expenseid;
            $name = $relation->category_name . ' - ' . _format_number($relation->amount);
        }
        $link = admin_url('expenses/list_expenses/' . $id);
    } else if ($type == 'lead' || $type == 'leads') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
            if ($relation['email'] != '') {
                $name .= ' - ' . $relation['email'];
            }
        } else {
            $id   = $relation->id;
            $name = $relation->name;
            if ($relation->email != '') {
                $name .= ' - ' . $relation->email;
            }
        }
        $link = admin_url('leads/lead/' . $id);
    } else if ($type == 'proposal') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['subject'];
        } else {
            $id   = $relation->id;
            $name = $relation->subject;
        }
        $link = admin_url('proposals/proposal/' . $id);
    } else if ($type == 'tasks') {
        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('tasks/list_tasks/' . $id);
    } else if ($type == 'staff') {

        if (is_array($relation)) {
            $id   = $relation['staffid'];
            $name = $relation['firstname'] . ' ' . $relation['lastname'];
        } else {
            $id   = $relation->staffid;
            $name = $relation->firstname . ' ' . $relation->lastname;
        }
        $link = admin_url('profile/' . $id);

    } else if ($type == 'project') {

        if (is_array($relation)) {
            $id   = $relation['id'];
            $name = $relation['name'];
        } else {
            $id   = $relation->id;
            $name = $relation->name;
        }
        $link = admin_url('projects/project/' . $id);

    }

    return array(
        'name' => $name,
        'id' => $id,
        'link' => $link
    );
}

function init_relation_options($data, $type, $rel_id = '')
{
    echo '<option value=""></option>';
    foreach ($data as $relation) {
        $selected        = '';
        $relation_values = get_relation_values($relation, $type);
        if ($rel_id == $relation_values['id']) {
            $selected = ' selected';
        }
        echo '<option value="' . $relation_values['id'] . '"' . $selected . '>' . $relation_values['name'] . '</option>';
    }
}


/**
 * All projects tasks have their own styling and this function will return the class based
 * on bootstrap framework like defualt,warning,info
 * @param  mixed  $id
 * @param  boolean $replace_default_by_muted
 * @return string
 */
function project_status_color_class($id,$replace_default_by_muted = false){
   if ($id == 1 || $id == 5) {
        $class = 'default';
        if($replace_default_by_muted == true){
            $class = 'muted';
        }
    } else if ($id == 2) {
        $class = 'info';
    } else if ($id == 3) {
        $class = 'warning';
    } else {
        // ID == 4 finished
        $class = 'success';
    }

    $hook_data = do_action('project_status_color_class', array(
        'id' => $id,
        'class' => $class
    ));

    $class     = $hook_data['class'];
    return $class;
}
/**
 * Project status translate
 * @param  mixed  $id
 * @return string
 */
function project_status_by_id($id){
    $label = _l('project_status_'.$id);
    $hook_data = do_action('project_status_label',array('id'=>$id,'label'=>$label));
    $label = $hook_data['label'];
    return $label;
}

function render_input($name, $label = '', $value = '', $type = 'text', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }

    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $input .= '<label class="control-label">' . $_label . '</label>';
    }

    $input .= '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,$value) . '">';
    $input .= '</div>';

    return $input;
}
function new_render_input($name, $id, $label = '', $value = '', $type = 'text', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }

    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $input .= '<label class="control-label">' . $_label . '</label>';
    }

    $input .= '<input type="' . $type . '" name="' . $name . '" id="' . $id . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,$value) . '">';
    //$input .= '</div>';

    return $input;
}
function render_date_input($name, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }


        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }


        $input .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $input .= '<div class="input-group date">';
    $input .= '<div class="input-group-addon">
               <i class="fa fa-calendar calendar-icon"></i>
               </div>';
    $input .= '<input type="text" name="' . $name . '" id="' . $name . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,_d($value)) . '">';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
function new_render_date_input($name, $id, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';

    foreach ($input_attrs as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }


        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);

        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }


        $input .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $input .= '<div class="input-group date">';
    $input .= '<div class="input-group-addon">
               <i class="fa fa-calendar calendar-icon"></i>
               </div>';
    $input .= '<input type="text" name="' . $name . '" id="' . $id . '" class="form-control datepicker' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name,_d($value)) . '">';
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}
function render_textarea($name, $label = '', $value = '', $textarea_attrs = array(), $form_group_attr = array(), $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';

    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_textarea_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($textarea_class)) {
        $textarea_class = ' ' . $textarea_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $textarea .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }
    $textarea .= '<textarea id="' . $name . '" name="' . $name . '" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name,clear_textarea_breaks($value)) . '</textarea>';
    $textarea .= '</div>';

    return $textarea;

}
function new_render_textarea($name, $id, $label = '', $value = '', $textarea_attrs = array(), $form_group_attr = array(), $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';

    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_textarea_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($textarea_class)) {
        $textarea_class = ' ' . $textarea_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {

        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $textarea .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }
    $textarea .= '<textarea name="' . $name . '" id="' . $id . '" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name,clear_textarea_breaks($value)) . '</textarea>';
    $textarea .= '</div>';

    return $textarea;

}
function render_textarea_avancer($id, $name, $label = '', $value = '', $textarea_attrs = array(), $form_group_attr = array(), $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';

    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_textarea_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {

        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($textarea_class)) {
        $textarea_class = ' ' . $textarea_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $textarea .= '<div class="form-group mtop10' . $form_group_class . '" ' . $_form_group_attr . '>';
    
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translation_not_found_') !== false) {
            $_label = $label;
        } else if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        } 
        $textarea .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $textarea .= '<textarea id="' . $id . '" name="' . $name . '" class="form-control ckeditor' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name,($value)) . '</textarea>';
    $textarea .= '</div>';

    return $textarea;
}
function render_select($name, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '')
{
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';

    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $select .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    $select .= '<option value=""></option>';

    foreach ($options as $option) {

        $val       = '';
        $_selected = '';

        $key = '';

        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }

        if (!is_array($option_attrs[1])) {
            $val .= $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {                
                $val .= $option[$_val] . ' ';
            }
        }
        if(isset($option_attrs[2])) {
            if (!is_array($option_attrs[2])) {
                $val .= $option[$option_attrs[2]];
            } else {
                foreach ($option_attrs[2] as $_val) {
                    $val .=' )';
                }
            }   
        }
        $val           = trim($val);
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }

        if (isset($option_attrs[2])) {
            $data_sub_text = ' data-subtext=' . '"' . $option[$option_attrs[2]] . '"';
        }
        $select .= '<option value="' . $key . '"' . $_selected . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}

function render_select_clients($name, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '')
{
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';


    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $select .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    $select .= '<option value=""></option>';

    foreach ($options as $option) {

        $val       = '';
        $_selected = '';

        $key = '';

        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }

        if (!is_array($option_attrs[1])) {
            $val .= $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {  
                if ($_val == "name_company") {
                    $val .= $option[$_val];
                }
                if ($option["name_company"] == "" and $_val != "name_company") {              
                    $val .= $option[$_val] . ' ';
                }

            }
        }
        if (!is_array($option_attrs[2])) {
            $val .= $option[$option_attrs[2]];
        } else {
            foreach ($option_attrs[2] as $_val) {
                $val .=' )';
            }
        }
        $val           = trim($val);
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }

        if (isset($option_attrs[2])) {
            $data_sub_text = ' data-subtext=' . '"' . $option[$option_attrs[2]] . '"';
        }
        $select .= '<option value="' . $key . '"' . $_selected . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}

function render_select_supplier($name, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '')
{
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';


    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $select .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $select .= '<select id="' . $name . '" name="' . $name . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    $select .= '<option value=""></option>';

    foreach ($options as $option) {

        $val       = '';
        $_selected = '';

        $key = '';

        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }

        if (!is_array($option_attrs[1])) {
            $val .= $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {  
                if ($_val == "name_supplier") {
                    $val .= $option[$_val];
                }
                if ($option["name_supplier"] == "" and $_val != "name_supplier") {              
                    $val .= $option[$_val] . ' ';
                }

            }
        }
        if (!is_array($option_attrs[2])) {
            $val .= $option[$option_attrs[2]];
        } else {
            foreach ($option_attrs[2] as $_val) {
                $val .=' )';
            }
        }
        $val           = trim($val);
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }

        if (isset($option_attrs[2])) {
            $data_sub_text = ' data-subtext=' . '"' . $option[$option_attrs[2]] . '"';
        }
        $select .= '<option value="' . $key . '"' . $_selected . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}
function new_render_select($name, $id, $options, $option_attrs = array(), $label = '', $selected = '', $select_attrs = array(), $form_group_attr = array(), $form_group_class = '', $select_class = '')
{
    $select           = '';
    $_form_group_attr = '';
    $_select_attrs    = '';


    if (!isset($select_attrs['data-width'])) {
        $select_attrs['data-width'] = '100%';
    }
    foreach ($select_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_select_attrs .= $key . '=' . '"' . $val . '"';
    }

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($select_class)) {
        $select_class = ' ' . $select_class;
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    $select .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $select .= '<label for="' . $name . '" class="control-label">' . $_label . '</label>';
    }

    $select .= '<select name="' . $name . '" id="' . $id . '" class="selectpicker' . $select_class . '" ' . $_select_attrs . ' data-live-search="true">';
    $select .= '<option value=""></option>';

    foreach ($options as $option) {

        $val       = '';
        $_selected = '';

        $key = '';

        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }

        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val           = trim($val);
        $data_sub_text = '';
        if (!is_array($selected)) {
            if ($selected != '') {
                if ($selected == $key) {
                    $_selected = ' selected';
                }
            }
        } else {
            foreach ($selected as $id) {
                if ($key == $id) {
                    $_selected = ' selected';
                }
            }
        }

        if (isset($option_attrs[2])) {
            $data_sub_text = ' data-subtext=' . '"' . $option[$option_attrs[2]] . '"';
        }
        $select .= '<option value="' . $key . '"' . $_selected . '' . $data_sub_text . '>' . $val . '</option>';
    }
    $select .= '</select>';
    $select .= '</div>';

    return $select;
}

/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formated table
 */
function render_datatable($headings = array(), $class = '', $additional_classes = array(''), $table_attributes = array(), $tfoot = false, $tfoot_colspan = '', $nbr_column_total = 1)
{
    $_additional_classes = '';
    $_table_attributes   = '';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }


    $CI =& get_instance();
    $CI->load->library('user_agent');
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = ' ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '=' . '"' . $val . '"';
    }


    $_hide_header = '';
    if (in_array('hide-header', $additional_classes)) {
        $_hide_header = 'dt-hide-header';
    }
    $table = '<div class="table-responsive mtop15' . $IEfix . '"><table ' . $_table_attributes . ' class="table table-striped table-' . $class . ' animated fadeIn' . $_additional_classes . '">';
    $table .= '<thead class="' . $_hide_header . '">';
    $table .= '<tr>';

    foreach ($headings as $heading) {
        $table .= '<th>' . $heading . '</th>';
    }

    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    if($tfoot == true && is_numeric($tfoot_colspan)){
        $table .= '<tfoot><tr><th class="bold" colspan="'.$tfoot_colspan.'" style="font-size: 16px;">Total par page :</th>';
        for ($i=0; $i<$nbr_column_total; $i++) { 
            $table .= '<th class="bold ptop10"></th>';
        }
        $table .= '</tr></tfoot>';
    }
    $table .= '</table></div>';

    echo $table;
}
/**
 * Get company logo from company uploads folder
 * @param  string $url     href url of image
 * @param  string $classes Additional classes on href
 */
function get_company_logo($url = '', $classes = '')
{
    $company_logo = get_option('company_logo');
    $company_name = get_option('companyname');

    if ($url == '') {
        $url = site_url();
    } else {
        $url = site_url($url);
    }

    if ($company_logo != '') {
?>
       <a href="<?php
        echo $url;
?>" class="<?php
        echo $classes;
?> logo">
           <img src="<?php
        echo site_url('uploads/company/' . $company_logo);
?>" alt="<?php
        echo $company_name;
?>"></a>
           <?php
    } else if ($company_name != '') {
?>
           <a href="<?php
        echo $url;
?>" class="<?php
        echo $classes;
?> logo"><?php
        echo $company_name;
?></a>
           <?php
    } else {
        echo '';
    }
}

/**
 * Get staff profile image
 * @param  boolean $id      staff ID
 * @param  array   $classes Additional image classes
 * @param  string  $type    small/thumb
 * @return string           Image link
 */
function staff_profile_image($id = false, $classes = array('staff-profile-image'), $type = 'small', $img_attrs = array())
{
    $CI =& get_instance();

    $CI->db->select('profile_image,firstname,lastname');
    $CI->db->where('staffid', $id);
    $result = $CI->db->get('tblstaff')->row();

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }
    
    if ($result && !is_null($result->profile_image)) {
        $profile_image = '<img '.$_attributes.' src="' . site_url('uploads/staff_profile_images/' . $id . '/' . $type . '_' . $result->profile_image) . '" class="' . implode(' ', $classes) . '" alt="' . $result->firstname . ' ' . $result->lastname . '" />';
    } else {
        $profile_image = '<img src="' . site_url('assets/images/defaults/user-placeholder.jpg') . '" '.$_attributes.' class="' . implode(' ', $classes) . '" />';
    }

    return $profile_image;
}
/**
 * Generate small icon button / font awesome
 * @param  string $url        href url
 * @param  string $type       icon type
 * @param  string $class      button class
 * @param  array  $attributes additional attributes
 * @return string
 */
function icon_btn($url = '', $type = '', $class = 'btn-default', $attributes = array(), $staticUrl = false, $text = '')
{
    $_attributes = '';
    foreach ($attributes as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }

    if($url !== '#' && $url !== 'javascript:void(0)'){
        if($staticUrl == false) {
            $url = site_url($url);
        }
    }
    
    return '<a href="' . $url . '" class="btn ' . $class . ' btn-icon" ' . $_attributes . '><i class="fa fa-' . $type . '"></i> ' . $text . '</a>';
}

/**
 * Callback for check_for_links
 */
function _make_url_clickable_cb($matches)
{
    $ret = '';
    $url = $matches[2];
    if (empty($url))
        return $matches[0];
    // removed trailing [.,;:] from URL
    if (in_array(substr($url, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret = substr($url, -1);
        $url = substr($url, 0, strlen($url) - 1);
    }
    return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target='_blank'>$url</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_web_ftp_clickable_cb($matches)
{
    $ret  = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;
    if (empty($dest))
        return $matches[0];
    // removed trailing [,;:] from URL
    if (in_array(substr($dest, -1), array(
        '.',
        ',',
        ';',
        ':'
    )) === true) {
        $ret  = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest) - 1);
    }
    return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target='_blank'>$dest</a>" . $ret;
}
/**
 * Callback for check_for_links
 */
function _make_email_clickable_cb($matches)
{
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}
/**
 * Check for links/emails/ftp in string to wrap in href
 * @param  string $ret
 * @return string      formatted string with href in any found
 */
function check_for_links($ret)
{
    $ret = ' ' . $ret;
    // in testing, using arrays here was found to be faster
    $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    $ret = trim($ret);
    return $ret;
}

/**
 * Strip tags
 * @param  string $html string to strip tags
 * @return string
 */
function _strip_tags($html)
{
    return strip_tags($html, '<br>,<em>,<p>,<ul>,<li>,<h3>,<pre>,<code>,<a>,<img>,<ol>,<strong>,<blockquote>');
}

/**
 * Get mime class by mime - admin system function
 * @param  string $mime file mime type
 * @return string
 */
function get_mime_class($mime)
{
    if (empty($mime) || is_null($mime)) {
        return 'mime mime-file';
    }
    $_temp_mime = explode('/', $mime);
    $part1      = $_temp_mime[0];
    $part2      = $_temp_mime[1];

    // Image
    if ($part1 == 'image') {
        if (strpos($part2, 'photoshop') !== false) {
            return 'mime mime-photoshop';
        }
        return 'mime mime-image';
    } else if ($part1 == 'audio') {
        // Audio
        return 'mime mime-audio';
    } else if ($part1 == 'video') {
        // Video
        return 'mime mime-video';
    } else if ($part1 == 'text') {
        // Text
        return 'mime mime-file';
    } else if ($part1 == 'application') {
        if ($part2 == 'pdf') {
            // Pdf
            return 'mime mime-pdf';
        } else if ($part2 == 'illustrator') {
            // Ilustrator
            return 'mime mime-illustrator';
        } else if ($part2 == 'zip' || $part2 == 'gzip' || strpos($part2, 'tar') !== false || strpos($part2, 'compressed') !== false) {
            // Zip
            return 'mime mime-zip';
        } else if (strpos($part2, 'powerpoint') !== false || strpos($part2, 'presentation') !== false) {
            // PowerPoint
            return 'mime mime-powerpoint ';
        } else if (strpos($part2, 'excel') !== false || strpos($part2, 'sheet') !== false) {
            // Excel
            return 'mime mime-excel';
        } else if ($part2 == 'msword' || $part2 == 'rtf' || strpos($part2, 'document') !== false) {
            // Word
            return 'mime mime-word';
        } else {
            // Else
            return 'mime mime-file';
        }
    } else {
        return 'mime mime-file';
    }
}

/**
 * Function that format task status for the final user
 * @param  string  $id    status id
 * @param  boolean $text
 * @param  boolean $clean
 * @return string
 */
function format_task_status($id, $text = false, $clean = false)
{
    $status_name = _l('task_status_' . $id);
    $hook_data = do_action('task_status_name',array('current'=>$status_name,'status_id'=>$id));
    $status_name = $hook_data['current'];

    if ($clean == true) {
        return $status_name;
    }

    $label = get_status_label($id);

    if ($text == false) {
        $class = 'label label-' . $label;
    } else {
        $class = 'text-' . $label;
    }

    return '<span class="inline-block ' . $class . '">' . $status_name . '</span>';
}

/**
 * Return staff profile image url
 * @param  mixed $staff_id
 * @param  string $type
 * @return string
 */
function staff_profile_image_url($staff_id, $type = 'small')
{
    $url = base_url('assets/images/defaults/user-placeholder.jpg');
    $CI =& get_instance();
    $CI->db->select('profile_image');
    $CI->db->from('tblstaff');
    $CI->db->where('staffid', $staff_id);
    $staff = $CI->db->get()->row();
    if ($staff) {
        if (!is_null($staff->profile_image)) {
            $url = base_url('uploads/staff_profile_images/' . $staff_id . '/' . $type . '_' . $staff->profile_image);
        }
    }
    return $url;
}

/**
 * Render date time picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
function render_datetime_input($name, $label = '', $value = '', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '')
{
    $html = render_date_input($name, $label, $value, $input_attrs, $form_group_attr, $form_group_class, $input_class);
    $html = str_replace('datepicker', 'datetimepicker', $html);
    return $html;
}

/**
 * Coma separated tags for input
 * @param  array $tag_names
 * @return string
 */
function prep_tags_input($tag_names){
    $tag_names = array_filter($tag_names, function($value) { return $value !== ''; });
    return implode(',',$tag_names);
}

/**
 * Load app script based on option
 * Can load minified stylesheet and non minified
 *
 * This function also check if there is my_ prefix stylesheet to load them.
 * If in options is set to load minified files and the filename that is passed do not contain minified version the
 * original file will be used.
 *
 * @param  string $path
 * @param  string $filename
 * @return string
 */
function app_script($path,$filename){

    if(file_exists(FCPATH.$path.'/my_'.$filename)){
        $filename = 'my_'.$filename;
    }

    if(get_option('use_minified_files') == 1){
        $original_file_name = $filename;
        $_temp = explode('.',$filename);
        $last = count($_temp) -1;
        $extension = $_temp[$last];
        unset($_temp[$last]);
        $filename = '';
        foreach($_temp as $t){
            $filename .= $t.'.';
        }
        $filename.= 'min.'.$extension;
        if(!file_exists($path.'/'.$filename)){
            $filename = $original_file_name;
        }
    }
    return '<script src="'.base_url($path.'/'.$filename).'"></script>'.PHP_EOL;
}

/**
 * Return class based on task priority id
 * @param  mixed $id
 * @return string
 */
function get_task_priority_class($id)
{
    if ($id == 1) {
        $class = 'muted';
    } else if ($id == 2) {
        $class = 'info';
    } else if ($id == 3) {
        $class = 'warning';
    } else {
        $class = 'danger';
    }
    return $class;
}

/**
 * Get status label class for task
 * @param  mixed $id
 * @return string
 */
function get_status_label($id)
{
    $label = 'default';

    if ($id == 2) {
        $label = 'light-green';
    } else if ($id == 3) {
        $label = 'default';
    } else if ($id == 4) {
        $label = 'info';
    } else if ($id == 5) {
        $label = 'success';
    } else if ($id == 6) {
        $label = 'warning';
    }

    $hook_data = do_action('task_status_label',array('label'=>$label,'status_id'=>$id));
    $label = $hook_data['label'];
    return $label;
}

/**
 * Format task priority based on passed priority id
 * @param  mixed $id
 * @return string
 */
function task_priority($id)
{
    if ($id == '1') {
        $priority = _l('task_priority_low');
    } else if ($id == '2') {
        $priority = _l('task_priority_medium');
    } else if ($id == '3') {
        $priority = _l('task_priority_high');
    } else if ($id == '4') {
        $priority = _l('task_priority_urgent');
    } else {
        $priority = $id;
    }
    return $priority;
}

function init_colis_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('colis_list_code_barre'),
        _l('phone_number'),
        _l('colis_list_date_pickup'),
        _l('colis_list_status'),
        _l('colis_list_etat'),
        _l('colis_list_date_livraison'),
        _l('colis_list_city'),
        _l('colis_list_crbt'),
        _l('colis_list_fresh')
    );

    $table = render_datatable($table_data, 'colis-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_colis_facture_table($table_attributes = array())
{
    $table_data = array(
        '',
        _l('colis_list_code_barre'),
        _l('colis_list_name'),
        _l('colis_list_crbt'),
        _l('colis_list_date_pickup'),
        _l('colis_list_date_livraison'),
        _l('colis_list_etat'),
        _l('colis_list_status'),
        _l('colis_list_fresh')
    );

    $table = render_datatable($table_data, 'colis-facture', array(), $table_attributes);
    
    return $table;
}


function init_etat_facture_table($table_attributes = array())
{
    $table_data = array(
            '',
        _l('name'),
        _l('total'),
        _l('commision'),
       'Reste',
        _l('etat_colis_livrer_date_created'),
        _l('number_of_colis'),
        _l('number_colis_livre'),
        _l('number_colis_refuse')

    );

    $table = render_datatable($table_data, 'colis-facture', array(), $table_attributes);

    return $table;
}



function init_colis_bon_livraison_table($table_attributes = array())
{
    $table_data = array(
        '',
        _l('code_barre'),
        _l('client'),
        _l('city'),
        _l('crbt'),
        _l('colis_list_fresh'),
        _l('colis_list_date_pickup'),
        _l('colis_list_etat'),
        _l('status'),

    );

    $table = render_datatable($table_data, 'colis-bon-livraison', array(), $table_attributes);
    
    return $table;
}
function init_colis_bon_livraison_table2($table_attributes = array())
{
    $table_data = array(

        _l('code_barre'),
        _l('sms_sent'),
        'commentaire'
    );

    $table = render_datatable($table_data, 'colis-bon-livraison2', array(), $table_attributes);

    return $table;
}

function init_colis_en_attente_bon_livraison_table($table_attributes = array())
{
    $table_data = array(
        '',
        _l('colis_list_code_barre'),
        _l('colis_list_name'),
        _l('colis_list_city'),
        _l('colis_list_crbt'),
        _l('colis_list_date_created')
    );

    $table = render_datatable($table_data, 'colis-bon-livraison', array(), $table_attributes);
    
    return $table;
}


/**
 * Get client logo
 * @param  boolean $id      staff ID
 * @param  array   $classes Additional image classes
 * @param  string  $type    small/thumb
 * @return string           Image link
 */
function client_logo($id = false, $classes = array('staff-profile-image'), $type = 'small',$img_attrs = array())
{
    $CI =& get_instance();
    $CI->db->select('logo, nom');
    $CI->db->where('id', $id);
    $result = $CI->db->get('tblexpediteurs')->row();

    $_attributes = '';
    foreach ($img_attrs as $key => $val) {
        $_attributes .= $key . '=' . '"' . $val . '" ';
    }
    
    $urlImage = site_url('assets/images/defaults/user-placeholder.jpg');
    if (!is_null($result->logo)) {
        if(file_exists('uploads/clients/logo/' . $id . '/' . $type . '_' . $result->logo)) {
            $urlImage = site_url('uploads/clients/logo/' . $id . '/' . $type . '_' . $result->logo);
        }
        $logo = '<img '.$_attributes.' src="' . $urlImage . '" class="' . implode(' ', $classes) . '" title="' . $result->nom . '" />';
    } else {
        $logo = '<img src="' . $urlImage . '" '.$_attributes.' class="' . implode(' ', $classes) . '" title="' . $result->nom . '" />';
    }

    return $logo;
}

function init_items_etat_colis_livrer_table($table_attributes = array())
{
    $table_data = array(
        '',
        _l('colis_list_code_barre'),
        _l('client'),
        _l('colis_list_crbt'),
        _l('colis_list_date_pickup'),
        _l('colis_list_date_livraison'),
        _l('colis_list_etat'),
        _l('colis_list_status'),
        _l('colis_list_fresh')
    );

    $table = render_datatable($table_data, 'items-etat-colis-livrer', array(), $table_attributes);
    
    return $table;
}

function init_items_facture_interne_table($table_attributes = array())
{
    $table_data = array(
        '',
        '',
        _l('name'),
        _l('total_net'),
        _l('invoice_type'),
        _l('status'),
        _l('facture_interne_date_created'),
        _l('facture_interne_client')
    );

    $table = render_datatable($table_data, 'items-facture-interne', array(), $table_attributes);
    
    return $table;
}

function init_colis_en_attente_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('colis_list_code_barre'),
        _l('phone_number'),
        _l('colis_list_date_created'),
        _l('colis_list_status'),
        _l('colis_list_city'),
        _l('colis_list_crbt')
    );

    $table = render_datatable($table_data, 'colis-en-attente-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_bons_livraison_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('name'),
        _l('delivery_note_date_created'),
        _l('options')
    );

    $table = render_datatable($table_data, 'bons-livraison-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_factures_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('name'),
        _l('invoice_type'),
        _l('status'),
        _l('list_facture_date_created'),
        _l('list_facture_staff'),
        _l('options')
    );

    $table = render_datatable($table_data, 'factures-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_reclamations_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('reclamation_list_object'),
        _l('reclamation_list_states'),
        _l('reclamation_list_date_created'),
        _l('reclamation_list_staff'),
        _l('reclamation_list_date_traitement')
    );

    $table = render_datatable($table_data, 'reclamations-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_activity_log_expediteur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('utility_activity_log_dt_description'),
        _l('utility_activity_log_dt_date')
    );

    $table = render_datatable($table_data, 'activity-log-expediteur', array(), $table_attributes);
    
    return $table;
}

function init_colis_livreur_table($table_attributes = array())
{
    $table_data = array(
        _l('id'),
        _l('colis_list_code_barre'),
        _l('phone_number'),
        _l('colis_list_date_pickup'),
        _l('colis_list_status'),
        _l('colis_list_etat'),
        _l('colis_list_date_livraison'),
        _l('colis_list_city'),
        _l('colis_list_crbt'),
        _l('colis_list_fresh')
    );

    $table = render_datatable($table_data, 'colis-delivery-men', array(), $table_attributes);
    
    return $table;
}

function loader_waiting_ajax($top = '', $left = ''){
    return '<div class="wait" style="top:'.$top.';left:'.$left.';"><img src="'.site_url("assets/images/defaults/ajax-loader.gif").'" /></div>';
}

function render_input_group($name, $label = '', $value = '', $type = 'text', $input_attrs = array(), $form_group_attr = array(), $form_group_class = '', $input_class = '', $input_group_addon_text = '', $input_group_addon = 'right')
{
    $input = '';
    $_form_group_attr = '';
    $_input_attrs = '';

    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_input_attrs .= $key . '=' . '"' . $val . '"';
    }
    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = _l($val);
        }

        $_form_group_attr .= $key . '=' . '"' . $val . '"';
    }

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }

    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $_label = _l($label);
        if (mb_strpos($_label, 'translate_not_found_') !== false) {
            $_label = $label;
        }
        $input .= '<label class="control-label">' . $_label . '</label>';
    }
    
    $input .= '<div class="input-group date">';
    if($input_group_addon == 'left') {
        $input .= '<div class="input-group-addon">' . $input_group_addon_text . '</div>';   
    }
    $input .= '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '">';
    if($input_group_addon == 'right') {
        $input .= '<div class="input-group-addon">' . $input_group_addon_text . '</div>';    
    }
    $input .= '</div>';
    $input .= '</div>';

    return $input;
}

function render_input_hidden($id, $name = '', $value = '')
{
    if (!empty($name)) {
        $name = 'name="' . $name . '"';
    }
    
    $input = '<input type="hidden" id="' . $id . '" ' . $name . ' value="' . $value . '">';

    return $input;
}

function rating_demande($nbrRating = '')
{
    $imgRatingStart1 = site_url('assets/images/defaults/rating-star1.png');
    $imgRatingStart2 = site_url('assets/images/defaults/rating-star2.png');
    
    $rating = '';
    for($i=1; $i<=$nbrRating; $i++) {
        $rating .= '<img class="image-rating" src="' . $imgRatingStart2 . '">';
    }
    for($i=$nbrRating; $i<5; $i++) {
        $rating .= '<img class="image-rating" src="' . $imgRatingStart1 . '">';
    }
    
    return $rating;
}

function render_btn_copy($id, $title, $class = '', $type = 'text')
{
    $btn = '<span class="btn btn-info btn-icon ' . $class . '" data-id="' . $id . '" data-type="' . $type . '" data-title="' . _l($title) . '" onclick="__copy($(this))" data-toggle="title" title="' . _l('_make_a_copy_of', _l($title)) . '"><i class="fa fa-copy"></i></span>';
    
    return $btn;
}
/**
 * Icon user
 * @param  string $classes
 * @return string
 */
function render_icon_user($class = '')
{
    return '<i class="fa fa-user fs15 mright5 ' . $class . '"></i>';
}
/**
 * Icon motorcycle
 * @param  string $classes
 * @return string
 */
function render_icon_motorcycle($class = '')
{
    return '<i class="fa fa-motorcycle fs15 mright5 ' . $class . '"></i>';
}
/**
 * Icon university
 * @param  string $classes
 * @return string
 */
function render_icon_university($class = '')
{
    return '<i class="fa fa-university fs15 mright5 ' . $class . '"></i>';
}

/**
 * Icon by type user
 * @param integer $type
 * @return string
 */
function render_icon_user_by_type($type)
{
    $icon = '';
    if (is_numeric($type)) {
        switch ($type) {
            case 0:
                $icon = render_icon_motorcycle();
                break;
            case 4:
                $icon = render_icon_university();
                break;
            default:
                $icon = render_icon_user();
                break;
        }
    }
    
    return $icon;
}

/**
 * Format label nombre colis
 * @param integer $nombre
 * @return string
 */
function render_nombre_colis($nombre)
{
    $label = '';
    if (is_numeric($nombre)) {
        $classLabel = 'default';
        if ($nombre == 0) {
            $classLabel = 'danger';
        }
        
        $label = '<p style="text-align: center;"><span class="label label-' . $classLabel . ' inline-block">' . $nombre . '</span></p>';
    }
    
    return $label;
}

function render_nombre_colis_export($nombre)
{
    $label = '';
    if (is_numeric($nombre)) {
        $classLabel = 'default';
        if ($nombre == 0) {
            $classLabel = 'danger';
        }

        $label =  $nombre;
    }

    return $label;
}



<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12 no-padding">
                <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            </div>
            <?php if (isset($member)) { ?>
                <div class="member">
                    <?= form_hidden('isedit'); ?>
                    <?= form_hidden('memberid', $member->staffid); ?>
                </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs no-margin" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
                                    <?= _l('staff_profile_string'); ?>
                                </a>
                            </li>
                            <?php 
                            $displayOngletPermissions = '';
                            if ((!isset($member) && ($type != 1 || $type != 4)) || (isset($member) && (is_admin($member->staffid) || is_point_relais($member->staffid)))) {
                                $displayOngletPermissions = 'display-none';
                            }
                            ?>  
                            <li role="presentation">
                                <a id="bloc-onglet-permissions" class="<?= $displayOngletPermissions; ?>" href="#tab_staff_permissions" aria-controls="tab_staff_permissions" role="tab" data-toggle="tab">
                                    <?= _l('staff_add_edit_permissions'); ?>
                                </a>
                            </li>
                            
                            <?php if (isset($member) && is_point_relais($member->staffid)) { ?>
                            <li role="presentation">
                                <a id="bloc-onglet-point-relais" href="#points_relais" aria-controls="commisions" role="tab" data-toggle="tab">
                                    <?= _l('points_relais'); ?>
                                </a>
                            </li>
                            <?php } ?>  
                            <?php if (isset($member) && is_livreur($member->staffid)) { ?>
                                <li role="presentation">
                                    <a href="#commisions" aria-controls="commisions" role="tab" data-toggle="tab">
                                        <?= _l('commisions'); ?>
                                    </a>
                                </li>
                                <?php if (has_permission('colis_en_attente', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#colis-en-attente" aria-controls="colis-en-attente" role="tab" data-toggle="tab">
                                            <?= _l('colis_en_attente'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('colis', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#colis" aria-controls="colis" role="tab" data-toggle="tab">
                                            <?= _l('colis'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('bon_livraison', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#bon-livraison" aria-controls="bon-livraison" role="tab" data-toggle="tab">
                                            <?= _l('delivery_notes'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('etat_colis_livrer', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#etat-colis-livrer" aria-controls="etat-colis-livrer" role="tab" data-toggle="tab">
                                            <?= _l('etats_colis_livrer'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (has_permission('invoices', '', 'view')) { ?>
                                    <li role="presentation">
                                        <a href="#factures" aria-controls="factures" role="tab" data-toggle="tab">
                                            <?= _l('invoices'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li role="presentation">
                                    <a href="#chart" aria-controls="chart" role="tab" data-toggle="tab">
                                        <?= _l('chart'); ?>
                                    </a>
                                </li>
                                <?php if (is_admin()) { ?>
                                    <li role="presentation">
                                        <a href="#localisation" aria-controls="chart" role="tab" data-toggle="tab">
                                            <?= _l('localization'); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li role="presentation">
                                    <a href="#activity_log" aria-controls="activity-log" role="tab" data-toggle="tab">
                                        <?= _l('utility_activity_log'); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>

                    <?php if (isset($member)) { ?>
                        <div class="panel-heading">
                            <?= $member->firstname . ' ' . $member->lastname; ?>
                        </div>
                    <?php } ?>

                    <div class="panel-body">
                        <?= form_open_multipart($this->uri->uri_string(), array('class' => 'staff-form', 'autocomplete' => 'off')); ?>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
                                <?php if (is_admin() || has_permission('staff', '', 'create')) { ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="bold"><?= _l('set_as'); ?></h5>
                                            <?php 
                                            $_type = 0;
                                            if(isset($member) && is_numeric($member->admin)) {
                                                $_type = $member->admin;
                                            }
                                            ?>
                                            <input type="hidden" id="_type" value="<?= $_type; ?>">
                                            <input type="hidden" id="type_staff" value="<?= $type; ?>">
                                            
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ((isset($member) && $member->admin == 1) || (!isset($member) && $type == 1) ? 'checked' : ''); ?>
                                                <input type="radio" name="administrator" value="1" <?= $checked; ?> />
                                                <label class="control-label"><?= _l('administrator'); ?></label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ((isset($member) && $member->admin == 0) || (!isset($member) && $type == 0) ? 'checked' : ''); ?>
                                                <input type="radio" name="administrator" value="0" <?= $checked; ?> />
                                                <label class="control-label"><?= _l('delivery_man'); ?></label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ((isset($member) && $member->admin == 2) || (!isset($member) && $type == 2) ? 'checked' : ''); ?>
                                                <input type="radio" name="administrator" value="2" <?= $checked; ?> />
                                                <label class="control-label"><?= _l('agent_logistic'); ?></label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ((isset($member) && $member->admin == 3) || (!isset($member) && $type == 3) ? 'checked' : ''); ?>
                                                <input type="radio" name="administrator" value="3" <?= $checked; ?> />
                                                <label class="control-label"><?= _l('others'); ?></label>
                                            </div>
                                            <?php if (get_permission_module('points_relais') == 1) { ?>
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ((isset($member) && $member->admin == 4) || (!isset($member) && $type == 4) ? 'checked' : ''); ?>
                                                <input type="radio" name="administrator" value="4" <?= $checked; ?> />
                                                <label class="control-label"><?= _l('point_relai'); ?></label>
                                            </div>
                                            <?php } ?>
                                            <hr />
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?php $value = (isset($member) ? $member->firstname : ''); ?>
                                        <?php $attrs = (isset($member) ? array() : array('autofocus' => true)); ?>
                                        <?= render_input('firstname', 'staff_add_edit_firstname', $value, 'text', $attrs); ?>

                                        <?php $selected = (isset($member) ? $member->city : ''); ?>
                                        <?= render_select('city', $cities, array('id', array('name')), 'staff_add_edit_city', $selected); ?>

                                        <?php $value = (isset($member) ? $member->email : ''); ?>
                                        <?= render_input('email', 'staff_add_edit_email', $value, 'email', array('autocomplete' => 'off')); ?>


                                        <?php $value = (isset($member) ? $member->rib : ''); ?>
                                        <?= render_input('rib', 'staff_add_edit_rib', $value, 'text', array('autocomplete' => 'off')); ?>



                                        <div class="form-group">
                                            <p class="bold"><?= _l('mode_of_payment'); ?></p>
                                            <?php $value = (isset($member) ? $member->payment_type : 1); ?>
                                            <input type="hidden" id="payment_type" value="<?= $value; ?>">
                                            
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ($value == 1 ? 'checked' : ''); ?>
                                                <input type="radio" name="payment_type" value="1" <?= $checked; ?> />
                                                <label><?= _l('per_month'); ?></label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <?php $checked = ($value == 2 ? 'checked' : ''); ?>
                                                <input type="radio" name="payment_type" value="2" <?= $checked; ?> />
                                                <label><?= _l('per_day'); ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <?php $value = (isset($member) ? $member->lastname : ''); ?>
                                        <?= render_input('lastname', 'staff_add_edit_lastname', $value); ?>

                                        <?php if (isset($member->departments)){ ?>
                                            <?php  $selected = (isset($member) ? json_decode( $member->departments) : ''); ?>
                                        <?php  } else if (isset($member->department)) { ?>
                                            <?php $selected = (isset($member) ? $member->department : ''); }?>

                                        <?= form_hidden('departments[]', $selected); ?>

                                        <?= render_select('department', $departements,array('id', array('name')), 'department', $selected,array('multiple' => true)); ?>

                                        <div class="form-group">
                                            <label for="password" class="control-label"><?= _l('staff_add_edit_password'); ?></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control password" name="password" autocomplete="off">
                                                <span class="input-group-addon">
                                                    <a href="#password" class="show_password" onclick="showPassword('password');
                                                            return false;"><i class="fa fa-eye"></i></a>
                                                </span>
                                                <span class="input-group-addon">
                                                    <a href="#" class="generate_password" onclick="generatePassword(this);
                                                            return false;"><i class="fa fa-refresh"></i></a>
                                                </span>
                                            </div>
                                        </div>

                                        <?php $value = (isset($member) ? $member->menu_ouvert : 0); ?>
                                        <?= render_yes_no('menu_ouvert', $value, 'laisser_le_menu_toujours_ouvert'); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                                        <?= render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>

                                        <div class="form-group">
                                            <label for="default_language" class="control-label"><?= _l('localization_default_language'); ?></label>
                                            <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?= _l('system_default_string'); ?></option>
                                                <?php
                                                foreach (list_folders(APPPATH . 'language') as $language) {
                                                    $selected = '';
                                                    if (!isset($member) || (isset($member) && is_null($member->default_language))) {
                                                        $l = 'french';
                                                    } else {
                                                        $l = $member->default_language;
                                                    }
                                                    if ($l == $language) {
                                                        $selected = 'selected';
                                                    }

                                                    ?>
                                                    <option value="<?= $language; ?>" <?= $selected; ?>><?= ucfirst($language); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane bloc-permissions" id="tab_staff_permissions">
                                <?php
                                do_action('staff_render_permissions');
                                $selected = '';
                                foreach ($roles as $role) {
                                    if (isset($member)) {
                                        if ($member->role == $role['roleid']) {
                                            $selected = $role['roleid'];
                                        }
                                    }
                                }

                                ?>
                                <?= render_select('role', $roles, array('roleid', 'name'), 'staff_add_edit_role', $selected); ?>
                                <hr />
                                <h4 class="font-medium mbot15 bold"><?= _l('staff_add_edit_permissions'); ?></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered roles no-margin">
                                        <thead>
                                            <tr>
                                                <th class="bold"><?= _l('permission'); ?></th>
                                                <th class="text-center bold"><?= _l('permission_view'); ?> (<?= _l('permission_global'); ?>)</th>
                                                <th class="text-center bold"><?= _l('permission_view_own'); ?></th>
                                                <th class="text-center bold"><?= _l('permission_create'); ?></th>
                                                <th class="text-center bold"><?= _l('permission_edit'); ?></th>
                                                <th class="text-center bold"><?= _l('permission_download'); ?></th>
                                                <th class="text-center bold"><?= _l('permission_export'); ?></th>
                                                <th class="text-center text-danger bold"><?= _l('permission_delete'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($member)) {
                                                $is_admin = is_admin($member->staffid);
                                            }
                                            $conditions = get_permission_conditions();
                                            foreach ($permissions as $permission) {
                                                $permission_condition = $conditions[$permission['shortname']];

                                                ?>
                                                <tr data-id="<?= $permission['permissionid']; ?>">
                                                    <td class="bold">
                                                        <?= _l(str_replace(" ", "_", strtolower($permission["name"]))); ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($permission_condition['view'] == true) {
                                                            $disabled = '';
                                                            $statement = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'view')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" data-can-view <?= $statement; ?> <?= $disabled; ?> name="view[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php
                                                        if ($permission_condition['view_own'] == true) {
                                                            $disabled = '';
                                                            $statement = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'view_own')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" <?= $statement; ?> <?= $disabled; ?> data-shortname="<?= $permission['shortname']; ?>" data-can-view-own name="view_own[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td  class="text-center">
                                                        <?php
                                                        if ($permission_condition['create'] == true) {
                                                            $statement = '';
                                                            $disabled = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'create')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-create <?= $statement; ?> <?= $disabled; ?> name="create[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td  class="text-center">
                                                        <?php
                                                        if ($permission_condition['edit'] == true) {
                                                            $statement = '';
                                                            $disabled = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'edit')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-edit <?= $statement; ?> <?= $disabled; ?> name="edit[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td  class="text-center">
                                                        <?php
                                                        if ($permission_condition['download'] == true) {
                                                            $statement = '';
                                                            $disabled = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'download')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-download <?= $statement; ?> <?= $disabled; ?> name="download[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td  class="text-center">
                                                        <?php
                                                        if ($permission_condition['export'] == true) {
                                                            $statement = '';
                                                            $disabled = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'export')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox">
                                                                <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-export <?= $statement; ?> <?= $disabled; ?> name="export[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td  class="text-center">
                                                        <?php
                                                        if ($permission_condition['delete'] == true) {
                                                            $statement = '';
                                                            $disabled = '';
                                                            if (isset($is_admin) && $is_admin) {
                                                                $disabled = 'disabled';
                                                                $statement = 'checked';
                                                            } else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'delete')) {
                                                                $statement = 'checked';
                                                            }

                                                            ?>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-delete <?= $statement; ?> <?= $disabled; ?> name="delete[]" value="<?= $permission['permissionid']; ?>">
                                                                <label></label>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <?php if (isset($member)) { ?>
                                <?php if (is_point_relais($member->staffid)) { ?>
                                    <input type="hidden" id="staff_point_relais">
                                    <input type="hidden" id="staff_id" value="<?= $member->staffid; ?>">
                                    <div role="tabpanel" class="tab-pane bloc-point-relais" id="points_relais">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#point_relais_modal"><?= _l('new_point_relais'); ?></a>
                                                <div class="clearfix"></div>
                                                <?php
                                                render_datatable(array(
                                                    _l('point_relais'),
                                                    _l('address'),
                                                    _l('city'),
                                                    _l('staff'),
                                                    _l('date_created'),
                                                    _l('options'),
                                                    ), 'points-relais');

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (is_livreur($member->staffid)) { ?>
                                    <input type="hidden" id="livreurid" value="<?= $member->staffid; ?>">
                                    <div role="tabpanel" class="tab-pane" id="commisions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#commision_modal"><?= _l('new_commision'); ?></a>
                                                <div class="clearfix"></div>
                                                <?php
                                                render_datatable(array(
                                                    _l('city'),
                                                    _l('commision'),
                                                    _l('date_created'),
                                                    _l('staff'),
                                                    _l('commision') .'  '. _l('refused'),
                                                    _l('options'),

                                                    ), 'commisions');

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (has_permission('colis_en_attente', '', 'view')) { ?>
                                        <div role="tabpanel" class="tab-pane" id="colis-en-attente">
                                            <div class="row mbot15">
                                                <div class="col-md-12 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('colis_en_attente_summary'); ?></h3>
                                                </div>
                                                <div class="col-md-2">
                                                    <h3 class="bold"><?= total_rows('tblcolisenattente', array('id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-default bold"><?= _l('total'); ?></span></a>
                                                </div>
                                            </div>
                                            <?php
                                            render_datatable(array(
                                                _l('colis_list_code_barre'),
                                                _l('colis_list_num_commande'),
                                                _l('colis_list_client'),
                                                _l('phone_number'),
                                                _l('date_created'),
                                                _l('colis_list_status'),
                                                _l('colis_list_city'),
                                                _l('colis_list_crbt')
                                                ), 'colis-en-attente-delivery-men');

                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (has_permission('colis', '', 'view')) { ?>
                                        <div role="tabpanel" class="tab-pane" id="colis">
                                            <div class="row mbot15">
                                                <div class="col-md-6 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('colis_summary'); ?></h3>
                                                </div>
                                                <div class="col-md-6 mbot10">
                                                    <div class="btn-group pull-right" data-toggle="tooltip" title="<?= _l('state_colis_filter'); ?>">
                                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fa fa-list"></i> <?= _l('state_colis_filter'); ?>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a href="#" onclick="dt_etat_colis_view(1, '.table-colis-livreur');
                                                                                    return false;">
                                                                       <?= _l('unpaid'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" onclick="dt_etat_colis_view(2, '.table-colis-livreur');
                                                                                    return false;">
                                                                       <?= _l('paid'); ?>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#" onclick="dt_etat_colis_view(3, '.table-colis-livreur');
                                                                                    return false;">
                                                                       <?= _l('invoiced'); ?>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view('all', '.table-colis-livreur');
                                                                        return false;"><span class="text-success bold"><?= _l('total'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 2, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(2, '.table-colis-livreur');
                                                                        return false;"><span class="text-success bold"><?= _l('colis_delivered'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 3, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(3, '.table-colis-livreur');
                                                                        return false;"><span class="text-danger bold"><?= _l('colis_returned'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_id' => 1, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(1, '.table-colis-livreur');
                                                                        return false;"><span class="text-warning bold"><?= _l('status_colis_current'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 4, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(4, '.table-colis-livreur');
                                                                        return false;"><span class="text-success bold"><?= _l('status_colis_shipped'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 9, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(9, '.table-colis-livreur');
                                                                        return false;"><span class="text-danger bold"><?= _l('status_colis_refused'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 11, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(11, '.table-colis-livreur');
                                                                        return false;"><span class="text-info bold"><?= _l('status_colis_postponed'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 10, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(10, '.table-colis-livreur');
                                                                        return false;"><span class="text-warning bold"><?= _l('status_colis_cancelled'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 7, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(7, '.table-colis-livreur');
                                                                        return false;"><span class="text-info bold"><?= _l('status_colis_unreachable'); ?></span></a>
                                                </div>
                                                <div class="col-md-2">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 6, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(6, '.table-colis-livreur');
                                                                        return false;"><span class="text-primary bold"><?= _l('status_colis_no_answer'); ?></span></a>
                                                </div>
                                                <div class="col-md-2">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 8, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(8, '.table-colis-livreur');
                                                                        return false;"><span class="text-primary bold"><?= _l('status_colis_wrong_number'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblcolis', array('status_reel' => 13, 'livreur' => $member->staffid)); ?></h3>
                                                    <a href="#" onclick="dt_custom_view(13, '.table-colis-livreur');
                                                                        return false;"><span class="text-success bold"><?= _l('status_colis_in'); ?></span></a>
                                                </div>
                                            </div>
                                            <div class="row mbot15">
                                                <div class="col-md-12 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('colis_sum'); ?></h3>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 2 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-success bold"><?= _l('fresh_sum_colis_delivered'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 2 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-success bold"><?= _l('price_sum_colis_delivered'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 3 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-danger bold"><?= _l('price_sum_colis_returned'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 3 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-danger bold"><?= _l('price_sum_colis_returned'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'frais'), 'status_id = 1 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-info bold"><?= _l('price_sum_colis_in_progress'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h4 class="bold"><?= format_money(sum_from_table('tblcolis', array('field' => 'crbt'), 'status_id = 1 AND livreur = ' . $member->staffid)) . ' Dhs'; ?></h4>
                                                    <a href="#"><span class="text-info bold"><?= _l('price_sum_colis_in_progress'); ?></span></a>
                                                </div>
                                            </div>
                                            <?= form_hidden('custom_view'); ?>
                                            <?= form_hidden('etat'); ?>
                                            <?php init_colis_livreur_table(); ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (has_permission('bon_livraison', '', 'view')) { ?>
                                        <div role="tabpanel" class="tab-pane" id="bon-livraison">
                                            <div class="row mbot15">
                                                <div class="col-md-12 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('delivery_notes_summary'); ?></h3>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblbonlivraison', array('id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-default bold"><?= _l('total'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblbonlivraison', array('type' => 1, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-info bold"><?= _l('delivery_note_type_output'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblbonlivraison', array('type' => 2, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-danger bold"><?= _l('delivery_note_type_returned'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblbonlivraison', array('status' => 1, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-danger bold"><?= _l('status_not_confirmed'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblbonlivraison', array('status' => 2, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-success bold"><?= _l('status_confirmed'); ?></span></a>
                                                </div>
                                            </div>
                                            <?php
                                            render_datatable(array(
                                                _l('name'),
                                                _l('type'),
                                                _l('status'),
                                                _l('delivery_note_number_of_delivery_notes'),
                                                _l('date_created'),
                                                _l('staff')
                                                ), 'delivery-notes-delivery-men');

                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (has_permission('etat_colis_livrer', '', 'view')) { ?>
                                        <div role="tabpanel" class="tab-pane" id="etat-colis-livrer">
                                            <div class="row mbot15">
                                                <div class="col-md-12 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('etats_colis_livrer_summary'); ?></h3>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tbletatcolislivre', array('id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-default bold"><?= _l('total'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tbletatcolislivre', array('status' => 1, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-info bold"><?= _l('waiting'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tbletatcolislivre', array('status' => 2, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-success bold"><?= _l('validate'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tbletatcolislivre', array('etat' => 1, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-danger bold"><?= _l('status_non_regle'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tbletatcolislivre', array('etat' => 2, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-success bold"><?= _l('status_regle'); ?></span></a>
                                                </div>
                                            </div>
                                            <?php
                                            render_datatable(array(
                                                _l('name'),
                                                _l('total'),
                                                _l('total_received'),
                                                _l('rest'),
                                                _l('commision'),
                                                _l('number_of_colis'),
                                                _l('number_of_versements'),
                                                _l('status'),
                                                _l('state'),
                                                _l('date_created'),
                                                _l('staff')
                                                ), 'etat-colis-livrer-delivery-men');

                                            ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (has_permission('invoices', '', 'view')) { ?>
                                        <div role="tabpanel" class="tab-pane" id="factures">
                                            <div class="row mbot15">
                                                <div class="col-md-12 mbot10">
                                                    <h3 class="text-success no-margin"><?= _l('factures_summary'); ?></h3>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblfactures', array('id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-default bold"><?= _l('total'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblfactures', array('type' => 3, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-danger bold"><?= _l('returned'); ?></span></a>
                                                </div>
                                                <div class="col-md-2 border-right">
                                                    <h3 class="bold"><?= total_rows('tblfactures', array('status' => 1, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-danger bold"><?= _l('status_non_regle'); ?></span></a>
                                                </div>
                                                <div class="col-md-2">
                                                    <h3 class="bold"><?= total_rows('tblfactures', array('status' => 2, 'id_livreur' => $member->staffid)); ?></h3>
                                                    <a href="#"><span class="text-success bold"><?= _l('status_regle'); ?></span></a>
                                                </div>
                                            </div>
                                            <?php
                                            render_datatable(array(
                                                _l('name'),
                                                _l('expediteur'),
                                                _l('total_crbt'),
                                                _l('total_frais'),
                                                _l('total_net'),
                                                _l('number_of_colis'),
                                                _l('invoice_type'),
                                                _l('status'),
                                                _l('date_created'),
                                                _l('staff')
                                                ), 'factures-delivery-men');

                                            ?>
                                        </div>
                                    <?php } ?>
                                    <div role="tabpanel" class="tab-pane" id="chart">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group" id="report-time">
                                                    <label class="form-label"><?= _l('report_period'); ?></label>
                                                    <select class="selectpicker" id="months-report-livreur" data-width="100%">
                                                        <option value=""><?= _l('report_sales_months_all_time'); ?></option>
                                                        <option value="6"><?= _l('report_sales_months_six_months'); ?></option>
                                                        <option value="12"><?= _l('report_sales_months_twelve_months'); ?></option>
                                                        <option value="custom"><?= _l('report_sales_months_custom'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="date-range-livreur" class="form-group hide animated">
                                                <div class="col-md-4">
                                                    <label for="report-from" class="control-label"><?= _l('report_sales_from_date'); ?></label>
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" id="report-from-livreur">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="report-to" class="control-label"><?= _l('report_sales_to_date'); ?></label>
                                                    <div class="input-group date">
                                                        <input type="text" class="form-control datepicker" disabled="disabled" id="report-to-livreur">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar calendar-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <canvas id="chart-livreur" class="animated fadeIn"></canvas>
                                        </div>
                                    </div>
                                    <?php if (is_admin()) { ?>
                                        <div role="tabpanel" class="tab-pane" id="localisation">
                                            <div class="row">
                                                <?php if (!empty(get_option('google_api_key'))) { ?>
                                                    <div class="col-md-12">
                                                        <div id="map" class="bloc_map"></div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-md-12">
                                                        <h3 class="text-center"><?= _l('setup_google_api_key_customer_map'); ?></h3>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div role="tabpanel" class="tab-pane" id="activity_log">
                                    <?php
                                    render_datatable(array(
                                        _l('description'),
                                        _l('date_created')
                                        ), 'activity-log-staff');

                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <button id="submit" type="submit" class="btn btn-info pull-right mtop20"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal animated fadeIn" id="commision_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title"><?= _l('commision_edit_heading'); ?></span>
                        <span class="add-title"><?= _l('commision_add_heading'); ?></span>
                    </h4>
                </div>
                <?= form_open('admin/commisions/commision', array('id' => 'form-commision')); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= render_select('ville', $cities, array('id', array('name')), 'city'); ?>
                            <?= render_input('commision', 'commision', '', 'number'); ?>
                            <?= render_input('commision_refuse', 'commision refuse', '', 'number'); ?>

                            <?= form_hidden('livreur'); ?>
                            <?= form_hidden('id'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                    <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                    <?= form_close(); ?>
                </div>
            </div>
        </div>
    </div>
   
    <?php if (get_permission_module('points_relais') == 1) { ?>
    <div class="modal animated fadeIn" id="point_relais_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title"><?= _l('point_relais_edit_heading'); ?></span>
                        <span class="add-title"><?= _l('point_relais_add_heading'); ?></span>
                    </h4>
                </div>
                <?= form_open('admin/staff/point_relais', array('id' => 'form-point-relais')); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= render_select('point_relais_id', $points_relais, array('id', array('nom')), 'point_relais'); ?>
                            <?= form_hidden('staff_id'); ?>
                            <?= form_hidden('id'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button group="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                    <button group="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/staff/member.js?v=' . version_sources()); ?>"></script>
<link href="//cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<style>
    .swal2-popup {
        display: none;
        position: relative;
        box-sizing: border-box;
        flex-direction: column;
        justify-content: center;
        width: 555px !important;
        padding: 1.25em;
        border: none;
        border-radius: 5px;
        background: #ffffff !important;
        font-family: inherit;
        font-size: 14px!important;
    }
</style>


<?php if (isset($member) && is_livreur($member->staffid)) { ?>
    <script src="<?= site_url('assets/js/admin/staff/fresh-crbt-reports-livreur.js?v=' . version_sources()); ?>"></script>
    <?php if (!empty(get_option('google_api_key')) && is_admin()) { ?>
        <script src="<?= site_url('assets/js/admin/staff/map.js?v=' . version_sources()); ?>"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?= get_option('google_api_key'); ?>&callback=initMap"></script>
    <?php } ?>
<?php } ?>
</body>
</html>



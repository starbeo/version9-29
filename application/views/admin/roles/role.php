<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold no-margin font-medium">
                            <?= $title; ?>
                        </h4>
                        <hr />
                        <?php if (isset($role)) { ?>
                            <a href="<?= admin_url('roles/role'); ?>" class="btn btn-success pull-right mbot20 display-block"><?= _l('new_role'); ?></a>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?= form_open($this->uri->uri_string()); ?>
                        <?php if (isset($role)) { ?>
                            <?php if (total_rows('tblstaff', array('role' => $role->roleid)) > 0) { ?>
                                <div class="alert alert-warning bold">
                                    <?= _l('change_role_permission_warning'); ?>
                                    <div class="checkbox">
                                        <input type="checkbox" name="update_staff_permissions" id="update_staff_permissions">
                                        <label for="update_staff_permissions"><?= _l('role_update_staff_permissions'); ?></label>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php $attrs = (isset($role) ? array() : array('autofocus' => true)); ?>
                        <?php $value = (isset($role) ? $role->name : ''); ?>
                        <?= render_input('name', 'role_add_edit_name', $value, 'text', $attrs); ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="bold"><?= _l('permission'); ?></th>
                                        <th class="text-center bold"><?= _l('permission_view'); ?></th>
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
                                    $conditions = get_permission_conditions();
                                    foreach ($permissions as $permission) {
                                        $permission_condition = $conditions[$permission['shortname']];

                                        ?>
                                        <tr>
                                            <td class="bold">
                                                <?php
                                                if ($permission['shortname'] == "factures_ret")
                                                {
                                                    echo $permission ["name"];   
                                                } else 
                                                    echo _l(str_replace(" ", "_", strtolower($permission["name"])));
                                              
                                     

                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['view'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_view' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }

                                                        /* if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view_own'=>1)) > 0){
                                                          $statement = 'disabled';
                                                          } */
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-can-view <?= $statement; ?> name="view[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['view_own'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_view_own' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }

                                                        /* if(total_rows('tblrolepermissions',array('roleid'=>$role->roleid,'permissionid'=>$permission['permissionid'],'can_view'=>1)) > 0){
                                                          $statement = 'disabled';
                                                          } */
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" <?= $statement; ?> name="view_own[]" value="<?= $permission['permissionid']; ?>" data-can-view-own>
                                                        <label></label>
                                                    </div>
                                                    <?php
                                                } else if ($permission['shortname'] == 'payments') {
                                                    echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="' . _l('permission_payments_based_on_invoices') . '"></i>';
                                                }

                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['create'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_create' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-create <?= $statement; ?> name="create[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['edit'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_edit' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-edit <?= $statement; ?> name="edit[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['download'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_download' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-download <?= $statement; ?> name="download[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['export'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_export' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }
                                                    }

                                                    ?>
                                                    <div class="checkbox">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-export <?= $statement; ?> name="export[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if ($permission_condition['delete'] == true) {
                                                    $statement = '';
                                                    if (isset($role)) {
                                                        if (total_rows('tblrolepermissions', array('roleid' => $role->roleid, 'permissionid' => $permission['permissionid'], 'can_delete' => 1)) > 0) {
                                                            $statement = 'checked';
                                                        }
                                                    }

                                                    ?>
                                                    <div class="checkbox checkbox-danger">
                                                        <input type="checkbox" data-shortname="<?= $permission['shortname']; ?>" data-can-delete <?= $statement; ?> name="delete[]" value="<?= $permission['permissionid']; ?>">
                                                        <label></label>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-info pull-right"><?= _l('submit'); ?></button>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/roles/role.js?v=' . version_sources()); ?>"></script>
</body>
</html>


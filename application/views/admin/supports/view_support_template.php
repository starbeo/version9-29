<div class="col-md-12 task-single-col no-padding">
    <div class="panel_s">
        <div class="panel-body">
            <?php
            if ($support->finished == 1) {
                echo '<div class="ribbon success"><span>' . _l('support_finished') . '</span></div>';
            }

            ?>
            <div class="row padding-5 task-info-wrapper">
                <div class="col-md-12">
                    <div class="label label-info task-info pull-left">
                        <h5 class="no-margin"><i class="fa pull-left fa-bolt"></i> <?= _l('support_single_priority'); ?>: <?= $support->priority; ?></h5>
                    </div>
                    <div class="label label-info mleft10 task-info pull-left">
                        <h5 class="no-margin"><i class="fa pull-left fa-margin"></i> <?= _l('support_single_start_date'); ?>: <?= _d($support->startdate); ?></h5>
                    </div>
                    <div class="label mleft10 task-info pull-left <?php
                    if (!$support->finished) {
                        echo ' label-danger';
                    } else {
                        echo 'label-info';
                    }

                    ?><?php
                    if (!$support->duedate) {
                        echo ' hide';
                    }

                    ?>">
                        <h5 class="no-margin"><i class="fa pull-left fa-calendar"></i> <?= _l('support_single_due_date'); ?>: <?= _d($support->duedate); ?></h5>
                    </div>
                    <?php if ($support->finished == 1) { ?>
                        <div class="label mleft10 pull-left task-info label-success">
                            <h5 class="no-margin"><i class="fa pull-left fa-check"></i> <?= _l('support_single_finished'); ?>: <?= _d($support->datefinished); ?></h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="panel_s">
        <div class="panel-body">
            <div class="row mbot15">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="title-wrapper pull-left">
                                <?php if ($support->finished == 0) { ?>
                                    <a href="#" class="pull-left" onclick="mark_complete(<?= $support->id; ?>);
                                            return false;" data-toggle="tooltip" title="<?= _l('support_single_mark_as_complete'); ?>"><i class="fa fa-check task-icon task-unfinished-icon"></i></a>
                                   <?php } else if ($support->finished == 1) { ?>
                                    <a href="#" class="pull-left" onclick="unmark_complete(<?= $support->id; ?>);
                                            return false;" data-toggle="tooltip" title="<?= _l('support_unmark_as_complete'); ?>"><i class="fa fa-check task-icon task-finished-icon"></i></a>
                                   <?php } ?>
                                <h3 class="no-margin pull-left"> <?= $support->name; ?></h3>
                            </div>
                            <div class="pull-right">
                                <?= form_open_multipart('admin/supports/upload_file', array('id' => 'support-attachment', 'class' => 'inline-block')); ?>
                                <?= form_close(); ?>
                            </div>
                            <hr />

                            <div class="row mbot30">
                                <div class="col-md-3 mtop5">
                                    <i class="fa fa-users"></i> <span class="bold"><?= _l('support_single_assignees'); ?></span>
                                </div>
                                <div class="col-md-9" id="assignees">
                                    <?php
                                    $_assignees = '';
                                    foreach ($assignees as $assignee) {
                                        $_remove_assigne = '';
                                        if ($support->addedfrom == get_staff_user_id() || is_admin()) {
                                            $_remove_assigne = ' <a href="#" class="remove-task-user" onclick="remove_assignee(' . $assignee['id'] . ',' . $support->id . '); return false;"><i class="fa fa-remove"></i></a>';
                                        }
                                        $_assignees .= '
              <span class="task-user" data-toggle="tooltip" data-title="' . get_staff_full_name($assignee['assigneeid']) . '">
                <a href="' . admin_url('profile/' . $assignee['assigneeid']) . '">' . staff_profile_image($assignee['assigneeid'], array(
                                                'staff-profile-image-small'
                                            )) . '</a> ' . $_remove_assigne . '</span>
              </span>';
                                    }

                                    if ($_assignees == '') {
                                        $_assignees = '<div class="bold mtop5 task-connectors-no-indicator display-block">' . _l('support_no_assignees') . '</div>';
                                    }
                                    echo $_assignees;

                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php if (has_permission('supports', '', 'edit')) { ?>
                                <div class="text-left">
                                    <select data-width="100%" class="text-muted task-action-select selectpicker" name="select-assignees" data-live-search="true" title='<?= _l('support_single_assignees_select_title'); ?>'></select>
                                </div>
                            <?php } ?>
                            <a href="#" onclick="add_support_checklist_item();
                                    return false"><span class="label mtop10 label-default label-task-action new-checklist-item"><i class="fa fa-plus-circle"></i>
                                    <?= _l('add_checklist_item'); ?></span></a>
                            <a href="#" class="add-support-attachments"><span class="label mtop10 label-default label-task-action"><i class="fa fa-paperclip"></i> <?= _l('add_support_attachments'); ?></span></a>
                            <?php if (has_permission('supports', '', 'edit')) { ?>
                                <a href="<?= admin_url('supports/support/' . $id); ?>" class="edit_task">
                                    <span class="label label-default label-task-action mtop10"><i class="fa fa-pencil-square"></i> <?= _l('support_single_edit'); ?></span>
                                </a>
                            <?php } ?>
                            <?php if (has_permission('supports', '', 'delete')) { ?>
                                <a href="#" onclick="delete_support();
                                        return false;">
                                    <span class="label label-default mtop10 label-task-action task-delete"><i class="fa fa-remove"></i> <?= _l('support_single_delete'); ?></span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 mtop20" id="attachments"></div>
            </div>
            <div class="row">
                <div class="col-md-12 mbot20">
                    <div id="checklist-items" class="mtop15"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mbot20" id="description">
                    --<br />
                    <h4 class="bold"><?= _l('support_view_description'); ?></h4>
                    <?= check_for_links($support->description); ?>
                </div>
            </div>
            <div class="row tasks-comments">
                <div class="col-md-12">
                    <textarea name="comment" id="support_comment" rows="5" class="form-control mtop15"></textarea>
                    <button type="button" class="btn btn-primary mtop10 pull-right" onclick="add_support_comment();"><?= _l('support_single_add_new_comment'); ?></button>
                    <div class="clearfix"></div>
                </div>
                <div id="support-comments">
                    <?= $comments; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    supportid = '<?= $support->id; ?>';
    init_selectpicker();
    Dropzone.autoDiscover = false;
    var supportsAttachmentsDropzone = new Dropzone("#support-attachment", {
        clickable: '.add-support-attachments',
        autoProcessQueue: true,
        createImageThumbnails: false,
        addRemoveLinks: false,
        previewTemplate: '<div style="display:none"></div>',
        maxFiles: 10
    });

    supportsAttachmentsDropzone.on("sending", function (file, xhr, formData) {
        formData.append("supportid", supportid);
    });
    // On post added success
    supportsAttachmentsDropzone.on('complete', function (files, response) {
        if (supportsAttachmentsDropzone.getUploadingFiles().length === 0 && supportsAttachmentsDropzone.getQueuedFiles().length === 0) {
            reload_support_attachments();
        }
    });
</script>

<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="<?= admin_url('supports/support'); ?>" class="btn btn-info pull-left display-block"><?= _l('new_support'); ?></a>
                    </div>
                    <div class="panel-heading">
                        <?= $title; ?>
                    </div>
                    <div class="panel-body">
                        <?= form_open($this->uri->uri_string(), array('id' => 'support-form')); ?>
                        <div class="row">
                            <div class="col-md-12">
                                <?php $value = (isset($support) ? $support->name : ''); ?>
                                <?= render_input('name', 'subject', $value); ?>
                                <div class="form-group">
                                    <label for="priority" class="control-label"><?= _l('priority'); ?></label>
                                    <select name="priority" class="selectpicker" id="priority" data-width="100%">
                                        <?php
                                        foreach ($priorities as $key => $priority) {
                                            $selected = '';
                                            if (isset($support) && $priority['id'] == $support->priority) {
                                                $selected = 'selected';
                                            }

                                            ?>
                                            <option value="<?= $priority['id']; ?>" <?= $selected; ?>>
                                                <?= _l($priority['alias']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($support) ? _d($support->startdate) : _d(date(get_current_date_format()))); ?>
                                        <?= render_date_input('startdate', 'start_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php $value = (isset($support) ? _d($support->duedate) : ''); ?>
                                        <?= render_date_input('duedate', 'due_date', $value); ?>
                                    </div>
                                </div>

                                <p class="bold"><?= _l('description'); ?></p>
                                <?php
                                $contents = '';
                                if (isset($support)) {
                                    $contents = $support->description;
                                }

                                ?>
                                <?php $this->load->view('admin/editor/template', array('name' => 'description', 'contents' => $contents, 'editor_name' => 'support')); ?>
                            </div>
                        </div>
                        <button type="submit" style="float:right;" class="btn btn-primary"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?= site_url('assets/js/admin/supports/supports.js?v=' . version_sources()); ?>"></script>
<script>
    _validate_form($('#support-form'), {
        name: 'required',
        startdate: 'required'
    });

    init_datepicker();
    init_selectpicker();
    var Editor_supports;
    // var container = editor.addContainer(container);
    var Editor_supports = new Quill('.editor-container-support', {
        modules: {
            'toolbar': {
                container: '.toolbar-container-support'
            },
            'link-tooltip': true,
            'image-tooltip': true,
            'multi-cursor': true
        },
        theme: 'snow'
    });
    Editor_supports.on('text-change', function (delta, source) {
        data = Editor_supports.getHTML();
        $('.editor_change_contents-support').val(data);
    });
    Editor_supports.setHTML($('.editor_contents-support').html());
</script>
</body>
</html>

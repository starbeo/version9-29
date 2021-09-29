<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-offset-3 col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="bold no-margin font-medium">
                            <?= $title; ?>
                        </h4>
                        <hr />
                        <?php if (isset($slider)) { ?>
                            <a href="<?= admin_url('sliders/slider'); ?>" class="btn btn-success pull-right mbot20 display-block"><?= _l('new_slider'); ?></a>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'form-slider')); ?>
                        <?php $value = (isset($slider) ? $slider->name : ''); ?>
                        <?= render_input('name', 'name', $value); ?>
                        <?php $value = (isset($slider) ? $slider->description : ''); ?>
                        <?= render_textarea('description', 'description', $value); ?>
                        <?php if (!isset($slider) || is_null($slider->file) || !file_exists('uploads/sliders/' . $slider->id . '/' . $slider->file)) { ?>
                            <?= render_input('file', 'file_extension_slider', '', 'file', array('accept' => 'image/x-jpg,image/jpeg,image/png,image/gif')); ?>
                        <?php } else { ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-9">
                                        <label><?= _l('file'); ?></label>
                                        <p class="mright5">
                                            <i class="<?= get_mime_class($slider->file_type); ?>"></i> <a href="<?= site_url('download/file/slider/' . $slider->id); ?>" title="Télécharger slider"> <?= $slider->file; ?></a>
                                        </p>
                                    </div>
                                    <div class="col-md-3 text-right">
                                        <a href="<?= admin_url('sliders/remove_file_slider/' . $slider->id); ?>" title="Supprimer pièce jointe"><i class="fa fa-remove"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/sliders/slider.js?v=' . version_sources()); ?>"></script>
</body>
</html>

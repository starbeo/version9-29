<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#entreprise"><?= _l('new_entreprise'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        render_datatable(array(
                            _l('entreprise_list_name'),
                            _l('entreprise_list_email'),
                            _l('entreprise_list_telephone'),
                            _l('entreprise_list_active'),
                            _l('options')
                            ), 'entreprises');

                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="entreprise" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?= form_open(admin_url('entreprises/entreprise')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?= _l('edit_entreprise'); ?></span>
                    <span class="add-title"><?= _l('new_entreprise'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?= render_input('name_entreprise', 'name'); ?>
                        <?= render_input('email', 'email', '', 'email'); ?>
                        <?= render_input('telephone', 'phone_number'); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville"><?= _l('city'); ?></label>
                                    <input type="text" class="form-control" name="ville" placeholder="Entrer la ville">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pays_id" class="control-label">
                                        <?= _l('Country'); ?>
                                    </label>
                                    <select name="pays_id" class="form-control selectpicker" id="country" data-live-search="true">
                                        <option value=""></option>
                                        <?php
                                        $countries = get_all_countries();
                                        foreach ($countries as $country) {
                                            $selected = '';
                                            if (isset($client)) {
                                                if ($client->country == $country['country_id']) {
                                                    $selected = 'selected';
                                                }
                                            }

                                            ?>
                                            <option value="<?= $country['country_id']; ?>" <?= $selected; ?>>
                                                <?= $country['short_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="id">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?= _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?= form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        _validate_form($('form'), {name_entreprise: 'required'
        }, manage_entreprises);
    });
    function manage_entreprises(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).success(function (response) {
            response = $.parseJSON(response);
            if (response.success == true) {
                alert_float('success', response.message);
            }
            $('.table-entreprises').DataTable().ajax.reload();
            $('#entreprise').modal('hide');
        });
        return false;
    }

    $('#entreprise').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#entreprise .add-title').removeClass('hide');
        $('#entreprise .edit-title').addClass('hide');
        $('#entreprise input').val('');
        $('#entreprise input[name="telephone"]').val('+212');
        $('#entreprise select[name="pays_id"]').selectpicker('val', '');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "entreprises/get_info_entreprise/" + id, function (response) {
                var data = jQuery.parseJSON(response);
                $('#entreprise input[name="id"]').val(id);
                $('#entreprise .add-title').addClass('hide');
                $('#entreprise .edit-title').removeClass('hide');
                $('#entreprise input[name="name_entreprise"]').val(data['name_entreprise']);
                $('#entreprise input[name="email"]').val(data['email']);
                $('#entreprise input[name="telephone"]').val(data['telephone']);
                $('#entreprise input[name="adresse"]').val(data['adresse']);
                $('#entreprise input[name="ville"]').val(data['ville']);
                $('#entreprise select[name="pays_id"]').selectpicker('val', data['pays_id']);
            });
        }
    });
</script>
</body>
</html>

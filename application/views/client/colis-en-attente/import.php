<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <?php if (isset($errors) && count($errors) > 0) { ?>
                <?php
                $erreur_head = '<p><b>' . _l('head_alert_error_import') . '</b></p>';
                $erreur = '';
                foreach ($errors as $key => $value) {
                    if (!empty($value['num_commande']) || !empty($value['ville']) || !empty($value['crbt']) || !empty($value['phone']) || !empty($value['name']) || !empty($value['address'])) {
                        $erreur .= '<p>';
                        $erreur .= 'Dans la ligne : <b>' . $value['ligne'] . '</b> ';
                        $err = '';
                        if (!empty($value['name'])) {
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  Destinataire : <b>" . $value['name'] . "</b>";
                        }
                        if (!empty($value['address'])) {
                            if (!empty($err)) {
                                $err .= " et ";
                            }
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  Adresse : <b>" . $value['address'] . "</b>";
                        }
                        if (!empty($value['phone'])) {
                            if (!empty($err)) {
                                $err .= " et ";
                            }
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  Téléphone : <b>" . $value['phone'] . "</b>, il ne doit pas contenir +212 (Voilà la forme exacte : 0600000000)";
                        }
                        if (!empty($value['ville'])) {
                            if (!empty($err)) {
                                $err .= " et ";
                            }
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  Nom ville erroné : <b>" . $value['ville'] . "</b>";
                        }
                        if (!empty($value['crbt'])) {
                            if (!empty($err)) {
                                $err .= " et ";
                            }
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  CRBT : <b>" . $value['crbt'] . "</b>";
                        }
                        if (!empty($value['num_commande'])) {
                            if (!empty($err)) {
                                $err .= " et ";
                            }
                            $err .= "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i>  Numéro de commande existe déjà : <b>" . $value['num_commande'] . "</b>";
                        }
                        $erreur .= $err . '</p>';
                    }
                }
                $erreur .= '<h3 class="text-danger">' . _l('colis_import_alert_correct_before_uploading') . '</h3>';

                ?>
                <div class="col-lg-12">
                    <div class="alert alert-warning" style="color: #000 !important;">
                        <button group="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                        <?= $erreur_head . $erreur; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($btnimport == true && count($errors) == 0 && isset($errors)) { ?>
                <div class="col-lg-12">
                    <div class="alert alert-success" style="color: #000 !important;">
                        <button group="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
                        <?= _l('import_verification'); ?>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="col-md-6 no-padding">
                            <a href="<?= site_url('uploads/colis/exemple_importation.xlsx') ?>" class="btn btn-success mright5"><?= _l('download_sample'); ?></a>
                            <!--a href="<?= client_url('villes/export') ?>" class="btn btn-info mright5"><?= _l('download_list_cities'); ?></a-->
                        </div>
                        <div class="col-md-6 no-padding">
                            <?php if (isset($path_file_error) && !empty($path_file_error)) { ?>
                                <?= form_open(site_url('download/error_import_csv')); ?>
                                <?= form_hidden('path', $path_file_error); ?>
                                <button type="submit" class="btn btn-warning pull-right"><?= _l('download_error_import'); ?></button>
                                <?= form_close(); ?>
                            <?php } ?>
                        </div>
                        <hr />

                        <?php if (!isset($simulate) > 0) { ?>
                            <p><?= _l('import_note_1'); ?></p>
                            <div class="table-responsive no-dt">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <?php $total_fields = 0; ?>
                                            <?php
                                            foreach ($colis_db_fields as $field) {
                                                if (in_array($field, $not_importable)) {
                                                    continue;
                                                }
                                                if ($field == 'nom_complet') {
                                                    $field = 'Destinataire';
                                                } else if ($field == 'num_commande') {
                                                    $field = 'Numéro de commande';
                                                }
                                                $total_fields++;

                                                ?>
                                                <th class="bold"><?= str_replace('_', ' ', ucfirst($field)); ?><span class="text-danger"> *</span></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for ($i = 0; $i < 1; $i++) {
                                            echo '<tr>';
                                            for ($x = 0; $x < $total_fields; $x++) {
                                                echo '<td>Exemple de données</td>';
                                            }
                                            echo '</tr>';
                                        }

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-4 mtop15">
                                <?= form_open_multipart($this->uri->uri_string(), array('id' => 'form-import-colis-en-attente')); ?>
                                <?= form_hidden('test'); ?>
                                <?= render_input('file_xls', 'choose_xls_file', '', 'file'); ?>
                                <div class="form-group">
                                    <?php if ($btnimport == false || count($errors) > 0) { ?>
                                        <button type="button" class="btn btn-primary btn-import-submit"><?= _l('verification_data'); ?></button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-success btn-import-submit import"><?= _l('import'); ?></button>
                                    <?php } ?>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/colis-en-attente/import.js?v=' . version_sources()); ?>"></script>
</body>
</html>
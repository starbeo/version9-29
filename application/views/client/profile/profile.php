<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div class="col-md-8">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $title; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if ($showContrat) { ?>
                                    <a href="<?= client_url('profile/contrat') ?>" class="btn btn-success pull-right"><?= _l('download_contract'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?= form_open_multipart($this->uri->uri_string(), array('id' => 'form-client-profile')); ?>
                        <?= form_hidden('change_logo', true); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?php $value = (isset($client) ? $client->nom : ''); ?>
                                <?= render_input('nom', 'fullname', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->email : ''); ?>
                                <?= render_input('email', 'email', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->marque : ''); ?>
                                <?= render_input('marque', 'name_of_bank', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->code_parrainage : ''); ?>
                                <?= render_input('code_parrainage', 'affiliation_code', $value, '', array('disabled' => true), array(), '', 'input-code-affilation'); ?>
                            </div>
                            <div class="col-md-4">
                                <?php $value = (isset($client) ? $client->contact : ''); ?>
                                <?= render_input('contact', 'contact', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->adresse : ''); ?>
                                <?= render_input('adresse', 'address', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->rib : ''); ?>
                                <?= render_input('rib', 'rib', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->frais_livraison_interieur : ''); ?>
                                <?= render_input('frais_livraison_interieur', 'fresh_delivery_interior', $value, '', array('disabled' => true)); ?>
                            </div>
                            <div class="col-md-4">
                                <?php $value = (isset($client) ? $client->telephone : ''); ?>
                                <?= render_input('telephone', 'phone_number', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->ville : ''); ?>
                                <?= render_input('ville', 'city', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->registre_commerce : ''); ?>
                                <?= render_input('registre_commerce', 'trade_registry', $value, '', array('disabled' => true)); ?>
                                <?php $value = (isset($client) ? $client->frais_livraison_exterieur : ''); ?>
                                <?= render_input('frais_livraison_exterieur', 'fresh_delivery_exterior', $value, '', array('disabled' => true)); ?>
                            </div>
                        </div>
                        <?php if (is_null($client->logo)) { ?>
                            <div class="form-group">
                                <label for="logo" class="profile-image"><?= _l('clients_edit_logo_client_heading'); ?></label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                        <?php } else { ?>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-10">
                                            <?= client_logo($client->id, array('img', 'img-responsive', 'staff-profile-image-thumb'), 'thumb'); ?>
                                        </div>
                                        <div class="col-md-2 text-right">
                                            <a href="<?= client_url('profile/remove_logo'); ?>"><i class="fa fa-remove"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-heading">
                                <?= _l('clients_edit_profile_change_password_heading'); ?>
                            </div>
                            <div class="panel-body">
                                <?= form_open('client/profile/change_password', array('id' => 'form-change-password')); ?>
                                <?= render_input('oldpassword', 'staff_edit_profile_change_old_password', '', 'password'); ?>
                                <?= render_input('newpassword', 'staff_edit_profile_change_new_password', '', 'password'); ?>
                                <?= render_input('newpasswordr', 'staff_edit_profile_change_repet_new_password', '', 'password'); ?>
                                <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                                <?= form_close(); ?>
                            </div>
                            <?php if (isset($client->last_password_change) && !is_null($client->last_password_change)) { ?>
                                <div class="panel-footer">
                                    <?= _l('staff_add_edit_password_last_changed'); ?>: <?= time_ago($client->last_password_change); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel_s">
                            <div class="panel-heading">
                                <?= _l('settings'); ?>
                            </div>
                            <div class="panel-body">
                                <?= form_open('client/profile/change_settings', array('id' => 'form-change-settings')); ?>
                                <?= form_hidden('change_settings', true); ?>
                                <?php $checked = ((isset($client) && $client->ouverture == 1) ? 'checked' : ''); ?>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="ouverture" <?= $checked ?>>
                                    <label for="ouverture"><?= _l('colis_opening'); ?></label>
                                </div>
                                <?php $checked = ((isset($client) && $client->option_frais == 1) ? 'checked' : ''); ?>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="option_frais" <?= $checked ?>>
                                    <label for="option_frais"><?= _l('option_frais'); ?></label>
                                </div>
                                <?php $checked = ((isset($client) && $client->option_frais_assurance == 1) ? 'checked' : ''); ?>
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="option_frais_assurance" <?= $checked ?>>
                                    <label for="option_frais_assurance"><?= _l('option_frais_assurance'); ?></label>
                                </div>
                                <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
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
<script src="<?= site_url('assets/js/client/profile/profile.js?v=' . version_sources()); ?>"></script>
</body>
</html>

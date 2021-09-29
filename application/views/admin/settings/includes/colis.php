<div class="row">
    <div class="col-md-3">
        <?= render_input('settings[alias_barcode]', 'alias_barcode', get_option('alias_barcode')); ?>
        <hr />
        <?= render_input_group('settings[pourcentage_frais_assurance]', 'pourcentage_frais_assurance', get_option('pourcentage_frais_assurance'), 'number', array('step' => '0.1'), array(), '', '', '%'); ?>
        <hr />
        <?= render_input('settings[frais_parrainage]', 'frais_parrainage', get_option('frais_parrainage'), 'number', array('step' => '0.1')); ?>
    </div>
    <div class="col-md-6">
        <p class="bold"><?= _l('shipping_cost'); ?></p>
        <div class="form-check-inline input-radio-staff">
            <label class="form-check-label">
                <?php $checked = ((get_option('shipping_cost_by_ville') == 1) ? 'checked' : ''); ?>
                <input type="radio" class="form-check-input mr10" name="settings[shipping_cost_by_ville]" value="1" <?= $checked; ?>> <?= _l('shipping_cost_by_ville'); ?>
            </label>
        </div>
        <div class="form-check-inline input-radio-staff">
            <label class="form-check-label">
                <?php $checked = ((get_option('shipping_cost_by_ville') == 0) ? 'checked' : ''); ?>
                <input type="radio" class="form-check-input mr10" name="settings[shipping_cost_by_ville]" value="0" <?= $checked; ?>> <?= _l('shipping_cost_interior_exterior'); ?>
            </label>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                <?= render_input('settings[frais_livraison_interieur]', 'shipping_cost_interior', get_option('frais_livraison_interieur'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_livraison_exterieur]', 'shipping_cost_exterior', get_option('frais_livraison_exterieur'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_livraison_retour]', 'shipping_cost_return', get_option('frais_livraison_retour'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_colis_refuse_par_defaut]', 'parcel_fees_refused_by_default', get_option('frais_colis_refuse_par_defaut'), 'number', array('step' => '0.1')); ?>
            </div>
            <div class="col-md-6">
                <?= render_input('settings[frais_supplementaire]', 'frais_supplementaire', get_option('frais_supplementaire'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_stockage]', 'frais_stockage', get_option('frais_stockage'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_emballage]', 'frais_emballage', get_option('frais_emballage'), 'number', array('step' => '0.1')); ?>
                <?= render_input('settings[frais_etiquette]', 'frais_etiquette', get_option('frais_etiquette'), 'number', array('step' => '0.1')); ?>
            </div>
        </div>
    </div>
</div>

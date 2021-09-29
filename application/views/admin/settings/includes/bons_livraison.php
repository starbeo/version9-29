<div class="row">
    <div class="col-md-12">
        <p class="bold"><?= _l('default_setting'); ?></p>
        <hr />
        <?= render_yes_no('settings[show_colis_displayed_in_the_delivery_note_by_livreur]', get_option('show_colis_displayed_in_the_delivery_note_by_livreur'), 'show_colis_displayed_in_the_delivery_note_by_livreur'); ?>
        <hr />
        <?php
        $statusesSelected = (get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output') ? get_option('the_statuses_of_colis_displayed_in_the_delivery_note_output') : '');
        $selected = explode(",", $statusesSelected);

        ?>
        <?= render_select('settings[the_statuses_of_colis_displayed_in_the_delivery_note_output][]', $statuses, array('id', array('name')), 'the_statuses_of_colis_displayed_in_the_delivery_note_output', $selected, array('multiple' => true)); ?>
        <hr />
        <?php
        $statusesSelected = (get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned') ? get_option('the_statuses_of_colis_displayed_in_the_delivery_note_returned') : '');
        $selected = explode(",", $statusesSelected);

        ?>
        <?= render_select('settings[the_statuses_of_colis_displayed_in_the_delivery_note_returned][]', $statuses, array('id', array('name')), 'the_statuses_of_colis_displayed_in_the_delivery_note_returned', $selected, array('multiple' => true)); ?>
    </div>
</div>

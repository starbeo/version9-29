<div class="row">
    <div class="col-md-4">
        <?php
        if(!empty(get_option('display_statuses_from_date'))) {
            $value = get_option('display_statuses_from_date');
            if(!is_date($value)) {
                $value = '';
            }
        }
        ?>
        <?= render_date_input('settings[display_statuses_from_date]', 'display_statuses_from_date', $value); ?>
    </div>
</div>

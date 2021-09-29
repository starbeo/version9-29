<div class="row">
    <div class="col-md-4">
        <?= render_input_hidden('date_time'); ?>
        <?= render_yes_no('settings[coming_soon]', get_option('coming_soon'), 'switch_to_mode_in_construction'); ?>
        <hr />
        <?= render_datetime_input('settings[coming_soon_date_time_start]', 'date_start', get_option('coming_soon_date_time_start')); ?>
        <hr />
        <?= render_datetime_input('settings[coming_soon_date_time_end]', 'date_end', get_option('coming_soon_date_time_end')); ?>
    </div>   
    <div class="col-md-8">
        <?= render_textarea_avancer('coming_soon_message', 'settings[coming_soon_message]', 'message', get_option('coming_soon_message')); ?>
    </div> 
</div>

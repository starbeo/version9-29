<div class="row">
	<div class="col-md-4">
        <div class="form-group">
            <label for="timezones" class="control-label"><?= _l('settings_localization_default_timezone'); ?></label>
            <select name="settings[default_timezone]" id="timezones" class="form-control selectpicker" data-live-search="true">
                <?php foreach(get_timezones_list() as $timezone => $val){ ?>
                <option value="<?= $timezone; ?>" <?php if(get_option_client('default_timezone') == $timezone){echo 'selected';} ?>><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
        <?= render_input('settings[tables_pagination_limit]','number_of_rows_displayed_by_default_in_your_tables',get_option_client('tables_pagination_limit'),'number'); ?>
		<hr />
		<?= render_input('settings[limit_top_search_bar_results_to]','limit_number_of_results_in_the_search_bar',get_option_client('limit_top_search_bar_results_to'),'number'); ?>
        <hr />
        <?= render_yes_no('settings[laisser_le_menu_toujours_ouvert]', get_option_client('laisser_le_menu_toujours_ouvert'), 'laisser_le_menu_toujours_ouvert'); ?>
	</div>
</div>

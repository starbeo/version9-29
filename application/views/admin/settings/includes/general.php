<div class="row">
	<div class="col-md-4">
        <?= render_input('settings[companyname]','company_name',get_option('companyname')); ?>
		<hr />
        <?= render_input('settings[main_domain]','main_domain',get_option('main_domain')); ?>
		<hr />
        <?= render_input('settings[company_phonenumber]','company_phonenumber',get_option('company_phonenumber')); ?>
		<hr />
        <?= render_input('settings[company_phonenumber_2]','company_phonenumber_2',get_option('company_phonenumber_2')); ?>
    </div>
    <div class="col-md-4">
        <?= render_input('settings[website]','website',get_option('website')); ?>
		<hr />
        <?= render_input('settings[url_page_facebook]','link_facebook_page',get_option('url_page_facebook')); ?>
		<hr />
        <?= render_input('settings[url_page_instagram]','link_instagram_page',get_option('url_page_instagram')); ?>
		<hr />
        <?= render_input('settings[url_page_linkedin]','link_linkedin_page',get_option('url_page_linkedin')); ?>
	</div>
	<div class="col-md-4">
        <div class="form-group">
            <label for="active_language" class="control-label"><?= _l('settings_localization_default_language'); ?></label>
            <select name="settings[active_language]" id="active_language" class="form-control selectpicker">
                <?php foreach(list_folders(APPPATH .'language') as $language){ ?>
                <option value="<?= $language; ?>" <?php if($language == get_option('active_language')){echo ' selected'; } ?>><?= ucfirst($language); ?></option>
                <?php } ?>
            </select>
        </div>
		<hr />
        <div class="form-group">
            <label for="dateformat" class="control-label"><?= _l('settings_localization_date_format'); ?></label>
            <select name="settings[dateformat]" id="dateformat" class="form-control selectpicker">
                <?php foreach(get_available_date_formats() as $key => $val){ ?>
                <option value="<?= $key; ?>" <?php if($key == get_option('dateformat')){echo 'selected';} ?>><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
        <hr />
        <div class="form-group">
            <label for="timezones" class="control-label"><?= _l('settings_localization_default_timezone'); ?></label>
            <select name="settings[default_timezone]" id="timezones" class="form-control selectpicker" data-live-search="true">
                <?php foreach(get_timezones_list() as $timezone => $val){ ?>
                <option value="<?= $timezone; ?>" <?php if(get_option('default_timezone') == $timezone){echo 'selected';} ?>><?= $val; ?></option>
                <?php } ?>
            </select>
        </div>
        <hr />
        <?= render_input('settings[tables_pagination_limit]','number_of_rows_displayed_by_default_in_your_tables',get_option('tables_pagination_limit'),'number'); ?>
		<hr />
		<?= render_input('settings[limit_top_search_bar_results_to]','limit_number_of_results_in_the_search_bar',get_option('limit_top_search_bar_results_to'),'number'); ?>
	</div>
</div>

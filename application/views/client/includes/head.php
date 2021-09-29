<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if (get_option('favicon') != '') { ?>
            <link href="<?= site_url('uploads/company/' . get_option('companyalias') . '/' . get_option('favicon')); ?>" rel="shortcut icon">
        <?php } ?>
        <title> <?php
            if (get_option('companyname')) {
                echo get_option('companyname');
            } else {
                'EasyTrack';
            }

            ?><?php
            if (isset($title)) {
                echo ' - ' . $title;
            }

            ?></title>
        <link href="<?= site_url(); ?>assets/css/reset.css" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="<?= site_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <?php if (get_option('rtl_support_admin') == 1) { ?>
            <link rel="stylesheet" href="<?= site_url(); ?>bower_components/bootstrap-arabic/dist/css/bootstrap-arabic.min.css">
        <?php } ?>
        <link href='<?= site_url(); ?>bower_components/open-sans-fontface/open-sans.css' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css">
        <link href="<?= site_url(); ?>assets/plugins/quill/quill.snow.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= site_url(); ?>assets/plugins/ContentTools/build/content-tools.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/metisMenu/dist/metisMenu.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/bootstrap-select/dist/css/bootstrap-select.min.css">
        <!--link rel="stylesheet" href="<?= site_url(); ?>bower_components/bootstrap-phonenumber/css/bootstrap-formhelpers.min.css"-->
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/datatables/media/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="<?= site_url(); ?>bower_components/datatables-buttons/css/buttons.dataTables.scss">
        <link href="<?= site_url('bower_components/dropzone/dist/min/basic.min.css'); ?>" rel='stylesheet' type='text/css'>
        <link href="<?= site_url('bower_components/dropzone/dist/min/dropzone.min.css'); ?>" rel='stylesheet' type='text/css'>
        <link rel='stylesheet' href='<?= site_url('bower_components/fullcalendar/dist/fullcalendar.min.css'); ?>' />
        <?php if (isset($editor_assets)) { ?>
            <link href="<?= site_url(); ?>assets/plugins/quill/quill.snow.css" rel='stylesheet' type='text/css'>
        <?php } ?>
        <link href="<?= site_url(); ?>assets/css/bs-overides.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <?php if(get_option_client('laisser_le_menu_toujours_ouvert') == 0) { ?>
        <style>
            #wrapper {
                margin: 0;
                padding: 0;
                background: #eef2f4;
                transition: all 0.4s ease 0s;
                position: relative;
                min-height: 100%;
            }
        </style>
        <?php } else { ?>
        <style>
            #wrapper {
                margin: 0 0 0 225px;
                padding: 0;
                background: #eef2f4;
                transition: all 0.4s ease 0s;
                position: relative;
                min-height: 100%;
            }
        </style>
        <?php } ?>
        <link href="<?= site_url(); ?>assets/css/style.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <link href="<?= site_url(); ?>assets/css/custom.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <?php if (isset($chat_assets)) { ?>
            <link href="<?= site_url(); ?>assets/css/chat.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <?php } ?>

        <script>
            // Settings required for javascript
            var site_url = '<?= site_url(); ?>';
            var client_url = '<?= client_url(); ?>';
            var chat_assets = '<?= isset($chat_assets) ? $chat_assets : ""; ?>';
            var tables_pagination_limit = '<?= get_option_client("tables_pagination_limit"); ?>';
            var date_format = '<?= get_option("dateformat"); ?>';
            date_format = date_format.split('|');
            date_format_calendar = date_format[0];
            date_format = date_format[1];
            var decimal_separator = '<?= get_option("decimal_separator"); ?>';
            var thousand_separator = '<?= get_option("thousand_separator"); ?>';
            var timezone = '<?= get_option_client("default_timezone"); ?>';
            // Datatables language
            var dt_emptyTable = '<?= _l("dt_empty_table"); ?>';
            var dt_info = '<?= _l("dt_info"); ?>';
            var dt_infoEmpty = '<?= _l("dt_info_empty"); ?>';
            var dt_infoFiltered = '<?= _l("dt_info_filtered"); ?>';
            var dt_lengthMenu = '<?= _l("dt_length_menu"); ?>';
            var dt_length_menu_all = '<?= _l("dt_length_menu_all"); ?>';
            var dt_loadingRecords = '<?= _l("dt_loading_records"); ?>';
            var dt_search = '<?= _l("dt_search"); ?>';
            var dt_zeroRecords = '<?= _l("dt_zero_records"); ?>';
            var dt_paginate_first = '<?= _l("dt_paginate_first"); ?>';
            var dt_paginate_last = '<?= _l("dt_paginate_last"); ?>';
            var dt_paginate_next = '<?= _l("dt_paginate_next"); ?>';
            var dt_paginate_previous = '<?= _l("dt_paginate_previous"); ?>';
            var dt_sortAscending = '<?= _l("dt_sort_ascending"); ?>';
            var dt_sortDescending = '<?= _l("dt_sort_descending"); ?>';
            var dt_column_visibility_tooltip = '<?= _l("dt_column_visibility_tooltip"); ?>';
            // Datatables buttons
            var dt_button_column_visibility = '<?= _l("dt_button_column_visibility"); ?>';
            var dt_button_reload = '<?= _l("dt_button_reload"); ?>';
            var dt_button_excel = '<?= _l("dt_button_excel"); ?>';
            var dt_button_csv = '<?= _l("dt_button_csv"); ?>';
            var dt_button_pdf = '<?= _l("dt_button_pdf"); ?>';
            var dt_button_print = '<?= _l("dt_button_print"); ?>';
            var item_field_not_formated = '<?= _l("numbers_not_formated_while_editing"); ?>';
            // Chart general options
            var line_chart_options = {
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(0,0,0,.05)",
                scaleGridLineWidth: 1,
                bezierCurve: true,
                pointDot: true,
                pointDotRadius: 4,
                pointDotStrokeWidth: 1,
                pointHitDetectionRadius: 20,
                datasetStroke: true,
                datasetStrokeWidth: 1,
                datasetFill: true,
                responsive: true
            };

            var google_api = '';
            var calendarIDs = '';
            var months_json = '<?= json_encode(array(_l('January'), _l('February'), _l('March'), _l('April'), _l('May'), _l('June'), _l('July'), _l('August'), _l('September'), _l('October'), _l('November'), _l('December'))); ?>';
            var drop_files_here_to_upload = "<?= _l('drop_files_here_to_upload'); ?>";
            var browser_not_support_drag_and_drop = "<?= _l('browser_not_support_drag_and_drop'); ?>";
            var allowed_files = "<?= get_option('allowed_files'); ?>";
            var remove_file = "<?= _l('remove_file'); ?>";
            var you_can_not_upload_any_more_files = "You can not upload any more files";
            var confirm_action_prompt = "<?= _l('confirm_action_prompt'); ?>";
            var file_exceds_maxfile_size_in_form = "<?= _l('file_exceds_maxfile_size_in_form'); ?>";
            var drop_files_here_to_upload = "<?= _l('drop_files_here_to_upload'); ?>";
            var browser_not_support_drag_and_drop = "<?= _l('browser_not_support_drag_and_drop'); ?>";
            var remove_file = "<?= _l('remove_file'); ?>";
            var project_id = '';
        </script>
    </head>
    <body class="<?php
    if (isset($bodyclass)) {
        echo $bodyclass . ' ';
    }

    ?><?php
    if ($this->session->has_userdata('is_mobile') && $this->session->userdata('is_mobile') == true) {
        echo 'hide-sidebar ';
    }

    ?><?php
    if (get_option('rtl_support_admin') == 1) {
        echo 'rtl';
    }

    ?>">


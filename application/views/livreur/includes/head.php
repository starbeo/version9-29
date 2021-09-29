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
        <link href="<?= site_url(); ?>assets/css/reset.css" rel="stylesheet" type="text/css">
        <!-- Bootstrap -->
        <link href="<?= site_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/open-sans-fontface/open-sans.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/animate.css/animate.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>assets/css/bs-overides.css" rel="stylesheet" type="text/css">
        <?php if (get_staff()->menu_ouvert == 0) { ?>
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
        <?php if (!empty($_default_color) && !empty($_default_writing_color)) { ?>
            <style>
                .default-background-color {
                    background-color: <?= $_default_color ?> !important;
                }
                .default-color {
                    color: <?= $_default_writing_color ?> !important;
                }
                .default-txt-color {
                    color: <?= $_default_color ?> !important;
                }
            </style>
        <?php } else { ?>
            <style>
                .default-background-color {
                    background-color: aliceblue !important;
                }
                .default-color {
                    color: #323a45 !important;
                }
                .default-txt-color {
                    color: #323a45 !important;
                }
            </style>
        <?php } ?>
        <link href="<?= site_url(); ?>assets/css/style.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <link href="<?= site_url(); ?>assets/css/custom.css?v=<?= version_sources(); ?>" rel="stylesheet">
        <script>
            // Settings required for javascript
            var livreur_url = '<?= livreur_url(); ?>';
            //Format date
            var date_format = '<?= get_option("dateformat"); ?>';
            date_format = date_format.split('|');
            date_format_calendar = date_format[0];
            date_format = date_format[1];
            // Formatting money
            var decimal_separator = '<?= get_option("decimal_separator"); ?>';
            var thousand_separator = '<?= get_option("thousand_separator"); ?>';
            var timezone = '<?= get_option("default_timezone"); ?>';
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

    ?>">


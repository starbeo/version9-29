<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $title; ?></title>
        <link href="<?= site_url('uploads/company/' . get_option('companyalias') . '/' . get_option('favicon')); ?>" rel="shortcut icon">
        <link href="<?= site_url(); ?>assets/css/reset.css" rel="stylesheet" type='text/css'>
        <!-- Bootstrap -->
        <link href="<?= site_url(); ?>bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type='text/css'>
        <link href='<?= site_url(); ?>bower_components/open-sans-fontface/open-sans.css' rel='stylesheet' type='text/css'>
        <link href="<?= site_url(); ?>bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>bower_components/animate.css/animate.min.css" rel="stylesheet" type="text/css">
        <link href="<?= site_url(); ?>assets/css/authentication/<?= get_option('theme_login_admin'); ?>/authentication.css" rel="stylesheet" type="text/css">
    </head>

<!-- add/edit task modal will be appended here -->
<div id="_task"></div>
<script src="<?= site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-validation/dist/jquery.validate.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-validation/dist/additional-methods.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="<?= site_url(); ?>bower_components/metisMenu/dist/metisMenu.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?= site_url(); ?>assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js"></script>
<!--script src="<?= site_url(); ?>bower_components/bootstrap-phonenumber/js/bootstrap-formhelpers.min.js"></script-->
<script src="<?= site_url(); ?>bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="<?= site_url(); ?>bower_components/datatables/media/js/dataTables.bootstrap.min.js"></script>
<script src="<?= site_url(); ?>bower_components/datatables-buttons/js/dataTables.buttons.js"></script>
<script src="<?= site_url(); ?>bower_components/datatables-buttons/js/buttons.colVis.js"></script>
<script src="<?= site_url(); ?>bower_components/datatables-buttons/js/buttons.html5.js"></script>
<script src="<?= site_url(); ?>bower_components/datatables-buttons/js/buttons.print.js"></script>
<script src="<?= site_url(); ?>bower_components/jszip/dist/jszip.min.js"></script>
<script src="<?= site_url(); ?>bower_components/pdfmake/build/pdfmake.min.js"></script>
<script src="<?= site_url(); ?>bower_components/pdfmake/build/vfs_fonts.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="<?= site_url(); ?>bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
<script src="<?= site_url(); ?>bower_components/moment/min/moment-with-locales.js"></script>
<script src="<?= site_url(); ?>bower_components/moment-timezone/builds/moment-timezone-with-data.min.js"></script>
<script src="<?= site_url(); ?>bower_components/moment-timezone/moment-timezone-utils.js"></script>
<!--script src="<?= site_url(); ?>bower_components/Chart.js/Chart.min.js" type="text/javascript"></script-->
<script id="chart-js-script" src="<?= base_url('assets/plugins/Chart.js/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?= site_url('bower_components/dropzone/dist/min/dropzone.min.js'); ?>"></script>
<!-- are you sure safari fix -->
<script src="<?= site_url('bower_components/jquery.are-you-sure/ays-beforeunload-shim.js'); ?>"></script>
<!-- are you sure safari fix -->
<script src="<?= site_url('bower_components/jquery.are-you-sure/jquery.are-you-sure.js'); ?>"></script>
<!-- CKEDITOR JS -->
<?php if (isset($ckeditor_assets)) { ?>
    <script src="<?= site_url(); ?>assets/plugins/ckeditor/ckeditor.js"></script>
<?php } ?>
<!-- EDITOR JS -->
<?php if (isset($editor_assets)) { ?>
    <script src="<?= site_url(); ?>assets/plugins/quill/quill.js"></script>
    <script src="<?= site_url(); ?>assets/js/editor.js"></script>
<?php } ?>
<script src="<?= site_url(); ?>bower_components/jquery-circle-progress/dist/circle-progress.js"></script>
<script src="<?= site_url('bower_components/accounting.js/accounting.min.js'); ?>"></script>
<script src="<?= site_url('bower_components/fullcalendar/dist/fullcalendar.min.js'); ?>"></script>
<script src="<?= site_url('bower_components/fullcalendar/dist/gcal.js'); ?>"></script>
<script src="<?= site_url('assets/js/admin/calendar.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/sales.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/main_0.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/js/admin/main.js?v=' . version_sources()); ?>"></script>
<?php if (isset($chat_assets)) { ?>
    <script src="<?= site_url('assets/js/admin/live-chat/chat.js?v=' . version_sources()); ?>"></script>
<?php } ?>
<?php $unread_notifications_client = total_rows('tblnotificationsadmin', array('toadmin' => get_staff_user_id(), 'isread' => 0)); ?>
<?php $unread_notifications_staff = total_rows('tblnotifications', array('touserid' => get_staff_user_id(), 'isread' => 0)); ?>
<?php if (is_admin() && ($unread_notifications_client > 0 || $unread_notifications_staff > 0)) { ?>
    <script type="text/javascript">
        function playSound()
        {
            //var audio = new Audio(site_url+'assets/sounds/slow-spring-board.mp3');
            //audio.play();
        }
        $(document).ready(function () {
            playSound();
        });
    </script>
<?php } ?>
<script src="<?= site_url('assets/js/jquery.scrollbox.js'); ?>"></script>
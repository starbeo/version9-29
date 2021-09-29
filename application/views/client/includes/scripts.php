<!-- COMPONENTS JS -->
<script src="<?= site_url(); ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-validation/dist/jquery.validate.js"></script>
<script src="<?= site_url(); ?>bower_components/jquery-validation/dist/additional-methods.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap-switch/dist/js/bootstrap-switch.min.js"></script>
<script src="<?= site_url(); ?>bower_components/metisMenu/dist/metisMenu.min.js"></script>
<script src="<?= site_url(); ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
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
<script id="chart-js-script" src="<?= base_url('assets/plugins/Chart.js/Chart.min.js'); ?>"></script>
<script src="<?= site_url('bower_components/dropzone/dist/min/dropzone.min.js'); ?>"></script>
<!-- are you sure safari fix -->
<script src="<?= site_url('bower_components/jquery.are-you-sure/ays-beforeunload-shim.js'); ?>"></script>
<script src="<?= site_url('bower_components/jquery.are-you-sure/jquery.are-you-sure.js'); ?>"></script>
<script src="<?= site_url("bower_components/jquery-circle-progress/dist/circle-progress.js"); ?>"></script>
<script src="<?= site_url('bower_components/accounting.js/accounting.min.js'); ?>"></script>
<script src="<?= site_url('bower_components/fullcalendar/dist/fullcalendar.min.js'); ?>"></script>
<script src="<?= site_url('bower_components/fullcalendar/dist/gcal.js'); ?>"></script>
<script src="<?= site_url('assets/js/jquery.scrollbox.js'); ?>"></script>
<?php if(isset($calendar_assets)){ ?>
<!-- CALENDAR JS -->
<script src="<?= site_url('bower_components/fullcalendar/dist/fullcalendar.min.js'); ?>"></script>
<script src="<?= site_url('assets/js/client/calendar.js?v=' . version_sources()); ?>"></script>
<script src="<?= site_url('assets/plugins/jquery-comments/js/jquery-comments.min.js'); ?>"></script>
<script src="<?= site_url('assets/plugins/datetimepicker/jquery.datetimepicker.full.min.js?001'); ?>"></script>
<?php } ?>
<?php if(isset($ckeditor_assets)){ ?>
<!-- CKEDITOR JS -->
<script src="<?= site_url(); ?>assets/plugins/ckeditor/ckeditor.js"></script>
<?php } ?>
<?php if(isset($editor_assets)){ ?>
<!-- EDITOR JS -->
<script src="<?= site_url(); ?>assets/plugins/quill/quill.js"></script>
<script src="<?= site_url(); ?>assets/js/editor.js"></script>
<?php } ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/main.js?v=' . version_sources()); ?>"></script>


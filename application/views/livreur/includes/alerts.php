<?php
$alertclass = "";
if ($this->session->flashdata('message-success')) {
    $alertclass = "success";
} else if ($this->session->flashdata('message-warning')) {
    $alertclass = "warning";
} else if ($this->session->flashdata('message-info')) {
    $alertclass = "info";
} else if ($this->session->flashdata('message-danger')) {
    $alertclass = "danger";
}

if ($this->session->flashdata('debug')) {

    ?>
    <div class="col-lg-12">
        <div class="alert alert-warning">
            <?= $this->session->flashdata('debug'); ?>
        </div>
    </div>
<?php } ?>
<?php if ($this->session->flashdata('message-' . $alertclass)) { ?>
    <div class="col-lg-12" id="alerts">
        <div class="alert alert-<?= $alertclass; ?>">
            <?= $this->session->flashdata('message-' . $alertclass); ?>
        </div>
    </div>
<?php } ?>
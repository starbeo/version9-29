<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile default-background-color default-color">
        <span class="icon-return-mobile">
            <i class="fa fa-arrow-left mright5"></i><?= _l('return') ?>
        </span>
        <?= $title ?>
    </h4>
    <div class="content">
        <div class="row no-margin">
            <?php
            if (count($notifications) > 0) {
                foreach ($notifications as $notification) {

                    ?>
                    <div class="mbot5">
                        <label class="btn-info bloc-date "><?= date(get_current_date_time_format(), strtotime($notification['date'])) ?></label>
                        <div class="row no-margin bloc-status">
                            <h5 class="no-margin">
                                <i class="fa fa-bell mright5 fs15 lineh30"></i>
                                <span class="bold"><?= $notification['description'] ?></span>
                            </h5>
                        </div>
                    </div>
                    <?php
                }
            } else {

                ?>
                <h2 class="text-center"><?= _l('dt_empty_table') ?></h2>
                <?php
            }

            ?>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/notifications/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

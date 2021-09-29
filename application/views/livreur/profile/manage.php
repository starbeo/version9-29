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
            <p class="text-center mbot0">
                <?= staff_profile_image($_staff->staffid, array('img', 'img-responsive', 'staff-profile-image-delivery-men'), 'thumb'); ?>
            </p>
            <h2 class="text-center bold mbot10"><?= strtoupper($_staff->firstname . ' ' . $_staff->lastname) ?></h2>
            <h4 class="mbot5"><i class="fa fa-phone mright5 fs20"></i><span class="bold"><?= _l('phone_number') ?> : </span><?= $_staff->phonenumber ?></h4>
            <h4 class="mbot5"><i class="fa fa-envelope mright5 fs20"></i><span class="bold"><?= _l('email') ?> : </span><?= $_staff->email ?></h4>
            <?php if (!is_null($_staff->datecreated)) { ?>
                <h4 class="mbot5"><i class="fa fa-calendar-check-o mright5 fs20"></i><span class="bold"><?= _l('date_of_integration') ?> : </span><?= date(get_current_date_time_format(), strtotime($_staff->datecreated)) ?></h4>
                    <?php } ?>
        </div>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/profile/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

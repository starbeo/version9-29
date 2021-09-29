<?php init_head_client(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php include_once(APPPATH . 'views/client/includes/alerts.php'); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <a href="#" class="btn btn-info pull-left mright5 mbot5 btn-pack"><?= _l('see_rates'); ?></a>
                        <div id="bloc-pack" class="mtop40 display-none">
                            <div class="row">
                                <?php foreach ($packs as $pack) { ?>
                                    <div class="col-md-3 bloc-pack-api">
                                        <?php if ($pack['id'] == 2) { ?>
                                            <div class="popular-pack-api"><?= _l('the_most_popular') ?></div>
                                        <?php } else { ?>
                                            <div class="empty-bloc-pack-api"></div>
                                        <?php } ?>
                                        <div class="title-bloc-pack-api"><?= $pack['name'] ?></div>
                                        <img class="img-bloc-pack-api" src="<?= site_url() ?>assets/images/defaults/packs/<?= $pack['image'] ?>">
                                        <h3><?= $pack['price'] ?> MAD / mois</h3>
                                        <h5 class="text-muted">POUR <?= $pack['nbr_limit'] ?> REQUÊTES</h5>
                                        <?php if ($access && ($pack['id'] == $access->pack_id) && $access->status != 3) { ?>
                                            <div class="btn-bloc-pack-api-info"><?= _l('valid_access') ?></div>
                                        <?php } else { ?>
                                            <?php if ($access && is_numeric($access->pack_id) && $access->status != 3) { ?>
                                                <div class="empty-bloc-pack-api"></div>
                                            <?php } else { ?>
                                                <div id="btn-request-access" data-pack="<?= $pack['id'] ?>" class="btn-bloc-pack-api-success"><?= _l('request_access') ?></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel_s">
                    <h2><?= _l('list_of_apis') ?></h2>
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <?php
                        $columns = array(
                            _l('title'),
                            _l('url'),
                            _l('description')
                        );
                        render_datatable($columns, 'apis');

                        ?>
                    </div>
                </div>
                <?php if ($access && is_numeric($access->pack_id)) { ?>
                    <div class="panel_s">
                        <h2><?= _l('access') ?></h2>
                        <div class="panel-body">
                            <div class="clearfix"></div>
                            <?= render_input_hidden('access', 'access') ?>
                            <?php
                            $columns1 = array(
                                _l('pack'),
                                _l('number_of_requests'),
                                _l('status'),
                                _l('access_key'),
                                _l('date_created'),
                                _l('date_start'),
                                _l('date_end'),
                                _l('number_of_calls')
                            );
                            render_datatable($columns1, 'access-apis');

                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail_client(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/client/apis/manage.js?v=' . version_sources()); ?>"></script>
</body>
</html>

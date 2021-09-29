<?php init_head_livreur(); ?>
<div id="wrapper">
    <h4 class="bloc-title-mobile" style="background-color: <?= $background_color ?>;">
        <span class="icon-return-mobile"><i class="fa fa-arrow-left mright5"></i><?= _l('return') ?></span>
        <?= $title ?>
        <span class="title-nombre-total"><?= _l('total') ?> : <?= $total ?></span>
    </h4>
    <div class="content">
        <?php if (!empty($pagination)) { ?>
            <div class="row no-margin">  
                <div class="bloc-pagination">
                    <ul class="pagination bloc-ul-pagination">
                        <?= $pagination ?>
                    </ul>
                </div>   
            </div>
        <?php } ?>
        <div class="row">
            <?php
            if ($total > 0) {
                foreach ($bons_livraison as $bl) {

                    ?>
                    <div class="col-md-offset-4 col-md-4 col-sm-offset-3 col-sm-6 col-xs-12 mbot5">
                        <a href="<?= livreur_url('bons_livraison/detail/' . $bl['id']) ?>">
                            <label class="btn-info bloc-date "><?= date(get_current_date_time_format(), strtotime($bl['date_created'])) ?></label>
                            <div class="row no-margin bloc-bons-livraison">
                                <h5 class="no-margin">
                                    <i class="fa fa-file-text-o mright5 fs15 lineh30"></i>
                                    <span class="bold"><?= $bl['nom'] ?></span>
                                </h5>
                            </div>
                        </a>
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
        <?php if (!empty($pagination)) { ?>
            <div class="row no-margin">  
                <div class="bloc-pagination">
                    <ul class="pagination bloc-ul-pagination">
                        <?= $pagination ?>
                    </ul>
                </div>   
            </div>
        <?php } ?>
    </div>
</div>
<?php init_tail_livreur(); ?>
<script src="<?= site_url('assets/js/livreur/bons-livraison/liste.js?v=' . version_sources()); ?>"></script>
</body>
</html>

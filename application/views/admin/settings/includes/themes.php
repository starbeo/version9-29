<div class="row">
	<div class="col-md-4">
        <?php $selected = (get_option('theme_pdf_facture') ? get_option('theme_pdf_facture') : 'theme_1'); ?>
        <?= render_select('settings[theme_pdf_facture]',$themes,array('id',array('name')),'theme_pdf_facture',$selected); ?>
        <hr />
        <div id="bloc-show-theme-pdf-facture">
            <img id="img-theme-pdf-facture" data-name="<?= $selected ?>" class="img img-responsive" src="<?= site_url('assets/images/defaults/themes_pdf/facture/facture_pdf_' . $selected . '.jpg'); ?>" style="width: 300px; height: 400px; margin: 0 auto;" />
        </div>
    </div>
	<div class="col-md-4">
        <?php $selected = (get_option('theme_pdf_bon_livraison') ? get_option('theme_pdf_bon_livraison') : 'theme_1'); ?>
        <?= render_select('settings[theme_pdf_bon_livraison]',$themes,array('id',array('name')),'theme_pdf_bon_livraison',$selected); ?>
        <hr />
        <div id="bloc-show-theme-pdf-bon-livraison">
            <img id="img-theme-pdf-bon-livraison" data-name="<?= $selected ?>" class="img img-responsive" src="<?= site_url('assets/images/defaults/themes_pdf/bon_livraison/bon_livraison_pdf_' . $selected . '.jpg'); ?>" style="width: 300px; height: 400px; margin: 0 auto;" />
        </div>
    </div>
	<div class="col-md-4">
        <?php $selected = (get_option('theme_pdf_etiquette_bon_livraison') ? get_option('theme_pdf_etiquette_bon_livraison') : 'theme_1'); ?>
        <?= render_select('settings[theme_pdf_etiquette_bon_livraison]',$themes,array('id',array('name')),'theme_pdf_etiquette_bon_livraison',$selected); ?>
        <hr />
        <div id="bloc-show-theme-pdf-etiquette-bon-livraison">
            <img id="img-theme-pdf-etiquette-bon-livraison" data-name="<?= $selected ?>" class="img img-responsive" src="<?= site_url('assets/images/defaults/themes_pdf/bon_livraison/etiquette_bon_livraison_pdf_' . $selected . '.jpg'); ?>" style="width: 550px; height: 350px; margin: 0 auto;" />
        </div>
    </div>
</div>

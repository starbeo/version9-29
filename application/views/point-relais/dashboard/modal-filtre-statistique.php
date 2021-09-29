<div class="modal fade" id="filter-statisique" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
					<span class="edit-title"><?= _l('filter'); ?></span>
				</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
					    <div class="row">
							<div class="col-md-12">
                                <?php $selected = (isset($periode) ? $periode : ''); ?>
								<?= render_select('periode', $periodes, array('value', array('name')), 'periode', $selected); ?>
							</div>
                            <div id="wait-filtre-periode-statisique" class="col-md-12 text-center display-none">
                                <img class="width50 mtop10" src="<?= site_url('assets/images/wait.gif'); ?>" alt="Veuillez patienter SVP..." />
                                <h6 class="bold">Patientez pendant le chargement du contenu.</h6>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
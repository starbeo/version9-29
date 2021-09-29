<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<?= render_date_input('activity-log-date','utility_activity_log_filter_by_date','',array('data-column'=>1),array(),'','activity-log-date'); ?>
							</div>
							<div class="col-md-8">
		                        <div class="btn-group pull-right" data-toggle="tooltip" title="Filtre journale d'activité">
		                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                                <i class="fa fa-list"></i>
		                            </button>
		                            <ul class="dropdown-menu">
		                                <li>
		                                    <a href="#" onclick="dt_custom_view('','.table-activity-log'); return false;">
		                                        <?= _l('task_list_all'); ?>
		                                    </a>
		                                </li>
		                                <?php foreach ($livreurs as $key => $livreur) { ?>
		                                <li>
		                                    <a href="#" onclick="dt_custom_view(<?= $livreur['staffid']; ?>,'.table-activity-log'); return false;">
		                                        <?= staff_profile_image($notification['fromuserid'],array('staff-profile-image-small-1','img-circle','pull-left')).' '.$livreur['firstname'].' '.$livreur['lastname']; ?>
		                                    </a>
		                                </li>
		                                <?php } ?>
		                            </ul>
		                        </div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel_s">
					<div class="panel-body">
                    	<?= form_hidden('custom_view',$custom_view); ?>
						<?php render_datatable(array(
							_l('id'),
							_l('utility_activity_log_dt_description'),
							_l('utility_activity_log_dt_date'),
							_l('utility_activity_log_dt_staff'),
							),'activity-log'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/utilities/activity-log.js?v=' . version_sources()); ?>"></script>
</body>
</html>

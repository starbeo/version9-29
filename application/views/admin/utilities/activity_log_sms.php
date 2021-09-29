<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php include_once(APPPATH . 'views/admin/includes/alerts.php'); ?>
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
                    	<?php
                        render_datatable(array(
                            _l('code_barre'),
                            _l('status'),
                            _l('sms'),
                            _l('sms_sent'),
                            _l('date_created')
                            ), 'activities-log-sms');

                        ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<!-- JS -->
<script src="<?= site_url('assets/js/admin/utilities/activity-log-sms.js?v=' . version_sources()); ?>"></script>
</body>
</html>

<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
	      	<div class="tile tile-alt" id="messages-main">
		      	<div class="col-md-4 ms-menu">
		          	<div class="ms-user clearfix">
		            	<?= staff_profile_image($_staff->staffid,array('img-avatar', 'pull-left'), 'thumb', array('alt'=>$_staff->firstname . ' ' . $_staff->lastname)); ?>
		              	<div>
		              		<?= _l('username'); ?> :<br>
		              		<b><?= $_staff->firstname . ' ' . $_staff->lastname; ?></b>
		              	</div>
		          	</div>
		          
	                <div class="btn btn-block">
	                	<?= render_input('search_contact', '', '', 'text', array('placeholder'=>_l('search_or_start_a_new_discussion')), array(), '', 'input-transparent'); ?>
	            	</div>
		          
		          	<div class="list-group lg-alt list-contacts"></div>
		      	</div>

		      	<div class="col-md-8 ms-body ">
		        	<div class="bloc-conversation">
		        		<div class="text-center">
			        		<img src="<?= site_url(); ?>assets/images/background/background-conversation.png" style="margin-top: 70px;">
			        		<h3 class="bold">Bienvenue sur votre messagerie !</h3>
			        		<h5 style="line-height: 20px;">Simple et rapide, elle vous permet d'échanger entre les utilisateurs et les clients. La mise en relation s'effectue toujours depuis une conversation en cliquant sur le nom d'un utilisateur ou d'un client dans le bloc à gauche.</h5>
		        		</div>
		        	</div>
		          	<div class="msb-reply">
		            	<textarea id="message" placeholder="<?= _l('write_a_message'); ?>"></textarea>
		            	<input type="hidden" id="type_message" value="text">
		            	<button id="btn_message" onclick="add_message(); return false;"><i class="fa fa-paper-plane-o"></i></button>
		          	</div>
          			<?= loader_waiting_ajax('50%', '55%'); ?>
		      	</div>
	    	</div>
		</div>
	</div>

<!-- Begin Choice delete modal -->
<div class="modal animated fadeIn" id="choice_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="margin :250px 700px;">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span><?= _l('delete_messages'); ?></span>
                </h4>
            </div>
            <?= form_open('admin/live_chat/delete_message', array('class'=>'delete-message-form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
						<div class="messages-checked"></div>
                		<button group="submit" name="for_me" class="btn btn-primary pull-right"><?= _l('delete_for_me'); ?></button>
                        <button group="submit" name="for_all" class="btn btn-success pull-right mright5"><?= _l('delete_for_all'); ?></button>
                        <button group="button" class="btn btn-default pull-right mright5" data-dismiss="modal"><?= _l('close'); ?></button>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<!-- End Choice delete modal -->

<?php init_tail(); ?>
</body>
</html>

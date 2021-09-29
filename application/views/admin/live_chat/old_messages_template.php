
<div class="bloc-add-old-messages-conversation">
	<input type="hidden" id="conversationid" value="<?= $conversationid; ?>">
	<input type="hidden" id="receiverid" value="<?= $receiverid; ?>">
	<input type="hidden" id="type" value="<?= $type_conversation; ?>">
	<input type="hidden" id="page" value="<?= $page; ?>">
	<!--input type="hidden" id="query" value="<?= $query; ?>"-->
</div>
<?php if(isset($show_bloc_more_messages)){ ?>
	<div class="row no-margin bloc_show_more_messages">
  	<div class="col-sx-2 col-sm-3 col-md-4"></div>
  	<div class="col-sx-10 col-sm-6 col-md-4 show_more_messages">
  		<?= _l('show_more_messages'); ?>
  	</div>
	</div>
<?php } ?>
<?php 
foreach ($messages as $key => $m) {
	//Vérifier s'il faut afficher le message ou bien non
	if($m['creator_id'] == $staffid && !is_null($m['deleted_at'])){
		continue;
	} else if($m['creator_id'] !== $staffid && $m['_display'] == 0){
		continue;
	}
	//Direction
	$pull      = 'left';
	$direction = 'media';
	if($m['creator_id'] == $staffid && $m['creator_type'] == 'staff'){
		$pull      = 'right';
		$direction = 'right';
	}
	//Generate Username
	$username = '';
	if($type_conversation == 'staff'){
		$username = '';
	} else {
		$username = '';
	}
	//Generate Image
	$image = '';
	if($type_conversation == 'staff'){
		$image = staff_profile_image($m['creator_id'],array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	} else {
		$image = client_logo($m['creator_id'],array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	}
	//Generate Content
	$content = '';
	if($m['type'] == 'text'){
		$content = $m['content'];
	} else if($m['type'] == 'image'){
		$content = '';
	} else if($m['type'] == 'audio'){
		$content = '';
	} else if($m['type'] == 'video'){
		$content = '';
	}
	//Check if read or not read
	$check_class = 'check_notread';
	if($m['_read'] == 1){
		$check_class = 'check_read';
	}
	//Generate Date
	$date = date(get_current_date_format(), strtotime($m['created_at']));
	$time = date('H:i', strtotime($m['created_at']));
?>
<div id="message-<?= $m['id']; ?>" class="message-feed <?= $direction; ?>">
  <div class="pull-<?= $pull; ?>">
  	  <?= $image; ?>
  </div>
  <div class="media-body">
      <div class="mf-content">
    	<?= $content; ?>      
      </div>
      <small class="mf-date">
      	<i class="fa fa-clock-o"></i> 
      	<?= $date; ?> à <?= $time; ?>
      	<?php if($m['creator_id'] == $staffid){ ?>
      	<i class="fa fa-check mleft5 <?= $check_class; ?>"></i>
      	<?php if($online_recipient == 1 || $check_class == 'check_read'){ ?>
      	<i class="fa fa-check mleft-8 <?= $check_class; ?>"></i>
      	<?php } ?>
      	<?php } ?>
      </small>
  </div>
</div>
<?php } ?>

<?php 
if(isset($conversation) && isset($message)){
	//Generate Username
	$username = '';
	if($conversation->type == 'staff'){
		$username = '';
	} else {
		$username = '';
	}
	//Generate Image
	$image = '';
	if($conversation->type == 'staff'){
		$image = staff_profile_image($message->creator_id,array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	} else {
		$image = client_logo($message->creator_id,array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	}
	//Generate Content
	$content = '';
	if($message->type == 'text'){
		$content = $message->content;
	} else if($message->type == 'image'){
		$content = '';
	} else if($message->type == 'audio'){
		$content = '';
	} else if($message->type == 'video'){
		$content = '';
	}
	//Check if read or not read
	$check_class = 'check_notread';
	if($message->_read == 1){
		$check_class = 'check_read';
	}
	//Generate Date
	$date = date(get_current_date_format(), strtotime($message->created_at));
	$time = date('H:i', strtotime($message->created_at));
?>
<div id="message-<?= $message->id; ?>" class="message-feed right">
  <div class="pull-right">
  	  <?= $image; ?>
  </div>
  <div class="media-body">
      <div class="mf-content">
    	<?= $content; ?>      
      </div>
      <small class="mf-date">
      	<i class="fa fa-clock-o"></i> 
      	<?= $date; ?> à <?= $time; ?>
      	<?php if($message->creator_id == $staffid){ ?>
      	<i class="fa fa-check mleft5 <?= $check_class; ?>"></i>
      	<!--?php if($online_recipient == 1 || $check_class == 'check_read'){ ?-->
      	<?php if($check_class == 'check_read'){ ?>
      	<i class="fa fa-check mleft-8 <?= $check_class; ?>"></i>
      	<?php } ?>
      	<?php } ?>
      </small>
  </div>
</div>
<?php } ?>

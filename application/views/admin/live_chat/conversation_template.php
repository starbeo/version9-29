<?php 
if(!is_null($recipient)){
	//Generate Online Recipient
	$online_recipient = $recipient->online;
	//Generate Username Recipient
	$username_recipient = '';
	if(!is_null($recipient->username)){
		$username_recipient = $recipient->username;
	} else {
		if($type_conversation == 'staff'){
			$username_recipient = $recipient->firstname.' '.$recipient->lastname;
		} else {
			$username_recipient = $recipient->nom;
		}
	}
	//Generate Image Recipient
	$image = '';
	if($type_conversation == 'staff'){
		$image = staff_profile_image($receiverid,array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username_recipient));
	} else {
		$image = client_logo($receiverid,array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username_recipient));
	}
	//Last time connected
	$last_time = $recipient->last_time_connected;
	//Check Status
	$icon_online = icon_online_conversation($recipient->online, $last_time);
}
?>
<div class="action-header clearfix">
  	<div class="visible-xs" id="ms-menu-trigger">
    	<i class="fa fa-bars"></i>
  	</div>
  	<div class="pull-left hidden-xs">
      	<div class="lv-avatar pull-left">
      		<?= $image; ?>
      	</div>
      	<div class="lv-avatar pull-left">
      		<p class="bold no-margin"><?= $username_recipient; ?></p>
      		<?= $icon_online; ?>
      	</div>
      	<div class="lv-avatar pull-left">
      		<p class="no-margin"></p>
      	</div>
  	</div>
  	<ul class="ah-actions actions">
      	<li>
        	<a href="#" class="choice-delete-modal">
            	<i class="fa fa-trash"></i>
          	</a>
      	</li>
      	<li>
          	<a href="#" onclick="init_conversation('<?= $type_conversation; ?>', <?= $receiverid; ?>); return false;">
            	<i class="fa fa-refresh"></i>
        	</a>
      	</li>
      	<li>
          	<a href="#">
            	<i class="fa fa-cog"></i>
        	</a>
      	</li>
  	</ul>
</div>
<div class="list-messages-conversation">
	<div class="bloc-add-old-messages-conversation">
		<input type="hidden" id="conversationid" value="<?= $conversationid; ?>">
		<input type="hidden" id="receiverid" value="<?= $receiverid; ?>">
		<input type="hidden" id="type" value="<?= $type_conversation; ?>">
		<input type="hidden" id="page" value="1">
		<input type="hidden" id="total_messages" value="<?= $total_messages; ?>">
	</div>
	<?php if(count($messages) < $total_messages){ ?>
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
			$image = staff_profile_image($m['creator_id'],array('img-avatar', 'mright5'), 'small', array('alt'=>$username));
		} else {
			$image = client_logo($m['creator_id'],array('img-avatar', 'mright5'), 'small', array('alt'=>$username));
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
</div>
<script type="text/javascript">
	if($('.list-messages-conversation').html() != ''){
		$('.list-messages-conversation').animate({
               scrollTop: $('.list-messages-conversation')[0].scrollHeight}, "fast");
	}
    //On Scroll In List Message Conversation
	/*$(".list-messages-conversation").scroll(function(e) {
	    var scrollTop = $('.list-messages-conversation')[0].scrollTop;
	    var scrollHeight = $('.list-messages-conversation')[0].scrollHeight;
	    var difference = scrollHeight - scrollTop;
	    console.log(difference);
	    if(difference >= 790 && difference <= 810){
	    }
	});*/
</script>
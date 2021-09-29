
<?php 
foreach ($contacts as $key => $c) { 
	//Generate Username
	$username = '';
	if(!is_null($c['username'])){
		$username = $c['username'];
	} else {
		$username = $c['fullname'];
	}
	if(!empty($username) && strlen($username) > 30){
        $username = substr($username, 0, 30).'...';
	}
	//Generate Avatar
	$image = '';
	if($c['type'] == 'staff'){
		$image = staff_profile_image($c['contactid'],array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	} else {
		$image = client_logo($c['contactid'],array('img-avatar', 'mright5'), 'thumb', array('alt'=>$username));
	}
	//Check Status
	$icon_online = icon_online($c['online']);
	//Get last message
	$last_message = $c['last_message'];
	if(!empty($last_message) && strlen($last_message) > 40){
        $last_message = substr($last_message, 0, 40).'...';
	}
	//Get date last message
	$date_last_message = date('H:i', strtotime($c['last_message_created_at']));
?>
<div id="contact-<?= $c['contactid']; ?>" class="list-group-item contacts" onclick="init_conversation('<?= $c['type']; ?>', <?= $c['contactid']; ?>); return false;">
	<div class="list-group-item-left pull-left">
		<?= $image; ?>
	</div>
	<div class="list-group-item-body media-body">
		<p class="list-group-item-heading">
			<span><?= $username; ?></span>
			<span class="pull-right">
				<!--?php echo $icon_online; ?-->
				<?php if($c['nbr_message_unread'] > 0){ ?>
				<?= '<span id="bmu-'.$c['contactid'].'" class="label label-warning bloc_message_unread">'.$c['nbr_message_unread'].'</span>'; ?>
				<?php } ?>
			</span>
		</p>
		<p class="list-group-item-heading-1">
			<span><?= $last_message; ?></span>
			<?php if($date_last_message !== '00:00'){ ?>
			<span class="pull-right" style="font-size: 11px;"><?= $date_last_message; ?></span>
			<?php } ?>
		</p>
	</div>
</div>
<?php } ?>
		          	
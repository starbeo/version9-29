<style type="text/css">
	

	.btnPopup{
background-color: orangered;
padding: 1em 2em;
cursor: pointer;
}

.btnPopup:hover{
background-color: rgb(192, 192, 192);}

.overlay {
position: fixed;
left: 0px;
top:0px;
background-color: rgba(0,0 ,0 , 0.5);
width: 100%;
height: 100%;
z-index:1;
display:none;
}

.popup{
margin: 10% auto;
width : 70%;
background-color: rgb(243, 243, 243);
padding: 1em;
box-shadow: 0 15px 20px rgba(0, 0, 0, 0.3);
border-radius: 5px;
}

.btnClose {
float: right;
font-size:16pt;
cursor: pointer;
color: rgb(26, 26, 26);
}
span#close {
    float: right;
    display: inline-block;
    padding: 5px 10px;
    color: #fff;
    background-color: black;
    cursor: pointer;
}
</style>



<div id="overlay" class="overlay" style="z-index: 9 !important;"  >
<div id="popup" class="popup">
     <div class="">
                        <div class="panel_s">
                   
                            <div class="panel-heading" style="background-color: #323B45; text-align: center; color: #fff !important;">
                                <?= _l('clients_edit_profile_change_password_heading'); ?>
                            </div>
                            <div class="panel-body">
                                <?= form_open('client/profile/change_password_home', array('id' => 'form-change-password')); ?>
                                <?= render_input('oldpassword', 'staff_edit_profile_change_old_password', '', 'password'); ?>
                                <?= render_input('newpassword', 'staff_edit_profile_change_new_password', '', 'password'); ?>
                                <?= render_input('newpasswordr', 'staff_edit_profile_change_repet_new_password', '', 'password'); ?>
                                <button type="submit" class="btn btn-primary pull-right"><?= _l('submit'); ?></button>
                                <?= form_close(); ?>
                            </div>
                            <?php if (isset($client->last_password_change) && !is_null($client->last_password_change)) { ?>
                                <div class="panel-footer">
                                    <?= _l('staff_add_edit_password_last_changed'); ?>: <?= time_ago($client->last_password_change); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
</div>
</div>

<script type="text/javascript">
	window.onload = function(){
openMoadl();
};


function openMoadl() {
overlay.style.display='block';
}

</script>



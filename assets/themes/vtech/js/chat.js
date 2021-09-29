$(document).ready(function() {
  //Load List Contacts after 10 secondes
  setInterval(function(){
    init_list_contacts()
  },10000);
  //Load Conversation after 5 secondes
  setInterval(function(){
    var _id = $('input[id="receiverid"]').val();
    if($.isNumeric(_id)){
      sync_conversation()
    }
  },5000);
  //Load List Contacts
  init_list_contacts();
  //On keyup search contact
  $('body').on('keyup', 'input[name="search_contact"]', function() {
    init_list_contacts();
  });
  //On Click content message
  $('body').on('click', '.message-feed', function() {
    var area_id   = $(this).attr('id');
    var messageid = area_id.replace('message-', '');
    if($("#"+area_id).hasClass('selected')){
      $("#"+area_id).css('background', 'transparent');
      $("#"+area_id).removeClass('selected');
      $('#message_'+messageid).remove();
    } else {
      $("#"+area_id).css('background', '#FAFAFA');
      $("#"+area_id).addClass('selected');
      $('.messages-checked').append('<input id="message_'+messageid+'" type="hidden" name="messages[]" value="'+messageid+'" />');
    }
  });
  //On show modal choice delete message
  $('body').on('click', '.choice-delete-modal', function(e) {
    e.preventDefault();
    var nbr = $('#delete-message-form-espace-client input').length;
    if(nbr == 0){
      $('#choice_delete_modal').modal('hide');
      alert_float('warning', 'Sélectionner le message à supprimer en cliquant sur le message.')
    } else {
      $('#choice_delete_modal').modal('show');
    }
  });
  //Validate For delete message
  _validate_form($('#delete-message-form-espace-client'), {}, manage_delete_message_espace_client);
  //On Click in show more message
  $('body').on('click', '.show_more_messages', function() {
    var conversationid = $('input[id="conversationid"]').val();
    var type = $('input[id="type"]').val();
    var receiverid = $('input[id="receiverid"]').val();
    var page = $('input[id="page"]').val();
    var url = site_url + 'expediteurs/get_old_conversation_data_ajax/' + conversationid + '/' + type + '/' + receiverid;
    if($.isNumeric(page)){
      url += '/' + page;
    }
    // Hide Scroll
    $('.list-messages-conversation').css('overflow', 'hidden');
    $.get(url).success(function(response) {
      // Scroll
      $('.list-messages-conversation').animate({
        scrollTop: $(".show_more_messages").offset().top + 400
      }, 'fast');
      $('.bloc-add-old-messages-conversation').remove();
      $('.bloc_show_more_messages').remove();
      $('.list-messages-conversation').prepend(response);
      // Show Scroll
      setInterval(function(){
        $('.list-messages-conversation').css('overflow', 'scroll');
      }, 1000);
    });
  });
});

//Reload list contacts
function init_list_contacts() {
  var input_search_contact = $('input[name="search_contact"]').val();
  $('.list-contacts').load(site_url + 'expediteurs/get_list_contacts_data_ajax/' + input_search_contact);
}
//Reload Conversation
function init_conversation(type, id) {
  if(typeof(type) == 'undefined'){
    var type = $('input[id="type"]').val();
  }
  if(typeof(id) == 'undefined'){
    var id = $('input[id="receiverid"]').val();
  }
  if(typeof(type) !== 'undefined' && typeof(id) !== 'undefined'){
    $('#bmu-'+id).remove();
    $(".contacts").css('background-color', '#ffffff;');
    $("#contact-"+id).css('background-color', '#e9ebeb;');
    $(".msb-reply").css('visibility', 'visible');
    $('.bloc-conversation').load(site_url + 'expediteurs/get_conversation_data_ajax/' + type + '/' + id)
  }
}
//Synchronisation Message Conversation
function sync_conversation(type, id) {
  if(typeof(type) == 'undefined'){
    var type = $('input[id="type"]').val();
  }
  if(typeof(id) == 'undefined'){
    var id = $('input[id="receiverid"]').val();
  }
  if(typeof(type) !== 'undefined' && typeof(id) !== 'undefined'){
    $.get(site_url + 'expediteurs/get_sync_conversation_data_ajax/' + type + '/' + id).success(function(response) {
      response = jQuery.parseJSON(response);
      if(response.success == true && response.total > 0){
        $('.list-messages-conversation').append(response.bloc);
        scroll_bottom_list_messages_conversation();
      }
    });
  }
}
//Scroll to botton for reload conversation
function scroll_bottom_list_messages_conversation() {
  if($('.list-messages-conversation').html() != ''){
    $('.list-messages-conversation').animate({
               scrollTop: $('.list-messages-conversation')[0].scrollHeight}, "fast");
  }
}
//Add new message
function add_message() {
  $('button[id="btn_message"]').attr('disabled', true);
  var conversationid = $('input[id="conversationid"]').val();
  var area_message   = $('textarea[id="message"]'); 
  var message        = area_message.val();
  var type           = $('input[id="type_message"]').val();

  //Delete espace in message
  _message   = message.replace(/\s/g, '');
  msg_length = _message.length;
  if(msg_length == 0){
    message = '';
  }

  if(!$.isNumeric(conversationid)){
    alert_float('warning', 'Choisir un contact !!')
  } else if(message == ''){
    alert_float('warning', 'Saisir un message !!')
  } else if($.isNumeric(conversationid) && message != ''){
    $.post(site_url + 'expediteurs/add_message', {
      conversationid: conversationid,
      message: message,
      type: type
    }).success(function(response) {
      response = jQuery.parseJSON(response);
      if(response.success == true){
        area_message.val('');
        area_message.focus();
        $('.list-messages-conversation').append(response.bloc);
        scroll_bottom_list_messages_conversation();
        $('button[id="btn_message"]').attr('disabled', false);
      }
    });
  }
}
//Delete message
function manage_delete_message_espace_client(form) {
    var conversationid = $('input[id="conversationid"]').val();
    var url  = form.action;
    var data = $(form).serialize();
    data += '&conversationid='+conversationid;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.success == true) {
          $('.message-feed.selected').remove();
          $('.messages-checked').html('');
        }
        $('#choice_delete_modal').modal('hide');
    });

    return false;
}
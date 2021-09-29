$(document).ready(function() {
  _validate_form($('#enquete-form'), {reponse: 'required'}, manage_enquete);
});

function manage_enquete(form) {
    var data = $(form).serialize();
    var url  = form.action;
    $.post(url, data).success(function(response) {
        response = $.parseJSON(response);
        if (response.success == true) {
            $('#slide').html('');
            $('#slide').html(response.contenu);
            if (response.close == true) {
              $('#close_enquete').css('display', 'block');
              $('#btn_submit_enquete').css('display', 'none');
              $('#btn_close_enquete').css('display', 'block');
            }
        }
    });

    return false;
}

function close_enquete() {
    $('#bloc_enquete').css('display', 'none');
    window.location.href = site_url + 'expediteurs';
}
$(document).ready(function () {
    // Validate form marketing
    _validate_form($('#marketing-form'), {
        name: 'required',
        type: 'required',
        notification_by: 'required'
    });
    // On change type
    $('body').on('change', '#marketing-form select[name="type"]', function () {
        var type = $('#marketing-form select[name="type"]').selectpicker('val');
        if (!$('#marketing-form #bloc-select-rel-id').hasClass('display-none')) {
            $('#marketing-form #bloc-select-rel-id').addClass('display-none');
        }
        if (parseInt(type) === 2) {
            initSelectRelationByClients();
        } else if (parseInt(type) === 3) {
            initSelectRelationByGroupes();
        } else {
            $('#marketing-form select[name="rel_id"]').html('<option value=""></option>');
            $('#marketing-form select[name="rel_id"]').selectpicker('refresh');
        }
    });
    // Init relation
    var relId = $('#marketing-form #rel_id_hidden').val();
    if ($.isNumeric(relId)) {
        var typeMarketing = $('#marketing-form select[name="type"]').selectpicker('val');
        if (parseInt(typeMarketing) === 2) {
            initSelectRelationByClients(relId);
        } else if (parseInt(typeMarketing) === 3) {
            initSelectRelationByGroupes(relId);
        }
    }
    // On change
    $('body').on('change', '#marketing-form input[name="notification_by"]', function () {
        var notificationBy = $(this).val();
        if(notificationBy === 'email') {
            if(!$('#bloc-notification-by-sms').hasClass('display-none')) {
                $('#bloc-notification-by-sms').addClass('display-none');
            }
            $('#marketing-form textarea[name="sms"]').val('');
            if($('#bloc-notification-by-email').hasClass('display-none')) {
                $('#bloc-notification-by-email').removeClass('display-none');
            }
        } else {
            if(!$('#bloc-notification-by-email').hasClass('display-none')) {
                $('#bloc-notification-by-email').addClass('display-none');
            }
            $('#marketing-form input[name="subject"]').val('');
            $('#ql-editor-1').html('');
            if($('#bloc-notification-by-sms').hasClass('display-none')) {
                $('#bloc-notification-by-sms').removeClass('display-none');
            }
        }
    });
    // On change
    $('body').on('change', '#marketing-form input[name="notification_by_email"]', function () {
        var notificationByEmail = $(this).val();
        if(notificationByEmail === 'text') {
            if(!$('#bloc-notification-by-email-image').hasClass('display-none')) {
                $('#bloc-notification-by-email-image').addClass('display-none');
            }
            $('#marketing-form input[name="image"]').val('');
            if($('#bloc-notification-by-email-text').hasClass('display-none')) {
                $('#bloc-notification-by-email-text').removeClass('display-none');
            }
        } else {
            if(!$('#bloc-notification-by-email-text').hasClass('display-none')) {
                $('#bloc-notification-by-email-text').addClass('display-none');
            }
            $('#ql-editor-1').html('');
            if($('#bloc-notification-by-email-image').hasClass('display-none')) {
                $('#bloc-notification-by-email-image').removeClass('display-none');
            }
        }
    });
});

// Init Select Relation By Clients
function initSelectRelationByClients(clientId)
{
    $.post(admin_url + "marketing/get_all_clients", function (response) {
        var response = $.parseJSON(response);
        $('#marketing-form select[name="rel_id"]').html('<option value=""></option>');
        for (var i = 0; i < response.length; i++) {
            $('#marketing-form select[name="rel_id"]').append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
        }
        $('#marketing-form select[name="rel_id"]').selectpicker('refresh');
        if (typeof (clientId) !== 'undefined' && $.isNumeric(clientId)) {
            $('#marketing-form select[name="rel_id"]').selectpicker('val', clientId);
        }
        if ($('#marketing-form #bloc-select-rel-id').hasClass('display-none')) {
            $('#marketing-form #bloc-select-rel-id').removeClass('display-none');
        }
    });
}

// Init Select Relation By Groupes
function initSelectRelationByGroupes(groupeId)
{
    $.post(admin_url + "marketing/get_all_groupes", function (response) {
        var response = $.parseJSON(response);
        $('#marketing-form select[name="rel_id"]').html('<option value=""></option>');
        for (var i = 0; i < response.length; i++) {
            $('#marketing-form select[name="rel_id"]').append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
        }
        $('#marketing-form select[name="rel_id"]').selectpicker('refresh');
        if (typeof (groupeId) !== 'undefined' && $.isNumeric(groupeId)) {
            $('#marketing-form select[name="rel_id"]').selectpicker('val', groupeId);
        }
        if ($('#marketing-form #bloc-select-rel-id').hasClass('display-none')) {
            $('#marketing-form #bloc-select-rel-id').removeClass('display-none');
        }
    });
}
$(document).ready(function () {
    // Init Table Objets
    initDataTable('.table-objets', window.location.href, 'Objets', 'undefined', 'undefined', 'undefined', [2, 'ASC']);
    // Init textarea with ckeditor
    ckeditor_start_ckfinder('email_staff');
    ckeditor_start_ckfinder('email_livreur');
    ckeditor_start_ckfinder('email_client');
    //Affichage modal Objet
    $('#objet_departement_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#objet_departement_modal .add-title').removeClass('hide');
        $('#objet_departement_modal .edit-title').addClass('hide');
        $('#objet_departement_modal select').selectpicker('val', '');
        $('#objet_departement_modal input[type="text"]').val('');
        $('#objet_departement_modal input[name="id"]').val('');
        $('#objet_departement_modal input[id="radio-yes"]').prop('checked', false);
        $('#objet_departement_modal input[id="radio-no"]').prop('checked', true);
        $('#objet_departement_modal select[name="bind_to"]').selectpicker('val', '');
        if (!$('#bloc-select-bind-to').hasClass('display-none')) {
            $('#bloc-select-bind-to').addClass('display-none');
        }

        $('.nav-tabs a[href="#staff"]').tab('show');
        $('#objet_departement_modal input[type="checkbox"]').prop('checked', false);
        CKEDITOR.instances["email_client"].setData('');
        CKEDITOR.instances["email_livreur"].setData('');
        CKEDITOR.instances["email_client"].setData('');
        if (!$('#bloc-notification-by-email-staff').hasClass('display-none')) {
            $('#bloc-notification-by-email-staff').addClass('display-none');
        }
        if (!$('#bloc-notification-by-sms-staff').hasClass('display-none')) {
            $('#bloc-notification-by-sms-staff').addClass('display-none');
        }
        if (!$('#bloc-notification-by-email-livreur').hasClass('display-none')) {
            $('#bloc-notification-by-email-livreur').addClass('display-none');
        }
        if (!$('#bloc-notification-by-sms-livreur').hasClass('display-none')) {
            $('#bloc-notification-by-sms-livreur').addClass('display-none');
        }
        if (!$('#bloc-notification-by-email-client').hasClass('display-none')) {
            $('#bloc-notification-by-email-client').addClass('display-none');
        }
        if (!$('#bloc-notification-by-sms-client').hasClass('display-none')) {
            $('#bloc-notification-by-sms-client').addClass('display-none');
        }
        if (!$('.available_merge_fields_container').hasClass('hide')) {
            $('.available_merge_fields_container').addClass('hide');
        }
        $('#objet_departement_modal button[id="submit"]').attr('disabled', false);

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var type = $(invoker).data('type');
            var departementId = $(invoker).data('departement-id');
            var visibility = $(invoker).data('visibility');
            var bind = $(invoker).data('bind');
            var bind_to = $(invoker).data('bind-to');
            $('#objet_departement_modal input[name="id"]').val(id);
            $('#objet_departement_modal .add-title').addClass('hide');
            $('#objet_departement_modal .edit-title').removeClass('hide');
            $('#objet_departement_modal select[name="type"]').selectpicker('val', type);
            $('#objet_departement_modal select[name="departement_id"]').selectpicker('val', departementId);
            $('#objet_departement_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(2).text());
            $('#objet_departement_modal select[name="visibility"]').selectpicker('val', visibility);
            if (parseInt(bind) === 1) {
                $('input[id="radio-yes"]').prop('checked', true);
                $('input[id="radio-no"]').prop('checked', false);
                $('#objet_departement_modal select[name="bind_to"]').selectpicker('val', bind_to);
                if ($('#bloc-select-bind-to').hasClass('display-none')) {
                    $('#bloc-select-bind-to').removeClass('display-none');
                }
            }

            $.post(admin_url + "departements/get_objet/" + id, function (response) {
                var response = $.parseJSON(response);
                //Staff
                if (parseInt(response.send_notification_staff) === 1) {
                    $('#objet_departement_modal input[name="send_notification_staff"]').prop('checked', true);
                }
                if (parseInt(response.send_email_staff) === 1) {
                    $('#objet_departement_modal input[name="send_email_staff"]').prop('checked', true);
                    $('#objet_departement_modal input[name="subject_email_staff"]').val(response.subject_email_staff);
                    CKEDITOR.instances["email_staff"].setData(response.email_staff);
                    if ($('#bloc-notification-by-email-staff').hasClass('display-none')) {
                        $('#bloc-notification-by-email-staff').removeClass('display-none');
                    }
                }
                if (parseInt(response.send_sms_staff) === 1) {
                    $('#objet_departement_modal input[name="send_sms_staff"]').prop('checked', true);
                    $('#objet_departement_modal textarea[name="sms_staff"]').val(response.sms_staff);
                    if ($('#bloc-notification-by-sms-staff').hasClass('display-none')) {
                        $('#bloc-notification-by-sms-staff').removeClass('display-none');
                    }
                }
                //Livreur
                if (parseInt(response.send_notification_livreur) === 1) {
                    $('#objet_departement_modal input[name="send_notification_livreur"]').prop('checked', true);
                }
                if (parseInt(response.send_email_livreur) === 1) {
                    $('#objet_departement_modal input[name="send_email_livreur"]').prop('checked', true);
                    $('#objet_departement_modal input[name="subject_email_livreur"]').val(response.subject_email_livreur);
                    CKEDITOR.instances["email_livreur"].setData(response.email_livreur);
                    if ($('#bloc-notification-by-email-livreur').hasClass('display-none')) {
                        $('#bloc-notification-by-email-livreur').removeClass('display-none');
                    }
                }
                if (parseInt(response.send_sms_livreur) === 1) {
                    $('#objet_departement_modal input[name="send_sms_livreur"]').prop('checked', true);
                    $('#objet_departement_modal textarea[name="sms_livreur"]').val(response.sms_livreur);
                    if ($('#bloc-notification-by-sms-livreur').hasClass('display-none')) {
                        $('#bloc-notification-by-sms-livreur').removeClass('display-none');
                    }
                }
                //Client
                if (parseInt(response.send_notification_client) === 1) {
                    $('#objet_departement_modal input[name="send_notification_client"]').prop('checked', true);
                }
                if (parseInt(response.send_email_client) === 1) {
                    $('#objet_departement_modal input[name="send_email_client"]').prop('checked', true);
                    $('#objet_departement_modal input[name="subject_email_client"]').val(response.subject_email_client);
                    CKEDITOR.instances["email_client"].setData(response.email_client);
                    if ($('#bloc-notification-by-email-client').hasClass('display-none')) {
                        $('#bloc-notification-by-email-client').removeClass('display-none');
                    }
                }
                if (parseInt(response.send_sms_client) === 1) {
                    $('#objet_departement_modal input[name="send_sms_client"]').prop('checked', true);
                    $('#objet_departement_modal textarea[name="sms_client"]').val(response.sms_client);
                    if ($('#bloc-notification-by-sms-client').hasClass('display-none')) {
                        $('#bloc-notification-by-sms-client').removeClass('display-none');
                    }
                }
            });
        }
    });
    // On change input radio bind
    $('body').on('change', 'input[name="bind"]', function () {
        var bind = $('input[name="bind"]:checked').val();
        if (parseInt(bind) === 1) {
            if ($('#bloc-select-bind-to').hasClass('display-none')) {
                $('#bloc-select-bind-to').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-select-bind-to').hasClass('display-none')) {
                $('#bloc-select-bind-to').addClass('display-none');
            }
        }
    });
    // On change email or sms staff
    $('body').on('change', 'input[name="send_email_staff"]', function () {
        if ($('input[name="send_email_staff"]').prop('checked')) {
            if ($('#bloc-notification-by-email-staff').hasClass('display-none')) {
                $('#bloc-notification-by-email-staff').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-email-staff').hasClass('display-none')) {
                $('#bloc-notification-by-email-staff').addClass('display-none');
            }
            $('input[name="subject_email_staff"]').val('');
            $('textarea[name="email_staff"]').val('');
        }
    });
    $('body').on('change', 'input[name="send_sms_staff"]', function () {
        if ($('input[name="send_sms_staff"]').prop('checked')) {
            if ($('#bloc-notification-by-sms-staff').hasClass('display-none')) {
                $('#bloc-notification-by-sms-staff').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-sms-staff').hasClass('display-none')) {
                $('#bloc-notification-by-sms-staff').addClass('display-none');
            }
            $('textarea[name="sms_staff"]').val('');
        }
    });
    // On change email or sms livreur
    $('body').on('change', 'input[name="send_email_livreur"]', function () {
        if ($('input[name="send_email_livreur"]').prop('checked')) {
            if ($('#bloc-notification-by-email-livreur').hasClass('display-none')) {
                $('#bloc-notification-by-email-livreur').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-email-livreur').hasClass('display-none')) {
                $('#bloc-notification-by-email-livreur').addClass('display-none');
            }
            $('input[name="subject_email_livreur"]').val('');
            $('textarea[name="email_livreur"]').val('');
        }
    });
    $('body').on('change', 'input[name="send_sms_livreur"]', function () {
        if ($('input[name="send_sms_livreur"]').prop('checked')) {
            if ($('#bloc-notification-by-sms-livreur').hasClass('display-none')) {
                $('#bloc-notification-by-sms-livreur').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-sms-livreur').hasClass('display-none')) {
                $('#bloc-notification-by-sms-livreur').addClass('display-none');
            }
            $('textarea[name="sms_livreur"]').val('');
        }
    });
    // On change email or sms client
    $('body').on('change', 'input[name="send_email_client"]', function () {
        if ($('input[name="send_email_client"]').prop('checked')) {
            if ($('#bloc-notification-by-email-client').hasClass('display-none')) {
                $('#bloc-notification-by-email-client').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-email-client').hasClass('display-none')) {
                $('#bloc-notification-by-email-client').addClass('display-none');
            }
            $('input[name="subject_email_client"]').val('');
            $('textarea[name="email_client"]').val('');
        }
    });
    $('body').on('change', 'input[name="send_sms_client"]', function () {
        if ($('input[name="send_sms_client"]').prop('checked')) {
            if ($('#bloc-notification-by-sms-client').hasClass('display-none')) {
                $('#bloc-notification-by-sms-client').removeClass('display-none');
            }
        } else {
            if (!$('#bloc-notification-by-sms-client').hasClass('display-none')) {
                $('#bloc-notification-by-sms-client').addClass('display-none');
            }
            $('textarea[name="sms_client"]').val('');
        }
    });
    //Validation form departement
    _validate_form($('#from-objet'), {
        type: 'required',
        departement_id: 'required',
        name: 'required',
        bind: 'required'
    }, manage_objet);
});

function manage_objet(form) {
    var contentEmailStaff = CKEDITOR.instances["email_staff"].getData();
    $('#objet_departement_modal textarea[name="email_staff"]').val(contentEmailStaff);
    var contentEmailLivreur = CKEDITOR.instances["email_livreur"].getData();
    $('#objet_departement_modal textarea[name="email_livreur"]').val(contentEmailLivreur);
    var contentEmailClient = CKEDITOR.instances["email_client"].getData();
    $('#objet_departement_modal textarea[name="email_client"]').val(contentEmailClient);

    $('#objet_departement_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-objets').DataTable().ajax.reload();
        }
        $('#objet_departement_modal').modal('hide');
    });

    return false;
}


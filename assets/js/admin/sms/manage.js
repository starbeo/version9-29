$(document).ready(function () {
    // Init Table Sms
    var headers_sms = $('.table-sms').find('th');
    var not_sortable_sms = (headers_sms.length - 1);
    initDataTable('.table-sms', window.location.href, 'sms', [not_sortable_sms], [not_sortable_sms], 'undefined', [0, 'DESC']);
    //Affichage modal sms
    $('#sms_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        var action = $(invoker).data('action');
        $('#sms_modal .add-title').removeClass('hide');
        $('#sms_modal .edit-title').addClass('hide');
        $('#sms_modal select').selectpicker('val', '');
        $('#sms_modal input[name="id"]').val('');
        $('#sms_modal input[name="title"]').val('');
        $('#sms_modal textarea[name="message"]').val('');
        $("#sms_modal #automatic_sending_yes").prop("checked", false);
        $("#sms_modal #automatic_sending_no").prop("checked", true);
        $('#sms_modal button[id="submit-form-sms"]').attr('disabled', false);
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "sms/get_infos_sms/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#sms_modal input[name="id"]').val(id);
                $('#sms_modal .add-title').addClass('hide');
                $('#sms_modal .edit-title').removeClass('hide');
                $('#sms_modal input[name="title"]').val(data['title']);
                $('#sms_modal select[name="status_id"]').selectpicker('val', data['status_id']);
                $('#sms_modal textarea[name="message"]').val(data['message']);
                if (data['automatic_sending'] === 'Manuelle') {
                    $("#sms_modal #automatic_sending_yes").prop("checked", false);
                    $("#sms_modal #automatic_sending_no").prop("checked", true);
                } else {
                    $("#sms_modal #automatic_sending_yes").prop("checked", true);
                    $("#sms_modal #automatic_sending_no").prop("checked", false);
                }
            });
        }

        if (typeof (action) !== 'undefined' && action === 'edit') {
            $('#sms-footer-form').show();
        } else {
            $('#sms-footer-form').hide();
        }
    });

    //Validation form sms
    _validate_form($('form'), {
        status_id: 'required',
        title: 'required',
        message: 'required'
    }, manage_sms);

    //Affichage modal test sms
    $('#test_send_sms_modal').on('show.bs.modal', function () {
        $('#test_send_sms_modal input').val('');
        $('#test_send_sms_modal textarea').val('');
        $('#test_send_sms_modal button[id="submit-form-test-send-sms"]').attr('disabled', false);
    });

    //Validation form test send sms
    _validate_form($('#form-test-send-sms'), {
        phone_number_test: {
            required: true,
            remote: {
                url: site_url + "admin/misc/check_telephone",
                type: 'post',
                data: {
                    telephone: function () {
                        return $('input[name="phone_number_test"]').val();
                    }
                }
            }
        },
        message_test: 'required'
    }, manage_test_send_sms);
});

function manage_sms(form) {
    $('#sms_modal button[id="submit-form-sms"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            $('.table-sms').DataTable().ajax.reload();
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        $('#sms_modal').modal('hide');
    });

    return false;
}

function manage_test_send_sms(form) {
    $('#test_send_sms_modal button[id="submit-form-test-send-sms"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true) {
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        $('#test_send_sms_modal').modal('hide');
    });

    return false;
}
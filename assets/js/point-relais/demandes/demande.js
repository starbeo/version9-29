$(document).ready(function () {
    //Validate form demande
    _validate_form($('#demande-form'), {
        type: 'required',
        object: 'required',
        priorite: 'required',
        message: 'required'
    });
    //If relation id not empty
    var relId = $('input[name="hidden_rel_id"]').val();
    if (relId !== '') {
        onChangeSelectObject(relId);
    }
    //On change type
    $('body').on('change', 'select[name="type"]', function () {
        onChangeSelectType();
    });
    //On change client
    $('body').on('change', 'select[name="client_id"]', function () {
        var clientId = $('select[name="client_id"]').selectpicker('val');
        if ($.isNumeric(clientId)) {
            $('select[name="object"]').selectpicker('val', '');
            $('select[name="rel_id"]').html('<option value=""></option>');
            $('select[name="rel_id"]').selectpicker('refresh');
        }
    });
    //On change object
    $('body').on('change', 'select[name="object"]', function () {
        var clientId = $('select[name="client_id"]').selectpicker('val');
        if ($.isNumeric(clientId)) {
            onChangeSelectObject(relId);
        } else {
            alert_float('warning', 'Choississez un client');
        }
    });
});
//On change select type
function onChangeSelectType(type) {
    if (typeof (type) === 'undefined') {
        var type = $('select[name="type"]').selectpicker('val');
    }
    if (type !== '') {
        $.post(point_relais_url + "demandes/get_object_by_type", {type: type}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                list = response.objets;
                $('select[name="object"]').html('<option value=""></option>');
                for (var i = 0; i < list.length; i++) {
                    var selected = '';
                    if (parseInt(type) === parseInt(list[i].id)) {
                        selected = 'selected';
                    }
                    $('select[name="object"]').append('<option value="' + list[i].id + '" ' + selected + '>' + list[i].name + '</option>');
                }
                $('select[name="object"]').selectpicker('refresh');
            }
        });
    }
}
//On change select object
function onChangeSelectObject(relId) {
    var clientId = $('select[name="client_id"]').selectpicker('val');
    var object = $('select[name="object"]').selectpicker('val');
    // Get service
    $('input[name="department"]').val('');
    if (!$('#bloc-input-department').hasClass('display-none')) {
        $('#bloc-input-department').addClass('display-none');
    }
    $('select[name="rel_id"]').html('<option value=""></option>');
    if ($.isNumeric(object)) {
        // Get department by object
        $.post(point_relais_url + "demandes/get_department_by_object", {object_id: object}, function (response) {
            var response = $.parseJSON(response);
            if (response.success === true) {
                $('input[name="department"]').val(response.department_name);
                if ($('#bloc-input-department').hasClass('display-none')) {
                    $('#bloc-input-department').removeClass('display-none');
                }
            }
        });
        // Get relation
        $.post(point_relais_url + "demandes/get_relations_demande", {client_id: clientId, object_id: object}, function (response) {
            var list = $.parseJSON(response);
            if (list !== null) {
                for (var i = 0; i < list.length; i++) {
                    var selected = '';
                    if (parseInt(relId) === parseInt(list[i].id)) {
                        selected = 'selected';
                    }
                    $('select[name="rel_id"]').append('<option value="' + list[i].id + '" ' + selected + '>' + list[i].name + '</option>');
                }
                $('select[name="rel_id"]').selectpicker('refresh');
                if ($('#relation').hasClass('display-none')) {
                    $('#relation').removeClass('display-none');
                }
            }
        });
    } else {
        $('select[name="rel_id"]').selectpicker('refresh');
        if (!$('#relation').hasClass('display-none')) {
            $('#relation').addClass('display-none');
        }
    }
}
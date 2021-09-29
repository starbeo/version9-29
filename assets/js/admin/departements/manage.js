$(document).ready(function () {
    // Init Table Departements
    initDataTable('.table-departements', window.location.href, 'Départements', 'undefined', 'undefined', 'undefined', [0, 'ASC']);
    //Affichage modal Departement
    $('#departement_modal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#departement_modal .add-title').removeClass('hide');
        $('#departement_modal .edit-title').addClass('hide');
        $('#departement_modal input').val('');
        $('#departement_modal button[id="submit"]').attr('disabled', false);
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            var color = $(invoker).data('color');
            $('#departement_modal input[name="id"]').val(id);
            $('#departement_modal .add-title').addClass('hide');
            $('#departement_modal .edit-title').removeClass('hide');
            $('#departement_modal input[name="name"]').val($(invoker).parents('tr').find('td').eq(0).text());
            $('#departement_modal input[name="color"]').val(color);
        }
    });
    //Validation form departement
    _validate_form($('#from-departement'), {
        name: 'required',
        color: 'required'
    }, manage_departement);

});

function manage_departement(form) {
    $('#departement_modal button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        alert_float(response.type, response.message);
        if (response.success === true) {
            $('.table-departements').DataTable().ajax.reload();
        }
        $('#departement_modal').modal('hide');
    });

    return false;
}


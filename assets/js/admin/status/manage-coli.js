$(document).ready(function () {
    //Init Data Table Status
    var headers_status = $('.table-status').find('th');
    var not_sortable_status = (headers_status.length - 1);
    var StatusServerParams = {
        "f-statut": "[name='f-statut']",
        "f-date-created": "[name='f-date-created']"
    };
    initDataTable('.table-status', window.location.href, 'status', [not_sortable_status], [not_sortable_status], StatusServerParams);
    var hidden_columns = [0];
    $.each(hidden_columns, function (i, val) {
        var column_status = $('.table-status').DataTable().column(val);
        column_status.visible(false);
    });

    _validate_form($('#formstatu'), {
        code_barre_verifie: {
            required: true,
            remote: {
                url: admin_url + "status/check_code_barre_exist",
                type: 'post',
                data: {
                    code_barre_verifie: function () {
                        return $('input[name="code_barre_verifie"]').val();
                    }
                }
            }
        },
        type: 'required',
        emplacement_id: 'required'
    }, manage_status);

    $('body').on('click', '.btn-filtre', function () {
        if ($('#filtre-table').hasClass('display-none')) {
            $('#filtre-table').removeClass('display-none');
        } else {
            $('#filtre-table').addClass('display-none');
        }
    });

    $('body').on('click', '#filtre-submit', function () {
        $('.table-status').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });

    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-status').DataTable().ajax.reload();
    });

    $('#statuspopup').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        var barcode = $(invoker).data('barcode');
        $('#statuspopup .add-title').removeClass('hide');
        $('#statuspopup .edit-title').addClass('hide');
        $('#statuspopup input').val('');
        $('#statuspopup input[name="code_barre_verifie"]').attr('disabled', false);
        $('select[name="type"]').selectpicker('val', '');
        $('select[name="emplacement_id"]').selectpicker('val', '');
        $('#statuspopup button[id="submit"]').attr('disabled', false);
        if (!$('#date_reporte').hasClass('display-none')) {
            $('#date_reporte').addClass('display-none');
        }
        if (!$('#motif').hasClass('display-none')) {
            $('#motif').addClass('display-none');
        }

        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "status/get_info_status/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#statuspopup input[name="id"]').val(id);
                $('#statuspopup .add-title').addClass('hide');
                $('#statuspopup .edit-title').removeClass('hide');
                $('#statuspopup input[name="code_barre_verifie"]').val(data['code_barre']);
                $('#statuspopup input[name="code_barre_verifie"]').attr('disabled', true);
                $('#statuspopup select[name="type"]').selectpicker('val', data['type']);
                $('#statuspopup select[name="emplacement_id"]').selectpicker('val', data['emplacement_id']);
                if (parseInt(data['type']) === 11 && data['date_reporte'] !== '0000-00-00') {
                    $('#date_reporte').removeClass('display-none');
                    $('#statuspopup input[name="date_reporte"]').val(data['date_reporte']);
                }
                if (parseInt(data['type']) === 3 && data['motif'] !== 0) {
                    $('#motif').removeClass('display-none');
                    $('#status select[name="motif"]').selectpicker('val', data['motif']);
                }
                $('#statuspopup input[name="code_barre_verifie"]').keyup();
            });
        } else if (typeof (barcode) !== 'undefined') {
            $('#statuspopup input[name="code_barre_verifie"]').val(barcode);
            $('#statuspopup input[name="code_barre_verifie"]').attr('disabled', true);
            $('#statuspopup input[name="code_barre_verifie"]').keyup();
        } else {
            $('select[name="type"]').selectpicker('val', '');
            $('select[name="emplacement_id"]').selectpicker('val', '');
        }
    });

    $('body').on('keyup', 'input[name="code_barre_verifie"]', function () {
        var barcode = $(this).val();
        if (barcode !== '') {
            $.post(admin_url + "status/get_coli_by_barcode/" + barcode, function (response) {
                var data = $.parseJSON(response);
                //Check if colis is already invoiced
                if ($.isNumeric(data['num_facture'])) {
                    alert_float('warning', 'Colis déjà facturé.');
                    $('#statuspopup input[name="code_barre_verifie"]').val('');
                } else if ($.isNumeric(data['status_reel']) && (parseInt(data['status_reel']) === 2 || parseInt(data['status_reel']) === 3 || parseInt(data['status_reel']) === 9 || parseInt(data['status_reel']) === 13)) {
                    if (parseInt(data['status_reel']) === 2) {
                        alert_float('warning', 'Colis livré, Changement de statut est arreté.');
                    } else if (parseInt(data['status_reel']) === 3) {
                        alert_float('warning', 'Colis retourné, Changement de statut est arreté.');
                    } else if (parseInt(data['status_reel']) === 9) {
                        alert_float('warning', 'Colis refusé, Changement de statut est arreté.');
                    } else if (parseInt(data['status_reel']) === 13) {
                        alert_float('warning', 'Colis retourné à l\'agence, Changement de statut est arreté.');
                    }
                    $('#statuspopup input[name="code_barre_verifie"]').val('');
                } else {
                    console.log(data)

                    $('#statuspopup input[name="coli_id"]').val(data['id']);
                    $('#statuspopup input[name="clientid"]').val(data['id_expediteur']);
                    $('#statuspopup input[name="telephone"]').val(data['telephone']);
                    $('#statuspopup input[name="crbt"]').val(data['crbt']);
                }
            });
        }
    });

    $('body').on('change', 'select[name="type"]', function () {
        var type = $('select[name="type"]').selectpicker('val');
        if (!$('#date_reporte').hasClass('display-none')) {
            $('#date_reporte').addClass('display-none');
        }
        if (!$('#motif').hasClass('display-none')) {
            $('#motif').addClass('display-none');
        }
        $('input[name="date_reporte"]').val('');
        $('select[name="motif"]').selectpicker('val', '');
        if (parseInt(type) === 11) {
            $('#date_reporte').removeClass('display-none');
        } else if (parseInt(type) === 3) {
            $('#motif').removeClass('display-none');
        }
    });
});

function manage_status(form) {
    $('#statuspopup input[name="code_barre_verifie"]').attr('disabled', false);
    $('#statuspopup button[id="submit"]').attr('disabled', true);
    var data = $(form).serialize();
    var url = form.action;
    console.log(data)
console.log("hereee")
    $.post(url, data).success(function (response) {
        response = $.parseJSON(response);
        if (response.success === true && typeof (response.message) !== 'undefined') {
            alert_float('success', response.message);
        } else if (response.success === 'access_denied') {
            alert_float('warning', response.message);
        } else if (response.success === 1) {
            alert_float('success', 'Status ajouté avec succés');
            alert_float('success', 'SMS envoyé');
        } else if (response.error === 1) {
            alert_float('success', 'Status ajouté avec succés');
            alert_float('warning', 'SMS non envoyé');
        }
        //$('.table-status').DataTable().ajax.reload();
        $('#statuspopup').modal('hide');
        $('.table-colis').DataTable().ajax.reload();


    });
    return false;
}


$(document).ready(function () {
    //On click boutton return mobile
    $('body').on('click', '.icon-return-mobile', function () {
        var urlReferrer = $('input[name="url_referrer"]').val();
        if (urlReferrer === '') {
            window.location.href = livreur_url;
        } else {
            window.location.href = urlReferrer;
        }
    });

    //Validate form add status
    _validate_form($('#form-add-status'), {
        code_barre_verifie: 'required',
        type: 'required',
        emplacement_id: 'required',
        date_reporte: {
            required: {
                depends: function () {
                    return (parseInt($('select[name="type"]').val()) === 11) ? true : false;
                }
            }
        },
        motif: {
            required: {
                depends: function () {
                    return (parseInt($('select[name="type"]').val()) === 3) ? true : false;
                }
            }
        }
    });
    //On change select type
    $('body').on('change', 'select[name="type"]', function () {
        var type = $('select[name="type"]').selectpicker('val');
        
        $('input[name="date_reporte"]').val('');
        if (!$('#date_reporte').hasClass('display-none')) {
            $('#date_reporte').addClass('display-none');
        }
        $('select[name="motif"]').selectpicker('val', '');
        if (!$('#motif').hasClass('display-none')) {
            $('#motif').addClass('display-none');
        }
        
        if (parseInt(type) === 11) {
            $('#date_reporte').removeClass('display-none');
        } else if (parseInt(type) === 3) {
            $('#motif').removeClass('display-none');
        }
        
        var statusesLocationAtTheAgency = [6, 7, 8, 9, 10, 11, 13];
        var statusesLocationDistribution = [16, 17];
        var statusesLocationCustomerAddress = [2, 3];
        if(statusesLocationAtTheAgency.indexOf(parseInt(type)) >= 0) {
            $('select[name="emplacement_id"]').selectpicker('val', 9);
        } else if(statusesLocationDistribution.indexOf(parseInt(type)) >= 0) {
            $('select[name="emplacement_id"]').selectpicker('val', 10);
        } else if(statusesLocationCustomerAddress.indexOf(parseInt(type)) >= 0) {
            $('select[name="emplacement_id"]').selectpicker('val', 6);
        }
    });
});
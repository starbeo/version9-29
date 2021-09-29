$(document).ready(function () {
    //Validate Form 
    _validate_form($('#point-relai-form'), {
        societe_id: 'required',
        nom: 'required',
        ville: 'required',
        adresse: 'required',
        telephone: {
            remote: {
                url: site_url + "admin/misc/check_telephone",
                type: 'post',
                data: {
                    telephone: function () {
                        return $('input[name="telephone"]').val();
                    },
                    colis_id: function () {
                        return $('input[name="id"]').val();
                    }
                }
            }
        }
    });
    //Validate rib
    $('body').on('change', 'input[name="rib"]', function () {
        var rib = $('input[name="rib"]').val();
        var number = rib.length;
        var isnum = /^\d+$/.test(rib);
        if (isnum === false) {
            alert_float('warning', 'Ce champs doit contenir que des chiffres');
        } else if (number !== 24) {
            alert_float('warning', 'Ce champs doit contenir 24 chiffres');
        } else {
            alert_float('success', 'RIB Validé');
        }
    });
});
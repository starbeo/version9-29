function change_status_bon_livraison(toStatus) {
    if (toStatus === 'non_confirmer' || toStatus === 'confirmer') {
        var data = '';
        if($('#change-status-bon-livraison-form input[name="ids[]"]').length > 0) {
            data = $('#change-status-bon-livraison-form input[name="ids[]"]').serialize();
        } else {
            data = $('#bon-livraison-form input[name="ids[]"]').serialize();
        }
        var status = 0;
        if (toStatus === 'non_confirmer') {
            status = 1;
        } else if (toStatus === 'confirmer') {
            status = 2;
        }
        $.post(admin_url + 'bon_livraison/change_status/' + status, data).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-delivery-notes').DataTable().ajax.reload();
            }
        });
    }

    return false;
}
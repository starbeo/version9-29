$(document).ready(function () {
    // On click bouton import submit
    $('.btn-import-submit').on('click', function () {
        if ($('select[name="clientid"]').selectpicker('val') === '') {
            alert_float('warning', 'Choisissez un client !!');
        } else {
            if ($(this).hasClass('import')) {
                $('form').append(hidden_input('import', true));
            }
            $('form').submit();
        }
    });
    // Validate form
    _validate_form($('form'), {
        clientid: 'required',
        file_xls: {
            required: true,
            extension: "xlsx"
        }
    });
});
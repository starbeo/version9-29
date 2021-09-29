$(document).ready(function () {
    // Validate form import colis en attente
    _validate_form($('#form-import-colis-en-attente'), {
        file_xls: {
            required: true,
            extension: "xlsx"
        }
    });
    // On submit form import colis en attente
    $('.btn-import-submit').on('click', function () {
        if ($(this).hasClass('import')) {
            $('form').append(hidden_input('import', true));
        }
        $('form').submit();
    });
});
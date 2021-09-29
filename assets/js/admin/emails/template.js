$(document).ready(function() {
    var merge_fields_col = $('.merge_fields_col');
    // If not fields available
    $.each(merge_fields_col, function () {
        var total_available_fields = $(this).find('p');
        if (total_available_fields.length === 0) {
            $(this).remove();
        }
    });
    // Submit Form Email Template
    $('#email-template-form').submit(function() {
        //Annulation de la désactivation des champs
        $('#email-template-form input[name="fromname"]').prop('disabled', false);
        $('#email-template-form input[name="fromemail"]').prop('disabled', false);
        
        return true;
    });
    //Validation Form Email Template
    _validate_form($('#email-template-form'), {
        subject: 'required'
    });
});
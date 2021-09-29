$(document).ready(function () {
    var temp_location = window.location.href.replace('#', '');
    $('form').attr('action', temp_location);
    //On clic Tab
    $('body').on('click', 'a[data-toggle="tab"]', function () {
        var location = window.location.href.split("?")[0];
        var hash = this.hash;
        hash = hash.replace('#', '');
        $('form').attr('action', location + '?tab_hash=' + hash);
    });
    //Show Tab
    $('body').find('.nav-tabs a[href="#' + $('input[name="tab_hash"]').val() + '"]').tab('show');
    // Init textarea message migration with ckeditor
    ckeditor_start_ckfinder('coming_soon_message', 300);
    // Init textarea signature email with ckeditor
    ckeditor_start_ckfinder('email_signature', 300);
    // Init textarea template contract with ckeditor
    ckeditor_start_ckfinder('contrat_template', 300);
    // On change select background authentication admin
    $('body').on('change', 'select[name="settings[background_authentication_admin]"]', function () {
        var background = $('select[name="settings[background_authentication_admin]"]').selectpicker('val');
        var img = $('#img-background-authentication-admin').attr('src');
        var name = $('#img-background-authentication-admin').attr('data-name');
        img = img.replace(name, background);
        $('#img-background-authentication-admin').attr('src', img);
        $('#img-background-authentication-admin').attr('data-name', background);
    });
    // On change select background authentication client
    $('body').on('change', 'select[name="settings[background_authentication_client]"]', function () {
        var background = $('select[name="settings[background_authentication_client]"]').selectpicker('val');
        var img = $('#img-background-authentication-client').attr('src');
        var name = $('#img-background-authentication-client').attr('data-name');
        img = img.replace(name, background);
        $('#img-background-authentication-client').attr('src', img);
        $('#img-background-authentication-client').attr('data-name', background);
    });
    // Send SMTP test email
    $('.test_email').on('click', function () {
        var email = $('input[name="test_email"]').val();
        if (email !== '') {
            $(this).attr('disabled', true);
            $.post(admin_url + 'emails/sent_smtp_test_email', {test_email: email}).success(function () {
                var current_url = $('form').attr('action');
                window.location.href = current_url;
            });
        }
    });
    // Send test sms
    $('.test_sms').on('click', function () {
        var phoneNumber = $('input[name="phone_number_test"]').val();
        var message = $('textarea[name="message_test"]').val();
        if (phoneNumber !== '' && message !== '') {
            $(this).attr('disabled', true);
            $.post(admin_url + 'sms/test', {phone_number_test: phoneNumber, message_test: message}).success(function (response) {
                response = $.parseJSON(response);
                if (response.success === true) {
                    alert_float('success', response.message);
                    $('input[name="phone_number_test"]').val('');
                    $('textarea[name="message_test"]').val('');
                } else {
                    alert_float('warning', response.message);
                }
            });
        } else {
            alert_float('warning', 'Remplissez le téléphone et le message pour tester');
        }
    });
    // On change select theme pdf facture
    $('body').on('change', 'select[name="settings[theme_pdf_facture]"]', function () {
        var theme = $('select[name="settings[theme_pdf_facture]"]').selectpicker('val');
        var img = $('#img-theme-pdf-facture').attr('src');
        var name = $('#img-theme-pdf-facture').attr('data-name');
        img = img.replace(name, theme);
        $('#img-theme-pdf-facture').attr('src', img);
        $('#img-theme-pdf-facture').attr('data-name', theme);
    });
    // On change select theme pdf bon livraison
    $('body').on('change', 'select[name="settings[theme_pdf_bon_livraison]"]', function () {
        var theme = $('select[name="settings[theme_pdf_bon_livraison]"]').selectpicker('val');
        var img = $('#img-theme-pdf-bon-livraison').attr('src');
        var name = $('#img-theme-pdf-bon-livraison').attr('data-name');
        img = img.replace(name, theme);
        $('#img-theme-pdf-bon-livraison').attr('src', img);
        $('#img-theme-pdf-bon-livraison').attr('data-name', theme);
    });
    // On change select theme pdf etiquette bon livraison
    $('body').on('change', 'select[name="settings[theme_pdf_etiquette_bon_livraison]"]', function () {
        var theme = $('select[name="settings[theme_pdf_etiquette_bon_livraison]"]').selectpicker('val');
        var img = $('#img-theme-pdf-etiquette-bon-livraison').attr('src');
        var name = $('#img-theme-pdf-etiquette-bon-livraison').attr('data-name');
        img = img.replace(name, theme);
        $('#img-theme-pdf-etiquette-bon-livraison').attr('src', img);
        $('#img-theme-pdf-etiquette-bon-livraison').attr('data-name', theme);
    });
});
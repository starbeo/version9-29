$(document).ready(function () {
    _validate_form(
        $('#form-client-profile'), {
            logo: 'required'
        }
    );
    _validate_form(
        $('#form-change-password'),{
            oldpassword:'required',
            newpassword:'required',
            newpasswordr: { 
                equalTo: "#newpassword"
            }
        }
    );
});
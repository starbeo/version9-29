$(document).ready(function () {
    _validate_form($('#staff_profile_table'), {
        firstname: 'required',
        lastname: 'required',
        phonenumber: 'required'
    });
    _validate_form($('#staff_password_change_form'), {
        oldpassword: 'required',
        newpassword: 'required',
        newpasswordr: {
            equalTo: "#newpassword"
        }
    });
});
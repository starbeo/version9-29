$(document).ready(function () {
    $('body').on('change', '#show_password', function () {
        var checked = $(this).prop('checked');
        var element = document.getElementById("password");
        if (checked === true) {
            if (element.type === "password") {
                element.type = "text";
            }
        } else {
            if (element.type === "text") {
                element.type = "password";
            }
        }
    });
});
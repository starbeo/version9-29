$(document).ready(function () {
    // Init data table apis
    initDataTable('.table-apis', window.location.href, 'APIs');
    // Init data table access
    var access = $('#access').val();
    if (typeof (access) !== 'undefined') {
        initDataTable('.table-access-apis', client_url + 'apis/access', 'Accès APIs');
    }
    // On click button pack
    $('body').on('click', '.btn-pack', function () {
        if ($('#bloc-pack').hasClass('display-none')) {
            $('#bloc-pack').removeClass('display-none');
        } else {
            $('#bloc-pack').addClass('display-none');
        }
    });
    // On click button pack
    $('body').on('click', '#btn-request-access', function () {
        var pack = $(this).attr('data-pack');
        window.location.href = client_url + 'apis/add_access/' + pack;
    });
});
$(document).ready(function () {
    // Init data table demandes
    var headers_demandes = $('.table-demandes').find('th');
    var not_sortable_demandes = (headers_demandes.length - 1);
    var DemandesServerParams = {
        "f-priority": "[name='f-priority']",
        "f-status": "[name='f-status']",
        "f-date-created-start": "[name='f-date-created-start']",
        "f-date-created-end": "[name='f-date-created-end']"
    };
    initDataTable('.table-demandes', window.location.href, 'Demandes', [not_sortable_demandes], [not_sortable_demandes], DemandesServerParams);
    // Init demande
    init_demande();
    // On submit message discussion
    $('body').on('click', '#submit-message-discussion', function () {
        var content = $('textarea[name="message_discussion"]').val();
        var demandeId = $('input[name="demande_id"]').val();
        if ($.isNumeric(demandeId) && content !== '') {
            $.post(client_url + 'demandes/add_discussion', {demande_id: demandeId, content: content}).success(function (response) {
                response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    init_discussion(demandeId);
                    $('textarea[name="message_discussion"]').val('');
                }
            });
        }
    });
});

// Init demande
function init_demande(id) {
    var _demandeid = $('body').find('input[name="demandeid"]').val();
    // Check if demande id passed from url
    if (_demandeid !== '') {
        id = _demandeid;
    } else {
        if (typeof (id) === 'undefined' || id === '') {
            return;
        }
    }

    $('body').find('input[name="demandeid"]').val('');
    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-demandes', '#demande');
    }
    $('#demande').load(client_url + 'demandes/get_demande_data_ajax/' + id);

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('html, body').animate({
            scrollTop: $('#demande').offset().top + 150
        }, 600);
    }
}

// Init discussions
function init_discussion(demandeId) {
    if (typeof (demandeId) === 'undefined') {
        demandeId = $('input[name="demande_id"]').val();
    }

    if ($.isNumeric(demandeId)) {
        $('#bloc-discussions-demande').html('');
        $.post(client_url + 'demandes/discussions', {demande_id: demandeId}).success(function (response) {
            response = $.parseJSON(response);
            var discussions = '';
            $.each(response, function (i, obj) {
                discussions += '<li class="feed-item">';
                discussions += '<div class="date text-info"><i class="fa fa-clock-o mright5"></i>' + obj.date + '</div>';
                discussions += '<div class="text">' + obj.profile_image + '<b>' + obj.name + '</b> : ' + obj.content + '</div>';
                discussions += '</li>';
            });
            $('#bloc-discussions-demande').append(discussions);
            $('.table-demandes').DataTable().ajax.reload();
        });
    }
}
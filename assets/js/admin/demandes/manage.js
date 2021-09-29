$(document).ready(function () {
    // Init data table demandes
    var headers_demandes = $('.table-demandes').find('th');
    var not_sortable_demandes = (headers_demandes.length - 1);
    var DemandesServerParams = {
        "f-type": "[name='f-type']",
        "f-objet": "[name='f-objet']",
        "f-departement": "[name='f-departement']",
        "f-client": "[name='f-client']",
        "f-priority": "[name='f-priority']",
        "f-status": "[name='f-status']",
        "f-date-created": "[name='f-date-created']",
        "f-date-end": "[name='f-date-end']"
    };
    initDataTable('.table-demandes', window.location.href, 'Demandes', [0, not_sortable_demandes], [0, not_sortable_demandes], DemandesServerParams);
    // Init demande
    init_demande();
    // On click button add note form
    $('body').on('click', '#add-note-form', function () {
        var demandeId = $('input[name="demande_id"]').val();
        var note = $('textarea[name="note"]').val();
        if ($.isNumeric(demandeId) && note !== '') {
            $.post(admin_url + "demandes/add_note", {demande_id: demandeId, note: note}, function (response) {
                var response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    $('.table-demandes').DataTable().ajax.reload();
                    init_demande(demandeId);
                }
            });
        }
    });
    // On click checkbox all demandes
    $('body').on('change', '#checkbox-all-demandes', function () {
        if ($('#checkbox-all-demandes').is(':checked') === true) {
            $('.checkbox-demande').prop('checked', true);
        } else {
            $('.checkbox-demande').prop('checked', false);
        }
    });



    // On click
    $('body').on('click', '.paginate_button a', function () {
        $('#checkbox-all-demandes').prop('checked', false);
    });
    // On click button filter
    $('body').on('click', '.btn-filtre', function () {
        if ($('#filtre-table').hasClass('display-none')) {
            $('#filtre-table').removeClass('display-none');
            $('#statistique-requests').addClass('display-none');
        } else {
            $('#filtre-table').addClass('display-none');
        }
    });
    // On submit filter
    $('body').on('click', '#filtre-submit', function () {
        $('.table-demandes').DataTable().ajax.reload();
        $('#filtre-table').addClass('display-none');
    });
    // Reset filter
    $('body').on('click', '#filtre-reset', function () {
        $('#filtre-table select').selectpicker('val', '');
        $('#filtre-table input').val('');
        $('.table-demandes').DataTable().ajax.reload();
    });
    // On click button statistique requests
    $('body').on('click', '.btn-statistique-requests', function () {
        if ($('#statistique-requests').hasClass('display-none')) {
            $('#statistique-requests').removeClass('display-none');
            $('#filtre-table').addClass('display-none');
        } else {
            $('#statistique-requests').addClass('display-none');
        }
    });
    // On submit message discussion
    $('body').on('click', '#submit-message-discussion', function () {
        $('#submit-message-discussion').attr('disabled', true);
        var content = $('textarea[name="message_discussion"]').val();
        var demandeId = $('input[name="demande_id"]').val();
        if ($.isNumeric(demandeId) && content !== '') {
            $.post(admin_url + 'demandes/add_discussion', {demande_id: demandeId, content: content}).success(function (response) {
                response = $.parseJSON(response);
                alert_float(response.type, response.message);
                if (response.success === true) {
                    init_discussion(demandeId);
                    $('textarea[name="message_discussion"]').val('');
                }
                $('#submit-message-discussion').attr('disabled', false);
            });
        } else {
            alert_float('warning', 'Le message est obligatoire !!');
            $('#submit-message-discussion').attr('disabled', false);
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
    $('#demande').load(admin_url + 'demandes/get_demande_data_ajax/' + id);

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('html, body').animate({
            scrollTop: $('#demande').offset().top + 150
        }, 600);
    }
}

// Init discussion
function init_discussion(demandeId) {
    if (typeof (demandeId) === 'undefined') {
        demandeId = $('input[name="demande_id"]').val();
    }

    if ($.isNumeric(demandeId)) {
        $('#bloc-discussions-demande').html('');
        $.post(admin_url + 'demandes/discussions', {demande_id: demandeId}).success(function (response) {
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

// change status demande
function change_status_demande(toType) {
    if (toType === 'en_cours' ) {
        var data = $('#change-status-demande-form input[name="ids[]"]').serialize();
        var status = 0;
        if (toType === 'en_cours') {
            status = 1;
        }
        $.post(admin_url + 'demandes/change_status/' + status, data).success(function (response) {
            response = $.parseJSON(response);
            console.log(response)

            alert_float(response.type, response.message);
            if (response.success === true) {
                $('.table-demandes').DataTable().ajax.reload();
            }
        });
    }

    if ( toType === 'cloturer') {
        var data = $('#change-status-demande-form input[name="ids[]"]').serialize();
        var status = 0;
        if (toType === 'cloturer') {
            status =4;
        }
        $.post(admin_url + 'demandes/change_status/' + status, data).success(function (response) {
            response = $.parseJSON(response);
            alert_float(response.type, response.message);
            console.log(response)
            if (response.success === true) {
                $('.table-demandes').DataTable().ajax.reload();
            }
        });
    }




    return false;
}


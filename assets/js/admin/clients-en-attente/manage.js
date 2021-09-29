$(document).ready(function () {
    // Init data table for client en attente
    var headers_clients_en_attente = $('.table-clients-en-attente').find('th');
    var not_sortable_clients_en_attente = (headers_clients_en_attente.length - 1);
    initDataTable('.table-clients-en-attente', window.location.href, 'Clients en attente', [not_sortable_clients_en_attente], [not_sortable_clients_en_attente]);
    // Show modal client en attente
    $('#client-en-attente').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#client-en-attente span[id="societe"]').html('');
        $('#client-en-attente span[id="contact"]').html('');
        $('#client-en-attente span[id="email"]').html('');
        $('#client-en-attente span[id="telephone"]').html('');
        $('#client-en-attente span[id="adresse"]').html('');
        $('#client-en-attente span[id="ville"]').html('');
        $('#client-en-attente span[id="affiliation_code"]').html('');
        $('#convert-client-en-attente-to-client').attr('data-id', '');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(admin_url + "clients_en_attente/get/" + id, function (response) {
                var data = $.parseJSON(response);
                $('#client-en-attente span[id="societe"]').html(data.societe);
                $('#client-en-attente span[id="contact"]').html(data.personne_a_contacte);
                $('#client-en-attente span[id="email"]').html(data.email);
                $('#client-en-attente span[id="telephone"]').html(data.telephone);
                $('#client-en-attente span[id="adresse"]').html(data.adresse);
                if (data.complement_adresse !== '') {
                    $('#client-en-attente h5[name="adresse"]').append(' ');
                    $('#client-en-attente span[id="adresse"]').append(data.complement_adresse);
                }
                $('#client-en-attente span[id="ville"]').html(data.ville_name);
                if (data.pays !== '') {
                    $('#client-en-attente span[id="code_postale"]').append(', ');
                    $('#client-en-attente span[id="code_postale"]').append(data.pays);
                }
                $('#client-en-attente span[id="affiliation_code"]').html(data.affiliation_code);
            });
            $('#convert-client-en-attente-to-client').attr('data-id', id);
        }
    });

    $('#convert-client-en-attente-to-client').on('click', function () {
        var id = $('#convert-client-en-attente-to-client').attr('data-id');
        window.location.href = admin_url + 'clients_en_attente/convert_to_client/' + id;
    });

});

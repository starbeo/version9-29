$(document).ready(function () {
    // Default statistique
    statistiqueDashbord('all');
    // Redirection statistique selon la période choisi, période par défaut 'Day'
    $('.redirect-statistique').on('click', function (e) {
        e.preventDefault();
        var periode = $('#filter-statisique select[name="periode"]').selectpicker('val');
        if (periode === '') {
            periode = 'all';
        }
        var href = $(this).attr('href');
        if (href.indexOf("?") === -1) {
            href += '?periode=' + periode;
        } else {
            href += '&periode=' + periode;
        }
        window.open(href, '_blank');
    });
    // Filter Statistique Tableau de bord
    $('#filter-statisique select[name="periode"]').on('change', function () {
        var periode = $(this).selectpicker('val');
        statistiqueDashbord(periode);
    });
});

function statistiqueDashbord(periode) {
    if (periode !== '') {
        if ($('#wait-filtre-periode-statisique').hasClass('display-none')) {
            $('#wait-filtre-periode-statisique').removeClass('display-none');
        }
        $.post(client_url + 'home/statistique_by_periode', {periode: periode}).success(function (response) {
            response = $.parseJSON(response);
            $('#total-colis-en-attente').html(response.totalRowsColisEnAttente);
            $('#total-colis').html(response.totalRowsColis);
            $('#total-colis-livrer').html(response.totalRowsColisLivre);
            $('#total-colis-retourner').html(response.totalRowsColisRetourner);
            $('#total-bons-livraison').html(response.totalRowsBonsLivraison);
            $('#total-factures').html(response.totalRowsFactures);
            $('#total-demandes').html(response.totalRowsDemandes);
            $('#total-reclamations').html(response.totalRowsReclamations);
            //Cacher la boite de dialogue du filtre
            $('#filter-statisique').modal('hide');
        });
    }
}

function showModalFiltreStatistiqueDashbord() {
    $('#filter-statisique').modal({
        backdrop: 'static',
        keyboard: false
    });
    if (!$('#wait-filtre-periode-statisique').hasClass('display-none')) {
        $('#wait-filtre-periode-statisique').addClass('display-none');
    }
}
$(document).ready(function () {
    // Default statistique
    statistiqueDashbord('day');
    // Redirection statistique selon la période choisi, période par défaut 'Day'
    $('.redirect-statistique').on('click', function(e) {
        e.preventDefault();
        var periode = $('#filter-statisique-dashbord select[name="periode"]').selectpicker('val');
        if(periode === '') {
            periode = 'day';
        }
        var href = $(this).attr('href');
        if(href.indexOf("?") === -1) {
            href += '?periode=' + periode;
        } else {
            href += '&periode=' + periode;
        }
        window.open(href, '_blank');
    });
    // Filter Statistique Tableau de bord
    $('#filter-statisique-dashbord select[name="periode"]').on('change', function() {
        var periode = $(this).selectpicker('val');
        statistiqueDashbord(periode);
    });
});

function statistiqueDashbord(periode) {
    if(periode !== '') {
        if($('#wait-filtre-periode-statisique').hasClass('display-none')) {
            $('#wait-filtre-periode-statisique').removeClass('display-none');
        }
        $.post(admin_url + 'home/statistique_by_periode', {periode : periode}).success(function(response) {
            response = $.parseJSON(response);
            $('#total-colis-en-attente').html(response.totalRowsColisEnAttente);
            $('#total-colis-en-cours').html(response.totalRowsColisEnCours);
            $('#total-colis-livre').html(response.totalRowsColisLivre);
            $('#total-colis-retourner').html(response.totalRowsColisRetourner);
            $('#total-clients').html(response.totalRowsClients);
            $('#total-bons-livraison').html(response.totalRowsBonsLivraison);
            $('#total-bons-livraison-sortie').html(response.totalRowsBonsLivraisonSortie);
            $('#total-bons-livraison-retourner').html(response.totalRowsBonsLivraisonRetourner);
            $('#total-utilisateurs').html(response.totalRowsUtilisateurs);
            $('#total-etat-colis-livre').html(response.totalRowsEtatColisLivre);
            $('#total-etat-colis-livre-non-regle').html(response.totalRowsEtatColisLivreNonRegle);
            $('#total-etat-colis-livre-regle').html(response.totalRowsEtatColisLivreRegle);
            $('#total-factures').html(response.totalRowsFactures);
            $('#total-factures-paye').html(response.totalRowsFacturesPaye);
            $('#total-factures-impaye').html(response.totalRowsFacturesImpaye);
            $('#total-factures-internes').html(response.totalRowsFacturesInternes);
            $('#total-nombre-paiements').html(response.totalRowsNombrePaiements);
            $('#total-paiements').html(response.totalRowsPaiements);
            $('#total-nombre-depenses').html(response.totalRowsNombreDepenses);
            $('#total-depenses').html(response.totalRowsDepenses);
            $('#total-supports').html(response.totalRowsSupports);
            $('#total-demandes').html(response.totalRowsDemandes);
            $('#total-demandes-en-cours').html(response.totalRowsDemandesEnCours);
            $('#total-demandes-cloturer').html(response.totalRowsDemandesCloturer);
            //Cacher la boite de dialogue du filtre
            $('#filter-statisique-dashbord').modal('hide');
        });  
    }
}

function showModalFiltreStatistiqueDashbord() {
    $('#filter-statisique-dashbord').modal({
        backdrop: 'static',
        keyboard: false
    });
    if(! $('#wait-filtre-periode-statisique').hasClass('display-none')) {
        $('#wait-filtre-periode-statisique').addClass('display-none');
    }
}
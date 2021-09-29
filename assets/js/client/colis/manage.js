$(document).ready(function () {
    // Get paramétres periode
    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var periode = urlParams.get('periode');
    // Init data table colis
    var headers_colis = $('.table-colis').find('th');
    var not_sortable_colis = (headers_colis.length - 1);
    var ColisServerParams = {
        "f-type-livraison": "[name='f-type-livraison']",
        "f-point-relai": "[name='f-point-relai']",
        "f-statut": "[name='f-statut']",
        "f-etat": "[name='f-etat']",
        "f-ville": "[name='f-ville']",
        "f-date-ramassage-start": "[name='f-date-ramassage-start']",
        "f-date-ramassage-end": "[name='f-date-ramassage-end']",
        "f-date-livraison-start": "[name='f-date-livraison-start']",
        "f-date-livraison-end": "[name='f-date-livraison-end']"
    };
    initDataTable('.table-colis', window.location.href, 'Colis', [not_sortable_colis], [not_sortable_colis], ColisServerParams);
    //Validate form export colis
    $('body').on('click', '#export-colis', function (e) {
        e.preventDefault();
        var dateRamassageStart = $('input[name="f-date-ramassage-start"]').val();
        var dateRamassageEnd = $('input[name="f-date-ramassage-end"]').val();
        var dateLivraisonStart = $('input[name="f-date-livraison-start"]').val();
        var dateLivraisonEnd = $('input[name="f-date-livraison-end"]').val();
        var check = false;
        if(dateRamassageStart === '' && dateLivraisonStart === '') {
            alert_float('warning', 'Date début de ramassage ou bien Date début de livraison est obligatoire !!');
        } else if(dateRamassageStart !== '' && dateLivraisonStart === '') {
            if(dateRamassageEnd !== '') {
                var diffDateRamassage = difference_entre_date(dateRamassageStart, dateRamassageEnd);
                if(diffDateRamassage.day <= 30) {
                    check = true;
                }
            } else {
                alert_float('warning', 'Date fin de ramassage est obligatoire !!');
            }
        } else if(dateLivraisonStart !== '' && dateRamassageStart === '') {
            if(dateLivraisonEnd !== '') {
                var diffDateLivraison = difference_entre_date(dateLivraisonStart, dateLivraisonEnd);
                if(diffDateLivraison.day <= 30) {
                    check = true;
                }
            } else {
                alert_float('warning', 'Date fin de livraison est obligatoire !!');
            }
        } else {
            if(dateRamassageEnd === '') {
                alert_float('warning', 'Date fin de ramassage est obligatoire !!');
            }
            if(dateLivraisonEnd === '') {
                alert_float('warning', 'Date fin de livraison est obligatoire !!');
            }
            if(dateRamassageEnd !== '' && dateLivraisonEnd !== '') {
                var diffDateRamassage = difference_entre_date(dateRamassageStart, dateRamassageEnd);
                var diffDateLivraison = difference_entre_date(dateLivraisonStart, dateLivraisonEnd);
                if(diffDateRamassage.day <= 30 && diffDateLivraison.day <= 30) {
                    check = true;
                }
            }
        }
        
        if(check === true) {
            $('#form-export-colis').submit();
        } else {
            alert_float('warning', 'L\'intervale entre la date de début et de fin est d\'un mois');
        }
    });
    // Init url
    window.history.pushState({}, document.title, client_url + "colis");
    // Historiques colis
    $('#historiques').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var coli_id = $(invoker).data('coli-id');
        $('#historiques input[name="f-coli-id"]').val(coli_id);
        var barcode = $(invoker).data('barcode');
        $('#historiques input[name="f-code-barre"]').val(barcode);

        var count = $('#historiques input[name="historique-count"]').val();
        if (count === '0') {
            $('#historiques input[name="historique-count"]').val(1);
            //Init Data Table Historiques Status Coli
            var HistoriquesStatusServerParams = {
                "f-code-barre": "[name='f-code-barre']"
            };
            initDataTable('.table-historiques-status', client_url + 'colis/status', 'Status', 'undefined', 'undefined', HistoriquesStatusServerParams, [3, 'DESC']);
            //Init Data Table Historiques Appels Livreur Coli
            var HistoriquesAppelsLivreurServerParams = {
                "f-coli-id": "[name='f-coli-id']"
            };
            initDataTable('.table-historiques-appels-livreur', client_url + 'appels/livreurs', 'Appels livreur', 'undefined', 'undefined', HistoriquesAppelsLivreurServerParams, [2, 'DESC']);
        } else {
            $('.table-historiques-status').DataTable().ajax.reload();
            $('.table-historiques-appels-livreur').DataTable().ajax.reload();
        }
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
        window.location.href = client_url + 'colis/index?periode=' + periode;
        //Cacher la boite de dialogue du filtre
        $('#filter-statisique').modal('hide');
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

// Datatables colis by statuses
function dt_colis_by_statuses(statusId) {
    $('select[name="f-statut"]').selectpicker('val', statusId);
    $('.table-colis').DataTable().ajax.reload();
    $('.btn-statistique').click();
}
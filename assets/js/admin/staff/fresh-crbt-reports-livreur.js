var FreshCrbtChart;
var report_from_1 = $('input[id="report-from-livreur"]');
var report_to_1 = $('input[id="report-to-livreur"]');
var date_range_1 = $('#date-range-livreur');

$(document).ready(function () {

    gen_reports_1();

    report_from_1.on('change', function () {
        var val = $(this).val();
        var report_to_val = report_to_1.val();
        if (val !== '') {
            report_to_1.attr('disabled', false);
            if (report_to_val !== '') {
                gen_reports_1();
            }
        } else {
            report_to_1.attr('disabled', true);
        }
    });

    report_to_1.on('change', function () {
        var val = $(this).val();
        if (val !== '') {
            gen_reports_1();
        }
    });

    $('select[id="months-report-livreur"]').on('change', function () {
        var val = $(this).val();

        report_to_1.attr('disabled', true);
        report_to_1.val('');
        report_from_1.val('');
        if (val === 'custom') {
            date_range_1.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range_1.hasClass('hide')) {
                date_range_1.removeClass('fadeIn').addClass('hide');
            }
        }
        gen_reports_1();
    });
});

// Generate total income bar
function default_fresh_crbt_colis_livreur() {
    if (typeof (FreshCrbtChart) !== 'undefined') {
        FreshCrbtChart.destroy();
    }
    var data = {};
    data.livreur = $('input[id="livreurid"]').val();
    data.months_report = $('select[id="months-report-livreur"]').val();
    data.report_from_1 = report_from_1.val();
    data.report_to_1 = report_to_1.val();
    $.post(admin_url + 'staff/default_fresh_crbt_colis_livreur', data, function (response) {
        FreshCrbtChart = new Chart($('#chart-livreur'), {
            type: 'bar',
            data: response,
            options: {
                responsive: true,
                scales: {
                    yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                }
            }
        });
    }, 'json');
}

// Main generate report function
function gen_reports_1() {
    if (!$('#chart-livreur').hasClass('hide')) {
        default_fresh_crbt_colis_livreur();
    }
}

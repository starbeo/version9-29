var FreshCrbtChart;
var report_from_1 = $('input[id="report-from-expediteur"]');
var report_to_1   = $('input[id="report-to-expediteur"]');
var date_range_1  = $('#date-range-expediteur');

$(document).ready(function() {

	gen_reports_1();

    report_from_1.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to_1.val();
        if (val != '') {
            report_to_1.attr('disabled', false);
            if (report_to_val != '') {
                gen_reports_1();
            }
        } else {
            report_to_1.attr('disabled', true);
        }
    });

    report_to_1.on('change', function() {
        var val = $(this).val();
        if (val != '') {
            gen_reports_1();
        }
    });

    $('select[id="months-report-expediteur"]').on('change', function() {
        var val = $(this).val();

        report_to_1.attr('disabled', true);
        report_to_1.val('');
        report_from_1.val('');
        if (val == 'custom') {
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
function default_fresh_crbt_colis_expediteur() {
    if (typeof(FreshCrbtChart) !== 'undefined') {
        FreshCrbtChart.destroy();
    }
    var data = {};
    data.client       = $('input[id="clientid"]').val();
    data.months_report = $('select[id="months-report-expediteur"]').val();
    data.report_from_1 = report_from_1.val();
    data.report_to_1   = report_to_1.val();
    $.post(admin_url + 'expediteurs/default_fresh_crbt_colis_expediteur', data, function(response) {
        FreshCrbtChart = new Chart($('#chart-expediteur'), {
            type: 'bar',
            data: response,
            options: {
                responsive:true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                },
            },
    	});
    }, 'json');
}

// Main generate report function
function gen_reports_1() {
    if (!$('#chart-expediteur').hasClass('hide')) {
        default_fresh_crbt_colis_expediteur();
    }
}

var FreshCrbtChart;
var report_from_1 = $('input[name="report-from-1"]');
var report_to_1   = $('input[name="report-to-1"]');
var date_range_1  = $('#date-range-1');

$(document).ready(function() {

    gen_reports_1();

    report_from_1.on('change', function() {
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

    report_to_1.on('change', function() {
        var val = $(this).val();
        if (val !== '') {
            gen_reports_1();
        }
    });

    $('select[name="livreur_id"]').on('change', function() {
        var val = $(this).val();
        $('select[name="months-report-1"]').val('');
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

    $('select[name="months-report-1"]').on('change', function() {
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
function default_fresh_crbt_colis() {
    if (typeof(FreshCrbtChart) !== 'undefined') {
        FreshCrbtChart.destroy();
    }
    var data = {};
    data.livreur       = $('select[name="livreur_id"]').val();
    data.months_report = $('select[name="months-report-1"]').val();
    data.report_from_1 = report_from_1.val();
    data.report_to_1   = report_to_1.val();
    $.post(admin_url + 'colis/default_fresh_crbt_colis', data, function(response) {
        if(! $('#wait-chart-1').hasClass('display-none')) {
            $('#wait-chart-1').addClass('display-none');
        }
        FreshCrbtChart = new Chart($('#chart-1'), {
            type: 'bar',
            data: response,
            options: {
                responsive:true,
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
    if (!$('#chart-1').hasClass('hide')) {
        if($('#wait-chart-1').hasClass('display-none')) {
            $('#wait-chart-1').removeClass('display-none');
        }
        
        default_fresh_crbt_colis();
    }
}

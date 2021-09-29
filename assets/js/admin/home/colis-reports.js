var ColisChart;
var report_from = $('input[name="report-from"]');
var report_to   = $('input[name="report-to"]');
var date_range  = $('#date-range');

$(document).ready(function() {

    gen_reports();

    report_from.on('change', function() {
        var val = $(this).val();
        var report_to_val = report_to.val();
        if (val !== '') {
            report_to.attr('disabled', false);
            if (report_to_val !== '') {
                gen_reports();
            }
        } else {
            report_to.attr('disabled', true);
        }
    });

    report_to.on('change', function() {
        var val = $(this).val();
        if (val !== '') {
            gen_reports();
        }
    });

    $('select[name="client_id"]').on('change', function() {
        var val = $(this).val();
        $('select[name="months-report"]').val('');
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val === 'custom') {
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }
        gen_reports();
    });

    $('select[name="months-report"]').on('change', function() {
        var val = $(this).val();
        report_to.attr('disabled', true);
        report_to.val('');
        report_from.val('');
        if (val === 'custom') {
            date_range.addClass('fadeIn').removeClass('hide');
            return;
        } else {
            if (!date_range.hasClass('hide')) {
                date_range.removeClass('fadeIn').addClass('hide');
            }
        }
        gen_reports();
    });
});

// Generate total income bar
function default_total_colis() {
    if (typeof(ColisChart) !== 'undefined') {
        ColisChart.destroy();
    }
    var data = {};
    data.client        = $('select[name="client_id"]').val();
    data.months_report = $('select[name="months-report"]').val();
    data.report_from   = report_from.val();
    data.report_to     = report_to.val();
    $.post(admin_url + 'colis/default_total_colis', data, function(response) {
        if(! $('#wait-chart').hasClass('display-none')) {
            $('#wait-chart').addClass('display-none');
        }
        ColisChart = new Chart($('#chart'), {
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
function gen_reports() {
    if (!$('#chart').hasClass('hide')) {
        if($('#wait-chart').hasClass('display-none')) {
            $('#wait-chart').removeClass('display-none');
        }
        
        default_total_colis();
    }
}

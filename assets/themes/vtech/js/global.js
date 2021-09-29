$(document).ready(function(){
	// Set timeout to remove php alerts added from flashdata
    setTimeout(function() {
        $('#alerts').slideUp();
    }, 3500);
    
	init_progress_bars();
	init_datepicker();
	$('.article_useful_buttons button').on('click',function(e){
		e.preventDefault();
		var data = {};
		data.answer = $(this).data('answer');
		data.articleid = $('input[name="articleid"]').val();
		$.post(window.location.href,data).success(function(response){
			response = $.parseJSON(response);
			if(response.success == true){
				$(this).focusout();
			}
			$('.answer_response').html(response.message);
		});
	});

    // Set notifications to read when notifictions dropdown is opened
    $('.notifications-icon').on('click', function() {
        $.post(site_url + 'expediteurs/set_notifications_client_read').success(function(response) {
            response = $.parseJSON(response);
            if (response.success == true) {
                $(".icon-notifications").addClass('hide');
                setTimeout(function() {
                    $('.notification-box.unread').removeClass('unread', 'slow');
                }, 1000);
            }
        })
    });
});
function init_progress_bars() {
	setTimeout(function() {
		$('.progress .progress-bar').each(function() {
			var bar = $(this);
			var perc = bar.attr("data-percent");
			var current_perc = 0;
			var progress = setInterval(function() {
				if (current_perc >= perc) {
					clearInterval(progress);
				} else {
					current_perc += 1;
					bar.css('width', (current_perc) + '%');
				}
				bar.text((current_perc) + '%');
			}, 10);
		});

	}, 300);
}
function init_datepicker() {
	$('.datepicker').datepicker({
		autoclose: true,
		format: date_format
	});
	$('.calendar-icon').on('click', function() {
		$(this).parents('.date').find('.datepicker').datepicker('show');
	});
}
// Datatables sprintf language help function
if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) {
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}
// Init bootstrap select picker
function init_selectpicker() {
    $('.selectpicker').selectpicker({
        showSubtext: true
    });
}
// Generate float alert
function alert_float(type, message) {
    $.notify({
        message: message,

    }, {
        type: type,
        animate: {
            enter: 'animated bounceInRight',
            exit: 'animated bounceOutRight'
        },
    });
}

$(document).ready(function () {
    // Show notifications
    var notifications = $('#notifications');
    if (notifications.length > 0) {
        var page = 0;
        var total_pages = $('input[name="total_pages"]').val();
        $('.loader').on('click', function (e) {
            e.preventDefault();
            if (page <= total_pages) {
                $.post(client_url + 'profile/notifications', {page: page}).success(function (response) {
                    response = $.parseJSON(response);
                    var notifications = '';
                    $.each(response, function (i, obj) {
                        notifications += '<div class="notification-box-all">';
                        var link_notification = '';
                        var link_class_indicator = '';
                        if (obj.link) {
                            link_notification = ' href="' + obj.link + '" target="_blank"';
                            link_class_indicator = ' notification_link';
                        }
                        notifications += '<a class="media-body' + link_class_indicator + '"' + link_notification + '>';
                        notifications += '<div class="description" style="color: #000;"><i class="fa fa-bell"></i> ' + obj.description + '</div>';
                        notifications += '<small class="text-muted text-right">' + obj.date + '</small>';
                        notifications += '</a>';
                        notifications += '</div>';
                    });
                    $('#notifications').append(notifications);
                    page++;
                });

                if (page >= total_pages - 1)
                {
                    $(".loader").addClass("disabled");
                }
            }
        });

        $('.loader').click();
    }
});
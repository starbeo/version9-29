// JS File used in events
$(document).ready(function () {
    var settings = {
        lang: 'fr',
        customButtons: {},
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,viewFullCalendar'
        },
        loading: function (isLoading) {
            if (!isLoading) {// isLoading gives boolean value
                $('.dt-loader').addClass('display-none');
            } else {
                $('.dt-loader').removeClass('display-none');
            }
        },
        eventColor: '#28B8DA',
        editable: true,
        eventLimit: 3,
        eventSources: [
            {
                url: client_url + 'home/get_calendar_data',
                type: 'GET',
                error: function () {
                    alert("Une erreur s'est produite lors de la récupération des données du calendrier!");
                }
            }
        ],
        eventRender: function (event, element) {
            element.attr('title', event._tooltip);
            element.attr('data-toggle', 'tooltip');
            // Only add remove icon on events
            if (!event.url) {
                // is not event creator or admin
                if (!event.is_not_creator) {
                    element.append('<span class="closeon"><i class="fa fa-trash" title="Supprimer Evenement"></i></span>');
                    element.find(".closeon").click(function () {
                        $.get(client_url + 'home/delete_event/' + event.eventid, function (response) {
                            if (response.success === true) {
                                $('#calendar').fullCalendar('removeEvents', event._id);
                                alert_float('success', response.message);
                            }
                        }, 'json');
                    });
                }
            }
        },
        dayClick: function (date) {
            $('#newEventModal').modal('show');
            date = date.toDate();
            $("input[name='start'].datepicker").datepicker("update", date);
            return false;
        }
    };
    if ($('body').hasClass('home')) {
        settings.customButtons.viewFullCalendar = {
            text: 'expand',
            click: function () {
                window.location.href = client_url + 'home/calendar';
            }
        };
    }

    // Init calendar
    $('#calendar').fullCalendar(settings);
    // New event modal
    $('#newEventModal form').submit(function () {
        if ($(this).find('input[name="start"]').val() === '') {
            alert('La date de début ne peut pas être vide');
            return false;
        }
        $.post(this.action, $(this).serialize(), function (response) {
            response = $.parseJSON(response);
            if (response.success === true) {
                alert_float('success', response.message);
                $('#newEventModal').modal('hide');
                $('#calendar').fullCalendar('refetchEvents');
                $('#newEventModal input').val('');
            }
        });
        return false;
    });
    $('#newEventModal').on('show.bs.modal', function (e) {
        var invoker = $(e.relatedTarget);
        var id = $(invoker).data('id');
        $('#newEventModal .add-title').removeClass('hide');
        $('#newEventModal .edit-title').addClass('hide');
        $('#newEventModal input').val('');
        // is from the edit button
        if (typeof (id) !== 'undefined') {
            $.post(client_url + "home/get_event_by_id", {id: id}, function (response) {
                var response = jQuery.parseJSON(response);
                $('#newEventModal .add-title').addClass('hide');
                $('#newEventModal .edit-title').removeClass('hide');
                $('#newEventModal input[name="id"]').val(id);
                $('#newEventModal input[name="title"]').val(response.title);
                $('#newEventModal input[name="start"]').val(response.start);
                $('#newEventModal input[name="end"]').val(response.end);
                var checked = false;
                if (parseInt(response.public) === 1) {
                    checked = true;
                }
                $('#newEventModal input[name="public"]').prop('checked', checked);
            });
        }
    });
});

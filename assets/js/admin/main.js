// Delay function
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

var top_date = null,
        date = null;
var original_top_search_val;

var update_top_date = function () {
    date = moment(new Date()).tz(timezone);
    var date_value = date.format('dddd D MMMM YYYY HH:mm:ss');
    date_value = date_value.charAt(0).toUpperCase() + date_value.slice(1);
    top_date.html(date_value);
};

$(document).ready(function () {
    if (is_mobile()) {
        var search_bar = $('#top_search').clone();
        var search_button = $('#top_search_button').clone();
        $('#mobile-search ul').append(search_bar);
        $('#mobile-search ul').append(search_button);
        $('#mobile-search').removeClass('hide');
        $('.navbar-right #top_search').remove();
        $('.navbar-right #top_search_button').remove();
    }
    //Init Datepicker
    if ($('input[id="date_time"]').length > 0) {
        init_datepicker(date_format_calendar);
    } else {
        init_datepicker();
    }
    // Init all necessary data
    init_progress_bars();
    init_datepicker();
    init_selectpicker();
    setBodySmall();
    mainWrapperHeightFix();
    //Show & Hide Bloc
    $('body').on('click', '.show_hide_bloc', function () {
        var area_bloc = $('#bloc_to_show_hide');
        if (area_bloc.hasClass('hide')) {
            area_bloc.removeClass('hide');
            if ($('.show_hide_bloc').hasClass('fa-chevron-circle-down')) {
                $('.show_hide_bloc').removeClass('fa-chevron-circle-down');
                $('.show_hide_bloc').addClass('fa-chevron-circle-up');
            }
        } else {
            area_bloc.addClass('hide');
            if ($('.show_hide_bloc').hasClass('fa-chevron-circle-up')) {
                $('.show_hide_bloc').removeClass('fa-chevron-circle-up');
                $('.show_hide_bloc').addClass('fa-chevron-circle-down');
            }
        }
    });

    $('form').not('#single-ticket-form,#calendar-event-form,#settings-form,#proposal-form,#task-form').areYouSure();

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $('body').popover({
        selector: '[data-toggle="popover"]'
    });

    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    $('body').on('click', '.btn-delete-customer', function () {
        var r = confirm("Are you sure you want to delete this customer?");
        if (r === true) {
            return true;
        }
        return false;
    });

    $('body').on('click', '.btn-delete-company', function () {
        var r = confirm("Are you sure you want to delete this company?");
        if (r === true) {
            return true;
        }
        return false;
    });

    $('body').on('click', '.btn-delete-product', function () {
        var r = confirm("Are you sure you want to delete this product?");
        if (r === true) {
            return true;
        }
        return false;
    });

    $('body').on('click', '.btn-delete-confirm', function () {
        var r = confirm("Etes-vous sûr que vous voulez supprimer ?");
        if (r === true) {
            return true;
        }
        return false;
    });

    $('body').on('click', '.btn-initialisation-colis-confirm', function () {
        var r = confirm("Etes-vous sûr que vous voulez initialiser cette colis ?");
        if (r === true) {
            return true;
        }
        return false;
    });

    top_date = $('#top_date');
    update_top_date();
    setInterval(update_top_date, 1000);

    // Remove tooltip fix on body click (in case user clicked link and tooltip stays open)
    $('body').on('click', function () {
        $('.tooltip').remove();
    });

    _validate_form($('#form-reminder'), {
        date: 'required',
        staff: 'required'
    }, reminderFormHandler);

    $('body').on('click', '.delete-reminder', function () {
        $.get($(this).attr('href'), function (response) {
            alert_float(response.alert_type, response.message);
            $('body').find('.table-reminders').DataTable().ajax.reload();
        }, 'json');
        return false;
    });
    $('.switch-box').bootstrapSwitch();
    $('#side-menu').metisMenu();
    $('#customize-sidebar').metisMenu();
    // Set timeout to remove php alerts added from flashdata
    setTimeout(function () {
        $('#alerts').slideUp();
    }, 3500);
    // Check for active class in sidebar links
    var side_bar = $('#side-menu');
    var sidebar_links = side_bar.find('li > a');
    $.each(sidebar_links, function (i, data) {
        var href = $(data).attr('href');
        if (location === href) {
            side_bar.find('a[href="' + href + '"]').parents('li').not('.quick-links').addClass('active');
            side_bar.find('a[href="' + href + '"]').parents('ul.nav-second-level').addClass('in');
        }
    });
    // Fix for dropdown search to close if user click anyhere on html except on dropdown
    $("body").click(function (e) {
        if (!$(e.target).parents('#top_search_dropdown').hasClass('search-results')) {
            $('#top_search_dropdown').remove();
            $('#search_input').val('');
        }
    });
    $('body').on('click', '.cpicker', function () {
        var color = $(this).data('color');
        $(this).parents('.kan-ban-settings').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
        $(this).parents('.panel-heading-bg').css('background', color);
        $(this).parents('.panel-heading-bg').css('border', '1px solid ' + color);
    });
    // Focus search input on click
    $('#top_search_button button').on('click', function () {
        $('#search_input').focus();
    });
    $('#search_input').on('keyup paste', function () {
        var q = $(this).val();
        var search_results = $('#search_results');
        if (q === '') {
            search_results.html('');
            return;
        }
        delay(function () {
            if (q === original_top_search_val) {
                return;
            }
            $.post(admin_url + 'misc/search', {
                q: q
            }).success(function (results) {
                search_results.html(results);
                original_top_search_val = q;
            });
        }, 700);
    });
    // Check for customizer active class
    var customizer_sidebar = $('#customize-sidebar');
    if (customizer_sidebar.hasClass('display-block')) {
        var customizer_links = customizer_sidebar.find('li > a');
        $.each(customizer_links, function (i, data) {
            var href = $(data).attr('href');
            if (location === href) {
                customizer_sidebar.find('a[href="' + href + '"]').parents('li').addClass('active');
                customizer_sidebar.find('a[href="' + href + '"]').prev('active');
                customizer_sidebar.find('a[href="' + href + '"]').parents('ul.nav-second-level').addClass('in');
            }
        });
    }
    // Customizer close and remove open from session
    $('.close-customizer').on('click', function (e) {
        e.preventDefault();
        $('#customize-sidebar').addClass('fadeOutLeft');
        $.get(admin_url + 'misc/set_customizer_closed');
    });
    // Open customizer and add that is open to session
    $('.open-customizer').on('click', function (e) {
        e.preventDefault();
        var customizer = $('#customize-sidebar');

        if (customizer.hasClass('fadeOutLeft')) {
            customizer.removeClass('fadeOutLeft');
        }
        customizer.addClass('fadeInLeft');
        customizer.addClass('display-block');
        $.get(admin_url + 'misc/set_customizer_open');
    });
    $('.new-company-pdf-field').on('click', function () {
        var field = $('input[name="new_company_field_name"]').val();
        var value = $('input[name="new_company_field_value"]').val();
        if (field !== '') {
            // Alphanumeric and only space allowed
            var myRegEx = /[^a-z\d\s]/i;
            var isValid = !(myRegEx.test(field));
            if (isValid === false) {
                alert('This field is alphanumeric');
                return false;
            }
            $.post(admin_url + 'settings/new_pdf_company_field', {
                field: field,
                value: value
            }).success(function (response) {
                window.location.reload();
            });
        }
    });
    // Notification profile link click
    $('body').on('click', '.notification_link', function () {
        var link = $(this).data('link');
        window.location.href = link;
    });
    // Set notifications clients to read when notifictions dropdown is opened
    $('.notifications-icon-clients').on('click', function () {
        $.post(admin_url + 'misc/set_notifications_clients_read').success(function (response) {
            response = $.parseJSON(response);
            if (response.success === true) {
                $(".icon-notifications-clients").addClass('hide');
                setTimeout(function () {
                    $('.notification-box.unread').removeClass('unread', 'slow');
                }, 1000);
            }
        });
    });
    // Set notifications staffs to read when notifictions dropdown is opened
    $('.notifications-icon-staffs').on('click', function () {
        $.post(admin_url + 'misc/set_notifications_staffs_read').success(function (response) {
            response = $.parseJSON(response);
            if (response.success === true) {
                $(".icon-notifications-staffs").addClass('hide');
                setTimeout(function () {
                    $('.notification-box.unread').removeClass('unread', 'slow');
                }, 1000);
            }
        });
    });

    $('#send_file').on('show.bs.modal', function (e) {
        var return_url = $($(e.relatedTarget)).data('return-url');
        var file_path = $($(e.relatedTarget)).data('path');
        var file_name = $($(e.relatedTarget)).data('file-name');
        var filetype = $($(e.relatedTarget)).data('filetype');
        $('#send_file').find('input[name="filetype"]').val(filetype);
        $('#send_file').find('input[name="file_path"]').val(file_path);
        $('#send_file').find('input[name="file_name"]').val(file_name);
        $('#send_file').find('input[name="return_url"]').val(return_url);

        if ($('input[name="email"]').length > 0) {
            $('#send_file').find('input[name="send_file_email"]').val($('input[name="email"]').val());
        }
    });


    // bootstrap switch active or inactive global function
    $('body').on('switchChange.bootstrapSwitch', '.switch-box', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });

    // Set password checkbox change
    $('body').on('change', 'input[name="send_set_password_email"]', function () {
        $('body').find('.client_password_set_wrapper').toggleClass('hide');
    });
    // Todo status change checkbox click
    $('body').on('change', '.todo input[type="checkbox"]', function () {
        var status = 0;
        var redirect_helper = 1;

        var todoid = $(this).val();
        if ($(this).prop('checked') === true) {
            status = 1;
        }
        if ($('body').hasClass('main_todo_page')) {
            redirect_helper = 0;
        }
        window.location.href = admin_url + 'todo/change_todo_status/' + todoid + '/' + status + '/' + redirect_helper;
    });

    // Makes todos sortable
    var todos_sortable = $(".todos-sortable").sortable({
        connectWith: ".todo",
        items: "li:not(.ui-state-disabled)",
        appendTo: "body",
        update: function (event, ui) {
            if (this === ui.item.parent()[0]) {
                if (ui.sender !== null) {
                    update_todo_items();
                } else {
                    update_todo_items();
                }
            }
        }
    }).disableSelection();

    // Dismiss announcement
    $('.dismiss_announcement').on('click', function () {
        var announcementid = $(this).data('id');
        $.post(admin_url + 'misc/dismiss_announcement', {
            announcementid: announcementid
        });
    });

    $('.toggle-custom-kan-ban-tab').on('click', function () {
        if ($('#list_tab').hasClass('toggled')) {
            $('#list_tab').css('display', 'none');
            $('#list_tab').removeClass('toggled');
            $('.kan-ban-tab').css('display', 'block');
            $('input[name="search[]"]').removeClass('hide');
        } else {
            $('#list_tab').css('display', 'block');
            $('#list_tab').addClass('toggled');
            $('.kan-ban-tab').css('display', 'none');
            $('input[name="search[]"]').addClass('hide');
        }
    });

    // Add merge field
    $('.add_merge_field').on('click', function (e) {
        e.preventDefault();
        var data = Editor.getHTML();
        data += $(this).text();
        Editor.setHTML(data);
    });

    // Since version 1.0.1
    // Send SMTP test email
    $('.test_email').on('click', function () {
        var email = $('input[name="test_email"]').val();
        if (email !== '') {
            $(this).attr('disabled', true);
            $.post(admin_url + 'emails/sent_smtp_test_email', {
                test_email: email
            }).success(function (data) {
                var current_url = $('form').attr('action');
                window.location.href = current_url;
            });
        }
    });

    // Member profile
    $('select[name="role"]').on('change', function () {
        var roleid = $(this).val();
        init_roles_permissions(roleid);
    });

    // Used for knowledge base reports
    $('select[name="report-group-change"]').on('change', function () {
        var groupid = $(this).val();
        $('.progress .progress-bar').each(function () {
            $(this).css('width', 0 + '%');
            $(this).text(0 + '%');
        });

        setTimeout(function () {
            $('.group-report').addClass('hide');
            $('#group_' + groupid).removeClass('hide');
        }, 200);

        init_progress_bars();
    });
});

$(window).bind("resize click", function () {
    // Add special class to minimalize page elements when screen is less than 768px
    setBodySmall();
    // Waint until metsiMenu, collapse and other effect finish and set wrapper height
    setTimeout(function () {
        mainWrapperHeightFix();
    }, 300);
});
// Progress bar animation load
function init_progress_bars() {
    setTimeout(function () {
        $('.progress .progress-bar').each(function () {
            var bar = $(this);
            var perc = bar.attr("data-percent");
            var current_perc = 0;
            var progress = setInterval(function () {
                if (current_perc >= perc) {
                    clearInterval(progress);
                    if (perc === 0) {
                        bar.css('width', 0 + '%');
                    }
                } else {
                    current_perc += 1;
                    bar.css('width', (current_perc) + '%');
                }
                bar.text((current_perc) + '%');
            }, 10);
        });
    }, 300);
}

function get_url_param(param) {
    var vars = {};
    window.location.href.replace(location.hash, '').replace(
            /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
            function (m, key, value) { // callback
                vars[key] = value !== undefined ? value : '';
            }
    );

    if (param) {
        return vars[param] ? vars[param] : null;
    }
    return vars;
}
function mainWrapperHeightFix() {

    // Get and set current height
    var headerH = 56;
    var navigationH = $("#navigation").height();
    var contentH = $(".content").height();

    // Set new height when contnet height is less then navigation
    if (contentH < navigationH) {
        $("#wrapper").css("min-height", navigationH + 'px');
    }

    // Set new height when contnet height is less then navigation and navigation is less then window
    if (contentH < navigationH && navigationH < $(window).height()) {
        $("#wrapper").css("min-height", $(window).height() - headerH + 'px');
    }

    // Set new height when contnet is higher then navigation but less then window
    if (contentH > navigationH && contentH < $(window).height()) {
        $("#wrapper").css("min-height", $(window).height() - headerH + 'px');
    }
}

function setBodySmall() {
    if ($(this).width() < 769) {
        $('body').addClass('page-small');
    } else {
        $('body').removeClass('page-small');
        $('body').removeClass('show-sidebar');
    }
}
// Generate random password
function generatePassword(field) {
    var length = 12,
            charset = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }

    $(field).parents().find('input.password').val(retVal);
}
// Switch field make request
function switch_field(field) {
    var status = 0;
    if ($(field).prop('checked') === true) {
        status = 1;
    }
    var url = $(field).data('switch-url');
    var id = $(field).data('id');
    $.get(site_url + url + '/' + id + '/' + status);
    if (status === 1) {
        alert_float('success', 'Activé avec succées');
    } else {
        alert_float('success', 'Désactivé avec succées');
    }
}
// General validate form function
function _validate_form(form, form_rules, submithandler) {
    $.validator.setDefaults({
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    $(form).validate({
        rules: form_rules,
        messages: {
            email: {
                remote: 'Email already exists'
            },
            reference_product: {
                remote: 'Reference already exists'
            },
            code_barre: {
                remote: 'Code Barre existe déjà.'
            },
            code_barre_verifie: {
                remote: "Code Barre n'existe pas."
            },
            num_commande: {
                remote: "Numéro de commande existe déjà."
            },
            telephone: {
                remote: 'Téléphone ne doit pas contenir +212 (Voilà la forme : 0600000000).'
            },
            phonenumber: {
                remote: 'Téléphone ne doit pas contenir +212 ou plus que 10 chiffres (Voilà la forme standart : 0600000000).'
            }
        },
        ignore: [],
        submitHandler: function (form) {
            if (typeof (submithandler) !== 'undefined') {
                submithandler(form);
            } else {
                return true;
            }
        }
    });

    setTimeout(function () {
        var custom_required_fields = $('[data-custom-field-required]');
        if (custom_required_fields.length > 0) {
            $.each(custom_required_fields, function () {
                $(this).rules("add", {
                    required: true
                });
            });
        }

    }, 10);
    return false;
}
// Delete option
function delete_option(child, id) {
    $.get(admin_url + 'settings/delete_option/' + id, function (response) {
        if (response.success === true) {
            $(child).parents('.option').remove();
        }
    }, 'json');
}
// Slide Toggle
function slideToggle(selector, callback) {

    if ($(selector).hasClass('hide')) {
        $(selector).toggleClass('hide', 'slow');
    } else {
        $(selector).slideToggle();
    }

    var progress_bars = $('.progress-bar');
    if (progress_bars.length > 0) {
        $('.progress .progress-bar').each(function () {
            $(this).css('width', 0 + '%');
            $(this).text(0 + '%');
        });
        init_progress_bars();
    }

    if (typeof (callback) === 'function') {
        callback();
    }

}
// Generate float alert
function alert_float(type, message) {
    $.notify({
        message: message

    }, {
        type: type,
        animate: {
            enter: 'animated bounceInRight',
            exit: 'animated bounceOutRight'
        }
    });
}
// General function for all datatables serverside
function initDataTable(table, url, item_name, notsearchable, notsortable, fnserverparams, defaultorder, column_tfoot_total) {
    var _table_name = table;

    if ($(table).length === 0) {
        return;
    }
    var export_columns = [':visible'];
    // If not order is passed order by the first column
    if (typeof (defaultorder) === 'undefined') {
        defaultorder = [0, 'DESC'];
    }

    var buttons = [
        {
            extend: 'colvis',
            postfixButtons: ['colvisRestore'],
            className: 'btn btn-default dt-column-visibility',
            text: dt_button_column_visibility
        }, {
            text: dt_button_reload,
            className: 'btn btn-default',
            action: function (e, dt, node, config) {
                dt.ajax.reload();
            }
        }, {
            extend: 'collection',
            text: 'Export',
            className: 'btn btn-default',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: dt_button_excel,
                    Tooltip: 'test',
                    exportOptions: {
                        columns: export_columns
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: dt_button_csv,
                    exportOptions: {
                        columns: export_columns
                    }

                },
                {
                    extend: 'pdfHtml5',
                    text: dt_button_pdf,
                    orientation: 'landscape',
                    customize: function (doc) {
                        // Fix for column widths
                        var table_api = table.DataTable();
                        var columns = table_api.columns().visible();
                        var columns_total = columns.length;
                        var pdf_widths = [];
                        for (i = 0; i < columns_total; i++) {
                            // Is only visible column
                            if (columns[i] === true) {
                                pdf_widths.push('*');
                            }
                        }
                        doc.styles.tableHeader.alignment = 'left';
                        doc.styles.tableHeader.margin = [5, 5, 5, 5];
                        doc.content[1].table.widths = pdf_widths;
                        doc.pageMargins = [12, 12, 12, 12];

                    },
                    exportOptions: {
                        columns: export_columns
                    }
                },
                {
                    extend: 'print',
                    text: dt_button_print,
                    exportOptions: {
                        columns: export_columns
                    }
                }
            ]
        }
    ];

    if (table === '.table-delivery-notes') {
        buttons.push({
            extend: 'collection',
            text: 'Confirmer Bons de Livraison',
            className: 'btn btn-info',
            action: function () {
                change_status_bon_livraison('confirmer');
            }
        });
    } else if (table === '.table-historique-colis-bon-livraison') {
        var statusBonLivraison = $('input[name="bonlivraison_status"]').val();
        if (parseInt(statusBonLivraison) === 1) {
            buttons.push({
                extend: 'collection',
                text: 'Confirmer',
                className: 'btn btn-success',
                action: function () {
                    change_status_bon_livraison('confirmer');
                }
            });
        } else {
            buttons.push({
                extend: 'collection',
                text: 'Déjà confirmé',
                className: 'btn btn-info',
                action: function () {
                    alert_float('success', 'Bon de livraison déjà confirmé');
                }
            });
        }
    } else if (table === '.table-etat-colis-livrer') {
        buttons.push({
            extend: 'collection',
            text: 'Changer Statut',
            className: 'btn btn-info',
            buttons: [
                {
                    text: 'En attente',
                    action: function () {
                        change_status_etat_colis_livrer('en_attente');
                    }
                },
                {
                    text: 'Valider',
                    action: function () {
                        change_status_etat_colis_livrer('valider');
                    }
                }
            ]
        });

        if (parseInt(is_admin) !== 1) {
            buttons.push({
                extend: 'collection',
                text: 'Changer Etat',
                className: 'btn btn-info',
                buttons: [
                    {
                        text: 'Non Réglé',
                        action: function () {
                            change_etat_etat_colis_livrer('non_regle');
                        }
                    },
                    {
                        text: 'Réglé',
                        action: function () {
                            change_etat_etat_colis_livrer('regle');
                        }
                    }
                ]
            });
        }
    } else if (table === '.table-historique-items-etat-colis-livrer') {
        var statusEtatColisLivrer = $('input[name="etat_colis_livrer_status"]').val();
        if (parseInt(statusEtatColisLivrer) === 1) {
            buttons.push({
                extend: 'collection',
                text: 'Valider',
                className: 'btn btn-success',
                action: function () {
                    validate_etat_etat_colis_livrer();
                }
            });
        } else {
            buttons.push({
                extend: 'collection',
                text: 'Déjà validé',
                className: 'btn btn-info',
                action: function () {
                    alert_float('success', 'Etat colis livré déjà validé');
                }
            });
        }
    }
    else if (table === '.table-factures12') {
        buttons.push({
            extend: 'collection',
            text: 'Changer Statut',
            className: 'btn btn-info',
            buttons: [
                {
                    text: 'Non Réglé',
                    action: function () {
                        change_status_etat('noregle');
                    }
                },
                {
                    text: 'Réglé',
                    action: function () {
                        change_status_etat('regle');
                    }
                }
            ]
        });

        if (parseInt(is_admin) !== 1) {
            buttons.push({
                extend: 'collection',
                text: 'Changer Etat',
                className: 'btn btn-info',
                buttons: [
                    {
                        text: 'Non Réglé',
                        action: function () {
                            change_etat_etat_colis_livrer('non_regle');
                        }
                    },
                    {
                        text: 'Réglé',
                        action: function () {
                            change_etat_etat_colis_livrer('regle');
                        }
                    }
                ]
            });
        }
    }


    else if (table === '.table-demandes') {
        buttons.push({
            extend: 'collection',
            text: 'Changer Statut',
            className: 'btn btn-info',
            buttons: [
                {
                    text: 'Cloturé',
                    action: function () {
                        change_status_demande('cloturer');
                    }
                },
                {
                    text: 'En Cours',
                    action: function () {
                        change_status_demande('en_cours');
                    }
                }
            ]
        });


    }

    var length_options = [10, 20, 50, 100];
    var length_options_names = [10, 20, 50, 100];
    // La variable tables_pagination_limit est appelé dans le fichier head.php
    if (!$.isNumeric(tables_pagination_limit)) {
        tables_pagination_limit = 20;
    }
    tables_pagination_limit = parseFloat(tables_pagination_limit);

    if ($.inArray(tables_pagination_limit, length_options) === -1) {
        length_options.push(tables_pagination_limit);
        length_options_names.push(tables_pagination_limit);
    }

    length_options.sort(function (a, b) {
        return a - b;
    });
    length_options_names.sort(function (a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(dt_length_menu_all);

    var table = $(table).dataTable({
        "language": {
            "emptyTable": dt_emptyTable.format(item_name),
            "info": dt_info.format(item_name),
            "infoEmpty": dt_infoEmpty.format(item_name),
            "infoFiltered": dt_infoFiltered.format(item_name),
            "lengthMenu": dt_lengthMenu.format(item_name),
            "loadingRecords": dt_loadingRecords,
            "processing": '<div class="dt-loader"></div>',
            "search": dt_search,
            "zeroRecords": dt_zeroRecords,
            "paginate": {
                "first": dt_paginate_first,
                "last": dt_paginate_last,
                "next": dt_paginate_next,
                "previous": dt_paginate_previous
            },
            "aria": {
                "sortAscending": ": " + dt_sortAscending,
                "sortDescending": ": " + dt_sortDescending
            }
        },
        "processing": true,
        "serverSide": true,
        'paginate': true,
        'searchDelay': 300,
        'responsive': true,
        "bLengthChange": false,
        "pageLength": tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "autoWidth": false,
        dom: 'Bfrtip',
        buttons: buttons,
        "columnDefs": [{
                "searchable": false,
                "targets": notsearchable
            }, {
                "sortable": false,
                "targets": notsortable
            }],
        "fnDrawCallback": function (oSettings) {
            $('.switch-box').bootstrapSwitch();
        },
        "fnCreatedRow": function (nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;

            // Remove the formatting to get integer data for summation
            var intVal = function (i) {
                return typeof i === 'string' ?
                        i.replace(/[\ dh,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
            };

            if (typeof (column_tfoot_total) !== 'undefined') {
                $.each(column_tfoot_total, function (i, val) {
                    // Total over all pages
                    Total = api
                            .column(val)
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                    Total = accounting.formatNumber(Total);

                    // Total over this page
                    pageTotal = api
                            .column(val, {page: 'current'})
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                    pageTotal = accounting.formatNumber(pageTotal);
                    // Update total
                    $('.total_table').html(Total + ' Dhs');
                    $(api.column(val).footer()).html(Total + ' Dhs');
                });
            }
        },
        "order": [defaultorder],
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function (d) {
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
            }
        }
    });

    var tableApi = table.DataTable();

    $('body').find('.dt-column-visibility').attr('data-toggle', 'tooltip');
    $('body').find('.dt-column-visibility').attr('title', dt_column_visibility_tooltip);
    $('.datepicker.activity-log-date').on('change', function () { // for select box
        var i = $(this).attr('data-column');
        var v = $(this).val();
        tableApi.column(i).search(v).draw();
    });
}
// General function for all datatables serverside
function initDataTable_1(table, url, item_name, notsearchable, notsortable, fnserverparams, defaultorder) {
    var _table_name = table;

    if ($(table).length === 0) {
        return;
    }
    var export_columns = [':visible'];
    // If not order is passed order by the first column
    if (typeof (defaultorder) === 'undefined') {
        defaultorder = [0, 'DESC'];
    }

    var buttons = [
        {
            extend: 'colvis',
            postfixButtons: ['colvisRestore'],
            className: 'btn btn-default dt-column-visibility',
            text: dt_button_column_visibility
        }, {
            text: dt_button_reload,
            className: 'btn btn-default',
            action: function (e, dt, node, config) {
                dt.ajax.reload();
            }
        }
    ];

    if (is_admin === 1) {
        buttons.push({
            extend: 'collection',
            text: 'Export',
            className: 'btn btn-default',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: dt_button_excel,
                    Tooltip: 'test',
                    exportOptions: {
                        columns: export_columns
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: dt_button_csv,
                    exportOptions: {
                        columns: export_columns
                    }

                },
                {
                    extend: 'pdfHtml5',
                    text: dt_button_pdf,
                    orientation: 'landscape',
                    customize: function (doc) {
                        // Fix for column widths
                        var table_api = table.DataTable();
                        var columns = table_api.columns().visible();
                        var columns_total = columns.length;
                        var pdf_widths = [];
                        for (i = 0; i < columns_total; i++) {
                            // Is only visible column
                            if (columns[i] === true) {
                                pdf_widths.push('*');
                            }
                        }
                        doc.styles.tableHeader.alignment = 'left';
                        doc.styles.tableHeader.margin = [5, 5, 5, 5];
                        doc.content[1].table.widths = pdf_widths;
                        doc.pageMargins = [12, 12, 12, 12];

                    },
                    exportOptions: {
                        columns: export_columns
                    }
                },
                {
                    extend: 'print',
                    text: dt_button_print,
                    exportOptions: {
                        columns: export_columns
                    }
                }
            ]
        }
        );
    }

    var length_options = [10, 20, 50, 100];
    var length_options_names = [10, 20, 50, 100];
    var tables_pagination_limit = 20;
    tables_pagination_limit = parseFloat(tables_pagination_limit);

    if ($.inArray(tables_pagination_limit, length_options) === -1) {
        length_options.push(tables_pagination_limit);
        length_options_names.push(tables_pagination_limit);
    }

    length_options.sort(function (a, b) {
        return a - b;
    });
    length_options_names.sort(function (a, b) {
        return a - b;
    });

    length_options.push(-1);
    length_options_names.push(dt_length_menu_all);

    var table = $(table).dataTable({
        "language": {
            "emptyTable": dt_emptyTable.format(item_name),
            "info": dt_info.format(item_name),
            "infoEmpty": dt_infoEmpty.format(item_name),
            "infoFiltered": dt_infoFiltered.format(item_name),
            "lengthMenu": dt_lengthMenu.format(item_name),
            "loadingRecords": dt_loadingRecords,
            "processing": '<div class="dt-loader"></div>',
            "search": dt_search,
            "zeroRecords": dt_zeroRecords,
            "paginate": {
                "first": dt_paginate_first,
                "last": dt_paginate_last,
                "next": dt_paginate_next,
                "previous": dt_paginate_previous
            },
            "aria": {
                "sortAscending": ": " + dt_sortAscending,
                "sortDescending": ": " + dt_sortDescending
            }
        },
        "processing": true,
        //"serverSide": false,
        'paginate': true,
        'searchDelay': 300,
        'responsive': true,
        "bLengthChange": false,
        "pageLength": tables_pagination_limit,
        "lengthMenu": [length_options, length_options_names],
        "autoWidth": false,
        dom: 'Bfrtip',
        buttons: buttons,
        "columnDefs": [{
                "searchable": false,
                "targets": notsearchable
            }, {
                "sortable": false,
                "targets": notsortable
            }],
        "fnDrawCallback": function (oSettings) {
            $('.switch-box').bootstrapSwitch();
        },
        "fnCreatedRow": function (nRow, aData, iDataIndex) {
            // If tooltips found
            $(nRow).attr('data-title', aData.Data_Title);
            $(nRow).attr('data-toggle', aData.Data_Toggle);
        },
        "order": [defaultorder],
        "ajax": {
            "url": url,
            "type": "POST",
            "data": function (d) {
                for (var key in fnserverparams) {
                    d[key] = $(fnserverparams[key]).val();
                }
            }
        }
    });

    var tableApi = table.DataTable();

    $('body').find('.dt-column-visibility').attr('data-toggle', 'tooltip');
    $('body').find('.dt-column-visibility').attr('title', dt_column_visibility_tooltip);
    $('.datepicker.activity-log-date').on('change', function () { // for select box
        var i = $(this).attr('data-column');
        var v = $(this).val();

        tableApi.column(i).search(v).draw();
    });
}
// Update todo items when drop happen
function update_todo_items() {
    var unfinished_items = $('.unfinished-todos li:not(.no-todos)');
    var finished = $('.finished-todos li:not(.no-todos)');
    var i = 1;
    // Refresh orders

    $.each(unfinished_items, function () {
        $(this).find('input[name="todo_order"]').val(i);
        $(this).find('input[name="finished"]').val(0);
        i++;
    });

    if (unfinished_items.length === 0) {
        $('.nav-total-todos').addClass('hide');
        $('.unfinished-todos li.no-todos').removeClass('hide');
    } else if (unfinished_items.length > 0) {
        if (!$('.unfinished-todos li.no-todos').hasClass('hide')) {
            $('.unfinished-todos li.no-todos').addClass('hide');
        }
        $('.nav-total-todos').removeClass('hide');
        $('.nav-total-todos').html(unfinished_items.length);
    }
    x = 1;
    $.each(finished, function () {
        $(this).find('input[name="todo_order"]').val(x);
        $(this).find('input[name="finished"]').val(1);
        $(this).find('input[type="checkbox"]').prop('checked', true);
        i++;
        x++;
    });

    if (finished.length === 0) {

        $('.finished-todos li.no-todos').removeClass('hide');
    } else if (finished.length > 0) {
        if (!$('.finished-todos li.no-todos').hasClass('hide')) {
            $('.finished-todos li.no-todos').addClass('hide');
        }
    }

    var update = [];
    $.each(unfinished_items, function () {
        var todo_id = $(this).find('input[name="todo_id"]').val();
        var order = $(this).find('input[name="todo_order"]').val();
        var finished = $(this).find('input[name="finished"]').val();
        var description = $(this).find('.todo-description');
        if (description.hasClass('todo-finished')) {
            description.removeClass('todo-finished');
        }

        $(this).find('input[type="checkbox"]').prop('checked', false);
        update.push([todo_id, order, finished]);
    });

    $.each(finished, function () {
        var todo_id = $(this).find('input[name="todo_id"]').val();
        var order = $(this).find('input[name="todo_order"]').val();
        var finished = $(this).find('input[name="finished"]').val();
        var description = $(this).find('.todo-description');
        if (!description.hasClass('todo-finished')) {
            description.addClass('todo-finished');
        }
        update.push([todo_id, order, finished]);
    });

    data = {};
    data.data = update;
    $.post(admin_url + 'todo/update_todo_items_order', data);
}
// Delete single todo item
function delete_todo_item(list, id) {
    $.get(admin_url + 'todo/delete_todo_item/' + id, function (response) {
        if (response.success === true) {
            $(list).parents('li').remove();
            update_todo_items();
        }
    }, 'json');
}
// Date picker init with selected timeformat from settings
//function init_datepicker() {
//    $('.datepicker').datepicker({
//        autoclose: true,
//        todayHighlight: true,
//        format: date_format
//    });
//    $('.calendar-icon').on('click', function () {
//        $(this).parents('.date').find('.datepicker').datepicker('show');
//    });
//}
// Date picker init with selected timeformat from settings
function init_datepicker(dateFormatCalendar) {
    var datepickers = $('.datepicker');
    var datetimepickers = $('.datetimepicker');
    if (datetimepickers.length === 0 && datepickers.length === 0) {
        return;
    }

    var dateFormat = date_format;
    if (typeof (dateFormatCalendar) !== 'undefined') {
        dateFormat = date_format_calendar;
    }

    var opt;
    // Datepicker without time
    $.each(datepickers, function () {
        var opt = {
            format: dateFormat,
            timepicker: false,
            scrollInput: false,
            lazyInit: true,
            dayOfWeekStart: 1
        };
        // Check in case the input have date-end-date or date-min-date
        var max_date = $(this).data('date-end-date');
        var min_date = $(this).data('date-min-date');
        if (max_date) {
            opt.maxDate = max_date;
        }
        if (min_date) {
            opt.minDate = min_date;
        }
        // Init the date picker
        $(this).datepicker(opt);
    });
    var opt_time;
    // Datepicker with time
    $.each(datetimepickers, function () {
        opt_time = {
            format: dateFormat + ' H:i',
            lazyInit: true,
            scrollInput: false,
            dayOfWeekStart: 1
        };
        // Check in case the input have date-end-date or date-min-date
        var max_date = $(this).data('date-end-date');
        var min_date = $(this).data('date-min-date');
        if (max_date) {
            opt_time.maxDate = max_date;
        }
        if (min_date) {
            opt_time.minDate = min_date;
        }
        // Init the date time picker
        $(this).datetimepicker(opt_time);
    });
}
// Init bootstrap select picker
function init_selectpicker() {
    $('.selectpicker').selectpicker({
        showSubtext: true
    });
}
// Datatables custom view
function dt_custom_view(view, table) {
    $('input[name="custom_view"]').val(view);
    $(table).DataTable().ajax.reload();
    if (table === '.table-colis' && view === 11) {
        $('table tr:eq(0) th:eq(6)').text("Date Reportation");
    } else if (table === '.table-colis' && view !== 11) {
        $('table tr:eq(0) th:eq(6)').text("Date Livraison");
    }
    //$('input[name="custom_view"]').val('');
}
// Datatables etat facture view
function dt_etat_colis_view(view, table) {
    $('input[name="etat"]').val(view);
    $(table).DataTable().ajax.reload();
    $('input[name="etat"]').val('');
}
// Called when editing member profile
function init_roles_permissions(roleid) {

    if (typeof (roleid) === 'undefined') {
        roleid = $('select[name="role"]').val();
    }

    var isedit = $('.member > input[name="isedit"]');
    if (isedit.length > 0 && typeof (roleid) !== 'undefined') {
        return;
    }

    var permissions = $('#staff_permissions').find('input[type="checkbox"].role');

    $.get(admin_url + 'misc/get_role_permissions_ajax/' + roleid).success(function (response) {
        response = $.parseJSON(response);
        permissions.bootstrapSwitch('state', false);
        $.each(permissions, function () {
            var permissionid = $(this).val();
            var permission = $(this);
            $.each(response, function (i, obj) {
                if (permissionid === obj.permissionid) {
                    permission.bootstrapSwitch('state', true);
                }
            });
        });
    });
}
// Generate hidden input field
function hidden_input(name, val) {
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}
// Show/hide full table
function toggle_small_view(table, main_data) {
    $('body').toggleClass('small-table');
    var tablewrap = $('#small-table');
    var visible = false;
    if (tablewrap.hasClass('col-md-6')) {
        tablewrap.removeClass('col-md-6');
        tablewrap.addClass('col-md-12');
        //$(main_data).toggleClass('hide');
        if (!$(main_data).hasClass('hide')) {
            $(main_data).addClass('hide');
        }
        visible = true;
    } else {
        tablewrap.addClass('col-md-6');
        tablewrap.removeClass('col-md-12');
        if ($(main_data).hasClass('hide')) {
            $(main_data).removeClass('hide');
        }
    }
    if (typeof (hidden_columns) !== 'undefined') {
        $.each(hidden_columns, function (i, val) {
            var column = $(table).DataTable().column(val);
            column.visible(visible);
        });
    }
}
// Datatables sprintf language help function
if (!String.prototype.format) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] !== 'undefined' ? args[number] : match;
        });
    };
}

function stripTags(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}

function reminderFormHandler(form) {
    form = $(form);
    var data = form.serialize();
    var serializeArray = $(form).serializeArray();
    $.post(form.attr('action'), data).success(function (data) {
        data = $.parseJSON(data);
        alert_float(data.alert_type, data.message);
        $('body').find('.table-reminders').DataTable().ajax.reload();
    });
    $('.reminder-modal').modal('hide');
    return false;
}

function empty(data) {
    if (typeof (data) === 'number' || typeof (data) === 'boolean') {
        return false;
    }
    if (typeof (data) === 'undefined' || data === null) {
        return true;
    }
    if (typeof (data.length) !== 'undefined') {
        return data.length === 0;
    }
    var count = 0;
    for (var i in data) {
        if (data.hasOwnProperty(i)) {
            count++;
        }
    }
    return count === 0;
}

function is_mobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true;
    }

    return false;
}

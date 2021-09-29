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
    // Used for formatting money
    accounting.settings.currency.decimal = decimal_separator;
    accounting.settings.currency.thousand = thousand_separator;
    // Used for numbers
    accounting.settings.number.decimal = decimal_separator;
    accounting.settings.number.thousand = thousand_separator;
    accounting.settings.number.precision = 2;
    //Init Datepicker
    if ($('input[name="start"]').length > 0) {
        init_datepicker(date_format_calendar);
    } else {
        init_datepicker();
    }
    //Init top date
    top_date = $('#top_date');
    update_top_date();
    setInterval(update_top_date, 1000);
    //Ajax Start
    $(document).ajaxStart(function () {
        if (chat_assets !== 1) {
            $(".wait").css("display", "block");
        }
    });
    //Ajax Complete
    $(document).ajaxComplete(function () {
        if (chat_assets !== 1) {
            $(".wait").css("display", "none");
        }
    });
    //On click button menu
    $('.menu-header-left').on('click', function () {
        show_hide_menu();
    });
    $('.icon-circle-left-aside').on('click', function () {
        show_hide_menu();
    });
    //Change language
    $('body').on('change', 'select[id="_language"]', function () {
        value = $('select[id="_language"]').val();
        window.location.href = client_url + 'home/change_language/' + value;
    });
    //Open customizer and add that is open to session
    $('.open-customizer').on('click', function (e) {
        e.preventDefault();
        var customizer = $('#customize-sidebar');

        if (customizer.hasClass('fadeOutLeft')) {
            customizer.removeClass('fadeOutLeft');
        }
        customizer.addClass('fadeInLeft');
        customizer.addClass('display-block');
        $.get(client_url + 'misc/set_customizer_open');
    });
    // Customizer close and remove open from session
    $('.close-customizer').on('click', function (e) {
        e.preventDefault();
        $('#customize-sidebar').addClass('fadeOutLeft');
        $.get(client_url + 'misc/set_customizer_closed');
    });
    //Confirmation lors de la suppression
    $('body').on('click', '.btn-delete-confirm', function () {
        var r = confirm("Etes-vous sûr que vous voulez supprimer ?");
        if (r === true) {
            return true;
        }

        return false;
    });
    //Switch box
    $('.switch-box').bootstrapSwitch();
    $('#side-menu').metisMenu();
    $('#customize-sidebar').metisMenu();
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
    // bootstrap switch active or inactive global function
    $('body').on('switchChange.bootstrapSwitch', '.switch-box', function () {
        var switch_url = $(this).data('switch-url');
        var reload_table = $(this).data('reload-table');
        if (!switch_url) {
            return;
        }
        switch_field(this);
        if (typeof (reload_table) !== 'undefined') {
            if ($('.' + reload_table).length > 0) {
                $('.' + reload_table).DataTable().ajax.reload();
            }
        }
    });
    //Show & Hide Bloc statistique dashboard
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
    //Filter Module
    $('body').on('change', '#filter-table select[name="f-type-livraison"]', function () {
        var typeLivraison = $('#filter-table select[name="f-type-livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            $('#filter-table select[name="f-point-relai"]').selectpicker('val', '');
            if ($('#filter-table #bloc-f-livreur').hasClass('display-none')) {
                $('#filter-table #bloc-f-livreur').removeClass('display-none');
            }
            if (!$('#filter-table #bloc-f-point-relai').hasClass('display-none')) {
                $('#filter-table #bloc-f-point-relai').addClass('display-none');
            }
        } else {
            $('#filter-table select[name="f-livreur"]').selectpicker('val', '');
            if (!$('#filter-table #bloc-f-livreur').hasClass('display-none')) {
                $('#filter-table #bloc-f-livreur').addClass('display-none');
            }
            if ($('#filter-table #bloc-f-point-relai').hasClass('display-none')) {
                $('#filter-table #bloc-f-point-relai').removeClass('display-none');
            }
        }
    });
    //Show & Hide Bloc Filter
    $('body').on('click', '.btn-filter', function () {
        if ($('#filter-table').hasClass('display-none')) {
            $('#filter-table').removeClass('display-none');
        } else {
            $('#filter-table').addClass('display-none');
        }
    });
    //Validation Filter
    $('body').on('click', '#filter-submit', function () {
        var table = $(this).attr('data-table');
        $('.table-' + table).DataTable().ajax.reload();
        $('#filter-table').addClass('display-none');
    });
    //Reset filter
    $('body').on('click', '#filter-reset', function () {
        var table = $(this).attr('data-table');
        $('#filter-table select').selectpicker('val', '');
        $('#filter-table input').val('');
        $('.table-' + table).DataTable().ajax.reload();
    });
    //Close filter
    $('body').on('click', '#icon-remove-bloc-filter, #filter-close', function () {
        if (!$('#filter-table').hasClass('display-none')) {
            $('#filter-table').addClass('display-none');
        }
    });
    //Init height filter
    if ($('#filter-table').length > 0) {
        $('#filter-table').css('height', parseInt($(window).height()));
    }
    //Show & Hide Bloc Statistique
    $('body').on('click', '.btn-statistique', function () {
        if ($('#statistique').hasClass('display-none')) {
            $('#statistique').removeClass('display-none');
            $('#filtre-table').addClass('display-none');
        } else {
            $('#statistique').addClass('display-none');
        }
    });
    // Set notifications admin to read when notifictions dropdown is opened
    $('.notifications-icon-staffs').on('click', function () {
        $.post(client_url + 'misc/set_notifications_client_read').success(function (response) {
            response = $.parseJSON(response);
            if (response.success === true) {
                $(".icon-notifications-staffs").addClass('hide');
                setTimeout(function () {
                    $('.notification-box.unread').removeClass('unread', 'slow');
                }, 1000);
            }
        });
    });
    // Fix for dropdown search to close if user click anyhere on html except on dropdown
    $("body").click(function (e) {
        if (!$(e.target).parents('#top_search_dropdown').hasClass('search-results')) {
            $('#top_search_dropdown').remove();
            $('#search_input').val('');
        }
    });
    // Focus search input on click
    $('#top_search_button button').on('click', function () {
        $('#search_input').focus();
    });
    // Key Up search input on click
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
            $.post(client_url + 'misc/search', {
                q: q
            }).success(function (results) {
                search_results.html(results);
                original_top_search_val = q;
            });
        }, 700);
    });
    //Validation Regex
    $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i> Les caractères spéciaux sont interdits, (Merci de revoir l'adresse que vous avez entrer, s'il ya un espace à la fin ou bien un saut de la ligne il faut le supprimer)"
            );
});

//Show & Hide Menu
function show_hide_menu() {
    var menu_header_left = $('.menu-header-left');
    var menu_header_left_i = $('.menu-header-left>i');
    var wrapper = $('#wrapper');
    var customizer = $('#customize-sidebar');
    if (!menu_header_left.hasClass('hide-menu')) {
        menu_header_left.addClass('hide-menu');
        if (menu_header_left_i.hasClass('fa-arrow-left')) {
            menu_header_left_i.removeClass('fa-arrow-left');
            menu_header_left_i.addClass('fa-bars');
        }
        wrapper.css('margin', '0');
        if (!customizer.hasClass('fadeOutLeft')) {
            customizer.removeClass('fadeInLeft');
            customizer.addClass('fadeOutLeft');
        }
    } else {
        menu_header_left.removeClass('hide-menu');
        if (menu_header_left_i.hasClass('fa-bars')) {
            menu_header_left_i.removeClass('fa-bars');
            menu_header_left_i.addClass('fa-arrow-left');
        }
        wrapper.css('margin', '0 0 0 225px');
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
//General function for all datatables serverside
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
        }
    ];

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
                remote: 'Téléphone ne doit pas contenir +212 ou plus que 10 chiffres (Voilà la forme : 0600000000).'
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
// Switch field make request
function switch_field(field) {
    var status = 0;
    if ($(field).prop('checked') === true) {
        status = 1;
    }
    var url = $(field).data('switch-url');
    var id = $(field).data('id');
    $.get(site_url + url + '/' + id + '/' + status);
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
// Show/hide full table
function toggle_small_view(table, main_data) {
    $('body').toggleClass('small-table');
    var tablewrap = $('#small-table');
    var visible = false;
    if (tablewrap.hasClass('col-md-6')) {
        tablewrap.removeClass('col-md-6');
        tablewrap.addClass('col-md-12');
        visible = true;
        if (!$(main_data).hasClass('hide')) {
            $(main_data).addClass('hide');
        }
    } else {
        tablewrap.addClass('col-md-6');
        tablewrap.removeClass('col-md-12');
        if ($(main_data).hasClass('hide')) {
            $(main_data).removeClass('hide');
        }
    }
    if (typeof (hidden_columns) !== 'undefined') {
        $.each(hidden_columns, function (i, val) {
            if (table !== '.table-orders' && val !== 0) {
                var column = $(table).DataTable().column(val);
                column.visible(visible);
            }
        });
    }
}
// Is Mobile
function is_mobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true;
    }

    return false;
}
// Init bootstrap select picker
function init_selectpicker() {
    $('.selectpicker').selectpicker({
        showSubtext: true
    });
}
// Format money functions
function format_money(total) {
    return accounting.formatMoney(total, {
        format: "%v"
    });
}
// Generate hidden input field
function hidden_input(name, val) {
    return '<input type="hidden" name="' + name + '" value="' + val + '">';
}
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
// Ckeditor
function ckeditor_start_ckfinder(id) {
    CKEDITOR.replace(id, {
        height: 200
    });
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

    if (typeof (callback) == 'function') {
        callback();
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
// Différence entre date
function difference_entre_date(dateDebut, dateFin) {
    // Initialisation du retour
    var diff = {};
    // Formatted date
    var arrayDateDebut = dateDebut.split("/");
    var dateDebutFormatted = arrayDateDebut[1] + '/' + arrayDateDebut[0] + '/' + arrayDateDebut[2];
    var arraydateFin = dateFin.split("/");
    var dateFinFormatted = arraydateFin[1] + '/' + arraydateFin[0] + '/' + arraydateFin[2];
    
    var dateDebutFormatted = new Date(dateDebutFormatted);
    var dateFinFormatted = new Date(dateFinFormatted);
    
    var tmp = dateFinFormatted - dateDebutFormatted;

    // Nombre de secondes entre les 2 dates
    tmp = Math.floor(tmp / 1000);
    // Extraction du nombre de secondes
    diff.sec = tmp % 60;

    // Nombre de minutes (partie entière)
    tmp = Math.floor((tmp - diff.sec) / 60);
    // Extraction du nombre de minutes
    diff.min = tmp % 60;

    // Nombre d'heures (entières)
    tmp = Math.floor((tmp - diff.min) / 60);
    // Extraction du nombre d'heures
    diff.hour = tmp % 24;

    // Nombre de jours restants
    tmp = Math.floor((tmp - diff.hour) / 24);
    diff.day = tmp;
    
    return diff;
}
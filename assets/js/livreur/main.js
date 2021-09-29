// Delay function
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

var original_top_search_val;

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
    // Init all necessary data
    init_datepicker();
    init_selectpicker();
    setBodySmall();
    mainWrapperHeightFix();

    // bBody
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
    // Remove tooltip fix on body click (in case user clicked link and tooltip stays open)
    $('body').on('click', function () {
        $('.tooltip').remove();
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
    // On key up search input
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

    // Cpicker
    $('body').on('click', '.cpicker', function () {
        var color = $(this).data('color');
        $(this).parents('.kan-ban-settings').find('.cpicker-big').removeClass('cpicker-big').addClass('cpicker-small');
        $(this).removeClass('cpicker-small', 'fast').addClass('cpicker-big', 'fast');
        $(this).parents('.panel-heading-bg').css('background', color);
        $(this).parents('.panel-heading-bg').css('border', '1px solid ' + color);
    });

    // bootstrap switch active or inactive global function
    $('body').on('switchChange.bootstrapSwitch', '.switch-box', function (event, state) {
        var switch_url = $(this).data('switch-url');
        if (!switch_url) {
            return;
        }
        switch_field(this);
    });

    // Set timeout to remove php alerts added from flashdata
    setTimeout(function () {
        $('#alerts').slideUp();
    }, 3500);
});

$(window).bind("resize click", function () {
    // Add special class to minimalize page elements when screen is less than 768px
    setBodySmall();
    // Waint until metsiMenu, collapse and other effect finish and set wrapper height
    setTimeout(function () {
        mainWrapperHeightFix();
    }, 300);
});
// main wrapper height fix
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
// Set body small
function setBodySmall() {
    if ($(this).width() < 769) {
        $('body').addClass('page-small');
    } else {
        $('body').removeClass('page-small');
        $('body').removeClass('show-sidebar');
    }
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
// Date picker init with selected timeformat from settings
function init_datepicker() {
    $('.datepicker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: date_format
    });
    $('.calendar-icon').on('click', function () {
        $(this).parents('.date').find('.datepicker').datepicker('show');
    });
}
// Init bootstrap select picker
function init_selectpicker() {
    $('.selectpicker').selectpicker({
        showSubtext: true
    });
}
// Is mobile
function is_mobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true;
    }

    return false;
}
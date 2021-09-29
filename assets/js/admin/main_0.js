$(document).ready(function () {
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
        window.location.href = admin_url + 'home/change_language/' + value;
    });
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
    //Validate Regex
    $.validator.addMethod(
            "regex",
            function (value, element, regexp) {
                var check = false;
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "<i class='fa fa-exclamation-triangle' style='font-size:14px;color:red'></i> Les caractères spéciaux sont interdits, (Merci de revoir l'adresse que vous avez entrer, s'il ya un espace à la fin ou bien un saut de la ligne il faut le supprimer)"
            );
    //Filtre Module
    $('body').on('change', '#filtre-table select[name="f-type-livraison"]', function () {
        var typeLivraison = $('#filtre-table select[name="f-type-livraison"]').selectpicker('val');
        if (typeLivraison === '' || typeLivraison === 'a_domicile') {
            $('#filtre-table select[name="f-point-relai"]').selectpicker('val', '');
            if ($('#filtre-table #bloc-f-livreur').hasClass('display-none')) {
                $('#filtre-table #bloc-f-livreur').removeClass('display-none');
            }
            if (!$('#filtre-table #bloc-f-point-relai').hasClass('display-none')) {
                $('#filtre-table #bloc-f-point-relai').addClass('display-none');
            }
        } else {
            $('#filtre-table select[name="f-livreur"]').selectpicker('val', '');
            if (!$('#filtre-table #bloc-f-livreur').hasClass('display-none')) {
                $('#filtre-table #bloc-f-livreur').addClass('display-none');
            }
            if ($('#filtre-table #bloc-f-point-relai').hasClass('display-none')) {
                $('#filtre-table #bloc-f-point-relai').removeClass('display-none');
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
});
// Show & Hide menu
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
// Ckeditor
function ckeditor_start_ckfinder(id, height) {
    if (typeof (height) === 'undefined') {
        height = 200;
    }
    CKEDITOR.replace(id, {
        height: height
    });
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
// Faire une copie
function __copy(element) {
    var title = element.attr('data-title');
    var id = element.attr('data-id');
    var type = element.attr('data-type');
    var value = '';
    if (type === 'text') {
        value = $('#' + id).text();
    } else if (type === 'input') {
        value = $('#' + id).val();
    }
    alert_float('success', title + ' copié : ' + value);
    copyElement(value);
}
// Copie de l'element
function copyElement(value) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(value).select();
    document.execCommand("copy");
    $temp.remove();
}
$(document).ready(function () {
    var temp_location = window.location.href.replace('#', '');
    $('form').attr('action', temp_location);
    //On clic Tab
    $('body').on('click', 'a[data-toggle="tab"]', function () {
        var location = window.location.href.split("?")[0];
        var hash = this.hash;
        hash = hash.replace('#', '');
        $('form').attr('action', location + '?tab_hash=' + hash);
    });
    //Show Tab
    $('body').find('.nav-tabs a[href="#' + $('input[name="tab_hash"]').val() + '"]').tab('show');
});
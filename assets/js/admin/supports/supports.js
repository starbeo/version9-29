$(document).ready(function () {
    // Init data table support data
    var SupportsServerParams = {
        "custom_sort_by": "[name='supports_sort_by']",
        "all": "[name='all']",
        "finished": "[name='finished']",
        "unfinished": "[name='unfinished']",
        "not_assigned": "[name='not_assigned']",
        "due_date_passed": "[name='due_date_passed']"
    };
    initDataTable('.table-supports', window.location.href, 'supports', 'undefined', 'undefined', SupportsServerParams);

    // Hide Column Data Tables Supports
    if (typeof (hidden_columns) !== 'undefined') {
        $.each(hidden_columns, function (i, val) {
            var column_supports = $('.table-supports').DataTable().column(val);
            column_supports.visible(false);
        });
    }

    // Init single support data
    // supportid is defined in manage.php
    if (typeof (supportid) !== 'undefined') {
        init_support_data(supportid);
    }

    $('body').on('change', 'input[name="checklist-box"]', function () {
        var checked = $(this).prop('checked');
        if (checked === true) {
            val = 1;
        } else {
            val = 0;
        }
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.get(admin_url + 'supports/checkbox_action/' + listid + '/' + val);
        recalculate_checklist_items_progress();
    });

    $('body').on('blur', 'textarea[name="checklist-description"]', function () {
        var description = $(this).val();
        var listid = $(this).parents('.checklist').data('checklist-id');
        $.post(admin_url + 'supports/update_checklist_item', {
            description: description,
            listid: listid
        });
    });
    // Assign support to staff member
    $('body').on('change', 'select[name="select-assignees"]', function () {
        $('body').append('<div class="dt-loader"></div>');
        var data = {};
        data.assignee = $('select[name="select-assignees"]').val();
        data.supportid = supportid;
        $.post(admin_url + 'supports/add_support_assignees', data).success(function (response) {
            response = $.parseJSON(response);
            $('body').find('.dt-loader').remove();
            if (response.success === true) {
                init_support_data();
                $('.table-supports').DataTable().ajax.reload();
            }
        });
    });

});

// Custom sory by supports function
function supports_sort_by(sort) {
    $('input[name="supports_sort_by"]').val(sort);
    $('.table-supports').DataTable().ajax.reload();
    $('input[name="supports_sort_by"]').val('');
}

// Datatables custom view supports
function dt_supports_custom_view(bloc, view, table) {
    var area_view = $('input[name="' + view + '"]');
    if (area_view.val() === '') {
        area_view.val(view);
        $(bloc).css('background-color', '#f5f5f5');
    } else {
        area_view.val('');
        $(bloc).css('background-color', '#ffffff');
    }
    $(table).DataTable().ajax.reload();
}

// Marking support as complete support_id
function mark_complete(support_id, invoker) {
    $('body').append('<div class="dt-loader"></div>');
    $.get(admin_url + 'supports/mark_complete/' + support_id, function (response) {
        $('body').find('.dt-loader').remove();
        if (response.success === true) {
            $('.table-supports').DataTable().ajax.reload();
            alert_float('success', response.message);
            init_support_data(support_id);
            if ($('body').hasClass('home')) {
                $(invoker).parents('.widget-task').remove();
            }
        }
    }, 'json');
}

// Marking support as complete
function unmark_complete(support_id) {
    $('body').append('<div class="dt-loader"></div>');
    $.get(admin_url + 'supports/unmark_complete/' + support_id, function (response) {
        $('body').find('.dt-loader').remove();
        if (response.success === true) {
            $('.table-supports').DataTable().ajax.reload();
            alert_float('success', response.message);
            init_support_data(support_id);
        }
    }, 'json');
}

function init_support(id) {
    supportid = id;
    init_support_data();
}

// Single support data init
function init_support_data() {
    if (supportid === '') {
        return;
    }

    if (!$('body').hasClass('small-table')) {
        toggle_small_view('.table-supports', '#support');
    }

    $.post(admin_url + 'supports/get_support_data/', {
        supportid: supportid
    }).success(function (response) {
        $('#support').html(response);
        reload_support_attachments();
        reload_assignees_select();
        init_supports_checklist_items();

        if (is_mobile()) {
            $('html, body').animate({
                scrollTop: $('#support').offset().top + 150
            }, 600);
        }
    });
}

function reload_support_attachments() {
    $('#attachments').empty();
    $.get(admin_url + 'supports/reload_support_attachments/' + supportid, function (response) {
        html = '';
        if (response.length > 0) {
            html += '<h4 class="bold">Attachments</h4>';
            html += '<ul class="list-unstyled">';
            $.each(response, function (i, obj) {
                html += '<li class="mtop10"><i class="' + obj.mimeclass + '"></i><a href="' + site_url + 'download/file/supportattachment/' + obj.id + '" download>' + obj.file_name + '</a>';
                html += '<a href="#" class="pull-right text-danger" onclick="remove_support_attachment(this,' + obj.id + '); return false;"><i class="fa fa-trash-o"></i></a>';
                html += '</li>';
            });
            html += '</ul>';
        }

        $('#attachments').append(html);
    }, 'json');
}

// Reload assignes select field and removes the already added assignees from the select field
function reload_assignees_select() {
    $.get(admin_url + 'supports/reload_assignees_select/' + supportid, function (response) {
        $('select[name="select-assignees"]').html(response);
        $('select[name="select-assignees"]').selectpicker('refresh');
    });
}

function init_supports_checklist_items(is_new) {
    $.post(admin_url + 'supports/init_checklist_items', {
        supportid: supportid
    }).success(function (data) {
        $('#checklist-items').html(data);
        if (typeof (is_new) !== 'undefined') {
            $('body').find('.checklist textarea').eq(0).focus();
        }
        update_support_checklist_order();
    });
}

function remove_support_attachment(link, id) {
    $.get(admin_url + 'supports/remove_support_attachment/' + id, function (response) {
        if (response.success === true) {
            $(link).parents('li').remove();
        }
    }, 'json');
}

function update_support_checklist_order() {
    var order = [];
    var status = $('body').find('.checklist');
    var i = 1;
    $.each(status, function () {
        order.push([$(this).data('checklist-id'), i]);
        i++;
    });
    var data = {};
    data.order = order;
    $.post(admin_url + 'supports/update_checklist_order', data);
}

// Remove support assignee
function remove_assignee(id, supportid) {
    $.get(admin_url + 'supports/remove_assignee/' + id + '/' + supportid, function (response) {
        if (response.success === true) {
            alert_float('success', response.message);
            init_support_data();
            $('.table-supports').DataTable().ajax.reload();
        }
    }, 'json');
}

function add_support_checklist_item() {
    $.post(admin_url + 'supports/add_checklist_item', {
        supportid: supportid
    }).success(function () {
        init_supports_checklist_items(true);
    });
}

function add_support_comment() {
    var comment = $('#support_comment').val();
    if (comment === '') {
        return;
    }
    var data = {};
    data.content = comment;
    data.supportid = supportid;
    $('body').append('<div class="dt-loader"></div>');
    $.post(admin_url + 'supports/add_support_comment', data).success(function (response) {
        response = $.parseJSON(response);
        $('body').find('.dt-loader').remove();
        if (response.success === true) {
            $('#support_comment').val('');
            get_support_comments();
        }
    });
}

// Get all support comments and append
function get_support_comments() {
    $.get(admin_url + 'supports/get_support_comments/' + supportid, function (response) {
        $('#support-comments').html(response);
    });
}

// Delete support comment from database
function remove_support_comment(commentid) {
    $.get(admin_url + 'supports/remove_comment/' + commentid, function (response) {
        if (response.success === true) {
            $('[data-commentid="' + commentid + '"]').remove();
        }
    }, 'json');
}

function delete_checklist_item(id, field) {
    $.get(admin_url + 'supports/delete_checklist_item/' + id, function (response) {
        if (response.success === true) {
            $(field).parents('.checklist').remove();
            recalculate_checklist_items_progress();
        }
    }, 'json');
}

function recalculate_checklist_items_progress() {
    var total_finished = $('input[name="checklist-box"]:checked').length;
    var total_checklist_items = $('input[name="checklist-box"]').length;
    var percent = 0;

    if (total_checklist_items === 0) {
        // remove the heading for checklist items
        $('body').find('.chk-heading').remove();
    }

    if (total_checklist_items > 2) {
        percent = (total_finished * 100) / total_checklist_items;
    } else {
        $('.support-progress-bar').parents('.progress').addClass('hide');
        return false;
    }

    $('.support-progress-bar').css('width', percent.toFixed(2) + '%');
    $('.support-progress-bar').text(percent.toFixed(2) + '%');
}

// Deletes support from database
function delete_support() {
    $.get(admin_url + 'supports/delete_support/' + supportid, function (response) {
        if (response.success === true) {
            $('.table-supports').DataTable().ajax.reload();
            $('#support').html('');
            $('.tool-container').remove();
            alert_float('success', response.message);
        }
    }, 'json');
}




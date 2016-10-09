jQuery(function () {
    jQuery('.action-done').click(function () {
        var url;
        var parent = jQuery(this).parents('.list-group-item');
        url = jQuery(this).attr('data-task-action');
        var page = jQuery(parent).attr('data-page-action');

        jQuery.ajax({
            url: url,
            data: {page: page},
            method: 'POST'
        }).done(function (data) {
            if (data.status == 1) {
                if (typeof data.action !== 'undefined' && data.action === 'reload') {
                    location.reload();
                    return false;
                }
                jQuery(parent).fadeOut(500);
            }
        });
        return false;
    });

    jQuery('.action-delete').click(function () {
        if (!confirm("Are you sure you want to delete this?")) {
            return false;
        }
        var url;
        var parent = jQuery(this).parents('.list-group-item');
        url = jQuery(this).attr('data-delete-action');
        var page = jQuery(parent).attr('data-page-action');

        jQuery.ajax({
            url: url,
            data: {page: page},
            method: 'POST'
        }).done(function (data) {
            if (data.status == 1) {
                if (typeof data.action !== 'undefined' && data.action !== '') {
                    window.location.replace(data.action);
                    return false;
                }
                jQuery(parent).fadeOut(500);
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            }
        });
        return false;
    });

    jQuery('.add-new-task').click(function () {
        var action = jQuery(this).attr('data-action');
        var category = jQuery(this).attr('data-category');

        jQuery.ajax({
            url: action,
            data: {category: category},
            method: 'POST'
        }).done(function (data) {
            if (data.status == 1) {
                console.log(data);
                var modal = $('#addNewTask');
                jQuery(modal).find('.modal-body').html(data.content);
                jQuery(modal).modal('show');

            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            }
        });
        return false;
    });
});
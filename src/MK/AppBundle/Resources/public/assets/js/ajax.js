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
        var $this = jQuery(this);
        $this.button('loading');

        jQuery.ajax({
            url: action,
            data: {},
            method: 'GET'
        }).done(function (data) {
            $this.button('reset');
            if (data.status == 1) {
                var modal = $('#addNewTask');
                var content = data.content;
                jQuery(modal).find('.modal-body').html(content);
                jQuery(modal).find('.modal-body').find('.field-category-id').val(category);
                jQuery(modal).find('.action-submit').attr('data-action', action);
                jQuery(modal).modal('show');
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            }
        });
        return false;
    });


    jQuery('.action-submit').click(function () {
        var $this = jQuery(this);
        $this.button('loading');

        var data = jQuery('.modal-body form').serialize();
        var action = jQuery(this).attr('data-action');
        jQuery.ajax({
            url: action,
            data: data,
            method: 'POST'
        }).done(function (data) {
            $this.button('reset');
            if (data.status == 1) {
                var modal = $('#addNewTask');
                console.log(data);
                createAlert(data.message);
                jQuery(modal).modal('hide');
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            }
        });
        return false;
    });


    function createAlert(message, type) {
        if (type == undefined) {
            type = 'success';
        }
        var html = '<div class="container"><div class="row margin-top"><div class="col-sm-12"><div class="alert alert-' + type + '" role="alert">';
        html += '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>';
        html += message;
        html += '</div></div></div></div>';
        jQuery('.flash-messages').html(html);
    }
});
jQuery(function () {

    jQuery('.add-new-task').click(function () {
        var action = jQuery(this).attr('data-action');
        var category = jQuery(this).attr('data-category');
        var $this = jQuery(this);
        $this.button('loading');

        jQuery.ajax({
            url: action,
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

                // http://xdsoft.net/jqplugins/datetimepicker/
                jQuery('.datepicker').datetimepicker({
                    format: 'd.m.Y H:i',
                    lang: 'en'
                });
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            } else {
                alert('Error!');
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
                createAlert(data.message);
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            } else {
                alert('Error!');
            }
            jQuery(modal).modal('hide');
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
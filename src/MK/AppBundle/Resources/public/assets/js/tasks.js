jQuery(function () {

    /** Task and catgories */
    jQuery('.action-submit').click(function () {
        var valid = true;
        jQuery('.modal-body form input.required').each(function () {
            if (jQuery(this).val() == '') {
                valid = false;
                jQuery(this).addClass('input-invalid');
            } else {
                jQuery(this).removeClass('input-invalid');
            }
        });

        if (!valid) {
            return false;
        }

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
                var modal = $('.modal-form');
                createAlert(data.message);
                if (currentCategoryBox == false || currentCategoryBox == undefined) {
                    loadCategories();
                } else {
                    loadTasksBox(currentCategoryBox);
                }
                loadArchiveTasks();
            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                createAlert(data.message, 'danger');
            } else {
                createAlert('Error!', 'danger');
            }
            jQuery(modal).modal('hide');
        });
        return false;
    });

    loadCategories();
});
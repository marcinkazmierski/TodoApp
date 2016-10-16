jQuery(function () {

    jQuery('.add-new-category').click(function () {
        var action = jQuery(this).attr('data-action');

        var $this = jQuery(this);
        $this.button('loading');

        jQuery.ajax({
            url: action,
            method: 'GET'
        }).done(function (data) {
            $this.button('reset');
            if (data.status == 1) {
                var modal = $('#addNewCategory');
                var content = data.content;
                jQuery(modal).find('.modal-body').html(content);

                jQuery(modal).find('.action-submit').attr('data-action', action);
                jQuery(modal).modal('show');

            } else if (data.status == 0 && typeof data.message !== 'undefined') {
                alert(data.message);
            } else {
                alert('Error!');
            }
        });
        return false;
    });
});

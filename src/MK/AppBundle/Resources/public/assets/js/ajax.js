jQuery(function () {
    jQuery('.action-done').click(function () {
        var url;
        var parent = jQuery(this).parents('.list-group-item');
        url = jQuery(this).attr('data-task-action');
        jQuery.ajax({
            url: url,
            method: 'POST'
        }).done(function (data) {
            console.log(data);
            if (data.status == 1) {
                jQuery(parent).fadeOut(500);
                console.log("ok");
            }
        });
    });

    jQuery('.action-delete').click(function () {
        if (!confirm("Are you sure you want to delete this?")) {
            return false;
        }

        var url;
        var parent = jQuery(this).parents('.list-group-item');
        url = jQuery(this).attr('data-task-action');
        jQuery.ajax({
            url: url,
            method: 'POST'
        }).done(function (data) {
            if (data.status == 1) {
                jQuery(parent).fadeOut(500);
            }
        });
    });
});
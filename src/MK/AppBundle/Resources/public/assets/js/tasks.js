jQuery(function () {

    var currentCategoryBox = false;

    /** Task and catgories */
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
                var modal = $('.modal-form');
                createAlert(data.message);
                loadTasksBox(currentCategoryBox);
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

    function loadCategories() {
        loadTasksBox('.home-categories');
    }


    function loadTasksBox(el) {
        var action = jQuery(el).attr('data-action');
        var $this = jQuery(el);
        jQuery.ajax({
            url: action,
            method: 'GET'
        }).done(function (data) {
            if (data.status == 1) {
                $this.html(data.content);
                bindAddNewTask($this);
            }
        });
    }

    function bindAddNewTask(el) {
        jQuery(el).find('.add-new-task').click(function () {
            var action = jQuery(this).attr('data-action');
            var category = jQuery(this).attr('data-category');
            var $this = jQuery(this);
            $this.button('loading');

            currentCategoryBox = jQuery(this).parents('.home-category-box');

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
                    tinymce.remove();
                    tinymce.init({
                        selector: '#addNewTask textarea',
                        menubar: false,
                        setup: function (editor) {
                            editor.on('change', function () {
                                editor.save();
                            });
                        }
                    });
                } else if (data.status == 0 && typeof data.message !== 'undefined') {
                    createAlert(data.message, 'danger');
                } else {
                    createAlert('Error!', 'danger');
                }
            });
            return false;
        });

        jQuery(el).find('.action-done').click(function () {
            var action = jQuery(this).attr('data-action');
            currentCategoryBox = jQuery(this).parents('.home-category-box');
            jQuery.ajax({
                url: action,
                method: 'POST'
            }).done(function (data) {
                if (data.status == 1) {
                    // createAlert(data.message);
                    loadTasksBox(currentCategoryBox);
                } else if (data.status == 0 && typeof data.message !== 'undefined') {
                    createAlert(data.message, 'danger');
                } else {
                    createAlert('Error!', 'danger');
                }
            });
            return false;
        });
    }
});
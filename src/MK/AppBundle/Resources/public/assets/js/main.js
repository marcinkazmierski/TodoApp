jQuery(function () {
    AddEditCategory('.index-main-wrapper');
    contact();
});

var currentCategoryBox = false;

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
    currentCategoryBox = false;
}

function bindAddNewTask(el) {
    jQuery(el).find('.add-new-task, .edit-task').click(function () {

        var action = jQuery(this).attr('data-action');
        var category = jQuery(this).attr('data-category');
        var $this = jQuery(this);
        $this.button('loading');


        currentCategoryBox = jQuery(this).parents('.home-category-box');

        if ($this.hasClass('edit-task')) {
            hideShowH4('h4-edit');
        } else {
            hideShowH4('h4-add');
        }

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
                    selector: '.modal-form textarea',
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

    jQuery(el).find('.delete-category').click(function () {
        if (confirm("Are you sure?")) {
            var action = jQuery(this).attr('data-action');

            jQuery.ajax({
                url: action,
                method: 'POST'
            }).done(function (data) {
                if (data.status == 1) {
                    createAlert(data.message);
                    loadCategories();
                } else if (data.status == 0 && typeof data.message !== 'undefined') {
                    createAlert(data.message, 'danger');
                } else {
                    createAlert('Error!', 'danger');
                }
            });
        }
        return false;
    });

    AddEditCategory(el);
}

function hideShowH4(action) {
    if (action != 'h4-add') {
        jQuery('.h4-edit').show();
        jQuery('.h4-add').hide();
    } else {
        jQuery('.h4-edit').hide();
        jQuery('.h4-add').show();
    }
    var name = jQuery(currentCategoryBox).find('h3 span.name').text();
    jQuery('.h4-add span, .h4-edit span').text(name);
}

function AddEditCategory(el) {
    jQuery(el).find('.add-new-category, .edit-category').click(function () {
        var action = jQuery(this).attr('data-action');

        var $this = jQuery(this);
        $this.button('loading');

        if ($this.hasClass('edit-category')) {
            hideShowH4('h4-edit');
        } else {
            hideShowH4('h4-add');
        }

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
}

function contact() {
    jQuery('a.contact-modal').click(function () {

        var action = jQuery(this).attr('data-action');
        var $this = jQuery(this);
        $this.button('loading');

        jQuery.ajax({
            url: action,
            method: 'GET'
        }).done(function (data) {
            $this.button('reset');
            if (data.status == 1) {
                var modal = $('#contactModal');
                var content = data.content;
                jQuery(modal).find('.modal-body').html(content);
                jQuery(modal).find('.action-submit').attr('data-action', action);
                jQuery(modal).modal('show');

                tinymce.remove();
                tinymce.init({
                    selector: '.modal-form textarea',
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
}
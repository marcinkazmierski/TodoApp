jQuery(function () {

    // http://xdsoft.net/jqplugins/datetimepicker/
    jQuery('.datepicker').datetimepicker({
        format: 'd.m.Y H:i',
        lang: 'en'
    });

    // http://mjolnic.com/bootstrap-colorpicker/
    jQuery('.colorpicker-input').colorpicker({
        format: 'hex'
    });
});
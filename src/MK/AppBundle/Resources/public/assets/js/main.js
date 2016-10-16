jQuery(function () {

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
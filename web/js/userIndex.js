$(document).ready(function () {
    $.getJSON( ajaxRoutes["user_getVerified"],
    function (json) {
        var ubody = $('#userbody');
        for (var i = 0; i < json.length; i++) {
            var tr = $("<tr/>");
            tr.append($("<td>").html(json[i].forename));
            tr.append($("<td>").html(json[i].surname));
            tr.append($("<td>").html(json[i].mail));
            var link = $('<a>',{
                text: 'Show',
                title: 'Show',
                href: ajaxRoutes['user_show']+json[i].id});
            tr.append($("<td>").append(link));
            ubody.append(tr);
        }
    })
});

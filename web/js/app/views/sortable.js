$(document).ready(function() {

    $(".source, .target").sortable({
        change:function(e, ui) {
            console.info('change');
            console.info(ui);
        },
        connectWith: ".connected"
    }).on('dragend', function(e, ui){
        console.info("sortup");
        console.info($(this.closest('.clanx-shift-item')).data('shift-id'));
    });
});
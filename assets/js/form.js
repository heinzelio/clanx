var Form = {
    initialize: function() {
        Form.initializeSubmitButton();
    },
    initializeSubmitButton: function() {
        // lock buttons after click to prevent double submits.
        var links = $('a.clanx-submit');
        for (var i = 0; i < links.length; i++) {
            var link = $(links[i]); // Make the DOM element into a jquery elment
            link.click(function() {
                $(this).addClass('disabled');
                var formName = $(this).attr('data-clanx-form-name');
                var form = $('form[name="' + formName + '"]');
                form.submit();
            });
        }
    }
    // initializeDatepicker: function(){
    //     $( ".datepicker.regular" ).datepicker({
    //       dateFormat: "dd.mm.yy", // this is equal to the symfony format "dd.MM.yyyy"
    //       changeYear: true,
    //       firstDay: 1, // 0=Su,1=Mo,..
    //     });
    //     $(".datepicker.birthday").datepicker({
    //         yearRange: "-120:+0",
    //         dateFormat: "dd.mm.yy", // this is equal to the symfony format "dd.MM.yyyy"
    //         changeYear: true,
    //         firstDay: 1, // 0=Su,1=Mo,..
    //     })
    // }
}

module.exports = Form;

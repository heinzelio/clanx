var Form = {
    submitAndLock: function(form, sender) {
        $(sender).addClass('disabled');
        form.submit();
    },
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

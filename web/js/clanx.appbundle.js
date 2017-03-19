
// call functions like this: clanx.appbundle.anotherFancyFunction();
var Clanx = Clanx || {
    AppBundle:{
        Tooltip:{
            initialize: function() {
                $('[data-toggle="tooltip"]').tooltip();
            },
            anotherFancyFunction: function() {
                // foo
            },
        },
        Form:{
            submitAndLock: function(form, sender) {
                $(sender).addClass('disabled');
                form.submit();
            },
        }
    }
};


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
        },
        Link:{
            lockAndFollow: function(sender) {
                $(sender).addClass('disabled');
            }
        },
        Bulk:{
            initialize: function(checkAllSelector) {
                 $(checkAllSelector).click(function () {
                    $('input[data-clanx-bulk-box="true"]').not(this).prop('checked', this.checked);
                    // if there is one on the top and one on the bottom, also do the other one.
                    $(checkAllSelector).not(this).prop('checked', this.checked);
                 });
            },
        }
    }
};

//VALIDATE USER EMAIL
$( "#regForm" ).validate({
    debug: true,
    submitHandler: function(form) {
        form.submit();
    },
    rules: {
        sex:{required:true},
        forename:{required:true},
        surname:{required:true},
        street:{required:true},
        zip:{required:true},
        city:{required:true},
        occupation:{required:false},
        dateOfBirth:{required:true},
        phone:{required:false},
        mail: {
            required: true,
            email: true,
            remote: ajaxRoutes["validateMail"]
            // better use the FOSJsRoutingBundle.
            // https://symfony.com/doc/current/book/routing.html#index-11
        },
        password:{required:true}
    },
    messages: {
        sex:{required:"Wähle dein Geschlecht"},
        forename:{required:"Gib bitte deinen Vornamen an."},
        surname:{required:"Gib bitte deinen Familiennamen an."},
        street:{required:"Gib bitte deine Wohnadresse an."},
        zip:{required:"Gib bitte die Postleitzahl deines Wohnortes an."},
        city:{required:"Gib bitte deinen Wohnort an."},
        dateOfBirth:{required:"Gib bitte dein Geburtsdatum an."},
        mail:{
            required: "Gib eine EMailadresse an.",
            email: "Verwende eine Adresse in der Form name@server.com",
            remote: "Diese EMailadresse wird schon verwendet."
        },
        password:{required:"Wähle ein Passwort."}
    }
});

define([
        'jquery',
        'underscore',
        'backbone',
        'handlebars',
        '../models/Shift'
    ],
    function($, _, Backbone, Shift) {

        return Backbone.Collection.extend({
            // url: function() {
            //     var url = $("[name=shiftsUrl]").val();
            //     console.info("debug collection:Shifts:url - "+url);
            //     return url;
            // },

            initialize: function() {
                console.info("debug collection:Shifts:init");
            }
        });
    }
);

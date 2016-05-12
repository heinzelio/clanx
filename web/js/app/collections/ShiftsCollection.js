define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    '../models/Shift'
    ],
    function($, _, Backbone , Shift) {

        return Backbone.Collection.extend({
            model: Shift,
            initialize: function() {
                console.log('New collection initialized...');

                this.fetch({ 
                    url: "/web/js/collections/shifts.json", 
                    success: function() {
                        console.log("JSON file load was successful");
                    },
                    error: function(){
                        console.log('There was some error in loading and processing the JSON file');
                    }
                });


            }
        });
    }
);
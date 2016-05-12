define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars'
    ],
    function($, _, Backbone, Handlebars) {
        'use strict';

        return Backbone.Model.extend({
            defaults:{
                users:[]
            },
            initialize: function() {
                console.log('New shift created...');
            }
        });
        
    }
);
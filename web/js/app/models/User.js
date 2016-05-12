define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars'
    ],
    function($, _, Backbone, Handlebars) {
        'use strict';

        return Backbone.Model.extend({
            initialize: function() {
                console.log('New user created...');
            }
        });
        
    }
);
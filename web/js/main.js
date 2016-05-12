requirejs.config({  
    baseUrl: '/clanx/web/js',
    paths: {
        'jquery': 'jquery-2.2.3',
        'jquery.sortable': 'jquery.sortable',
        'custom-jquery-ui': 'custom-jquery-ui.min',
        'bootstrap': 'bootstrap',
        'underscore': 'underscore',
        'backbone': 'backbone',
        'handlebars': 'handlebars',
        'text':'require/text'
    },
    shim: {
        'underscore': {
            exports: '_'
        },
        'backbone': {
            deps: ['underscore', 'jquery'],
            exports: 'Backbone'
        },
        'handlebars': {
            deps: ['underscore', 'jquery'],
            exports: 'Handlebars'
        },
         'jquery.sortable':{
            deps: ['jquery'],
            exports: 'JquerySortable'
        },
        'custom-jquery-ui':{
            deps: ['jquery', 'jquery.sortable'],
            exports: 'CustomJQueryUi'
        }
    }
});

require(['jquery'], function($) {  
    'use strict';

    $.ajaxSetup({ cache: false });

});
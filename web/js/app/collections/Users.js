define([
    'jquery',
    'handlebars',
    'backbone',
    '../models/User'
    ], function($, Handlebars, Backbone, UserModel ) {

    var UsersCollection = Backbone.Collection.extend({
        url:'./../../js/app/collections/users.json'
    });

    return UsersCollection;
});
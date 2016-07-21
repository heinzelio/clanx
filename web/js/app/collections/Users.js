define([
    'jquery',
    'handlebars',
    'backbone',
    '../models/User'
    ], function($, Handlebars, Backbone, UserModel ) {

    var UsersCollection = Backbone.Collection.extend({
        //TODO Somehow bring the dpt id into this path...
        url:'./../../api/departments/2/volunteers'
    });

    return UsersCollection;
});

define([
    'jquery',
    'handlebars',
    'backbone',
    '../models/User'
    ], function($, Handlebars, Backbone, UserModel ) {

    var UsersCollection = Backbone.Collection.extend({
        //TODO Somehow bring the dpt id into this path...
        url:'./../../department/8/volunteers'
    });

    return UsersCollection;
});

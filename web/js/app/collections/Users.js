define([
    'jquery',
    'handlebars',
    'backbone',
    '../models/User'
], function($, Handlebars, Backbone, UserModel) {

    var UsersCollection = Backbone.Collection.extend({
        url: function() {
            var url = $("[name=volunteersUrl]").val();
            console.info("debug collection:Shifts:url - " + url);
            return url;
        }
    });

    return UsersCollection;
});

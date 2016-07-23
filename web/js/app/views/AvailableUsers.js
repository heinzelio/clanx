define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    '../collections/Users',
    'text!./../templates/AvailableUsers.handlebars'
    ],
    function($, _, Backbone, Handlebars, AvailableUsersCollection, AvailableUsersTpl) {

        var view = Backbone.View.extend({
            initialize:function(options){
                console.info("debug view:Users:init");
                this.users = new AvailableUsersCollection();
                this.users.url = $("[name=volunteersUrl]").val();
                this.users.on('sync', $.proxy(this.onCollectionSync, this));
                this.users.fetch();
                this.render();
            },
            render: function () {
                console.info("debug view:Users:render");
            },
            onCollectionSync:function() {
                console.info("debug view:Users:onCollSync");

                var template = Handlebars.compile(AvailableUsersTpl);
                var html = template({users:this.users.toJSON()});
                this.$el.html(html);

                this.trigger('ready');
            }
        });

        return view;

    }
);

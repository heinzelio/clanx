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
                this.users = new AvailableUsersCollection();
                this.users.on('sync', $.proxy(this.onCollectionSync, this));
                this.users.fetch();
                this.render();
            },
            render: function () {
                
            },
            onCollectionSync:function() {

                console.info("=====================");
                console.info(this.users.toJSON());

                var template = Handlebars.compile(AvailableUsersTpl);
                var html = template({users:this.users.toJSON()});
                this.$el.html(html);

                this.trigger('ready');
            }
        });

        return view;

    }
);
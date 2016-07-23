define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    '../collections/Shifts',
    'text!./../templates/ShiftList.handlebars'
    ],
    function($, _, Backbone, Handlebars, ShiftsCollection, ShiftsTpl) {

        var view = Backbone.View.extend({
            initialize:function(options){
                console.info("debug view:Shifts:init");
                this.shifts = new ShiftsCollection();
                this.shifts.url = $("[name=shiftsUrl]").val();
                this.shifts.on('sync', $.proxy(this.onCollectionSync, this));
                this.shifts.fetch();
                this.render();
            },
            render: function () {
                console.info("debug view:Shifts:render");
            },
            onCollectionSync:function() {
                console.info("debug view:Shifts:onCollSync");
                console.info(this.shifts.toJSON());

                var template = Handlebars.compile(ShiftsTpl);
                var html = template({shifts:this.shifts.toJSON()});
                this.$el.html(html); // this removes the existing content

                this.trigger('ready');
            }
        });

        return view;

    }
);

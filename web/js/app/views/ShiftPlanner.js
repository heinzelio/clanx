define([
        'jquery',
        'underscore',
        'backbone',
        'handlebars',
        'custom-jquery-ui',
        'text!./../templates/ShiftPlanner.handlebars',
        './AvailableUsers',
        './Shifts'
    ],
    function($, _, Backbone, Handlebars, CustomJQueryUi,
        ShiftPlannerTpl, UsersView, ShiftsView
    ) {
        'use strict';

        $(document).ready(function() {

            var view = Backbone.View.extend({
                el: $('.content').get(0),
                initialize: function() {
                    this.render();
                    this.afterrender();
                },
                render: function() {
                    console.info("debug view:Planner:render");
                    var template = Handlebars.compile(ShiftPlannerTpl);
                    var html = template();
                    this.$el.html(html);
                },
                afterrender: function() {
                    console.info("debug view:Planner:afterRender");
                    this.usersView = new UsersView({
                        'el': $('.content .clx-available-users').get(0)
                    });
                    this.usersView.on('ready', $.proxy(this.initDragNDrop, this));

                    this.shiftsView = new ShiftsView({
                        'el': $('.clx-shift-list').get(0)
                    });
                },
                initDragNDrop: function() {
                    console.info("debug view:Planner:initDnD");

                    $(".source li").draggable({
                        addClasses: false,
                        appendTo: "body",
                        helper: "clone"
                    });

                    $(".target").droppable({
                        addClasses: false,
                        activeClass: "listActive",
                        accept: ":not(.ui-sortable-helper)",
                        drop: function(event, ui) {
                            $(this).find(".placeholder").remove();
                            var link = $("<a href='#' class='dismiss'>x</a>");
                            var list = $("<li></li>").text(ui.draggable.text());
                            $(list).append(link);
                            $(list).appendTo(this);
                            // updateValues();
                        }
                    }).sortable({
                        items: "li:not(.placeholder)",
                        sort: function() {
                            $(this).removeClass("listActive");
                        },
                        update: function() {
                            // updateValues();
                        }
                    }).on("click", ".dismiss", function(event) {
                        event.preventDefault();
                        $(this).parent().remove();
                        // updateValues();
                    });

                }

            });

            return new view();
        });
    }
);

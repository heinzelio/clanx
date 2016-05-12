define([
    'jquery',
    'underscore',
    'backbone',
    'handlebars',
    'custom-jquery-ui',
    '../collections/ShiftsCollection',
    'text!./../templates/ShiftPlanner.handlebars',
    './AvailableUsers'
    ],
    function($, _, Backbone, Handlebars, CustomJQueryUi, ShiftsCollection, ShiftPlannerTpl, AvailableUsersView) {
        'use strict';

        $(document).ready(function() {

            var view = Backbone.View.extend({
                el: $('.content').get(0),
                initialize:function(){
                    this.render();
                    this.afterrender();
                },
                render: function () {
                    var template = Handlebars.compile(ShiftPlannerTpl);
                    //var html = template(ShiftsCollection.toJSON());
                    var html = template();
                    this.$el.html(html);

                },
                afterrender:function(){
                    this.availableUsersView = new AvailableUsersView({'el':$('.content .clx-available-users').get(0)});
                    this.availableUsersView.on('ready', $.proxy(this.initDragNDrop, this));
                },
                initDragNDrop: function () {

                    console.info('init drag and drop');

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
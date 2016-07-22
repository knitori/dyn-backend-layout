
define(['jquery'], function($){

    var FlexLayout = {

    };


    FlexLayout.init = function(){
        this.canvas = $('.flex-layout-canvas');
        this.field = $('input[name="' + this.canvas.attr('data-canvas-for') + '"]');

        this.layout = [
            {size: 12, items: []},
            {size: 12, items: [
                {size: 6, items: []},
                {size: 6, items: []}
            ]},
            {size: 12, items: [
                {size: 8, items: [
                    {size: 12, items: []},
                    {size: 12, items: []}
                ]},
                {size: 4, items: []}
            ]}
        ];
    };

    FlexLayout.emptyContainer = function(container) {
        container.text('');
        container.html('');
        container.children().remove();
    };

    FlexLayout.buildList = function (list, container) {
        var i, obj, child, tools;

        for(i=0; i<list.length; i++) {
            obj = list[i];

            child = $('<div></div>');
            child.addClass('flc-' + obj.size);
            container.append(child);

            if(obj.items.length > 0) {
                this.buildList(obj.items, child);
            } else {
                tools = $('<div class="flc-tools"></div>');
                tools.append($('<div class="flc-icon flc-v"><i class="fa fa-arrows-v"></i></div>'));
                tools.append($('<div class="flc-icon flc-h"><i class="fa fa-arrows-h"></i></div>'));
                tools.appendTo(child);
                $('<div class="flc-inner"></div>').appendTo(child);
            }
        }
    };

    FlexLayout.buildLayout = function(){

        this.emptyContainer(this.canvas);
        this.buildList(this.layout, this.canvas);
        this.enableTools();
    };

    FlexLayout.enableTools = function() {
        var that = this;
        this.canvas
            .find('.flc-icon')
            .off('click')
            .on('click', function(e) { that.splitColumn.call(this, that, e) });
    };

    FlexLayout.splitColumn = function(that, event){
        var btn = $(this);
        var type = btn.hasClass('flc-v') ? 'v' : (btn.hasClass('flc-h') ? 'h' : null);
        if (type == null) {
            console.error('Unknown button type');
            return;
        }
        var container = btn.parent().parent();
        if(type == 'h') {
            that.emptyContainer(container);
            that.buildList([
                {size: 6, items: []},
                {size: 6, items: []}
            ], container);
            that.enableTools();
        } else if(type == 'v') {
            var size = parseInt(container.attr('class').substr(4));
            if(size == 12) {
                container.clone().insertAfter(container);
            } else {
                that.emptyContainer(container);
                that.buildList([
                    {size: 12, items: []},
                    {size: 12, items: []}
                ], container);
            }
            that.enableTools();
        }
    };

    $(function() {
        FlexLayout.init();
        FlexLayout.buildLayout();
    });


    return FlexLayout;
});

/**
 * Created by Shoom on 16.06.15.
 */


var Shm_rel_tooltip = Backbone.View.extend({
    tagName: 'div',
    className: 'rel_tooltip',
    options: {
        html: '',
        target: null
    },
    initialize: function(){
        this.$el.html('<div class="content"></div>');
        this.add_close_btn();
        this.options.target.parent().append(this.el);
        this.content();
    },
    content: function(html){
        if(html) this.options.html = html;
        this.$el.children('.content').html(this.options.html);
        return this;
    },
    show: function(){
        this.$el.css(this.options.target.position()).show(300);
    },
    hide: function(){
        var th = this;
        this.$el.hide(300, function(){
            th.destroy();
        });
    },
    wait: function(){
        this.content('<img src="images/tooltip_loading.gif">');
        return this;
    },
    destroy: function () {
        this.undelegateEvents();
        this.$el.removeData().unbind();
        this.remove();
        Backbone.View.prototype.remove.call(this);
    },
    add_close_btn: function(){
        var th = this;
        var btn = $('<button class="close_tooltip"></button>');
        btn.click(function(){
            th.hide();
        });
        this.$el.append(btn);
    }
});
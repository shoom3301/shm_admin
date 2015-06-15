/**
 * Created by Shoom on 14.06.15.
 */

var Shm_add_form = Backbone.Model.extend({
    defaults: {
        el: null,
        table: null
    },
    initialize: function(){
        this.el = $(this.get('el'));
        this.set('primary', this.el.data('primary'));
        this.set('name', this.el.data('name'));

        var th = this;

        this.el.find('button.create').click(function(){
            th.create(this, $(this).parents('tr'));
        });
    },
    create: function(button, $tr){
        if(confirm('Вы действительно хотите создать запись?')){
            button.disabled = true;
            var data = {};
            $tr.find('input[type="text"]').each(function(){
                var $th = $(this);
                data[$th.data('col')] = $th.val();
            });

            var th = this;

            $.ajax({
                url: location.origin+location.pathname,
                data: {
                    action: 'create',
                    table: this.get('name'),
                    key: this.get('primary'),
                    record: JSON.stringify(data)
                },
                dataType: 'json',
                success: function(record){
                    if(record != 0){
                        th.get('table').createField(record);
                    }
                    button.disabled = false;
                    th.el.find('input[type="text"]').val('');
                },
                error: function(){
                    button.disabled = false;
                }
            });
        }
    }
});
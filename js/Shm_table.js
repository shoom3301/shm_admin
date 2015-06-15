/**
 * Created by Shoom on 13.06.15.
 */

var Shm_table = Backbone.Model.extend({
    defaults: {
        el: null
    },
    initialize: function(){
        this.el = $(this.get('el'));
        this.set('primary', this.el.data('primary'));
        this.set('name', this.el.data('name'));

        this.initRecord(this.el.find('tbody'));

        var add_form = this.el.find('.shm_add_form');
        if(add_form && add_form[0]){
            new Shm_add_form({
                el: add_form[0],
                table: this
            });
        }
    },
    initRecord: function($record){
        var th = this;

        $record.find('input[type="text"]').each(function(i, input){
            th.initField(input);
        });

        $record.find('button.remove').click(function(){
            th.deleteRecord($(this).parents('tr'));
        });
    },
    initField: function(input){
        var th = this;

        input.default_value = input.value;
        $(input).on('blur', function(){
            th.updateField(this);
        });
    },
    createField: function(data){
        var $tbody = this.el.find('tbody');
        var $record = $tbody.find('tr:first-child').clone();
        for(var v in data){
            if(data.hasOwnProperty(v)){
                var inp = $record.find('input[data-col="'+v+'"]');
                inp.val(data[v]);
                inp[0].default_value = data[v];
            }
        }

        this.initRecord($record);

        $tbody.append($record);
    },
    updateField: function(input){
        if(input.value != input.default_value){
            var $inp = $(input);
            $inp.attr('disabled', true);

            this.request('update', {
                id: this.getId(input),
                col: $inp.data('col'),
                val: $inp.val()
            }, function(e){
                if(e == 1){
                    input.default_value = input.value;
                    $inp.attr('disabled', false);
                }else{
                    input.value = input.default_value;
                    $inp.attr('disabled', false);
                }
            }, function(){
                input.value = input.default_value;
                $inp.attr('disabled', false);
            });
        }
    },
    deleteRecord: function($tr){
        if(confirm('Вы действительно хотите удалить запись?')){
            this.request('delete', {
                remove: this.get('name'),
                id: this.getId($tr.find('input'))
            }, function(e){
                if(e==1){
                    $tr.remove();
                }
            });
        }
    },
    request: function(action, data, success, error){
        var _data = {
            action: action,
            table: this.get('name'),
            key: this.get('primary')
        };

        for(var v in data){
            if(data.hasOwnProperty(v)){
                _data[v] = data[v];
            }
        }

        return $.ajax({
            url: location.origin+location.pathname,
            data: _data,
            dataType: 'json',
            success: success,
            error: error
        });
    },
    getId: function(input){
        return $(input).parents('tr').find('input[data-col="'+this.get('primary')+'"]')[0].default_value;
    }
});
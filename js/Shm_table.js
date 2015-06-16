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
            var s_add_form = new Shm_add_form({
                el: add_form[0],
                table: this
            });

            this.initRelList(s_add_form.el.find('input[type="text"]'));
        }

        this.initViewRelations();
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
        this.initRelList(
            $(input).on('blur', function(){
                th.updateField(this);
            })
        );
    },
    initRelList: function($inp){
        var th = this;
        $inp.on('focus', function(){
            var $th = $(this);
            var rel = th.getColumnRel($th);
            if(rel){
                var tooltip = new Shm_edit_list({
                    target: $th,
                    html: ''
                });
                tooltip.wait().show();

                th.request('get_list', {
                        target: rel[0]
                    }, function(table){
                        var $table = $(table);
                        init_shm_table($table);
                        tooltip.content($table).initTable(rel[1]).centering();
                        setTimeout(function(){
                            tooltip.centering();
                        }, 50);
                    }, function(){},
                    {
                        dataType: 'html',
                        cache: 'target'
                    });
            }
        });
    },
    createField: function(data){
        var $tbody = this.el.find('tbody');
        var prot = $tbody.find('tr:first-child');
        if(!prot || !prot[0]){
            location.reload();
        }
        var $record = prot.clone();
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
    requestCache: {},
    request: function(action, data, success, error, params){
        if(params && params.cache){
            var cached = this.requestCache[action+data[params.cache]];
            if(cached){
                success(cached);
                return false;
            }
        }

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

        var th = this;
        var _params = {
            url: location.origin+location.pathname,
            data: _data,
            dataType: 'json',
            success: function(res){
                if(params && params.cache){
                    th.requestCache[_data.action+_data[_params.cache]] = res;
                }
                if(success) success(res);
            },
            error: error
        };

        if(params){
            for(var s in params){
                if(params.hasOwnProperty(s)){
                    _params[s] = params[s];
                }
            }
        }

        return $.ajax(_params);
    },
    getId: function(input){
        return $(input).parents('tr').find('input[data-col="'+this.get('primary')+'"]')[0].default_value;
    },
    getColumnRel: function($cell){
        if($cell[0].tagName.toLowerCase() != 'td'){
            $cell = $cell.parents('td');
        }
        var $td = $(this.el.find('thead td')[$cell.index()]);
        var $inp = $td.children('input.rel_source');
        if($inp && $inp[0]){
            return $inp.val().split('::');
        }
        return false;
    },
    initViewRelations: function(){
        var th = this;
        this.el.find('a.related_field').click(function(e){
            var $th = $(this);
            var rel = th.getColumnRel($th);
            var tooltip = new Shm_rel_tooltip({
                target: $th,
                html: ''
            });
            tooltip.wait().show();

            th.request('get_relation', {
                target: rel[0],
                field: rel[1],
                value: $th.html()
            }, function(table){
                var $table = $(table);
                init_shm_table($table);
                tooltip.content($table);
            }, function(){
            }, {
                dataType: 'html'
            });

            e.preventDefault();
            return false;
        });
        return this;
    }
});
/**
 * Created by Shoom on 16.06.15.
 */

var Shm_edit_list = Shm_rel_tooltip.extend({
    initTable: function(field){
        var th = this;

        var $tbl = this.options.html;
        $tbl.find('thead tr').append('<td>Выбрать</td>');
        $tbl.find('tbody tr').each(function(){
            var $tr = $(this);
            var $td = $('<td><button>select</button></td>');
            $tr.append($td);
            $td.children('button').click(function(){
                var val = $tr.find('td[data-col="'+field+'"]').text();
                th.destroy();
                th.options.target.val(val).blur();
            });
        });

        return this;
    }
});
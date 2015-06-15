function char_repeat(char, count){
    var res = '';
    for(var i=0; i<count; i++){
        res += char;
    }
    return res;
}

$(function(){
    $('.shm_table').each(function(){
        new Shm_table({
            el: this
        });
    });

    $('input[data-shm-type="number"]').each(function(){
        var $th = $(this);
        $th.filter_input({
            regex:'[0-9]'
        });
    });
});
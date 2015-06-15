function init_shm_table($table){
    ($table || $('.shm_table')).each(function(){
        new Shm_table({
            el: this
        });
    });
}

$(function(){
    init_shm_table();

    $('input[data-shm-type="number"]').each(function(){
        var $th = $(this);
        $th.filter_input({
            regex:'[0-9]'
        });
    });
});
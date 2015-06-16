<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 13.06.15
 * Time: 21:42
 */

/**
 * Конструктор поля
 */
class Shm_field {
    /**
     * @param $column Array столбец
     */
    public function __construct($column){
        $this->column = $column;
        $this->length = $this->column['length'];
    }

    /**
     * Рендерер ячейки
     * @param $type String тип рендеринга (вид, редактирование, добавление)
     * @param $value String значение ячейки
     */
    public function render($type, $value = ''){
        if($type=='view'){
            if(isset($this->column['rel'])){
                echo '<a class="related_field">'.$value.'</a>';
            }else{
                echo '<span>'.$value.'</span>';
            }
        }elseif($type=='edit'){
            echo '<input
                type="text"
                value="'.$value.'"
                data-col="'.$this->column['id'].'"
                />';
        }elseif($type=='add'){
            echo '<input type="text" data-col="'.$this->column['id'].'"/>';
        }
    }
}
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
     * @param $field String название столбца
     * @param $value String значение ячейки
     */
    public function render($type, $field, $value = ''){
        if($type=='view'){
            echo $value;
        }elseif($type=='edit'){
            echo '<input
                type="text"
                value="'.$value.'"
                data-col="'.$field.'"
                />';
        }elseif($type=='add'){
            echo '<input type="text" data-col="'.$field.'"/>';
        }
    }
}
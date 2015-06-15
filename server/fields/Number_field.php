<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 14.06.15
 * Time: 15:06
 */

class Number_field extends Shm_field{
    public function render($type, $value = ''){
        if($type=='view'){
            echo $value;
        }elseif($type=='edit'){
            echo '<input
                data-shm-type="number"
                maxlength="'.$this->length.'"
                type="text"
                value="'.$value.'"
                data-col="'.$this->column['id'].'"
                />';
        }elseif($type=='add'){
            echo '<input
            type="text"
            data-shm-type="number"
            maxlength="'.$this->length.'"
            data-col="'.$this->column['id'].'"/>';
        }
    }
}
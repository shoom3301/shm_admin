<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 13.06.15
 * Time: 20:48
 */

include_once('Shm_field.php');
include_once('fields/Number_field.php');

/**
 * Модель таблицы
 */
class Shm_table {
    //Название таблицы
    public $name = '';
    //Записи таблицы
    private $records = array();
    //Столбцы таблицы
    private $columns = array();
    //Ключевое поле таблицы
    private $primary = '';
    //Ключевое слово для ключевого поля
    private $fields_renders = array();
    //Ключевое слово для ключевого поля
    public static $primary_name = 'PRI';
    //Регулярка для парсинга типа и длинны поля
    public static $column_type_regex = "/^(\w+)\(?(\d*)\)?\s?(\w*)$/";

    /**
     * Инициализация БД
     * @param $table_name String название таблицы
     * @param $fields Array дополнительные настройки полей
     * @param $fields_renders Array модели рендеринга полей
     */
    public function __construct($table_name, $fields = array(), $fields_renders = array()){
        $this->name = $table_name;
        $this->records = $this->table()->find_array();

        $heading = $this->table()->raw_query('SHOW FULL COLUMNS FROM '.$this->name)->find_array();

        foreach($heading as $col){
            if($col['Key'] == self::$primary_name){
                $this->primary = $col['Field'];
            }

            preg_match(self::$column_type_regex, $col['Type'], $type_res);

            $name = $col['Comment']?$col['Comment']:$col['Field'];

            $type = $type_res[1];
            $length = $type_res[2];

            $col_res = array('id' => $col['Field'], 'name' => $name, 'type' => $type, 'length' => $length);

            $column = isset($fields[$col['Field']])
                ?self::init_column($fields[$col['Field']], $col_res)
                :$col_res;

            $this->columns[] = $column;

            $this->fields_renders[$column['id']] = isset($fields_renders[$column['id']])
                ?$fields_renders[$column['id']]($column)
                :Shm_database::field_auto_type($column);
        }
    }

    /**
     * ORM модель таблицы
     */
    private function table(){
        return ORM::for_table($this->name);
    }

    /**
     * Создание кастомного поля
     * @param $field Array кастомные данные
     * @param $col_res Array данные из БД
     * @return Array кастомное поле
     */
    private static function init_column($field, $col_res){
        foreach($field as $key => $val){
            $col_res[$key] = $val;
        }
        return $col_res;
    }

    /**
     * Рендерер заголовка таблицы при просмотре
     */
    public function render_view_heading(){
        echo '<thead><tr>';
        foreach($this->columns as $column){
            echo '<td data-col="'.$column['id'].'">';
            echo $column['name'];
            echo '</td>';
        }
        echo '</tr></thead>';
    }

    /**
     * Рендерер заголовка таблицы при редактировании
     */
    public function render_edit_heading(){
        echo '<thead><tr>';
        foreach($this->columns as $column){
            echo '<td data-col="'.$column['id'].'">';
            echo $column['name'];
            echo '</td>';
        }
        echo '<td>Действие</td>';
        echo '</tr></thead>';
    }

    /**
     * Рендерер тела таблицы при просмотре
     */
    public function render_view_body(){
        echo '<tbody>';

        foreach($this->records as $record){
            echo '<tr>';
            foreach($record as $field => $value){
                echo '<td data-col="'.$field.'">';
                echo $this->fields_renders[$field]->render('view', $field, $value);
                echo '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody>';
    }

    /**
     * Рендерер тела таблицы при редактировании
     */
    public function render_edit_body(){
        echo '<tbody>';

        foreach($this->records as $i => $record){
            echo '<tr data-num="'.$i.'">';
            foreach($record as $field => $value){
                echo '<td>';
                echo $this->fields_renders[$field]->render('edit', $field, $value);
                echo '</td>';
            }
            echo '<td><button class="remove">remove</button></td>';
            echo '</tr>';
        }

        echo '</tbody>';
    }

    /**
     * Рендерер просмотра таблицы
     */
    public function render_view(){
        echo '<table class="shm_table">';

        $this->render_view_heading();
        $this->render_view_body();

        echo '<table>';
    }

    /**
     * Рендерер редактирования таблицы
     */
    public function render_edit(){
        echo '<table class="shm_table" data-primary="'.$this->primary.'" data-name="'.$this->name.'">';

        $this->render_edit_heading();
        $this->render_edit_body();
        $this->render_add_form();

        echo '</table>';
    }

    /**
     * Рендерер формы добавления записи
     */
    public function render_add_form(){
        echo '<tfoot class="shm_add_form" data-primary="'.$this->primary.'" data-name="'.$this->name.'"><tr>';

        foreach($this->columns as $column){
            echo '<td>';
            echo $this->fields_renders[$column['id']]->render('add', $column['id']);
            echo '</td>';
        }
        echo '<td><button class="create">create</button></td>';

        echo '</tr></tfoot>';
    }
}
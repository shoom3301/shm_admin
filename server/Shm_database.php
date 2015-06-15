<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 13.06.15
 * Time: 23:05
 */

/**
 * Модель управления БД
 */
class Shm_database {
    /**
     * Таблицы
     */
    private $tables = array();
    private $tables_names = array();
    private $view_params = array();
    private $edit_params = array();

    /**
     * Инициализация БД
     * @param $db_name String  название бд
     * @param $tables_names Array названия таблиц
     * @param $params Array дополнительные параметры
     */
    public function __construct($db_name, $tables_names = Array(), $params = array()){
        $this->name = $db_name;
        $this->tables_names = $tables_names;
        $tables = ORM::for_table($this->name)->raw_query('SHOW TABLES FROM `'.$this->name.'`')->find_array();
        if(isset($tables[0])){
            $key = key($tables[0]);

            foreach($tables as $table){
                $this->tables[] = $table[$key];
            }
        }

        if(isset($params['view'])){
            $this->view_params = $params['view'];
        }

        if(isset($params['edit'])){
            $this->edit_params = $params['edit'];
        }
    }

    /**
     * Визуализация списка таблиц
     */
    public function render_list(){
        foreach($this->tables as $table){
            echo '<p>';
            echo '<h3>'.$this->table_name($table).'</h3>';
            echo '<a href="?view='.$table.'">Смотреть</a>';
            echo '<br>';
            echo '<a href="?edit='.$table.'">Редактировать</a>';
            echo '</p>';
            echo '<br>';
        }
    }

    /**
     * Название таблицы
     */
    private function table_name($table){
        return isset($this->tables_names[$table])?$this->tables_names[$table]:$table;
    }

    /**
     * Редактирование таблицы
     */
    public function edit_table($table_name){
        $table = new Shm_table($table_name, $this, $this->edit_params['fields'], $this->edit_params['renders']);
        $table->render_edit();
    }

    /**
     * Отображение таблицы
     */
    public function view_table($table_name){
        $table = new Shm_table($table_name, $this, $this->view_params['fields'], $this->view_params['renders']);
        $table->render_view();
    }

    /**
     * Автоопределение типа поля
     * @param $column Array столбец
     * @return Shm_field конструктор ячейки
     */
    public static function field_auto_type($column){
        switch($column['type']){
            case 'int':
                return new Number_field($column);
                break;
            default:
                return new Shm_field($column);
            break;
        }
    }
} 
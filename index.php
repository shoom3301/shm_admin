<?php
/**
 * Created by PhpStorm.
 * User: Shoom
 * Date: 13.06.15
 * Time: 20:43
 */

include_once('Util.php');
include_once('server/Shm_database.php');
include_once('server/Shm_table.php');

Util::DBConnect();

/**
 * Обработка CUD запросов
 */
if(isset($_REQUEST['action'])){
    /**
     * Назначаем ключевое поле
     */
    ORM::configure('id_column_overrides', array(
        $_REQUEST['table'] => $_REQUEST['key']
    ));

    /**
     * Модель таблицы
     */
    $table = ORM::for_table($_REQUEST['table']);

    switch($_REQUEST['action']){
        /**
         * Создание записи
         */
        case 'create':
            $record = $table->create();

            foreach(json_decode($_REQUEST['record']) as $key => $val){
                if($val!='') $record->set($key, $val);
            }

            if($record->save()){
                echo json_encode($record->as_array());
            }else{
                echo 0;
            }
            return 1;
            break;
        /**
         * Получение записи
         */
        case 'read':
            echo json_encode(
                ORM::for_table($_REQUEST['target'])
                ->where($_REQUEST['field'], $_REQUEST['value'])
                ->find_one()
                ->as_array()
            );
            return 1;
            break;
        /**
         * Редактирование записи
         */
        case 'update':
            echo $table
                ->where($_REQUEST['key'], $_REQUEST['id'])
                ->find_one()
                ->set($_REQUEST['col'], $_REQUEST['val'])
                ->save()
            ;
            return 1;
            break;
        /**
         * Удаление записи
         */
        case 'delete':
            echo $table
                ->where($_REQUEST['key'], $_REQUEST['id'])
                ->find_one()
                ->delete()
            ;
            return 1;
            break;
    }
}

/**
 * Модель управления БД
 */
$db = new Shm_database('xface', array(
    'accesses' => 'Доступы',
    'courses' => 'Курсы',
    'languages' => 'Языки',
    'lessons' => 'Занятия',
    'students' => 'Студенты',
    'teachers' => 'Преподаватели',
    'tests' => 'Тесты',
    'users_groups' => 'Группы пользователей',
    'sex' => 'Пол'
), array(
    'view' => array(
        'fields' => array(
            'language_id' => array(
                'name' => 'НОЗвание языка'
            )
        ),
        'renders' => array()
    ),
    'edit' => array(
        'fields' => array(),
        'renders' => array(
            'language_id' => function($column){
                    return new Shm_field($column);
                }
        )
    )
));

if(isset($_REQUEST['action'])){
    switch($_REQUEST['action']){
        case 'relation':
            (new Shm_table($_REQUEST['target'], $db, array(), array(), true))->render_view();
            return 1;
            break;
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Shoom layout</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="copyright" content="Shoom" />

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css" type="text/css" />

    <script type="text/javascript" src="lib/jquery-min.js"></script>
    <script type="text/javascript" src="lib/underscore-min.js"></script>
    <script type="text/javascript" src="lib/backbone-min.js"></script>

    <script type="text/javascript" src="lib/filter_input.js"></script>

    <script type="text/javascript" src="js/shm.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/Shm_rel_tooltip.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/Shm_table.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/Shm_add_form.js" charset="utf-8"></script>

    <script type="text/javascript" src="js/main.js" charset="utf-8"></script>
</head>
<body>
<div id="main">
    <div id="rel_tooltip"></div>
    <div class="shm_table_form">
    <?php
    /**
     * Визуализация редактирования таблицы
     */
    if(isset($_REQUEST['edit'])){
        $db->edit_table($_REQUEST['edit']);
    /**
     * Визуализация редактирования таблицы
     */
    }elseif(isset($_REQUEST['view'])){
        $db->view_table($_REQUEST['view']);
    /**
     * Список таблиц
     */
    }else{
        $db->render_list();
    }
    ?>
    </div>
</div>
</body>
</html>
<?php
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}


/**
 * Считает количество категорий дел
 * @param $business массив заданий
 * @param $title название категории
 * @return int вохвращает кол-во категорий
 */
function count_title($business, $title){
    $index = 0;
    foreach ($business as $taskes) {
        if ($taskes['project_id'] === $title['id'])
        {
            $index++;
        };
    }
    return $index;
}

function count_categories($tasks, $category) {
    $index = 0;
    foreach ($tasks as $task) {
        if ($task['project_id'] === $category['id'])
        {
            $index++;
        };
    }
    return $index;
}

/**
 * Функция проверки на корректность выбора категории и вывода ошибки 404 в случае неудачи
 * @param $list_category массив проектов
 * @param $choosen_project id выбранного проекта на английском языке
 */

function check_response($list_category,$choosen_project){
    $categories_id = array_column($list_category, 'alias');
    if ( $_GET['category']!='null' && !in_array($choosen_project, $categories_id)){
        http_response_code(404);
        die('error 404!');
    }
}

/**
 * Функция соотношения названия проекта и url в ссылке
 * @param $list_category массив проектов, с которым будет вестись соотношение
 * @param $business массив заданий
 */
function view_tasks($list_category,$business){
    foreach ($list_category as $category) {
        if(in_array($category['id'], $business)){
            echo "is!";
        }
    }
}

function get_all_tasks(mysqli $con){
    $sql_all_tasks = 'SELECT t.title,t.project_id,t.user_id,t.status,t.task_crete, t.file, t.deadline  FROM users u
                    INNER JOIN tasks t
                    ON u.id = t.user_id
                    WHERE u.id = 3;';
    $res_all_tasks = mysqli_query($con, $sql_all_tasks);
    return mysqli_fetch_all($res_all_tasks, MYSQLI_ASSOC);
}

function get_categories(mysqli $con){
    $sql_categories = 'SELECT name,id,alias FROM projects
                    WHERE user_id = 3;';
    $res_categories = mysqli_query($con, $sql_categories);
    return mysqli_fetch_all($res_categories, MYSQLI_ASSOC);
}

function get_tasks_by_categories(mysqli $con,$id_choosen_project){
    if($id_choosen_project === -1){
        $sql_tasks = 'SELECT t.title,t.project_id,t.user_id,t.status,t.task_crete, t.file,t.deadline FROM users u
                INNER JOIN tasks t
                ON u.id = t.user_id
                WHERE u.id = 3;';
        $res_tasks = mysqli_query($con , $sql_tasks);
        return mysqli_fetch_all($res_tasks, MYSQLI_ASSOC);
    }
    else
    {
        $sql_tasks = 'SELECT t.title,t.project_id,t.user_id,t.status,t.task_crete, t.file, t.deadline FROM users u
                INNER JOIN tasks t
                ON u.id = t.user_id
                WHERE u.id = 3
                AND t.project_id = '.$id_choosen_project.';';
        $res_tasks = mysqli_query($con , $sql_tasks);
        return mysqli_fetch_all($res_tasks, MYSQLI_ASSOC);
    }
}



function move_file_to_uploads(){
    $file_name = $_FILES['file']['name'];
    $file_path = __DIR__ . '/uploads/';
    //$file_url = '/uploads/' . $file_name;
    $file_url = $file_name;
    move_uploaded_file($_FILES['file']['tmp_name'], $file_path.$file_name);
    return $file_url;
    }





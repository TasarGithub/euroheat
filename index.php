<?php # одна точка входа всех запросов на сайт

### отладка
# print_r($_GET);
# print_r($_POST);
# print_r($_FILES);
echo "ggggggg";
include('app/loader.php');

# анализируем запрос и передаем его соответствующему конроллеру
$router->delegate();
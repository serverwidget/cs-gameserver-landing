<?php
$dbhost = ""; // Введите хост базы данных
$dbuser = ""; // Введите имя пользователя
$dbpassword = ""; // Введите пароль
$dbname = ""; // Введите имя базы данных
 
// Подключаемся к mysql серверу (здесь ничего не менять)
$link = mysql_connect($dbhost, $dbuser, $dbpassword);
// Выбираем нашу базу данных (здесь ничего не менять)
mysql_select_db($dbname, $link);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<title>Entra</title>
<meta charset="utf-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
<link type="text/css" rel="stylesheet" href="css/style.css">
<meta name="viewport" content="width=1024">
<link href="favicon.ico" rel="icon" type="image/x-icon" />
</head>
<body>
<div class="main_wrapper">
<?php

function formatTime($seconds) {
  $days   = floor($seconds / 86400);
  $hours   = floor(($seconds - ($days * 86400)) / 3600);
  $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);

  $seconds = $seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60);

  if ($hours && $hours < 10) { $hours = "0".$hours; }
  if ($minutes < 10) { $minutes = "0".$minutes; }
  if ($seconds < 10) { $seconds = "0".$seconds; }

  return ($days ? $days.'d ' : '').($hours ? $hours.':' : '').$minutes.':'.$seconds;
}

  // Скрипт serverwidget
  // Вам необходимо добавить сервер в панели управления.
  // Вам необходимо зарегистрироваться на serverwidget.com и в разделе "Настройки" -> "Доступ к API" скопировать свой Token ключ.
  $server_address = '31.28.168.178:27036';
  $map_image = 'img/maps/noimage.jpg';

  include 'serverwidget.api.php';

  $API = new ServerWidgetAPI('токен_ключ'); // Вот здесь необходимо вставить ваш токен ключ.
  $serverInfo = $API->method("server.get", array(
    "address" => $server_address,
    "fields" => "players,map"
  ));

  if (count($serverInfo['result']) && is_array($serverInfo['result'])) {
    $server = $serverInfo['result'][0];
    $players = $API->method("server.players", array("address" => $server_address));
  }

  $img_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.'maps'.DIRECTORY_SEPARATOR;

  $w = 0;

  if (is_array($server)) {
    if (file_exists($img_dir.$server['map']['name'].'.jpg')) {
      $map_image = 'img/maps/'.$server['map']['name'].'.jpg';
    }

    echo '<div class="background_map"><img width="100%" height="100%" src="'.$map_image.'" /></div>';
    echo '<div class="info">
    Сервер: '.$server_address.'
    <br>
    Название: '.$server['name'].'
    <br>
    Карта: '.$server['map']['name'].'
    <br>
    Игроков: '.$server['players']['now'].'/'.$server['players']['max'].'
    <br>
    Статус: '.($server['online'] ? 'On-line' : 'Offline').'
    <br></div>';

    $w = @ceil($server['players']['now'] / $server['players']['max'] * 100);
  } else {
    echo 'Статус: Off-line';
  }

// Конец скрипта serverwidget
?>

<div class="percent_of_filled">
  <div class="background_pof">
    <div class="text_pof">
      <?=$server['players']['now'];?> / <?=$server['players']['max'];?>
    </div>
    <div class="level_pof" style="width: <?=$w;?>%;">
    </div>
  </div>
</div>
<div class="side_menu">
  <ul>
    <li class="active"><a href="#">Информация</a></li>
    <li><a href="#">Описание сервера</a></li>
    <li><a href="#">Групппа ВК</a></li>
  </ul>
</div>
</div>
<div class="wrapper">
  <div class="new_bans">
    <center><p><i class="fa fa-ban"></i> СВЕЖИЕ БАНЫ</p></center>
    <table>
      <thead>
        <tr>
          <th id="nickbanned" align="left">Ник</th>
          <th id="reasonbanned" align="left" width="100px">Причина</th>
          <th align="right">Срок</th>
        </tr>
      </thead>
      <tbody>
        <?php
          @include "./config.php";
          $result=mysql_query("SELECT * FROM `amx_bans` ORDER BY `ban_created` DESC LIMIT 5" );
          while($row=mysql_fetch_array($result)) {
          echo '

          <tr>
            <td id="nickbanned" align="left">'.$row['player_nick'].'</td>
            <td id="reasonbanned" align="left">'.$row['ban_reason'].'</td>
            <td align="right">'. (($row['ban_length'] == 0) ? 'Навсегда' : $row['ban_length']) .'</td>
          </tr>

          ';
          }
        ?>
      </tbody>
    </table>
    <a class="watch_all" href="http://entra.xban.info/bans/bans/index.html">Смотреть всех</a>
  </div>
	<div class="new_admins"><center><p><i class="fa fa-user-plus"></i> НОВЫЕ АДМИНЫ</p></center>
  <table>
    <thead>
      <tr>
        <th id="nickadmin" align="left">Никнейм админа</th>
        <th align="right">Кол-во дней</th>
      </tr>
    </thead>
    <tbody>
      <?php
      @include "./config.php";
      $result=mysql_query("SELECT * FROM `amx_amxadmins` ORDER BY `created` DESC LIMIT 5" );// делаем выборку из таблицы
      while($row=mysql_fetch_array($result)) { // берем результаты из каждой строки
      echo '

       <tr>
          <td id="nickadmin" align="left">'.$row['nickname'].'</td>
          <td align="right">'. (($row['days'] == 0) ? 'Навсегда' : $row['days']) .'</td>
       </tr>

      ';
      }
      ?>
    </tbody>
  </table>
  <a class="watch_all" href="http://entra.xban.info/bans/amxadmins/index.html">Смотреть всех</a></div>
  <div class="players_on_server">
    <? if (isset($serverInfo['result'])): ?>
    <center><p><i class="fa fa-users"></i> ИГРОКИ НА СЕРВЕРЕ</p></center>
		<!-- Подрубаем библиотеку jQuery -->
<script src='http://code.jquery.com/jquery-1.7.1.js'></script>
<script>
function formatTime(seconds) {
  var days   = Math.floor(seconds / 86400);
  var hours   = Math.floor((seconds - (days * 86400)) / 3600);
  var minutes = Math.floor((seconds - (days * 86400) - (hours * 3600)) / 60);
  seconds = seconds - (days * 86400) - (hours * 3600) - (minutes * 60);
  if (hours && hours   < 10) {hours = "0"+hours;}
  if (minutes < 10) {minutes = "0"+minutes;}
  if (seconds < 10) {seconds = "0"+seconds;}
  return (days ? days+'d ' : '')+(hours ? hours+':' : '')+minutes+':'+seconds;
}
function updatePlayerTime(elements) {
  elements.each(function() {
    var element = $(this);
    var time = parseInt(element.data('time')) || 0;
    time++;
    element.data('time', time);
    element.html(formatTime(time));
  });
  if (elements.length) {
    setTimeout(function() {
      updatePlayerTime(elements);
    }, 1000);
  }
}
</script>
		<table class="list_of_players" width="100%" cellpadding="5" cellspacing="0">
			<tr>
				<th align="left" style="width:10px">#</th>
				<th align="left">Ник</th>
				<th align="center">Очки</th>
				<th align="right">Время в игре</th>
			</tr>
		<? if (count($players['result'])): ?>
		<? foreach ($players['result'] as $idx => $player): ?>
			<tr<? if ($idx%2 !== 0): ?> class="dark"<? endif; ?>>
				<td align="left"><?=($idx + 1);?></td>
				<td align="left"><?=htmlspecialchars($player['name']);?></td>
				<td align="center"><?=$player['score'];?></td>
				<td align="right" data-time="<?=$player['time'];?>"><?=formatTime($player['time']);?></td>
			</tr>
		<? endforeach; ?>
		<? else: ?>
			<tr>
				<td colspan="4"><div class="empty_table">Игроков нет</div></td>
			</tr>
		<? endif; ?>
		</table>

<script>
updatePlayerTime($('[data-time]'));
</script>

      <? else: ?>
          <div class="empty_table"><?=$serverInfo['error']['error_msg'];?></div>
      <? endif; ?>

  </div>
</div>
</body>
</html>

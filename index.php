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

    $server = '31.28.168.178'; // Здесь вписывайте IP сервера
    $port = '27036'; // Здесь порт сервера

    function GetServerInfo($server,$port) {
    $fp = @fsockopen("udp://".$server, $port);
    if (!$fp) return false;
    @fwrite($fp,"\xFF\xFF\xFF\xFF\x54\x53\x6F\x75\x72\x63\x65\x20\x45\x6E\x67\x69\x6E\x65\x20\x51\x75\x65\x72\x79\x00".chr(10));
    $start=time();
    socket_set_timeout($fp,1);
    $st=fread($fp,1);
    $r=socket_get_status($fp);
    $result['status'] = $r["timed_out"];
    $r=$r["unread_bytes"];
    if ($r == 0) { @fclose($fp); return false;}
    $st.=fread($fp,$r);
    @fclose($fp);
    $st=substr($st,5);
    $address=SubStr($st,0,StrPos($st,chr(0)));
    $address=str_replace(chr(0),"|",$address);
    $st=SubStr($st,StrPos($st,chr(0))+1);
    $name=SubStr($st,0,StrPos($st,chr(0)));
    $st=SubStr($st,StrPos($st,chr(0))+1);
    $map=SubStr($st,0,StrPos($st,chr(0)));
    $st=SubStr($st,StrPos($st,chr(0))+1);
    $st=SubStr($st,StrPos($st,chr(0))+1);
    $st=SubStr($st,StrPos($st,chr(0))+1);
    $current=ord(SubStr($st,0,1));
    $max=ord(SubStr($st,1,1));
    if ($map == "") return false;
    $result['map'] = $map;
    $result['name']= $name;
    $result['current'] = $current;
    $result['max'] = $max;
    return $result;
    }
    $serv = GetServerInfo($server,$port);

    if ($serv) {
      echo '<div class="background_map"><img width="100%" height="100%" src="img/maps/'.$serv['map'].'.jpg" /></div>';
      echo '<div class="info">
      Сервер: '.$server.':'.$port.'
      <br>
      Название: '.$serv['name'].'
      <br>
      Карта: '.$serv['map'].'
      <br>
      Игроков: '.$serv['current'].'/'.$serv['max'].'
      <br>
      Статус: On-line
      <br></div>';
    } else {
      echo 'Статус: Off-line';
    }
    $m = $serv['max'];
    $p = $serv['current'];

    $w = @ceil($p /$m * 100);
    
    // Скрипт serverwidget
    // Данный скрипт необходим для показа игроков на сервере в текущий момент.
    // Вам необходимо зарегистрироваться нв serverwidget.com и в разделе "Настройки" -> "Доступ к API" скопировать свой Token ключ.
    include 'serverwidget.api.php';

    $address = (isset($_GET['address']) && strlen($_GET['address'])) ? trim($_GET['address']) : '31.28.168.178:27036'; // А вот здесь нужно вставить свой IP:PORT

    $API = new ServerWidgetAPI('токен_ключ'); // Вот здесь необходимо вставить ваш токен ключ.
    $serverInfo = $API->method("server.get", array(
    "address" => $address,
    "fields" => "players,map,game,location,update,extra,uptime,rank,ping"
    ));

    if (count($serverInfo['result']) && is_array($serverInfo['result'])) {
    $server = $serverInfo['result'][0];
    $players = $API->method("server.players", array("address" => $address));
    $rules = $API->method("server.rules", array("address" => $address));
    $maps = $API->method("server.maps", array("address" => $address));
    }
  // Конец скрипта serverwidget	
	?>

  <div class="percent_of_filled">
    <div class="background_pof">
      <div class="text_pof">
        <?=$p;?> / <?=$m;?>
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
              <td align="right">'.$row['ban_length'].'</td>
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
            <td align="right">'.$row['days'].'</td>
         </tr>

        ';
        }
        ?>
      </tbody>
    </table>
    <a class="watch_all" href="http://entra.xban.info/bans/amxadmins/index.html">Смотреть всех</a></div>
    <div class="players_on_server">
      <? if (isset($serverInfo['result'])): ?>
      <script type="text/javascript">
        function geByTag(searchTag, node) {
          node = node || document;
          var elems = [], nodes = node.getElementsByTagName(searchTag);
          if (nodes.length) {
            for (var i = 0; i < nodes.length; i++) {
              elems.push(nodes[i]);
            }
          }
          return elems;
        }
        function geByClass(searchClass, node, tag) {
          node = node || document;
          tag = tag || '*';
          if (node.querySelectorAll && tag != '*') {
            return node.querySelectorAll(tag + '.' + searchClass.replace(/\s+/g, '.'));
          }
          var classElements = [];
          if (node.getElementsByClassName) {
            var nodes = node.getElementsByClassName(searchClass);
            if (tag != '*') {
              tag = tag.toUpperCase();
              for (var i = 0, l = nodes.length; i < l; ++i) {
                if (nodes[i].tagName.toUpperCase() == tag) {
                  classElements.push(nodes[i]);
                }
              }
            } else {
              classElements = Array.prototype.slice.call(nodes);
            }
            return classElements;
          }
          var els = geByTag(tag, node), pattern = new RegExp('(^|\\s)' + searchClass + '(\\s|$)');
          for (var i = 0, l = els.length; i < l; ++i) {
            if (pattern.test(els[i].className)) {
              classElements.push(els[i]);
            }
          }
          return classElements;
        }
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
        function declOfNum(number, str) {
          var cases = [2, 0, 1, 1, 1, 2];
          str = str[(number % 100 > 4 && number % 100 < 20) ? 2 : cases[(number % 10 < 5) ? number % 10 : 5]];
          return str.replace(/%n/g, number);
        }
        function updatePlayerTime(elements) {
          var i = 0;
          for (var key in elements) {
            var element = elements[key], time = parseInt(element.getAttribute('time')) || 0;
            time++;
            element.setAttribute('time', time);
            element.innerHTML = formatTime(time);
            i++;
          }
          if (i > 0) {
            setTimeout(function() {
              updatePlayerTime(elements);
            }, 1000);
          }
        }
      </script>
      <center><p><i class="fa fa-users"></i> ИГРОКИ НА СЕРВЕРЕ</p></center>
			<table width="100%" cellpadding="5" cellspacing="0">
				<tr>
					<th align="left">Ранк</th>
					<th align="left">Ник</th>
					<th align="center">Очки</th>
					<th align="right">Время в игре</th>
				</tr>
			<? if (count($players['result'])): ?>
			<? foreach ($players['result'] as $idx => $player): ?>
				<tr<? if ($idx%2 !== 0): ?> class="dark"<? endif; ?>>
					<td align="left"><?=$player['rank'];?>.</td>
					<td align="left"><?=htmlspecialchars($player['name']);?></td>
					<td align="center"><?=$player['score'];?></td>
					<td align="right" class="update-time" time="<?=$player['time'];?>"><?=$player['date'];?></td>
				</tr>
			<? endforeach; ?>
			<? else: ?>
				<tr>
					<td colspan="4"><div class="empty_table">Игроков нет</div></td>
				</tr>
			<? endif; ?>
			</table>

        <script type="text/javascript">
        updatePlayerTime(geByClass('update-time'));
        </script>

        <? else: ?>
            <div class="empty_table"><?=$serverInfo['error']['error_msg'];?></div>
        <? endif; ?>
        
    </div>
  </div>
 </body>
</html>
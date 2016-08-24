<?php

/**
 *  Пример:
 *    $API = new ServerWidgetAPI('Токен ключ');
 *    $serverInfo = $API->method('server.get', array('address' => 'IP адрес сервера'));
 *
 *    if (count($serverInfo['result']) && is_array($serverInfo['result'])) {
 *      echo $serverInfo['result'][0]['name'];
 *    }
**/

Class ServerWidgetAPI {
  private $api_url = 'http://api.serverwidget.com/';
  private $api_token = '';
  private $api_lang = 'ru';
  private $api_version = '2.2';

  // Иницилизация класса
  public function __construct($api_token = '', $api_lang = 'ru', $api_version = '2.0') {
    $this->api_token = $api_token;
    $this->api_lang = $api_lang;
    $this->api_version = $api_version;
  }

  // Выполнение метода
  public function method($name, $params = array()) {
    $url = $this->api_url."/method/$name";

    return json_decode($this->request($url, $params), true);
  }

  // Запрос на API сервер
  private function request($url, $params = array()) {
    $params['token'] = $this->api_token;
    $params['lang'] = $this->api_lang;
    $params['v'] = $this->api_version;
    $params['https'] = 1;

    $url = $url . (is_array($params) && count($params) ? '?'.http_build_query($params) : '');

    if (function_exists('curl_init')) {
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

      $result = curl_exec($ch);

      curl_close($ch);

      return $result;
    }

    return @file_get_contents($url);
  }
}

?>

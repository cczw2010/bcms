<?php
/**
 * Douban PHP SDK (using OAuth2)
 *
 * @author NetPuter <netputer@gmail.com>
 */

  class DoubanOA2 {

    private $api_root = 'https://api.douban.com/v2/';

    private $authorize_url = 'https://www.douban.com/service/auth2/auth';
    private $access_token_url = 'https://www.douban.com/service/auth2/token';

    private $client_key = NULL;
    private $client_secret = NULL;
    private $client_redirect_url = NULL;

    private $access_token = NULL;

    public $http_code = 0;

    public function __construct($key,$secret,$redirect_url,$access_token=false) {

      $this->client_key = $key;
      $this->client_secret = $secret;

      if (!empty($redirect_url)) {
        $this->client_redirect_url = $redirect_url;
      }

      if (!empty($access_token)) {
        $this->access_token = $access_token;
      }
    }

    public function getAuthorizeURL($scope = 'douban_basic_common', $state = 'douban') {

      $params = array(
        'client_id' => $this->client_key,
        'redirect_uri' => $this->client_redirect_url,
        'response_type' => 'code',
        'scope' => $scope,
        'state' => $state,
      );

      return $this->authorize_url . '?' . http_build_query($params);
    }

    public function getAccessToken($authorization_code) {
      $params = array(
        'client_id' => $this->client_key,
        'client_secret' => $this->client_secret,
        'redirect_uri' => $this->client_redirect_url,
        'grant_type' => 'authorization_code',
        'code' => $authorization_code,
      );

      $result = $this->post($this->access_token_url, $params);

      if (isset($result['access_token'])) {
        $this->access_token = $result['access_token'];
      }

      return $result;
    }

    public function setAccessToken($token) {
      $this->access_token = $token;
    }

    public function refreshToken($token) {
      $params = array(
        'client_id' => $this->client_key,
        'client_secret' => $this->client_secret,
        'redirect_uri' => $this->client_redirect_url,
        'grant_type' => 'refresh_token',
        'refresh_token' => $token,
      );

      $result = $this->post($this->access_token_url, $params);

      if (isset($result['access_token'])) {
        $this->access_token = $result['access_token'];
      }

      return $result;
    }

    public function get($resource, $params = array()) {
      return $this->oAuthRequest('GET', $resource, $params);
    }

    public function post($resource, $params = array()) {
      return $this->oAuthRequest('POST', $resource, $params);
    }

    public function put($resource, $params = array()) {
      return $this->oAuthRequest('PUT', $resource, $params);
    }

    public function delete($resource, $params = array()) {
      return $this->oAuthRequest('DELETE', $resource, $params);
    }

    public function oAuthRequest($method, $url, $params) {
      if (strpos($url, 'http') !== 0) {
        $url = $this->api_root . $url;
      }

      switch ($method) {
        case 'GET':
        case 'DELETE':
          $query_str = http_build_query($params);

          if (!empty($query_str)) {
            $url .= '?' . $query_str;
          }

          return $this->http($method, $url);
      }

      return $this->http($method, $url, $params);
    }

    private function getHeader() {
      if (is_null($this->access_token)) {
        return 'Content_type: application/x-www-form-urlencoded';
      }

      return 'Authorization: Bearer ' . $this->access_token;
    }

    public function http($method, $url, $params = array()) {
      $ch = curl_init();

      $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,

        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HEADER => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE,

      );

      $headers = array();

      switch ($method) {
        case 'POST':
          $options[CURLOPT_POST] = TRUE;
          $options[CURLOPT_POSTFIELDS] = $params;
          break;

        case 'PUT':
          $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
          $options[CURLOPT_POSTFIELDS] = $params;
          break;

        case 'DELETE':
          $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
          break;
      }

      $headers[] = $this->getHeader();
      $headers[] = 'Expect:';

      $options[CURLOPT_HTTPHEADER] = $headers;

      curl_setopt_array($ch, $options);

      $result = curl_exec($ch);
      $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      curl_close($ch);

      return json_decode($result, TRUE);
    }

  }
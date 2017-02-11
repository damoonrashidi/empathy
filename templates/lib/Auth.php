<?php

class Auth {

  static function getToken($header = 'Authorization') {
    return getallheaders()[$header];
  }

  static function isAuthorized() {
    $token = static::getToken();
    $jwt = JWT::decode($token, $GLOBALS['conf']['secret_key']);
    return gettype($jwt) === 'object';
  }

  static function payload() {
    $token = static::getToken();
    $jwt = JWT::decode($token, $GLOBALS['conf']['secret_key']);
    return $jwt;
  }

}
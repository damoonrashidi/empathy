<?php

  class Router {


    function __construct(){
      $this->index = null;
      $this->get = [];
      $this->post = [];
      $this->put = [];
      $this->delete = [];
      $this->uri = $_SERVER['REQUEST_URI'];
      $this->method = $_SERVER['REQUEST_METHOD'];
    }

    function get($url, $controller) {
      $this->get[$url] = $controller;
    }

    function post($url, $controller) {
      $this->post[$url] = $controller;
    }

    function put($url, $controller) {
      $this->put[$url] = $controller;
    }

    function delete($url, $controller) {
      $this->delete[$url] = $controller;
    }

    function parse_params($url) {
      return [];
    }

    function run() {
      $routes = ["GET" => $this->get, "POST" => $this->post, "DELETE" => $this->delete, "PUT" => $this->put][$this->method];
      $url = substr($this->uri, 1);
      //this is the index. do the index action
      if($url == "" && $this->index != null){
        if(is_callable($this->index)) {
          $fn = $this->index;
          $fn();
          return;
        }
        else {
          $controller = ucfirst(explode("#",$this->index)[0])."Controller";
          $action = explode("#",$this->index)[1];
          require __DIR__."/../controllers/".$controller.".php";
          $params = $this->parse_params($url);
          $controller = new $controller;
          $controller->__before($params, $action);
          $controller->$action($params);
          $controller->__after($params, $action);
          return;
        }
      }
      //we straight up matched a route, check to see if its callable or if it's a controller
      if(isset($routes[$url])) {
        if(is_callable($routes[$url])) {
          $routes[$url]([]);
        }
        else {
          $controller = ucfirst(explode("#",$routes[$url])[0])."Controller";
          $action = explode("#",$routes[$url])[1];
          require __DIR__."/../controllers/".$controller.".php";
          $controller = new $controller;
          $params = $this->parse_params($routes[$url]);
          $controller->__before($params, $action);
          $controller->$action($params);
          $controller->__after($params, $action);
          return;
        }
      }
      else {
        $url = explode("/", $url);
        foreach($routes as $route => $controller) {
          $route = explode("/", $route);
          $params = [];
          if(count($url) == count($route)){
            $match = true;
            for($i = 0; $i<count($url); $i++) {
              if($url[$i] != $route[$i] && substr($route[$i],0,1) != ":") {
                $match = false;
                continue;
              }
              if(substr($route[$i],0,1) == ":") {
                $params[substr($route[$i],1,strlen($route[$i])-1)] = $url[$i];
              }
              if($i == count($route)-1 && $match) {
                if($this->method == "POST") {
                  $params = array_merge($params, json_code(file_get_contents("php://input")), true);
                }
                if(is_callable($controller)){
                  $controller($params);
                  return;
                }
                $action = explode("#",$controller)[1];
                $controller = ucfirst(explode("#",$controller)[0])."Controller";
                require __DIR__."/../controllers/".$controller.".php";
                $controller = new $controller;
                $controller->__before($params, $action);
                $controller->$action($params);
                $controller->__after($params, $action);
                return;
              }
            }
          }
        }
        //redirect to 404 if there is 404, otherwise output standard 404
        header("HTTP/2.0 404 Not Found");
        echo "<h1>404!</h1><p>Sorry, We couldn't get this resource for you</p>";
      }
    }


  }
?>
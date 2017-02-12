<?php
class Router {
  public function __construct() {
    $this->index = null;
    $this->get = [];
    $this->post = [];
    $this->put = [];
    $this->delete = [];
    $this->options = [];
    $this->uri = $_SERVER['REQUEST_URI'];
    $this->method = $_SERVER['REQUEST_METHOD'];
  }

  public function index(string $controller) {
    $this->index = $controller;
  }

  /**
   * @param  string
   * @param  mixed
   * @return void
   */
  public function get($url, $controller) {
    $this->get[$url] = $controller;
  }

  public function post($url, $controller) {
    $this->post[$url] = $controller;
  }

  public function put($url, $controller) {
    $this->put[$url] = $controller;
  }

  public function delete($url, $controller) {
    $this->delete[$url] = $controller;
  }

  public function options($url, $controller) {
    $this->options[$url] = $controller;
  }

  public function unifyParams() {
    $params = $_POST;
    $params = array_merge($_GET, $params);
    $params = array_merge($params, (array) json_decode(file_get_contents('php://input')));
    return (object) $params;
  }

  public function run() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: $this->method, Authorization, Content-Type");

    $routes = [
      'GET' => $this->get,
      'POST' => $this->post,
      'DELETE' => $this->delete,
      'PUT' => $this->put,
      'OPTIONS' => array_merge($this->get, $this->post, $this->delete, $this->put)][$this->method];
    $url = substr($this->uri, 1);
    //this is the index. do the index action
    $params = $this->unifyParams();
    if ($url == '' && $this->index != null) {
      if (is_callable($this->index)) {
        $fn = $this->index;
        $result = $fn();
        echo $result;
        return;
      } else {
        $controller = ucfirst(explode('#', $this->index)[0]) . 'Controller';
        $action = explode('#', $this->index)[1];
        require __DIR__ . '/../controllers/' . $controller . '.php';
        $controller = new $controller();
        $controller->__before($params, $action);
        $result = $controller->$action($params);
        $controller->__after($params, $action);
        echo $result;
        return;
      }
    }
    //we straight up matched a route, check to see if its callable or if it's a controller
    if (isset($routes[$url])) {
      if (is_callable($routes[$url])) {
        $routes[$url]($params);
      } else {
        $controller = ucfirst(explode('#', $routes[$url])[0]) . 'Controller';
        $action = explode('#', $routes[$url])[1];
        require __DIR__ . '/../controllers/' . $controller . '.php';
        $controller = new $controller();
        $controller->__before($params, $action);
        $result = $controller->$action($params);
        $controller->__after($params, $action);
        echo $result;
        return;
      }
    } else {
      $url = explode('/', $url);
      foreach ($routes as $route => $controller) {
        $route = explode('/', $route);
        $params = [];
        if (count($url) == count($route)) {
          $match = true;
          for ($i = 0; $i < count($url); ++$i) {
            if ($url[$i] != $route[$i] && substr($route[$i], 0, 1) != ':') {
              $match = false;
              continue;
            }
            if (substr($route[$i], 0, 1) == ':') {
              $params[substr($route[$i], 1, strlen($route[$i]) - 1)] = $url[$i];
            }
            if ($i == count($route) - 1 && $match) {
              if (is_callable($controller)) {
                $params = (object) $params;
                $controller($params);
                return;
              }
              $action = explode('#', $controller)[1];
              $controller = ucfirst(explode('#', $controller)[0]) . 'Controller';
              require __DIR__ . '/../controllers/' . $controller . '.php';
              $params = (object) $params;
              $controller = new $controller();
              $controller->__before($params, $action);
              $result = $controller->$action($params);
              $controller->__after($params, $action);

              echo $result;

              return;
            }
          }
        }
      }
      //redirect to 404 if there is 404, otherwise output standard 404
      // header('HTTP/2.0 404 Not Found');
      // echo "<h1>404!</h1><p>Sorry, We couldn't get this resource for you</p>";
    }
  }
}

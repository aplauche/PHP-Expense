<?php

declare(strict_types=1);


namespace Framework;


class Router
{
  private array $routes = [];
  private array $middlewares = [];

  public function add(string $method, string $path, array $controller)
  {
    $this->routes[] = [
      'path' => $this->normalizePath($path),
      'method' => strtoupper($method),
      'controller' => $controller,
      'middlewares' => []
    ];
  }

  private function normalizePath(string $path): string
  {
    // Trim slashes from front and end if present
    $path = trim($path, '/');
    // add a slash to front and back
    $path = "/{$path}/";
    // replace any instances of consecutive slashes with just one slash
    $path = preg_replace('#[/]{2,}#', "/", $path);
    return $path;
  }

  public function dispatch(string $path, string $method, Container $container = null)
  {
    $path = $this->normalizePath($path);
    $method = strtoupper($method);

    foreach ($this->routes as $route) {
      if (!preg_match("#^{$route['path']}$#", $path) || $route["method"] !== $method) {
        // Route not found
        continue; // move to next item in array
      }

      // destructure out our controller namespace and method
      [$class, $method] = $route['controller'];

      // you can instantiate a class using just a string with full namespace
      // check if we are using a container, if so intantiate class from container
      $controllerInstance = $container ? $container->resolve($class) : new $class;

      // action is a function once run that will return the controller instance method
      $action = fn () => $controllerInstance->{$method}();

      $allMiddleware = [...$route['middlewares'], ...$this->middlewares];

      // handle middlware - loop through invoking and passing the prev function into our process method
      foreach ($allMiddleware as $middleware) {
        $middlewareInstance = $container ? $container->resolve($middleware) : new $middleware;
        $action = fn () => $middlewareInstance->process($action);
      }
      // finally after all other middleware our controller method is invoked
      $action();

      return;
    }
  }

  public function addMiddleware(string $middlware)
  {
    $this->middlewares[] = $middlware;
  }

  public function addRouteMiddleware(string $middleware)
  {
    $lastRouteKey = array_key_last($this->routes);
    //dd($this->routes[$lastRouteKey]);
    $this->routes[$lastRouteKey]['middlewares'][] = $middleware;
  }
}

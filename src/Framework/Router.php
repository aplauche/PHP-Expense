<?php

declare(strict_types=1);


namespace Framework;


class Router
{
  private array $routes = [];
  private array $middlewares = [];

  public function add(string $method, string $path, array $controller)
  {

    $path = $this->normalizePath($path);
    // creates a path with the secondary regex expression within it for an route params
    // /transaction/{transaction} => /transaction/([^/]+)/
    $regexPath = preg_replace('#{[^/]+}#', '([^/]+)', $path);


    $this->routes[] = [
      'path' => $this->normalizePath($path),
      'method' => strtoupper($method),
      'controller' => $controller,
      'middlewares' => [],
      'regexPath' => $regexPath
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
    // allow hidden method field to override method, otherwise fallback to server defined method
    $method = strtoupper($_POST['_METHOD'] ?? $method);

    foreach ($this->routes as $route) {
      // evaluates regex path which includes a placeholder for any value for the params without curly brackets eg. /transaction/([^/]+)/ matches /transaction/1/
      // adding third argument to preg_match capture matches in groups and stores them in an array
      if (!preg_match("#^{$route['regexPath']}$#", $path, $paramValues) || $route["method"] !== $method) {
        // Route not found
        continue; // move to next item in array
      }

      // remove first item from match array - this will always be the full path
      array_shift($paramValues);

      // get the name of placeholders from our route definition to use as keys
      preg_match_all('#{([^/]+)}#', $route['path'], $paramKeys);

      // only use the secondary item returned - since first will always be full match with curly brackets
      $paramKeys = $paramKeys[1];

      $params = array_combine($paramKeys, $paramValues);

      // destructure out our controller namespace and method
      [$class, $method] = $route['controller'];

      // you can instantiate a class using just a string with full namespace
      // check if we are using a container, if so intantiate class from container
      $controllerInstance = $container ? $container->resolve($class) : new $class;

      // action is a function once run that will return the controller instance method
      $action = fn () => $controllerInstance->{$method}($params);

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

<?php

declare(strict_types=1);


namespace Framework;


class Router
{
  private array $routes = [];

  public function add(string $method, string $path, array $controller)
  {
    $this->routes[] = [
      'path' => $this->normalizePath($path),
      'method' => strtoupper($method),
      'controller' => $controller
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

  public function dispatch(string $path, string $method)
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
      $controllerInstance = new $class;

      // you can run the method using a string as well
      $controllerInstance->$method();
    }
  }
}

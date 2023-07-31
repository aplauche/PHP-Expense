<?php

declare(strict_types=1);



namespace Framework;

use Framework\Exceptions\ContainerException;
use ReflectionClass;
use ReflectionNamedType;

class Container
{

  private array $definitions = [];
  private array $resolved = []; // store instance of already instantiated classes

  public function addDefinitions(array $newDefinitions)
  {
    $this->definitions = [...$this->definitions, ...$newDefinitions];
  }


  public function resolve(string $class)
  {

    // Look inside the provided class to determine needed parameters for the class
    // returns info about the class
    $reflectionClass = new ReflectionClass($class);

    // Validation of class

    // make sure class is not abstract
    if (!$reflectionClass->isInstantiable()) {
      throw new ContainerException("Class {$class} is not instantiable");
    }

    // make sure we can get constructor
    $constructor = $reflectionClass->getConstructor();

    if (!$constructor) {
      // if no constructor, there are no dependencies and we can just instantiate the class
      return new $class;
    }

    // check for parameters
    $params = $constructor->getParameters();

    if (count($params) === 0) {
      // if no params we also do not need dependencies
      return new $class;
    }


    // Validation of parameters
    $dependencies = [];

    foreach ($params as $param) {

      $name = $param->getName();
      $type = $param->getType();

      // if there is no type hint for parameter we wont be able to find the dependency
      if (!$type) {
        throw new ContainerException("Class {$class} has a param {$name} missing a type hint");
      }

      // we will just handle params with a specific named type - no complex union types etc...
      //  built in would be basic strings, ints etc, which we also cannot generate dependencies for
      if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
        throw new ContainerException("Class {$class} has a param {$name} that is not a named type and cannot be resolved");
      }
    }

    // instantiate our dependencies from parameters
    $paramTypeClassName = $type->getName();
    $dependencies[] = $this->get($paramTypeClassName);

    // this instantiates the class passing in the dependencies as arguments and returns it
    return $reflectionClass->newInstanceArgs($dependencies);
  }

  public function get(string $id)
  {
    if (!array_key_exists($id, $this->definitions)) {
      throw new ContainerException("Class {$id} does not exist in container definitions");
    }

    if (array_key_exists($id, $this->resolved)) {
      // if the instance already exists, just return the already instantiated instance - SINGLETON
      return $this->resolved[$id];
    }

    $factoryFunction = $this->definitions[$id];

    $dependency = $factoryFunction();

    // store the instance in the array of resolved
    $this->resolved[$id] = $dependency;


    return $dependency;
  }
}

<?php

declare(strict_types=1);


namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    // can add a try catch to middleware to catch specific error types and handle accordingly by wrapping next function
    try {
      $next();
    } catch (ValidationException $e) {
      // referer stores the page that submitted the form - SECURITY ISSUES EXIST
      $_SESSION['errors'] = $e->errors;
      $referer = $_SERVER['HTTP_REFERER'];
      redirectTo($referer);
    }
  }
}

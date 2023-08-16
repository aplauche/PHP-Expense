<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;

class CsrfGuardMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {

    $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

    $guardedMethods = [
      'POST',
      'PATCH',
      'DELETE'
    ];

    if (!in_array($requestMethod, $guardedMethods)) {
      // bail out - validation not needed
      $next();
      return;
    }

    if ($_SESSION['token'] !== $_POST['token']) {
      // validation fails
      redirectTo('/');
      // ideally would throw custom exception instead
    }

    // form is valid! unset token before continuing - csrf tokens only get used once
    unset($_SESSION['token']);

    $next();
  }
}

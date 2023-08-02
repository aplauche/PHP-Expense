<?php

declare(strict_types=1);


namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

class FlashMiddleware implements MiddlewareInterface
{

  public function __construct(private TemplateEngine $view)
  {
  }

  public function process(callable $next)
  {
    // add errors or empty array if none
    $this->view->addGlobal('errors', $_SESSION['errors'] ?? []);

    // after errors are added to the view we delete them from the session so that they only flash once
    unset($_SESSION['errors']);

    $next();
  }
}

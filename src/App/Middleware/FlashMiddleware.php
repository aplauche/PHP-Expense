<?php

/**
 * Check session variable and add data to our view based on what exists within the session
 * Then clear out the session after injecting in the view so that it is only displayed once.
 */

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
    $this->view->addGlobal('oldData', $_SESSION['oldData'] ?? []);

    // after errors are added to the view we delete them from the session so that they only flash once
    unset($_SESSION['errors']);
    unset($_SESSION['oldData']);

    $next();
  }
}

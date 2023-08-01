<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Middleware\TemplateDataMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use App\Middleware\SessionMiddleware;

function registerMiddlware(App $app)
{
  // add middleware passing full name as string
  $app->addMiddleware(TemplateDataMiddleware::class);
  $app->addMiddleware(ValidationExceptionMiddleware::class);
  $app->addMiddleware(SessionMiddleware::class);

  //  Note middleware runs in reverse order - sessions must come after validation in list to run first
}

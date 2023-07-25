<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Middleware\TemplateDataMiddleware;

function registerMiddlware(App $app)
{
  // add middleware passing full name as string
  $app->addMiddleware(TemplateDataMiddleware::class);
}

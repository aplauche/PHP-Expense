<?php

declare(strict_types=1);

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\ValidatorService;

// NOTE TemplateEngine::class returns the instantiable name of the class as a string for the key
// eq of "Framework\TemplateEngine"

return [
  TemplateEngine::class => function () {
    return new TemplateEngine(Paths::VIEW);
  },
  ValidatorService::class => function () {
    return new ValidatorService();
  }
];

<?php

declare(strict_types=1);

use Framework\TemplateEngine;
use App\Config\Paths;
use App\Services\UserService;
use App\Services\ValidatorService;
use App\Services\TransactionService;
use Framework\Database;
use Framework\Container;




// NOTE TemplateEngine::class returns the instantiable name of the class as a string for the key
// eq of "Framework\TemplateEngine"

return [
  TemplateEngine::class => function () {
    return new TemplateEngine(Paths::VIEW);
  },
  ValidatorService::class => function () {
    return new ValidatorService();
  },
  TransactionService::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new TransactionService($db);
  },
  Database::class => function () {
    return new Database($_ENV['DB_DRIVER'], [
      'host' => $_ENV['DB_HOST'],
      'port' => $_ENV['DB_PORT'],
      'dbname' => $_ENV['DB_NAME']
    ], $_ENV['DB_USER'], $_ENV['DB_PASS']);
  },
  UserService::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new UserService($db);
  }
];

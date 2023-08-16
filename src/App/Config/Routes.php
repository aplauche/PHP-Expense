<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\AboutController;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Middleware\AuthRequiredMiddleware;
use App\Middleware\GuestOnlyMiddleware;

function registerRoutes(App $app)
{
  // pass controller as string - more efficient than potentially instantianting multiple times in bootstrap
  // ::class will return the namespace + class as a string without instantiating
  $app->get("/", [HomeController::class, 'home'])->add(AuthRequiredMiddleware::class);
  $app->get("/about", [AboutController::class, 'about']);

  $app->get("/login", [AuthController::class, 'loginView'])->add(GuestOnlyMiddleware::class);
  $app->post("/login", [AuthController::class, 'login']);

  $app->get("/register", [AuthController::class, 'registerView'])->add(GuestOnlyMiddleware::class);
  $app->post("/register", [AuthController::class, 'register']);
}

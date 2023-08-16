<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\AboutController;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\TransactionController;
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

  $app->get('/logout', [AuthController::class, 'logout'])->add(AuthRequiredMiddleware::class);

  $app->get('/transaction', [TransactionController::class, 'createView'])->add(AuthRequiredMiddleware::class);
  $app->post('/transaction', [TransactionController::class, 'create'])->add(AuthRequiredMiddleware::class);
}

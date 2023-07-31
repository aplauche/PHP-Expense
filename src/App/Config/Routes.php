<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Controllers\AboutController;
use App\Controllers\HomeController;
use App\Controllers\AuthController;

function registerRoutes(App $app)
{
  // pass controller as string - more efficient than potentially instantianting multiple times in bootstrap
  // ::class will return the namespace + class as a string without instantiating
  $app->get("/", [HomeController::class, 'home']);
  $app->get("/about", [AboutController::class, 'about']);
  $app->get("/register", [AuthController::class, 'registerView']);
  $app->post("/register", [AuthController::class, 'register']);
}

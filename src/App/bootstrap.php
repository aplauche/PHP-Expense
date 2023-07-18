<?php

declare(strict_types=1);


require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use App\Controllers\HomeController;

$app = new App();

// pass controller as string - more efficient than potentially instantianting multiple times in bootstrap
// ::class will return the namespace + class as a string without instantiating
$app->get("/", [HomeController::class, 'home']);


return $app;

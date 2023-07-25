<?php

declare(strict_types=1);


require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use function App\Config\{registerRoutes, registerMiddlware};
use App\Config\Paths;


$app = new App(Paths::SRC . "App/container-definitions.php");

registerRoutes($app);
registerMiddlware($app);

return $app;

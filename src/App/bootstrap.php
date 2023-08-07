<?php

declare(strict_types=1);


require __DIR__ . "/../../vendor/autoload.php";

use Framework\App;
use function App\Config\{registerRoutes, registerMiddlware};
use App\Config\Paths;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$app = new App(Paths::SRC . "App/container-definitions.php");

registerRoutes($app);
registerMiddlware($app);

return $app;

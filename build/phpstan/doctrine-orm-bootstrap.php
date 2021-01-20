<?php

declare(strict_types=1);

use App\Kernel;

require __DIR__ . '/bootstrap.php';

$kernel = new Kernel("system", (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$req = \Illuminate\Http\Request::create('/petugas/incoming/search', 'GET', ['q' => 'Transmisi']);
$res = app(\App\Http\Controllers\Petugas\IncomingGoodController::class)->search($req);
echo $res->getContent();

<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$item = \App\Models\Item::first();
var_dump($item->locations()->pluck('locations.id')->toArray());

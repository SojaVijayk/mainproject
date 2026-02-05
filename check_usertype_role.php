<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$data = DB::table('usertype_role')->get();
echo "Data in usertype_role:\n";
foreach ($data as $row) {
    print_r($row);
}

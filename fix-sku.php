<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$produks = App\Models\Produk::whereNull('sku')->orWhere('sku', '')->get();
foreach ($produks as $p) {
    $categoryPrefix = $p->category ? strtoupper(substr($p->category, 0, 3)) : 'PRD';
    $p->sku = $categoryPrefix . '-' . rand(1000, 9999);
    $p->save();
}
echo "Done!\n";

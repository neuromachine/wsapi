<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$repo = app(\App\Repositories\BlockCategoryRepository::class);

try {
    $res = $repo->getOffersData('ru', 'redstar-collaboration-formats');
    echo "redstar-collaboration-formats OK, items count: " . $res['items']->count() . "\n";
    if ($res['items']->count() > 0) {
        echo "Item name: " . $res['items']->first()->name . "\n";
    }
} catch (\Exception $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
}

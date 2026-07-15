<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = \App\Models\BlockItem::find(434);
if ($item) {
    echo "Item 434 key: " . $item->key . "\n";
    echo "Item 434 category_id: " . $item->category_id . "\n";
    $cat = \App\Models\BlocksCategories::find($item->category_id);
    if ($cat) echo "Category name: " . $cat->key . "\n";
} else {
    echo "Item 434 not found\n";
}

<?php

use App\Support\EavContentResolver;
use Illuminate\Support\Collection;

class DummyProperty {
    public $key;
    public $is_collection;
    public function __construct($key, $is_collection = false) {
        $this->key = $key;
        $this->is_collection = $is_collection;
    }
}

class DummyPropertyValue {
    public $property;
    public $value;
    public $value_type;
    public function __construct($property, $value, $value_type) {
        $this->property = $property;
        $this->value = $value;
        $this->value_type = $value_type;
    }
}

class DummyItem {
    public $key;
    public $propertyValues;
    public function __construct($key, $propertyValues) {
        $this->key = $key;
        $this->propertyValues = $propertyValues;
    }
}

it('resolves eav content in single mode', function () {
    $prop = new DummyProperty('title');
    $val = new DummyPropertyValue($prop, 'My Title', 'string');
    $item = new DummyItem('item_1', collect([$val]));
    
    $items = collect([$item]);
    
    $resolved = EavContentResolver::resolve($items, true, false);
    
    expect($resolved)->toBeArray();
    expect($resolved)->toHaveKey('title', 'My Title');
});

it('resolves eav content in keyed mode', function () {
    $prop = new DummyProperty('price');
    $val = new DummyPropertyValue($prop, '100', 'integer');
    $item = new DummyItem('item_1', collect([$val]));
    
    $items = collect([$item]);
    
    $resolved = EavContentResolver::resolve($items, false, true);
    
    expect($resolved)->toBeArray();
    expect($resolved)->toHaveKey('item_1');
    expect($resolved['item_1'])->toHaveKey('price', 100);
});

it('resolves eav content in array mode', function () {
    $prop = new DummyProperty('active');
    $val = new DummyPropertyValue($prop, '1', 'boolean');
    $item = new DummyItem('item_1', collect([$val]));
    
    $items = collect([$item]);
    
    $resolved = EavContentResolver::resolve($items, false, false);
    
    expect($resolved)->toBeArray();
    expect(count($resolved))->toBe(1);
    expect($resolved[0])->toHaveKey('active', true);
});

it('casts json types correctly', function () {
    $prop = new DummyProperty('meta');
    $val = new DummyPropertyValue($prop, '{"color":"red"}', 'json');
    $item = new DummyItem('item_1', collect([$val]));
    
    $resolved = EavContentResolver::resolve(collect([$item]), true);
    
    expect($resolved['meta'])->toBeArray();
    expect($resolved['meta'])->toHaveKey('color', 'red');
});

it('handles is_collection correctly', function () {
    $prop = new DummyProperty('gallery', true);
    $val1 = new DummyPropertyValue($prop, 'img1.jpg', 'string');
    $val2 = new DummyPropertyValue($prop, 'img2.jpg', 'string');
    $item = new DummyItem('item_1', collect([$val1, $val2]));
    
    $resolved = EavContentResolver::resolve(collect([$item]), true);
    
    expect($resolved['gallery'])->toBeArray();
    expect($resolved['gallery'])->toContain('img1.jpg');
    expect($resolved['gallery'])->toContain('img2.jpg');
});

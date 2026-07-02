<?php

use Tests\Fixtures\EavFixture;

it('preserves the flat json contract for the offers endpoint', function () {
    EavFixture::createOffersCategory();

    $response = $this->get('/api/en/blocks/categories/offers/test-offers');

    $response->assertStatus(200);

    // Assert there is no "data" wrapper, it returns a flat JSON
    $response->assertJsonMissingExact(['data' => []]);

    $response->assertJsonStructure([
        'category',
        'block',
        'items' => [
            '*' => [
                'id',
                'key',
                'name',
                'description',
                'properties' // Assert it returns properties instead of flattened values
            ]
        ]
    ]);

    $response->assertJsonPath('category', 'Test Offers');
    $response->assertJsonPath('block', 'Offers');

    $items = $response->json('items');
    expect($items)->toBeArray();
    expect(count($items))->toBeGreaterThan(0);
    
    $firstItem = $items[0];
    expect($firstItem)->toHaveKey('key', 'offer_1');
    expect($firstItem['properties'])->toBeArray();
    expect(count($firstItem['properties']))->toBeGreaterThan(0);
});

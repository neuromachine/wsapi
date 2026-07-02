<?php

use Tests\Fixtures\EavFixture;

it('preserves the public API contract for the services category endpoint', function () {
    EavFixture::createServicesCategory();

    $response = $this->get('/api/en/blocks/categories/services');
    
    $response->assertStatus(200);

    // Root should have data wrapper
    $response->assertJsonStructure([
        'data' => [
            'id',
            'key',
            'name',
            'description',
            'content',
            'parent_id',
            'created_at',
            'updated_at',
            'section',
            'sections',
            'subcategories',
            'blocks',
            'children' // Legacy compatibility field
        ]
    ]);

    // Check specific values
    $response->assertJsonPath('data.key', 'services');
    $response->assertJsonPath('data.section', 'en');

    // Ensure EAV content is properly resolved and flattened
    $content = $response->json('data.content');
    expect($content)->toBeArray();
    expect($content)->toHaveKey('title');
    expect($content['title'])->toBe('Main Title');
    expect($content)->toHaveKey('description');
    expect($content['description'])->toBe('Some description');

    // Check sections exist
    $sections = $response->json('data.sections');
    expect($sections)->toBeArray();
    expect($sections)->toHaveKey('slide');
    expect($sections['slide']['slide_1']['slide_image'])->toBe('image.jpg');

    // Check subcategories list
    $subcategories = $response->json('data.subcategories');
    expect($subcategories)->toBeArray();
    expect(count($subcategories))->toBeGreaterThan(0);
    
    $sub = $subcategories[0];
    expect($sub)->toHaveKey('id');
    expect($sub)->toHaveKey('slug', 'sub-service');
    expect($sub)->toHaveKey('childs');
    expect($sub)->toHaveKey('title', 'Sub Title');
});

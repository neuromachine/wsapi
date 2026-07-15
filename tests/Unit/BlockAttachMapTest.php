<?php

use App\Support\BlockAttachMap;

it('maps descr_data to content', function () {
    expect(BlockAttachMap::is('descr_data', 'content'))->toBeTrue();
    expect(BlockAttachMap::get('descr_data'))->toBe('content');
});

it('maps slide to sections', function () {
    expect(BlockAttachMap::is('slide', 'sections'))->toBeTrue();
});

it('knows if a block is single', function () {
    expect(BlockAttachMap::isSingle('descr_data'))->toBeTrue();
    expect(BlockAttachMap::isSingle('slide'))->toBeFalse();
});

it('knows if a block is keyed', function () {
    expect(BlockAttachMap::isKeyed('slide'))->toBeTrue();
    expect(BlockAttachMap::isKeyed('descr_data'))->toBeFalse();
});

it('returns null for unknown blocks', function () {
    expect(BlockAttachMap::get('unknown_block'))->toBeNull();
    expect(BlockAttachMap::isSingle('unknown_block'))->toBeFalse();
    expect(BlockAttachMap::isKeyed('unknown_block'))->toBeFalse();
});

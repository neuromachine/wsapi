<?php

namespace App\Support;

final class BlockAttachMap
{
    /**
     * Блок → целевой раздел в ответе категории.
     * Заменить на чтение из БД (blocks.attach) когда колонка будет добавлена.
     */
    // TODO: make system attach
    private const MAP = [
        'descr_data'  => ['attach' => 'content',  'single' => true,  'keyed' => false],
        'slide'       => ['attach' => 'sections', 'single' => false, 'keyed' => true],
        'list'        => ['attach' => 'sections', 'single' => false, 'keyed' => true],
        'simplehtml'  => ['attach' => 'sections', 'single' => true,  'keyed' => false],
        'works'  => ['attach' => 'sections', 'single' => false,  'keyed' => true],
    ];

    public static function isKeyed(string $blockKey): bool
    {
        return self::MAP[$blockKey]['keyed'] ?? false;
    }

    public static function isSingle(string $blockKey): bool
    {
        return self::MAP[$blockKey]['single'] ?? false;
    }

    public static function get(string $blockKey): ?string
    {
        return self::MAP[$blockKey]['attach'] ?? null;
    }

    public static function is(string $blockKey, string $attach): bool
    {
        return self::get($blockKey) === $attach;
    }
}

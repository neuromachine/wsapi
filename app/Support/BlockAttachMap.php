<?php

namespace App\Support;

final class BlockAttachMap
{
    /**
     * Блок → целевой раздел в ответе категории.
     * Заменить на чтение из БД (blocks.attach) когда колонка будет добавлена.
     */
    private const MAP = [
        'descr_data' => ['attach' => 'content',  'single' => true],
        'hero'       => ['attach' => 'sections', 'single' => true],
        'services'   => ['attach' => 'sections', 'single' => true],
    ];

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

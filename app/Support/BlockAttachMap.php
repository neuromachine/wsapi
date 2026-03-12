<?php

namespace App\Support;

final class BlockAttachMap
{
    /**
     * Блок → целевой раздел в ответе категории.
     * Заменить на чтение из БД (blocks.attach) когда колонка будет добавлена.
     */
    private const MAP = [
        'descr_data' => 'content',
        'hero'       => 'sections',
        'services'   => 'sections',
    ];

    public static function get(string $blockKey): ?string
    {
        return self::MAP[$blockKey] ?? null;
    }

    public static function is(string $blockKey, string $attach): bool
    {
        return self::get($blockKey) === $attach;
    }
}

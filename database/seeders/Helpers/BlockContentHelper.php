<?php

namespace Database\Seeders\Helpers;

class BlockContentHelper
{
    public static function getContentFromJson(string $key): string
    {
        $jsonPath = storage_path('app/content.json');
        dd(compact('jsonPath', 'key'), file_exists($jsonPath));

        if (!file_exists($jsonPath)) {
            return "<p>Контент не найден (файл отсутствует).</p>";
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($data)) {
            return "<p>Контент не найден (ошибка формата JSON).</p>";
        }

        return $data[$key]['content'] ?? "<p>Контент не задан для {$key}.</p>";
    }
}

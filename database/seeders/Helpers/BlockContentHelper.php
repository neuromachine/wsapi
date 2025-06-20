<?php

namespace Database\Seeders\Helpers;

class BlockContentHelper
{
    public static function getContentFromJson(string $key): string
    {
        $jsonPath = storage_path('app/content.json');

        // Отладочная информация: выводим путь, существование, содержимое файла и искомую запись
        $raw = file_get_contents($jsonPath);
        $decoded = json_decode($raw, true);
        $exists = file_exists($jsonPath);

        // Вывод дампа для диагностики
        /*
        dd([
            'jsonPath' => $jsonPath,
            'file_exists' => $exists,
            'raw_content' => substr($raw, 0, 500), // первые 500 символов
            'decoded_keys' => is_array($decoded) ? array_keys($decoded) : null,
            'requested_key' => $key,
            'found_content' => $decoded[$key]['content'] ?? null,
        ]);
        */

        // Далее логика получения контента
        if (!file_exists($jsonPath)) {
            return "<p>Контент не найден (файл отсутствует).</p>";
        }

        if (!is_array($decoded)) {
            return "<p>Контент не найден (ошибка формата JSON).</p>";
        }

        return $decoded[$key]['content'] ?? "<p>Контент не задан для {$key}.</p>";
    }
}

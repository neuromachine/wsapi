<?php

namespace Database\Seeders\Helpers;

class BlockContentHelper
{
    public static function getData(string $key): array
    {
        $defaults = [
            'title' => 'Заголовок по умолчанию',
            'url' => '-',
            'price' => '0',
            'descr' => 'Описание отсутствует',
            'content' => [
                'head'   => '<h1>Заглушка H1</h1>',
                'body'   => '<p>Заглушка текста</p>',
                'footer' => '<footer>Конец</footer>',
            ],
            'image' => ['noimage1.png', 'noimage2.png'],
            'files' => [
                ['title' => 'Файл недоступен', 'path' => 'nofile1.pdf'],
                ['title' => 'Файл отсутствует', 'path' => 'nofile2.pdf'],
            ],
        ];

        $jsonPath = storage_path("app/blocks/{$key}.json");
        if (!file_exists($jsonPath)) {
            $defaults['descr'] = "Файл {$key}.json не найден";
            return $defaults;
        }

        $raw = file_get_contents($jsonPath);
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $defaults['descr'] = 'Ошибка формата JSON';
            return $defaults;
        }

        return array_replace_recursive($defaults, $data);
    }

    public static function getContent(string $key): string
    {
        return self::getData($key)['content']['body'];
    }
}

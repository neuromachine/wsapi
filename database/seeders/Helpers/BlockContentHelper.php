<?php

namespace Database\Seeders\Helpers;

class BlockContentHelper
{
    public static function getData(string $key): array
    {
        $defaults = [
            'title' => 'Заголовок по умолчанию',
            'url' => 'http://ws-pro.ru/',
            'price' => '0',
            'descr' => 'краткое описание отсутствует',
            'content' => [
                'head'   => '<h1>Раздел в стадии наполнения</h1>',
                'body'   => '<p>Мы обязательно позаботимся о подробном описании для этой позиции :)</p>',
                'footer' => '<p>Предлагаем вам выбрать другую страницу для изучения:<br><a href="/portfolio">среди наших работ</a><br>или перейти<br><a>на главную</a></p>',
            ],
            'image' => ['img_replacement.png'],
            'files' => [
                ['title' => 'Коммерческое предложение', 'path' => 'kp.pdf']
            ],
            'date' => '-',
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

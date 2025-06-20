<?php

namespace Database\Seeders\Helpers;

class BlockContentHelper
{
    /**
     * Возвращает массив данных блока по ключу из JSON-файла или заглушку.
     *
     * @param string $key
     * @return array{
     *     title: string,
     *     price: string,
     *     descr: string,
     *     content: array{head: string, body: string, footer: string},
     *     image: string[],
     *     files: array<array{title: string, path: string}>
     * }
     */
    public static function getData(string $key): array
    {
        $jsonPath = storage_path("app/blocks/{$key}.json");

        if (!file_exists($jsonPath)) {
            return self::stub($key, 'Файл не найден');
        }

        $raw = file_get_contents($jsonPath);
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            return self::stub($key, 'Ошибка формата JSON');
        }

        return [
            'title'   => $data['title'] ?? "Заголовок по умолчанию",
            'price'   => $data['price'] ?? "0",
            'descr'   => $data['descr'] ?? "Описание отсутствует",
            'content' => [
                'head'   => $data['content']['head'] ?? '<h1>Заглушка H1</h1>',
                'body'   => $data['content']['body'] ?? '<p>Заглушка текста</p>',
                'footer' => $data['content']['footer'] ?? '<footer>Конец</footer>',
            ],
            'image'   => is_array($data['image'] ?? null) ? $data['image'] : ['noimage1.png', 'noimage2.png'],
            'files'   => is_array($data['files'] ?? null) ? $data['files'] : [
                ['title' => 'Файл недоступен', 'path' => 'nofile1.pdf'],
                ['title' => 'Файл отсутствует', 'path' => 'nofile2.pdf'],
            ],
        ];
    }

    /**
     * Получение контентной части (body) для сидера
     *
     * @param string $key
     * @return string
     */
    public static function getContent(string $key): string
    {
        $data = self::getData($key);
        return $data['content']['body'];
    }

    /**
     * Заглушка с причиной неуспеха
     */
    protected static function stub(string $key, string $reason): array
    {
        return [
            'title'   => "Заглушка: {$key}",
            'price'   => "0",
            'descr'   => $reason,
            'content' => [
                'head'   => '<h1>Ошибка</h1>',
                'body'   => "<p>{$reason}</p>",
                'footer' => '<footer>Нет данных</footer>',
            ],
            'image'   => ['noimage1.png', 'noimage2.png'],
            'files'   => [
                ['title' => 'Файл недоступен', 'path' => 'nofile1.pdf'],
                ['title' => 'Файл отсутствует', 'path' => 'nofile2.pdf'],
            ],
        ];
    }
}

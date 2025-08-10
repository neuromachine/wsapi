<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailSend extends Command
{
    /**
     * Название команды
     *
     * @var string
     */
    protected $signature = 'mail:test';

    /**
     * Описание команды
     *
     * @var string
     */
    protected $description = 'Тест отправки письма через текущую конфигурацию SMTP';

    /**
     * Логика команды
     */
    public function handle()
    {
        $to = $this->ask('Введите email получателя', 'lili@ws-pro.ru');

        Mail::raw("Тестовое письмо из Laravel 12.\n\nДата: " . now(), function ($msg) use ($to) {
            $msg->to($to)
                ->subject('Тест SMTP — WS API');
        });

        $this->info("Письмо отправлено на {$to}");
    }
}

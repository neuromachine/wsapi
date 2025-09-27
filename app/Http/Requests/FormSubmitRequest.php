<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormSubmitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $key = $this->input('form_key');

        return match($key) {
            'feedback' => [
                'name' => 'required|string|max:255',
                'message' => 'required|string',
                'email' => 'nullable|email',
            ],
            'subscribe' => [
                'email' => 'required|email',
            ],
            'simplified' => [
                'mix' => [
                    'required',
                    'string',
                    'max:255',

                    // Переходник: принимает email, URL, telegram-ник (@name) или телефон
                    function ($attribute, $value, $fail) {
                        $v = trim((string) $value);

                        // 1) Email
                        if (filter_var($v, FILTER_VALIDATE_EMAIL)) {
                            return;
                        }

                        // 2) URL
                        if (filter_var($v, FILTER_VALIDATE_URL)) {
                            return;
                        }

                        // 3) Telegram-ник: допускаем с или без собачки, 5-32 символа (буквы, цифры, _)
                        if (preg_match('/^@?[A-Za-z0-9_]{5,32}$/', $v)) {
                            return;
                        }

                        // 4) Телефон: удаляем все не-цифры, проверяем длину (7..15 цифр — гибко для разных стран)
                        $digits = preg_replace('/\D+/', '', $v);
                        if (strlen($digits) >= 7 && strlen($digits) <= 15) {
                            return;
                        }

                        // Иначе — не подходит под ни один формат
                        $fail('Поле ' . $attribute . ' должно быть email, телефонным номером, Telegram-ником или ссылкой.');
                    },
                ],
            ],
            default => [
                'form_key' => 'required|string',
            ],
        };
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->input('data') ?? []);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Пожалуйста проверьте заполнение формы',
                'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Поле name обязательно',
            'message.required' => 'Поле message обязательно',
            'email.email' => 'Неверный формат email',
        ];
    }
}

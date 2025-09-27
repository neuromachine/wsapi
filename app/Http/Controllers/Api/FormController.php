<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\FormSubmitRequest;
use App\Models\Form;
use App\Enums\FormStatus;

class FormController extends Controller
{
    public function store(FormSubmitRequest $request)
    {

        $validated = $request->validated();
        $raw = $request->input('data');

        $form = Form::create([
            'form_key' => $request->form_key,
            'data'     => $raw,
            'meta'     => $request->meta ?? null,
            'status' => FormStatus::Pending,
        ]);

        return response()->json([
            'success' => true,
            'id'      => $form->id,
            'status'  => FormStatus::Pending,
        ]);
    }
}

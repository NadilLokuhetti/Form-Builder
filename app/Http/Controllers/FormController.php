<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        $forms = Form::withCount('submissions')->get();
        return response()->json($forms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $form = Form::create($request->only('title'));

        return response()->json($form, 201);
    }

    public function show(Form $form)
    {
        $form->load('fields');
        return response()->json($form);
    }

    public function update(Request $request, Form $form)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $form->update($request->only('title'));

        return response()->json($form);
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return response()->json(null, 204);
    }
}
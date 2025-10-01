<?php
// app/Http/Controllers/FormController.php

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array'
        ]);

        $form = Form::create($validated);

        foreach ($request->fields as $fieldData) {
            $form->fields()->create([
                'type' => $fieldData['type'],
                'label' => $fieldData['label'],
                'options' => $fieldData['options'] ?? null,
                'required' => $fieldData['required'] ?? false,
                'order' => $fieldData['order'] ?? 0
            ]);
        }

        return response()->json($form->load('fields'));
    }

    public function show(Form $form)
    {
        return response()->json($form->load('fields'));
    }

    public function update(Request $request, Form $form)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array'
        ]);

        $form->update($validated);

        $form->fields()->delete();

        foreach ($request->fields as $fieldData) {
            $form->fields()->create([
                'type' => $fieldData['type'],
                'label' => $fieldData['label'],
                'options' => $fieldData['options'] ?? null,
                'required' => $fieldData['required'] ?? false,
                'order' => $fieldData['order'] ?? 0
            ]);
        }

        return response()->json($form->load('fields'));
    }

    public function destroy(Form $form)
    {
        $form->delete();
        return response()->json(['message' => 'Form deleted successfully']);
    }
}

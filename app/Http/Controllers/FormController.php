<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Field;
use Illuminate\Http\Request;

class FormController extends Controller
{
    // GET /api/forms
    public function index()
    {
        return Form::with('fields')->get();
    }

    // POST /api/forms
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'fields' => 'required|array',
        ]);

        $form = Form::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        foreach ($data['fields'] as $i => $field) {
            $form->fields()->create([
                'label'    => $field['label'],
                'type'     => $field['type'],
                'required' => $field['required'] ?? false,
                'order'    => $i,
                'options'  => $field['options'] ?? null,
            ]);
        }

        return response()->json($form->load('fields'), 201);
    }

    // GET /api/forms/{form}
    public function show(Form $form)
    {
        return $form->load('fields');
    }

    // PUT /api/forms/{form}
    public function update(Request $request, Form $form)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'fields' => 'nullable|array',
        ]);

        $form->update($data);

        if (isset($data['fields'])) {
            $form->fields()->delete(); // replace old fields
            foreach ($data['fields'] as $i => $field) {
                $form->fields()->create([
                    'label'    => $field['label'],
                    'type'     => $field['type'],
                    'required' => $field['required'] ?? false,
                    'order'    => $i,
                    'options'  => $field['options'] ?? null,
                ]);
            }
        }

        return $form->load('fields');
    }

    // DELETE /api/forms/{form}
    public function destroy(Form $form)
    {
        $form->delete();
        return response()->json(['message' => 'Form deleted']);
    }

    // GET /api/forms/{form}/preview
    public function preview(Form $form)
    {
        return $form->load('fields');
    }
}
